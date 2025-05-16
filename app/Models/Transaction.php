<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'type', 'quantity', 'note'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
