<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    const MORPH_TYPE = 'packing';

    protected $fillable = ['name', 'cost', 'is_active'];

    //scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySearch($query, $search)
    {
        $exploded = explode(' ', $search);
        $query->where(function ($query) use ($exploded) {
            foreach ($exploded as $word) {
                $query->orWhere('name', 'like', '%' . $word . '%');
            }
        });
        return $query;
    }
}
