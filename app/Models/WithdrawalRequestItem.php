<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequestItem extends Model
{
    protected $table = 'withdrawal_request_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'wr_number',
        'id_product',
        'quantity',
        'selling_price',
        'total_price',
    ];

    public function withdrawalRequest()
    {
        return $this->belongsTo(WithdrawalRequest::class, 'wr_number', 'wr_number');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_product', 'id_product');
    }
}
