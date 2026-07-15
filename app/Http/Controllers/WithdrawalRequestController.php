<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawalRequest;
use App\Models\WithdrawalRequestItem;
use App\Models\Warehouses;
use App\Models\Products;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawalRequestController extends Controller
{
    const STATUSES = [
        'on going' => 'On Going',
        'done'     => 'Done',
    ];

    // ─────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request, $status = null)
    {
        $query = WithdrawalRequest::with(['sourceWarehouse', 'items.product']);

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
                if ($searchBy === 'wr_number') {
                    $q->where('wr_number', 'LIKE', "%$search%");
                } elseif ($searchBy === 'product') {
                    $q->whereHas('items.product', fn($pq) => $pq->where('product_name', 'LIKE', "%$search%"));
                } elseif ($searchBy === 'warehouse') {
                    $q->whereHas('sourceWarehouse', fn($wq) => $wq->where('warehouse_name', 'LIKE', "%$search%"));
                } elseif ($searchBy === 'purpose') {
                    $q->where('purpose', 'LIKE', "%$search%");
                } else {
                    $q->where('wr_number', 'LIKE', "%$search%")
                      ->orWhere('purpose', 'LIKE', "%$search%")
                      ->orWhereHas('items.product', fn($pq) => $pq->where('product_name', 'LIKE', "%$search%"));
                }
            });
        }

        $perPage            = $request->input('per_page', 10);
        $withdrawalRequests = $query->latest()->paginate($perPage);
        $withdrawalRequests->appends($request->all());

        $warehouses    = Warehouses::all();
        $products      = Products::all();
        $statuses      = self::STATUSES;
        $currentStatus = $status ?? 'all';

        return view('pages.withdrawalrequest', compact(
            'withdrawalRequests', 'warehouses', 'products', 'statuses', 'currentStatus', 'status'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'purpose'              => 'required|string|max:255',
            'source_location'      => 'required|exists:warehouses,id_warehouse',
            'tracking_number'      => 'nullable|string|max:255',
            'expedition_name'      => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
            'recipient_name'       => 'required|string|max:255',
            'recipient_phone'      => 'required|string|max:50',
            'recipient_email'      => 'nullable|email|max:255',
            'recipient_address'    => 'required|string',
            'items'                => 'required|array|min:1',
            'items.*.id_product'   => 'required|exists:products,id_product',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.selling_price'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $prefix   = 'WR-' . now()->format('Ymd') . '-';
            $last     = WithdrawalRequest::where('wr_number', 'LIKE', $prefix . '%')->orderByDesc('wr_number')->first();
            $seq      = $last ? intval(substr($last->wr_number, -4)) + 1 : 1;
            $wrNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

            WithdrawalRequest::create([
                'wr_number'         => $wrNumber,
                'status'            => 'on going',
                'purpose'           => $request->purpose,
                'source_location'   => $request->source_location,
                'tracking_number'   => $request->tracking_number,
                'expedition_name'   => $request->expedition_name,
                'notes'             => $request->notes,
                'recipient_name'    => $request->recipient_name,
                'recipient_phone'   => $request->recipient_phone,
                'recipient_email'   => $request->recipient_email,
                'recipient_address' => $request->recipient_address,
            ]);

            foreach ($request->items as $item) {
                $qty        = (int) $item['quantity'];
                $sellPrice  = (float) $item['selling_price'];
                $totalPrice = $qty * $sellPrice;

                WithdrawalRequestItem::create([
                    'wr_number'     => $wrNumber,
                    'id_product'    => $item['id_product'],
                    'quantity'      => $qty,
                    'selling_price' => $sellPrice,
                    'total_price'   => $totalPrice,
                ]);

                // Decrement stock from source warehouse
                $inv = Inventory::where('id_product', $item['id_product'])
                                ->where('id_warehouse', $request->source_location)
                                ->first();
                if ($inv) {
                    $inv->decrement('stock', $qty);
                }
            }
        });

        return redirect()->route('withdrawalrequest')->with('success', 'Withdrawal Request berhasil dibuat.');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW (AJAX JSON for detail modal)
    // ─────────────────────────────────────────────────────────────
    public function show($wr_number)
    {
        $wr = WithdrawalRequest::with([
            'sourceWarehouse',
            'items.product',
        ])->findOrFail($wr_number);

        $data                     = $wr->toArray();
        $data['source_warehouse'] = $wr->sourceWarehouse;

        $data['items'] = collect($wr->items)->map(function ($item) {
            $arr            = $item->toArray();
            $arr['product'] = $item->product;
            return $arr;
        })->values()->toArray();

        return response()->json([
            'withdrawal' => $data,
            'statuses'   => self::STATUSES,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE STATUS
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, $wr_number)
    {
        $request->validate([
            'status' => 'required|in:on going,done',
        ]);

        $wr      = WithdrawalRequest::findOrFail($wr_number);
        $isAjax  = $request->wantsJson() || $request->ajax();

        $wr->update(['status' => $request->status]);

        $msg = 'Status Withdrawal Request berhasil diupdate.';
        if ($isAjax) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->back()->with('success', $msg);
    }

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy($wr_number)
    {
        WithdrawalRequest::findOrFail($wr_number)->delete();
        return redirect()->route('withdrawalrequest')->with('success', 'Withdrawal Request berhasil dihapus.');
    }
}
