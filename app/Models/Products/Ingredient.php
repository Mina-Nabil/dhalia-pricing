<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['product_id', 'name', 'cost'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
