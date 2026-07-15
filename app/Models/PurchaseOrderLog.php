<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLog extends Model
{
    protected $table = 'purchase_order_logs';

    protected $fillable = [
        'id_purchase_order',
        'id_user',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order', 'id_purchase_order');
    }
}
