<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $table = 'stock_transfers';
    protected $primaryKey = 'tr_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tr_number',
        'status',
        'source_location',
        'destination_location',
        'notes',
    ];

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouses::class, 'source_location', 'id_warehouse');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouses::class, 'destination_location', 'id_warehouse');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class, 'tr_number', 'tr_number');
    }

    public function logs()
    {
        return $this->hasMany(StockTransferLog::class, 'tr_number', 'tr_number')
                    ->with('user')
                    ->orderBy('created_at', 'asc');
    }
}

