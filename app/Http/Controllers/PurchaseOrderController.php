<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use App\Models\Products;
use App\Models\Warehouses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLog;
use App\Imports\PurchaseOrderImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


class PurchaseOrderController extends Controller
{
    public function index(Request $request, $status = null)
    {
        $query = PurchaseOrder::where('id_brand', Session::get('brand_id'))
            ->with(['product', 'warehouse']);

        // 1. Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        // 2. Filter Status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // 3. Search (Fixed — search by po_number, product name, or vendor name)
        if ($request->filled('search')) {
            $search = $request->search;
            $searchBy = $request->search_by;
            $query->where(function ($q) use ($search, $searchBy) {
                if ($searchBy == 'po_number') {
                    $q->where('po_number', 'LIKE', "%$search%");
                } elseif ($searchBy == 'product') {
                    $q->whereHas('product', function ($pq) use ($search) {
                        $pq->where('product_name', 'LIKE', "%$search%");
                    });
                } elseif ($searchBy == 'vendor') {
                    $q->where('vendor_name', 'LIKE', "%$search%");
                } else {
                    // Search all fields
                    $q->where('po_number', 'LIKE', "%$search%")
                        ->orWhere('vendor_name', 'LIKE', "%$search%")
                        ->orWhereHas('product', function ($pq) use ($search) {
                            $pq->where('product_name', 'LIKE', "%$search%");
                        });
                }
            });
        }

        $purchaseOrders = $query->latest()->paginate($request->input('per_page', 10));
        $purchaseOrders->appends($request->all());

        return view('pages.purchaseorder', compact('purchaseOrders', 'status'));
    }

    public function show($id)
    {
        $brandId = Session::get('brand_id');

        $purchaseOrder = PurchaseOrder::with(['product', 'warehouse', 'brand', 'logs.user'])
            ->where('id_purchase_order', $id)
            ->where('id_brand', $brandId)
            ->firstOrFail();

        return view('pages.purchaseorder-detail', compact('purchaseOrder'));
    }

