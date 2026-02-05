<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $primaryKey = 'id_purchase_order';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_brand',
        'id_product',
        'status',
        'stock',
        'id_warehouse',
    ];
}
