<?php

namespace App\Imports;

use App\Models\PurchaseOrder;
use App\Models\Products;
use App\Models\Warehouses;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PurchaseOrderImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $imported = 0;
    protected int $skipped  = 0;
    protected array $errors = [];

    public function collection(Collection $rows)
    {
        $brandId = Session::get('brand_id');

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // header = baris 1

            $productName  = trim($row['product_name'] ?? '');
            $warehouseName = trim($row['warehouse_name'] ?? '');
            $qty          = trim($row['qty'] ?? '');
            $status       = strtolower(trim($row['status'] ?? 'pending'));

            // Validasi field wajib tidak kosong
            if ($productName === '' || $warehouseName === '' || $qty === '') {
                $this->errors[] = "Baris {$rowNum}: Kolom Product Name, Warehouse Name, atau Qty kosong — dilewati.";
                $this->skipped++;
                continue;
            }

            // Cari product berdasarkan nama & brand aktif
            $product = Products::where('product_name', $productName)
                ->where('id_brand', $brandId)
                ->first();

            if (!$product) {
                $this->errors[] = "Baris {$rowNum}: Produk \"{$productName}\" tidak ditemukan untuk brand ini — dilewati.";
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

            // Validasi qty numerik positif
            if (!is_numeric($qty) || intval($qty) <= 0) {
                $this->errors[] = "Baris {$rowNum}: Qty harus angka positif — dilewati.";
                $this->skipped++;
                continue;
            }

            // Validasi status
            $allowedStatus = ['pending', 'approved', 'completed', 'cancelled'];
            if (!in_array($status, $allowedStatus)) {
                $status = 'pending';
            }

            $po = PurchaseOrder::create([
                'id_brand'     => $brandId,
                'id_product'   => $product->id_product,
                'id_warehouse' => $warehouse->id_warehouse,
                'stock'        => intval($qty),
                'status'       => $status,
            ]);

            // Generate PO Number if empty
            if (!$po->po_number) {
                $po->po_number = 'PO-' . str_pad($po->id_purchase_order, 5, '0', STR_PAD_LEFT);
                $po->save();
            }

            if ($status === 'completed') {
                $inventory = \App\Models\Inventory::firstOrCreate(
                    [
                        'id_product' => $product->id_product,
                        'id_warehouse' => $warehouse->id_warehouse
                    ],
                    [
                        'stock' => 0
                    ]
                );
                $inventory->increment('stock', intval($qty));
            }

            $this->imported++;
        }
    }

    public function getImported(): int  { return $this->imported; }
    public function getSkipped(): int   { return $this->skipped; }
    public function getErrors(): array  { return $this->errors; }
}
