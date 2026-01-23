<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
   protected $primaryKey = 'id_brand';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'brand_name',
    ];
}
