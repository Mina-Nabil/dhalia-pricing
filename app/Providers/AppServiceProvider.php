<?php

namespace App\Providers;

use App\Models\AppLog;
use App\Models\Clients\Client;
use App\Models\Clients\ClientInfo;
use App\Models\Currency;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferComment;
use App\Models\Offers\OfferItem;
use App\Models\Offers\OfferItemIngredient;
use App\Models\Packing;
use App\Models\Products\Ingredient;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductCost;
use App\Models\Spec;
use App\Models\User;
use App\Policies\AppLogPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-app-logs', [AppLogPolicy::class, 'viewAny']);

        Relation::enforceMorphMap([
            User::MORPH_TYPE => User::class,
            AppLog::MORPH_TYPE => AppLog::class,
            Currency::MORPH_TYPE => Currency::class,
            Packing::MORPH_TYPE => Packing::class,
            Spec::MORPH_TYPE => Spec::class,
            Client::MORPH_TYPE => Client::class,
            ClientInfo::MORPH_TYPE => ClientInfo::class,
            Offer::MORPH_TYPE => Offer::class,
            OfferComment::MORPH_TYPE => OfferComment::class,
            OfferItem::MORPH_TYPE => OfferItem::class,
            OfferItemIngredient::MORPH_TYPE => OfferItemIngredient::class,
            Product::MORPH_TYPE => Product::class,
            ProductCategory::MORPH_TYPE => ProductCategory::class,
            ProductCost::MORPH_TYPE => ProductCost::class,
            Ingredient::MORPH_TYPE => Ingredient::class,
        ]);
    }
}
