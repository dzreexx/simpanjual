<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $table = 'withdrawal_requests';
    protected $primaryKey = 'wr_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'wr_number',
        'status',
        'purpose',
        'source_location',
        'tracking_number',
        'expedition_name',
        'notes',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'recipient_address',
    ];

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouses::class, 'source_location', 'id_warehouse');
    }

    public function items()
    {
        return $this->hasMany(WithdrawalRequestItem::class, 'wr_number', 'wr_number');
    }
}
