<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'rate'];

    //attributes
    public function getAbbrvAttribute()
    {
        return $this->code ?? $this->name;
    }
}
