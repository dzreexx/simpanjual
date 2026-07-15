<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Products;
use App\Models\Inventory;
use App\Models\SalesOrder;
use App\Imports\SalesOrderImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesOrderController extends Controller
{
    public function index(Request $request, $status = null)
{
    $query = SalesOrder::where('id_brand', Session::get('brand_id'))->with(['warehouse', 'product']);

    // 1. Filter Tanggal (Wajib pakai whereDate agar jam diabaikan, lebih robust dengan Carbon::parse)
    if ($request->filled('start_date')) {
        $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
    }
    if ($request->filled('end_date')) {
        $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
    }

    // 2. Filter Channel
    if ($request->filled('channel')) {
        $query->where('channel', $request->channel);
    }

    // 3. Status Tabs
    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    // 4. Search
    if ($request->filled('search')) {
        $search = $request->search;
        $searchBy = $request->search_by;
        $query->where(function($q) use ($search, $searchBy) {
            if ($searchBy == 'so_number') {
                $q->where('so_number', 'LIKE', "%$search%");
            } elseif ($searchBy == 'customer') {
                $q->where('so_customer', 'LIKE', "%$search%");
            } else {
                $q->where('so_number', 'LIKE', "%$search%")->orWhere('so_customer', 'LIKE', "%$search%");
            }
        });
    }

    $salesOrders = $query->latest()->paginate($request->input('per_page', 10));
    
    // Sangat Penting: Append agar filter tidak hilang saat pindah page
    $salesOrders->appends($request->all());

    return view('pages.salesorder', compact('salesOrders', 'status'));
}

    public function show($so_number)
    {
        // Pastikan SO yang diakses adalah milik brand yang sedang aktif
        $brandId = Session::get('brand_id');

        $salesOrder = SalesOrder::with(['product', 'warehouse', 'logs.user'])
            ->where('so_number', $so_number)
            ->where('id_brand', $brandId)
            ->firstOrFail();

        return view('pages.salesorder-detail', compact('salesOrder'));
    }

    public function createSalesOrder()
    {
        $brand = Session::get('brand_id');
        $products = Products::where('id_brand', $brand)->get();
        return view('addsalesorder', compact('brand', 'products'));
    }

    public function storeSalesOrder(Request $request)
    {
        $validate = $request->validate([
            'customer_name' => 'required',
            'quantity' => 'required|numeric|min:1',
            'id_product' => 'required',
            'id_warehouse' => 'required',
            'status' => 'required',
        ], [
            'customer_name.required' => 'Customer Name is required',
            'quantity.required' => 'Quantity is required',
            'quantity.numeric' => 'Quantity must be a number',
            'quantity.min' => 'Quantity must be at least 1',
            'id_product.required' => 'Product is required',
            'id_warehouse.required' => 'Warehouse is required',
            'status.required' => 'Status is required',
        ]);

        
        $idbrand = Session::get('brand_id');
        $idproduct = $request->id_product;
        $quantity = $request->quantity;
        $idwarehouse = $request->id_warehouse;

        $product = Products::where('id_product', $idproduct)->first();
        
        $soNumber = 'SO-' . str_pad($idbrand, 3, '0', STR_PAD_LEFT) . str_pad($idproduct, 3, '0', STR_PAD_LEFT) . str_pad($quantity, 5, '0', STR_PAD_LEFT) . date('YmdHis');
        $grandTotal = $product->price * $quantity;

        $inventory = Inventory::firstOrCreate(
            [
                'id_product' => $idproduct,
                'id_warehouse' => $idwarehouse
            ],
            [
                'stock' => 0
            ]
        );

        if (strtolower($request->status) === 'completed') {
            if ($inventory->stock < $quantity) {
                return redirect()->back()->with('error', 'Stock is not enough')->withInput();
            }
            $inventory->decrement('stock', $quantity);
        }

        $salesOrder = new SalesOrder();
        $salesOrder->so_number = $soNumber;
        $salesOrder->so_customer = $request->customer_name;
        $salesOrder->status = $request->status;
        $salesOrder->total_amount = $quantity;
        $salesOrder->grand_total = $grandTotal;
        $salesOrder->id_brand = $idbrand;
        $salesOrder->id_product = $idproduct;
        $salesOrder->source_location = $idwarehouse;
        $salesOrder->save();

        // Catat Activity Log - SO Created
        \App\Models\SalesOrderLog::create([
            'so_number'   => $soNumber,
            'id_user'     => auth()->id(),
            'action'      => 'Created',
            'description' => 'Sales Order created manually.',
        ]);

        // dd($salesOrder);
        return redirect()->route('addsalesorder')->with('success', 'Sales Order created successfully')->withInput();
    }

    public function getWarehouses($id_product)
    {
        $inventories = Inventory::with('warehouse')->where('id_product', $id_product)->get();
        return response()->json($inventories);
    }

    // ─── Import ──────────────────────────────────────────────────────────────

    public function importSO(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'import_file.required' => 'Pilih file terlebih dahulu.',
            'import_file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'import_file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new SalesOrderImport();
        Excel::import($import, $request->file('import_file'));

        $imported = $import->getImported();
        $skipped  = $import->getSkipped();
        $errors   = $import->getErrors();

        $message = "Import selesai: {$imported} data berhasil diimpor.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return redirect()->route('salesorder')
            ->with('import_success', $message)
            ->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Order Template');

        $headers = ['Customer Name*', 'Product Name*', 'Warehouse Name*', 'Qty*', 'Status'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);

        $hints = ['Nama customer', 'Nama produk (sesuai sistem)', 'Nama gudang', 'Jumlah (number)', 'unpaid/new order/completed/dll (default: unpaid)'];
        foreach ($hints as $i => $hint) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}2", $hint);
        }
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['argb' => 'FF555555'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F4FA']],
        ]);

        $example = ['John Doe', 'Produk A', 'Gudang Jakarta', 2, 'unpaid'];
        foreach ($example as $i => $val) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $val);
        }
        $sheet->getStyle('A3:E3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEBF5FB']],
        ]);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(38);
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->freezePane('A3');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_SalesOrder.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // ─── Export ──────────────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $brandId = Session::get('brand_id');
        $query = SalesOrder::where('id_brand', $brandId)->with(['warehouse']);

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        $orders = $query->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Order');

        $headers = ['No', 'SO Number', 'Customer', 'Product', 'Warehouse', 'Qty', 'Grand Total', 'Status', 'Created Date'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }
        $lastCol = chr(65 + count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($orders as $i => $so) {
            $row = $i + 2;
            $product = Products::find($so->id_product);
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $so->so_number);
            $sheet->setCellValue("C{$row}", $so->so_customer);
            $sheet->setCellValue("D{$row}", $product->product_name ?? '-');
            $sheet->setCellValue("E{$row}", $so->warehouse->warehouse_name ?? '-');
            $sheet->setCellValue("F{$row}", $so->total_amount);
            $sheet->setCellValue("G{$row}", $so->grand_total);
            $sheet->setCellValue("H{$row}", $so->status);
            $sheet->setCellValue("I{$row}", $so->created_at->format('d/m/Y H:i'));

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                ]);
            }
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFEEEEEE']]],
            ]);
        }

        $widths = [6, 26, 22, 22, 20, 8, 14, 16, 20];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }
        $sheet->freezePane('A2');

        $from = $request->filled('start_date') ? '_from_' . $request->start_date : '';
        $to   = $request->filled('end_date')   ? '_to_'   . $request->end_date   : '';
        $filename = 'SalesOrder' . $from . $to . '_' . now()->format('Ymd') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function updateStatus(Request $request, $so_number)
    {
        $request->validate([
            'status' => 'required|in:unpaid,new order,hold,ready to ship,shipping,completed,cancelled',
        ]);

        $brandId = Session::get('brand_id');

        $so = SalesOrder::where('so_number', $so_number)
            ->where('id_brand', $brandId)
            ->firstOrFail();

        $oldStatus = strtolower($so->status ?? 'unpaid');

        // Prevent update if already completed or cancelled
        if (in_array($oldStatus, ['completed', 'cancelled'])) {
            return redirect()->back()->withErrors(['status' => 'Status tidak dapat diubah karena dokumen ini sudah Completed atau Cancelled.']);
        }

        $newStatus = strtolower($request->status);
        $so->status = $newStatus;

        // If status changed to completed, set completed_date and update stock
        if ($newStatus === 'completed') {
            if (!$so->completed_date) {
                $so->completed_date = now();
            }

            // Update quantity in warehouse/inventory
            $inventory = Inventory::firstOrCreate(
                [
                    'id_product' => $so->id_product,
                    'id_warehouse' => $so->source_location
                ],
                [
                    'stock' => 0
                ]
            );
            $inventory->decrement('stock', $so->total_amount);
        }

        $so->save();

        // Log the status change
        \App\Models\SalesOrderLog::create([
            'so_number'   => $so->so_number,
            'id_user'     => auth()->id(),
            'action'      => 'Status Updated',
            'description' => "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . ".",
        ]);

        return redirect()->back()->with('status_success', 'Status berhasil diupdate menjadi ' . ucfirst($newStatus));
    }
}
