<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $table = 'stock_transfer_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tr_number',
        'id_product',
        'quantity',
        'delivery_quantity',
        'received_qty',
        'item_status',
    ];

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'tr_number', 'tr_number');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_product', 'id_product');
    }
}
