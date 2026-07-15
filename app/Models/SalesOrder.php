<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_order';
    protected $primaryKey = 'so_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'so_number',
        'so_customer',
        'total_amount',
        'status',
        'grand_total',
        'delivery_due_date',
        'completed_date',
        'id_brand',
        'id_product',
        'source_location',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class, 'source_location', 'id_warehouse');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_product', 'id_product');
    }

    public function logs()
    {
        return $this->hasMany(SalesOrderLog::class, 'so_number', 'so_number')->latest();
    }
}
