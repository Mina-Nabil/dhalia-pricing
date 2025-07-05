<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'rate'];

    //scopes
    public function scopeBySearch($query, $search)
    {
        $strings = explode(' ', $search);
        return $query->where(function ($query) use ($strings) {
            foreach ($strings as $string) {
                $query->where('name', 'like', '%' . $string . '%')
                    ->orWhere('id', '=', $string)
                    ->orWhere('code', 'like', '%' . $string . '%');
            }
        });
    }

    //attributes
    public function getAbbrvAttribute()
    {
        return $this->code ?? $this->name;
    }
}
