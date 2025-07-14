<?php

namespace App\Models\Offers;

use App\Models\Packing;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferItem extends Model
{
    const MORPH_TYPE = 'offer_item';

    const CALC_TYPE_FIXED = 'fixed';
    const CALC_TYPE_PER_TON = 'per_ton';
    const CALC_TYPE_PERCENTAGE = 'percentage';

    const CALC_TYPES = [
        self::CALC_TYPE_FIXED,
        self::CALC_TYPE_PER_TON,
        self::CALC_TYPE_PERCENTAGE,
    ];

    protected $fillable = [
        'offer_id',
        'product_id',
        'packing_id',
        'quantity_in_kgs',
        'internal_cost',
        'kg_per_package',
        'one_package_cost',
        'total_packing_cost',
        'base_cost_currency',
        'profit_margain',
        'fob_price',
        'freight_cost',
        'freight_type',
        'freight_total_cost',
        'sterilization_cost',
        'sterilization_type',
        'sterilization_total_cost',
        'agent_commission_cost',
        'agent_commission_type',
        'agent_commission_total_cost',
        'total_costs',
        'total_profit',
    ];

    const REQUIRED_FIELDS = [
        'product_id',
        'packing_id',
        'quantity_in_kgs',
        'internal_cost',
        'kg_per_package',
        'one_package_cost',
        'total_packing_cost',
        'base_cost_currency',
        'profit_margain',
        'fob_price',
        'freight_cost',
        'freight_type',
        'freight_total_cost',
        'sterilization_cost',
        'sterilization_type',
        'sterilization_total_cost',
        'agent_commission_cost',
        'agent_commission_type',
        'agent_commission_total_cost',
        'total_costs',
        'total_profit',
        'ingredients',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function packing(): BelongsTo
    {
        return $this->belongsTo(Packing::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(OfferItemIngredient::class);
    }
}
