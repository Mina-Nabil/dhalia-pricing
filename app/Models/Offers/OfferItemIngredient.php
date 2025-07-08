<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferItemIngredient extends Model
{
    const MORPH_TYPE = 'offer_item_ingredient';

    protected $fillable = [
        'offer_item_id',
        'name',
        'cost',
    ];

    public function offerItem(): BelongsTo  
    {
        return $this->belongsTo(OfferItem::class);
    }
}
