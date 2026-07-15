<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderLog extends Model
{
    protected $table = 'sales_order_logs';

    protected $fillable = [
        'so_number',
        'id_user',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'so_number', 'so_number');
    }
}
