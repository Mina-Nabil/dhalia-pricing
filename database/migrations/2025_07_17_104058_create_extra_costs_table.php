<?php

use App\Models\Offers\Offer;
use App\Models\Offers\OfferItem;
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
        Schema::create('extra_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OfferItem::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('cost', 10, 2);
            $table->enum('cost_type', OfferItem::CALC_TYPES)->default(OfferItem::CALC_TYPE_FIXED);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_costs');
    }
};
