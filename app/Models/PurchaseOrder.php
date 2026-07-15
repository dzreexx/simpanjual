<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_purchase_order';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'po_number',
        'id_brand',
        'id_product',
        'status',
        'stock',
        'id_warehouse',
        'buying_price',
        'vendor_name',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_product', 'id_product');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class, 'id_warehouse', 'id_warehouse');
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'id_brand', 'id_brand');
    }

    public function logs()
    {
        return $this->hasMany(PurchaseOrderLog::class, 'id_purchase_order', 'id_purchase_order')->latest();
    }
}
