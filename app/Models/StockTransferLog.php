<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferLog extends Model
{
    protected $table = 'stock_transfer_logs';

    protected $fillable = [
        'tr_number',
        'id_user',
        'type',
        'old_status',
        'new_status',
        'item_id',
        'item_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'tr_number', 'tr_number');
    }

    public function item()
    {
        return $this->belongsTo(StockTransferItem::class, 'item_id', 'id');
    }
}
