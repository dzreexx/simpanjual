<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockTransferLog;
use App\Models\Warehouses;
use App\Models\Products;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockTransferController extends Controller
{
    // 'received' removed per new business flow
    const STATUSES = [
        'picking'               => 'Picking',
        'packing'               => 'Packing',
        'on delivery'           => 'On Delivery',
        'receiving'             => 'Receiving',
        'submit qc'             => 'Submit QC',
        'accepted at warehouse' => 'Accepted at Warehouse',
    ];

    // Status order for forward-only progression
    const STATUS_ORDER = ['picking', 'packing', 'on delivery', 'receiving', 'submit qc', 'accepted at warehouse'];

    // Statuses where delivery_qty is locked (readonly) for items
    const DELIVERY_LOCKED_AFTER = ['receiving', 'submit qc', 'accepted at warehouse'];

    // Statuses where ALL item fields are locked
    const ITEM_FULLY_LOCKED = ['accepted at warehouse'];

    // ─────────────────────────────────────────────────────────────
    // CHECK STOCK (AJAX)
    // ─────────────────────────────────────────────────────────────
    public function checkStock(Request $request)
    {
        $idProduct   = $request->id_product;
        $idWarehouse = $request->id_warehouse;

        if (!$idProduct || !$idWarehouse) {
            return response()->json(['stock' => 0]);
        }

        $stock = Inventory::where('id_product', $idProduct)
                          ->where('id_warehouse', $idWarehouse)
                          ->value('stock') ?? 0;

        return response()->json(['stock' => (int) $stock]);
    }

    // ─────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request, $status = null)
    {
        $query = StockTransfer::with(['sourceWarehouse', 'destinationWarehouse', 'items.product']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        if ($request->filled('search')) {
            $search   = $request->search;
            $searchBy = $request->search_by;
            $query->where(function ($q) use ($search, $searchBy) {
                if ($searchBy === 'tr_number') {
                    $q->where('tr_number', 'LIKE', "%$search%");
                } elseif ($searchBy === 'product') {
                    $q->whereHas('items.product', fn($pq) => $pq->where('product_name', 'LIKE', "%$search%"));
                } elseif ($searchBy === 'warehouse') {
                    $q->whereHas('sourceWarehouse', fn($wq) => $wq->where('warehouse_name', 'LIKE', "%$search%"))
                      ->orWhereHas('destinationWarehouse', fn($wq) => $wq->where('warehouse_name', 'LIKE', "%$search%"));
                } else {
                    $q->where('tr_number', 'LIKE', "%$search%")
                      ->orWhereHas('items.product', fn($pq) => $pq->where('product_name', 'LIKE', "%$search%"));
                }
            });
        }

        $perPage        = $request->input('per_page', 10);
        $stockTransfers = $query->latest()->paginate($perPage);
        $stockTransfers->appends($request->all());

        $warehouses    = Warehouses::all();
        $products      = Products::all();
        $statuses      = self::STATUSES;
        $currentStatus = $status ?? 'all';

        return view('pages.stocktransfer', compact(
            'stockTransfers', 'warehouses', 'products', 'statuses', 'currentStatus', 'status'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'source_location'      => 'required|exists:warehouses,id_warehouse',
            'destination_location' => 'required|exists:warehouses,id_warehouse|different:source_location',
            'items'                => 'required|array|min:1',
            'items.*.id_product'   => 'required|exists:products,id_product',
            'items.*.quantity'     => 'required|integer|min:1',
        ], [
            'destination_location.different' => 'Source dan destination warehouse tidak boleh sama.',
        ]);

        DB::transaction(function () use ($request) {
            $prefix   = 'TR-' . now()->format('Ymd') . '-';
            $last     = StockTransfer::where('tr_number', 'LIKE', $prefix . '%')->orderByDesc('tr_number')->first();
            $seq      = $last ? intval(substr($last->tr_number, -4)) + 1 : 1;
            $trNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

            StockTransfer::create([
                'tr_number'            => $trNumber,
                'status'               => 'picking',
                'source_location'      => $request->source_location,
                'destination_location' => $request->destination_location,
                'notes'                => $request->notes,
            ]);

            StockTransferLog::create([
                'tr_number'  => $trNumber,
                'id_user'    => Auth::id(),
                'type'       => 'transfer',
                'old_status' => null,
                'new_status' => 'picking',
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'tr_number'         => $trNumber,
                    'id_product'        => $item['id_product'],
                    'quantity'          => $item['quantity'],
                    'delivery_quantity' => 0,
                    'received_qty'      => 0,
                    'item_status'       => 'picking',
                ]);
            }
        });

        return redirect()->route('stocktransfer')->with('success', 'Stock Transfer berhasil dibuat.');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW (AJAX JSON for detail modal)
    // ─────────────────────────────────────────────────────────────
    public function show($tr_number)
    {
        $transfer = StockTransfer::with([
            'sourceWarehouse',
            'destinationWarehouse',
            'items.product',
            'logs.user',
        ])->findOrFail($tr_number);

        $data                          = $transfer->toArray();
        $data['source_warehouse']      = $transfer->sourceWarehouse;
        $data['destination_warehouse'] = $transfer->destinationWarehouse;

        $data['items'] = collect($transfer->items)->map(function ($item) {
            $arr                    = $item->toArray();
            $arr['product']         = $item->product;
            $arr['qty_warning']     = $this->getItemQtyWarning($arr);
            $arr['delivery_locked'] = in_array($item->item_status, self::DELIVERY_LOCKED_AFTER);
            $arr['fully_locked']    = in_array($item->item_status, self::ITEM_FULLY_LOCKED);
            return $arr;
        })->values()->toArray();

        $data['logs'] = collect($transfer->logs)->map(function ($log) {
            return [
                'id'         => $log->id,
                'type'       => $log->type,
                'old_status' => $log->old_status,
                'new_status' => $log->new_status,
                'item_name'  => $log->item_name,
                'item_id'    => $log->item_id,
                'user_name'  => $log->user ? $log->user->name : 'System',
                'created_at' => $log->created_at
                    ? $log->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s')
                    : null,
            ];
        })->values()->toArray();

        return response()->json([
            'transfer'     => $data,
            'statuses'     => self::STATUSES,
            'status_order' => self::STATUS_ORDER,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE TRANSFER STATUS (supports AJAX JSON)
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, $tr_number)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUS_ORDER),
        ]);

        $transfer  = StockTransfer::with('items.product')->findOrFail($tr_number);
        $newStatus = $request->status;
        $oldStatus = $transfer->status;
        $isAjax    = $request->wantsJson() || $request->ajax();

        // ── Accepted at Warehouse validation ─────────────────────
        if ($newStatus === 'accepted at warehouse') {

            // PRIMARY CHECK: ALL items must be 'accepted at warehouse'
            $notAccepted = $transfer->items->filter(
                fn($i) => $i->item_status !== 'accepted at warehouse'
            );

            if ($notAccepted->isNotEmpty()) {
                $names = $notAccepted->map(function ($i) {
                    return '"' . (optional($i->product)->product_name ?? "#{$i->id_product}") . '"';
                })->implode(', ');

                $msg = "Item berikut belum berstatus Accepted at Warehouse: {$names}. Update seluruh item terlebih dahulu.";
                if ($isAjax) {
                    return response()->json(['error' => true, 'message' => $msg]);
                }
                return redirect()->back()->with('warning', $msg);
            }

            // SECONDARY CHECK: qty matching + stock movement
            $warnings = [];
            $canMove  = true;

            foreach ($transfer->items as $item) {
                $qty    = (int) $item->quantity;
                $recQty = (int) $item->received_qty;
                $delQty = (int) $item->delivery_quantity;
                $name   = optional($item->product)->product_name ?? "Product #{$item->id_product}";

                if ($delQty > $qty) {
                    $warnings[] = "\"{$name}\": delivery qty melebihi request ({$delQty} > {$qty}).";
                } elseif ($delQty < $qty) {
                    $warnings[] = "\"{$name}\": delivery qty kurang dari request ({$delQty} < {$qty}).";
                }
                if ($recQty > $qty) {
                    $warnings[] = "\"{$name}\": received qty melebihi request ({$recQty} > {$qty}).";
                } elseif ($recQty < $qty) {
                    $warnings[] = "\"{$name}\": received qty kurang dari request ({$recQty} < {$qty}).";
                }
                if ($recQty !== $qty) {
                    $canMove = false;
                }
            }

            if (!$canMove) {
                $transfer->update(['status' => $newStatus]);
                $this->writeTransferLog($tr_number, $oldStatus, $newStatus);
                $msg = 'Status diupdate ke Accepted at Warehouse, namun stok TIDAK dipindahkan karena: ' . implode(' | ', $warnings);
                if ($isAjax) {
                    return response()->json(['warning' => true, 'message' => $msg]);
                }
                return redirect()->back()->with('warning', $msg);
            }

            // All good → move stock
            DB::transaction(function () use ($transfer, $oldStatus, $tr_number) {
                $srcId  = $transfer->source_location;
                $destId = $transfer->destination_location;

                foreach ($transfer->items as $item) {
                    $srcInv = Inventory::firstOrCreate(
                        ['id_product' => $item->id_product, 'id_warehouse' => $srcId],
                        ['stock' => 0]
                    );
                    $srcInv->decrement('stock', (int) $item->received_qty);

                    $destInv = Inventory::firstOrCreate(
                        ['id_product' => $item->id_product, 'id_warehouse' => $destId],
                        ['stock' => 0]
                    );
                    $destInv->increment('stock', (int) $item->received_qty);
                }

                $transfer->update(['status' => 'accepted at warehouse']);
                $this->writeTransferLog($tr_number, $oldStatus, 'accepted at warehouse');
            });

            $msg = 'Transfer selesai! Stok berhasil dipindahkan ke gudang tujuan.';
            if (!empty($warnings)) {
                $msg .= ' Catatan: ' . implode(' | ', $warnings);
            }
            if ($isAjax) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        // ── Normal status update ──────────────────────────────────
        $transfer->update(['status' => $newStatus]);
        $this->writeTransferLog($tr_number, $oldStatus, $newStatus);

        if ($isAjax) {
            return response()->json(['success' => true, 'message' => 'Status transfer berhasil diupdate.']);
        }
        return redirect()->back()->with('success', 'Status transfer berhasil diupdate.');
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE ITEM STATUS / QTY (supports AJAX JSON)
    // ─────────────────────────────────────────────────────────────
    public function updateItemStatus(Request $request, $id)
    {
        $item      = StockTransferItem::with('product')->findOrFail($id);
        $oldStatus = $item->item_status;
        $isAjax    = $request->wantsJson() || $request->ajax();

        // Block all changes if item is fully locked
        if (in_array($item->item_status, self::ITEM_FULLY_LOCKED)) {
            $msg = 'Item sudah Accepted at Warehouse dan tidak dapat diubah lagi.';
            if ($isAjax) return response()->json(['warning' => true, 'message' => $msg]);
            return redirect()->back()->with('warning', $msg);
        }

        $newStatus = $request->input('item_status', $item->item_status);

        // Delivery qty required for 'on delivery'
        if ($newStatus === 'on delivery') {
            $deliveryQty = $request->input('delivery_quantity', $item->delivery_quantity);
            if ((int) $deliveryQty <= 0) {
                $msg = 'Delivery qty harus diisi dan lebih dari 0 ketika status On Delivery.';
                if ($isAjax) return response()->json(['error' => true, 'message' => $msg]);
                return redirect()->back()->with('warning', $msg);
            }
        }

        // Received qty required for 'accepted at warehouse'
        if ($newStatus === 'accepted at warehouse') {
            $receivedQty = $request->input('received_qty', $item->received_qty);
            if ((int) $receivedQty <= 0) {
                $msg = 'Received qty harus diisi dan lebih dari 0 ketika status Accepted at Warehouse.';
                if ($isAjax) return response()->json(['error' => true, 'message' => $msg]);
                return redirect()->back()->with('warning', $msg);
            }
        }

        $request->validate([
            'item_status'       => 'nullable|in:' . implode(',', self::STATUS_ORDER),
            'delivery_quantity' => 'nullable|integer|min:0',
            'received_qty'      => 'nullable|integer|min:0',
        ]);

        $updateData = [];

        if ($request->filled('item_status')) {
            $updateData['item_status'] = $newStatus;
        }
        // delivery_qty only updatable when not in delivery-locked state
        if (!in_array($item->item_status, self::DELIVERY_LOCKED_AFTER) && $request->has('delivery_quantity')) {
            $updateData['delivery_quantity'] = (int) ($request->delivery_quantity ?? 0);
        }
        if ($request->has('received_qty')) {
            $updateData['received_qty'] = (int) ($request->received_qty ?? 0);
        }

        if (!empty($updateData)) {
            $item->update($updateData);
        }

        // Log status change
        if (isset($updateData['item_status']) && $updateData['item_status'] !== $oldStatus) {
            StockTransferLog::create([
                'tr_number'  => $item->tr_number,
                'id_user'    => Auth::id(),
                'type'       => 'item',
                'old_status' => $oldStatus,
                'new_status' => $updateData['item_status'],
                'item_id'    => $item->id,
                'item_name'  => optional($item->product)->product_name ?? "Product #{$item->id_product}",
            ]);
        }

        // Qty warnings
        $item->refresh();
        $warningArr = [];
        $qty    = (int) $item->quantity;
        $delQty = (int) $item->delivery_quantity;
        $recQty = (int) $item->received_qty;

        if ($delQty > $qty) {
            $warningArr[] = "Delivery qty melebihi request qty ({$delQty} > {$qty})";
        } elseif ($delQty > 0 && $delQty < $qty) {
            $warningArr[] = "Delivery qty kurang dari request qty ({$delQty} < {$qty})";
        }
        if ($recQty > $qty) {
            $warningArr[] = "Received qty melebihi request qty ({$recQty} > {$qty})";
        } elseif ($recQty > 0 && $recQty < $qty) {
            $warningArr[] = "Received qty kurang dari request qty ({$recQty} < {$qty})";
        }

        if (!empty($warningArr)) {
            $msg = 'Item disimpan. ⚠️ ' . implode('; ', $warningArr);
            if ($isAjax) return response()->json(['warning' => true, 'message' => $msg]);
            return redirect()->back()->with('warning', $msg);
        }

        if ($isAjax) return response()->json(['success' => true, 'message' => 'Item berhasil diupdate.']);
        return redirect()->back()->with('success', 'Item berhasil diupdate.');
    }

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy($tr_number)
    {
        StockTransfer::findOrFail($tr_number)->delete();
        return redirect()->route('stocktransfer')->with('success', 'Stock Transfer deleted.');
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────
    private function writeTransferLog(string $trNumber, ?string $oldStatus, string $newStatus): void
    {
        StockTransferLog::create([
            'tr_number'  => $trNumber,
            'id_user'    => Auth::id(),
            'type'       => 'transfer',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    private function getItemQtyWarning(array $item): ?string
    {
        $qty    = (int) $item['quantity'];
        $delQty = (int) $item['delivery_quantity'];
        $recQty = (int) $item['received_qty'];

        $warnings = [];
        if ($delQty > $qty) {
            $warnings[] = "Delivery qty melebihi request ({$delQty} > {$qty})";
        } elseif ($delQty > 0 && $delQty < $qty) {
            $warnings[] = "Delivery qty kurang dari request ({$delQty} < {$qty})";
        }
        if ($recQty > $qty) {
            $warnings[] = "Received qty melebihi request ({$recQty} > {$qty})";
        } elseif ($recQty > 0 && $recQty < $qty) {
            $warnings[] = "Received qty kurang dari request ({$recQty} < {$qty})";
        }

        return count($warnings) > 0 ? implode('; ', $warnings) : null;
    }
}
