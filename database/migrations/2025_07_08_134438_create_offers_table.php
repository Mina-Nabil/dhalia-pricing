<?php

use App\Models\Clients\Client;
use App\Models\Currency;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferItem;
use App\Models\Packing;
use App\Models\Products\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->enum('status', Offer::STATUSES)->default(Offer::STATUS_DRAFT);
            $table->foreignIdFor(Offer::class, 'duplicate_of_id')->nullable()->constrained('offers');
            $table->foreignIdFor(Client::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->text('note')->nullable();
            $table->string('code')->unique();
            $table->decimal('currency_rate', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_tonnage', 10, 2);
            $table->decimal('total_base_price', 10, 2);
            $table->decimal('total_freight_cost', 10, 2);
            $table->decimal('total_packing_cost', 10, 2);
            $table->decimal('total_sterilization_cost', 10, 2);
            $table->decimal('total_agent_commission_cost', 10, 2);
            $table->decimal('total_internal_cost', 10, 2);
            $table->decimal('total_costs', 10, 2);
            $table->decimal('total_profit', 10, 2);
            $table->decimal('profit_percentage', 10, 2);
            $table->timestamps();
        });

        Schema::create('offer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Offer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Packing::class)->constrained();

            $table->decimal('quantity_in_tons', 10, 2); 
            $table->decimal('internal_cost', 10, 2);
            
            $table->decimal('kg_per_package', 10, 2);
            $table->decimal('one_package_cost', 10, 2);
            $table->decimal('total_packing_cost', 10, 2);
            
            $table->decimal('base_cost_currency', 10, 2); //from here downwards all costs are in currency
            $table->decimal('profit_margain', 10, 2); //percentage
            $table->decimal('fob_price', 10, 2);
            
            $table->decimal('freight_cost', 10, 2);
            $table->enum('freight_type', OfferItem::CALC_TYPES)->default(OfferItem::CALC_TYPE_FIXED);
            $table->decimal('freight_total_cost', 10, 2);

            $table->decimal('sterilization_cost', 10, 2);
            $table->enum('sterilization_type', OfferItem::CALC_TYPES)->default(OfferItem::CALC_TYPE_FIXED);
            $table->decimal('sterilization_total_cost', 10, 2);

            $table->decimal('agent_commission_cost', 10, 2);
            $table->enum('agent_commission_type', OfferItem::CALC_TYPES)->default(OfferItem::CALC_TYPE_FIXED);
            $table->decimal('agent_commission_total_cost', 10, 2);

            $table->decimal('total_costs', 10, 2); //in currency
            $table->decimal('total_profit', 10, 2); //in currency

            $table->timestamps();
        });

        Schema::create('offer_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OfferItem::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });

        Schema::create('offer_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Offer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained();
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_comments');
        Schema::dropIfExists('offer_item_ingredients');
        Schema::dropIfExists('offer_items');
        Schema::dropIfExists('offers');
    }
};
