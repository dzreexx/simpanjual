<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $primaryKey = 'id_inventory';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_product',
        'id_warehouse',
        'stock',
    ];
}