    public function exportExcel(Request $request)
    {
        $brandId = Session::get('brand_id');

        $query = PurchaseOrder::where('id_brand', $brandId)
            ->with(['product', 'warehouse']);

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        $orders = $query->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Purchase Order');

        // --- Header ---
        $headers = ['No', 'PO Number', 'Product', 'Warehouse', 'Qty', 'Status', 'Created Date'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // --- Data ---
        foreach ($orders as $i => $po) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $po->po_number ?? 'PO-' . str_pad($po->id_purchase_order, 5, '0', STR_PAD_LEFT));
            $sheet->setCellValue("C{$row}", $po->product->product_name ?? '-');
            $sheet->setCellValue("D{$row}", $po->warehouse->warehouse_name ?? '-');
            $sheet->setCellValue("E{$row}", $po->stock);
            $sheet->setCellValue("F{$row}", $po->status ?? 'pending');
            $sheet->setCellValue("G{$row}", $po->created_at->format('d/m/Y H:i'));

            // Zebra stripe
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                ]);
            }
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFEEEEEE']]],
            ]);
        }

        // --- Column widths ---
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->freezePane('A2');

        // --- Filename with date range ---
        $from = $request->filled('start_date') ? '_from_' . $request->start_date : '';
        $to   = $request->filled('end_date')   ? '_to_'   . $request->end_date   : '';
        $filename = 'PurchaseOrder' . $from . $to . '_' . now()->format('Ymd') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function createPurchaseOrder()
    {
        $brand_id = session('brand_id');
        $selectedBrands = Brands::where('id_brand', $brand_id)->get()->first();
        $brands = Brands::all();
        $productSelectedBrand = Products::where('id_brand', $brand_id)->get();
        $products = Products::all();
        $warehouses = Warehouses::all();
        return view('addpurchaseorder', compact('brands', 'products', 'warehouses', 'brand_id', 'selectedBrands', 'productSelectedBrand'));
    }

    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'id_warehouse' => 'required',
            'id_product' => 'required',
            'stock' => 'required',
        ]);

        $brand_id = $request->id_brand ?: $request->brand_id;

        $purchase_order = new PurchaseOrder();
        $purchase_order->id_warehouse = $request->id_warehouse;
        $purchase_order->id_brand = $brand_id;
        $purchase_order->id_product = $request->id_product;
        $purchase_order->stock = $request->stock;
        $purchase_order->buying_price = $request->buying_price ?? 0;
        $purchase_order->vendor_name = $request->vendor_name;
        $purchase_order->vendor_phone = $request->vendor_phone;
        $purchase_order->vendor_email = $request->vendor_email;
        $purchase_order->vendor_address = $request->vendor_address;
        $purchase_order->save();

        // Generate PO Number
        $poNumber = 'PO-' . str_pad($purchase_order->id_purchase_order, 5, '0', STR_PAD_LEFT);
        $purchase_order->po_number = $poNumber;
        $purchase_order->save();

        // Catat Activity Log - PO Created
        PurchaseOrderLog::create([
            'id_purchase_order' => $purchase_order->id_purchase_order,
            'id_user'           => auth()->id(),
            'action'            => 'Created',
            'description'       => 'Purchase Order created manually.',
        ]);

        return redirect()->route('dashboard.inbound');
    }

    public function importPO(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'import_file.required' => 'Pilih file terlebih dahulu.',
            'import_file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'import_file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new PurchaseOrderImport();
        Excel::import($import, $request->file('import_file'));

        $imported = $import->getImported();
        $skipped  = $import->getSkipped();
        $errors   = $import->getErrors();

        $message = "Import selesai: {$imported} data berhasil diimpor.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return redirect()->route('purchaseorder')
            ->with('import_success', $message)
            ->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Purchase Order Template');

        // --- Header Row ---
        $headers = ['Product Name*', 'Warehouse Name*', 'Qty*', 'Status'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i); // A, B, C, D
            $sheet->setCellValue("{$col}1", $header);
        }

        // Style header: biru tua, teks putih, bold
        $headerRange = 'A1:D1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);

        // --- Row 2: Keterangan tipe ---
        $hints = ['Tipe: Text (nama produk)', 'Tipe: Text (nama gudang)', 'Tipe: Number', 'pending/approved/completed/cancelled (default: pending)'];
        foreach ($hints as $i => $hint) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}2", $hint);
        }
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['argb' => 'FF555555'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F4FA']],
        ]);

        // --- Row 3: Contoh data ---
        $example = ['Produk A', 'Gudang Jakarta', 50, 'pending'];
        foreach ($example as $i => $val) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $val);
        }
        $sheet->getStyle('A3:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEBF5FB']],
        ]);

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(16);

        // Freeze header
        $sheet->freezePane('A3');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_PurchaseOrder.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,completed,cancelled',
        ]);

        $brandId = Session::get('brand_id');

        $po = PurchaseOrder::where('id_purchase_order', $id)
            ->where('id_brand', $brandId)
            ->firstOrFail();

        $oldStatus = strtolower($po->status ?? 'pending');

        // Prevent update if already completed or cancelled
        if (in_array($oldStatus, ['completed', 'cancelled'])) {
            return redirect()->back()->withErrors(['status' => 'Status tidak dapat diubah karena dokumen ini sudah Completed atau Cancelled.']);
        }

        $newStatus = strtolower($request->status);
        $po->status = $newStatus;

        // If status changed to completed, increment stock in the warehouse
        if ($newStatus === 'completed') {
            $inventory = \App\Models\Inventory::firstOrCreate(
                [
                    'id_product' => $po->id_product,
                    'id_warehouse' => $po->id_warehouse
                ],
                [
                    'stock' => 0
                ]
            );
            $inventory->increment('stock', $po->stock);
        }

        $po->save();

        // Log the status change
        PurchaseOrderLog::create([
            'id_purchase_order' => $po->id_purchase_order,
            'id_user'           => auth()->id(),
            'action'            => 'Status Updated',
            'description'       => "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . ".",
        ]);

        return redirect()->back()->with('status_success', 'Status berhasil diupdate menjadi ' . ucfirst($newStatus));
    }
}
