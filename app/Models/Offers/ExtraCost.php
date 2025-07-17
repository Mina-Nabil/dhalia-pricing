<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Model;

class ExtraCost extends Model
{
    protected $fillable = [
        'offer_item_id',
        'name',
        'cost',
        'cost_type',
        'total_cost',
    ];
    
    public function offerItem()
    {
        return $this->belongsTo(OfferItem::class);
    }
}
