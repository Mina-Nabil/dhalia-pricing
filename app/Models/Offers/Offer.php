<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Model;

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
}
