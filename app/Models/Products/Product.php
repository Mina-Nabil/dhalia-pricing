<?php

namespace App\Models\Products;

use App\Models\Spec;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const MORPH_TYPE = 'product';

    protected $fillable = ['name', 'product_category_id', 'base_cost', 'spec_id'];

    //function
    public function calculateBaseCost($tons)
    {
        $costs = $this->costs()->where('is_percentage', false)->get();
        $totalCost = $this->base_cost;
        foreach ($costs as $cost) {
            if ($cost->is_fixed) {
                $totalCost += ($tons > 0) ? $cost->cost / $tons : 0;
            } else {
                $totalCost += $cost->cost;
            }
        }
        return $totalCost;
    }

    public function calculateFinalCost($tons, $packagingCost, $ingredientsCost)
    {
        $baseCost = $this->calculateBaseCost($tons);
        $fullCost = $baseCost + $packagingCost + $ingredientsCost;

        foreach ($this->costs()->where('is_percentage', true)->get() as $cost) {
            $fullCost += ($fullCost * $cost->cost / 100);
        }

        return $fullCost - $packagingCost - $ingredientsCost;
    }

    //scopes
    public function scopeBySearch($query, $search)
    {
        $strings = explode(' ', $search);
        return $query->where(function ($query) use ($strings) {
            foreach ($strings as $string) {
                $query->where('name', 'like', '%' . $string . '%');
                $query->orWhereHas('category', function ($query) use ($string) {
                    $query->where('name', 'like', '%' . $string . '%');
                });
                $query->orWhere('id', '=', $string);
            }
        });
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('product_category_id', $categoryId);
    }

    public function scopeBySpec($query, $specId)
    {
        return $query->where('spec_id', $specId);
    }

    //relations
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function costs()
    {
        return $this->hasMany(ProductCost::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
}
