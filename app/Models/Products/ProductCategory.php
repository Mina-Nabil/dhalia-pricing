<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    const MORPH_TYPE = 'product_category';

    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
