<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    const MORPH_TYPE = 'ingredient';

    protected $fillable = ['product_id', 'name', 'cost'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
