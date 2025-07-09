<?php

namespace App\Models\Offers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferComment extends Model
{
    const MORPH_TYPE = 'offer_comment';

    protected $fillable = [
        'offer_id',
        'user_id',
        'comment',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
