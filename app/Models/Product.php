<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'stock', 'warehouse_id', 'image'];


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
