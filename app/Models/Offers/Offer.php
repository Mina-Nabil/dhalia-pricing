<?php

namespace App\Models\Offers;

use App\Models\Clients\Client;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    const MORPH_TYPE = 'offer';

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ARCHIVED = 'archived';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SENT,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'status',
        'duplicate_of_id',
        'client_id',
        'user_id',
        'currency_id',
        'code',
        'currency_rate',
        'total_price',
        'total_tonnage',
        'total_base_price',
        'total_freight_cost',
        'total_packing_cost',
        'total_sterilization_cost',
        'total_agent_commission_cost',
        'total_internal_cost',
        'total_costs',
        'total_profit',
        'profit_percentage',
    ];

    //scopes
    public function scopeSearch($query, $search)
    {
        $words = explode(' ', $search);
        foreach ($words as $word) {
            $query->where('code', 'like', '%' . $word . '%')
                ->orWhereHas('client', function ($query) use ($word) {
                    $query->where('name', 'like', '%' . $word . '%')
                        ->orWhere('phone', 'like', '%' . $word . '%');
                })->orWhereHas('user', function ($query) use ($word) {
                    $query->where('name', 'like', '%' . $word . '%')
                        ->orWhere('username', 'like', '%' . $word . '%');
                });
        }
        return $query;
    }

    public function scopeUsers($query, $user_ids)
    {
        return $query->whereIn('user_id', $user_ids);
    }   

    public function scopeClients($query, $client_ids)
    {
        return $query->whereIn('client_id', $client_ids);
    }

    public function scopeStatuses($query, $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    public function scopeDateFrom($query, $date_from)
    {
        return $query->where('created_at', '>=', $date_from);
    }

    public function scopeDateTo($query, $date_to)
    {
        return $query->where('created_at', '<=', $date_to);
    }

    public function scopeProfitFrom($query, $profit_from)
    {
        return $query->where('total_profit', '>=', $profit_from);
    }

    public function scopeProfitTo($query, $profit_to)
    {
        return $query->where('total_profit', '<=', $profit_to);
    }

    public function scopePriceFrom($query, $price_from)
    {
        return $query->where('total_price', '>=', $price_from);
    }

    public function scopePriceTo($query, $price_to)
    {
        return $query->where('total_price', '<=', $price_to);
    }

    //relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'duplicate_of_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(OfferComment::class);
    }
}
