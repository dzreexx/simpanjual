<?php

namespace App\Imports;

use App\Models\SalesOrder;
use App\Models\Products;
use App\Models\Warehouses;
use App\Models\Inventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SalesOrderImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $imported = 0;
    protected int $skipped  = 0;
    protected array $errors = [];

    public function collection(Collection $rows)
    {
        $brandId = Session::get('brand_id');

        foreach ($rows as $index => $row) {
            $rowNum        = $index + 2;
            $customerName  = trim($row['customer_name'] ?? '');
            $productName   = trim($row['product_name'] ?? '');
            $warehouseName = trim($row['warehouse_name'] ?? '');
            $qty           = trim($row['qty'] ?? '');
            $status        = strtolower(trim($row['status'] ?? 'unpaid'));

            // Validasi wajib
            if ($customerName === '' || $productName === '' || $warehouseName === '' || $qty === '') {
                $this->errors[] = "Baris {$rowNum}: Kolom wajib (Customer Name, Product Name, Warehouse, Qty) kosong — dilewati.";
                $this->skipped++;
                continue;
            }

            // Cari produk
            $product = Products::where('product_name', $productName)
                ->where('id_brand', $brandId)
                ->first();

            if (!$product) {
                $this->errors[] = "Baris {$rowNum}: Produk \"{$productName}\" tidak ditemukan — dilewati.";
                $this->skipped++;
                continue;
            }

            // Cari warehouse
            $warehouse = Warehouses::where('warehouse_name', $warehouseName)->first();

            if (!$warehouse) {
                $this->errors[] = "Baris {$rowNum}: Warehouse \"{$warehouseName}\" tidak ditemukan — dilewati.";
                $this->skipped++;
                continue;
            }

            // Validasi qty
            if (!is_numeric($qty) || intval($qty) <= 0) {
                $this->errors[] = "Baris {$rowNum}: Qty harus angka positif — dilewati.";
                $this->skipped++;
                continue;
            }

            $qty = intval($qty);

            // Cek/buat inventory
            $inventory = Inventory::firstOrCreate(
                [
                    'id_product' => $product->id_product,
                    'id_warehouse' => $warehouse->id_warehouse
                ],
                [
                    'stock' => 0
                ]
            );

            if ($status === 'completed') {
                if ($inventory->stock < $qty) {
                    $available = $inventory->stock ?? 0;
                    $this->errors[] = "Baris {$rowNum}: Stok tidak cukup untuk \"{$productName}\" di \"{$warehouseName}\" (tersedia: {$available}) — dilewati.";
                    $this->skipped++;
                    continue;
                }
            }

            // Validasi status
            $allowedStatus = ['unpaid', 'new order', 'hold', 'ready to ship', 'shipping', 'completed', 'cancelled', 'missing data', 'oversell'];
            if (!in_array($status, $allowedStatus)) {
                $status = 'unpaid';
            }

            // Generate SO Number
            $soNumber = 'SO-' . str_pad($brandId, 3, '0', STR_PAD_LEFT)
                      . str_pad($product->id_product, 3, '0', STR_PAD_LEFT)
                      . str_pad($qty, 5, '0', STR_PAD_LEFT)
                      . now()->format('YmdHis') . rand(10, 99);

            $grandTotal = $product->price * $qty;

            SalesOrder::create([
                'so_number'       => $soNumber,
                'so_customer'     => $customerName,
                'status'          => $status,
                'total_amount'    => $qty,
                'grand_total'     => $grandTotal,
                'id_brand'        => $brandId,
                'id_product'      => $product->id_product,
                'source_location' => $warehouse->id_warehouse,
            ]);

            // Catat Activity Log - SO Created via Import
            \App\Models\SalesOrderLog::create([
                'so_number'   => $soNumber,
                'id_user'     => auth()->id(),
                'action'      => 'Created via Import',
                'description' => 'Sales Order imported from Excel file.',
            ]);

            // Kurangi stok jika status completed
            if ($status === 'completed') {
                $inventory->decrement('stock', $qty);
            }

            $this->imported++;
        }
    }

    public function getImported(): int  { return $this->imported; }
    public function getSkipped(): int   { return $this->skipped; }
    public function getErrors(): array  { return $this->errors; }
}
