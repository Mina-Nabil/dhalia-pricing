<?php

namespace App\Models;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Model;

class Spec extends Model
{
    const MORPH_TYPE = 'spec';

    protected $fillable = ['name'];

    //scopes
    public function scopeBySearch($query, $search)
    {
        $exploded = explode(' ', $search);
        $query->where(function ($query) use ($exploded) {
            foreach ($exploded as $word) {
                $query->orWhere('name', 'like', '%' . $word . '%')
                      ->orWhere('id', '=', $word);
            }
        });
        return $query;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
