<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductCost extends Model
{
    const MORPH_TYPE = 'product_cost';

    protected $fillable = ['product_id', 'name', 'cost', 'is_percentage', 'sort_order', 'is_fixed'];

    //always sort by sort_order
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('sort_by_order', function ($query) {
            $query->orderBy('sort_order', 'asc');
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
