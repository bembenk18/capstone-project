<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;


class Product extends Model
{
    protected $fillable = ['name', 'sku', 'stock', 'warehouse_id', 'image', 'minimum_stock'];



    public function warehouses()
{
    return $this->belongsToMany(Warehouse::class)->withPivot('stock')->withTimestamps();
}

    
    
}
