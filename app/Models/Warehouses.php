<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    protected $primaryKey = 'id_warehouse';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'warehouse_name',
    ];
}
