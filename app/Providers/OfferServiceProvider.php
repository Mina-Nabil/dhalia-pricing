<?php

namespace App\Providers;

use App\Exceptions\OfferManagementException;
use App\Models\AppLog;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferItem;
use App\Policies\OfferPolicy;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class OfferServiceProvider extends ServiceProvider
{

    const SORT_FIELDS = [
        'created_at',
        'total_price',
        'total_tonnage'
    ];

    /**
     * Get offers
     * 
     * @param string|null $search
     * @param int|null $user_id
     * @param int|null $client_id
     * @param string|null $status
     * @param string|null $date_from
     * @param string|null $date_to
     * @param float|null $price_from
     * @param float|null $price_to
     * @param int $paginate
     * @param string $sort
     * @param string $sort_direction
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOffers($search = null, $user_ids = [], $client_ids = [], $statuses = [], $date_from = null, $date_to = null, $price_from = null, $price_to = null, $paginate = 10, $sort = 'created_at', $sort_direction = 'desc')
    {

        if (!in_array($sort, self::SORT_FIELDS)) {
            throw new OfferManagementException('Invalid sort field');
        }

        if (!in_array($sort_direction, ['asc', 'desc'])) {
            throw new OfferManagementException('Invalid sort direction');
        }

        $returnAll = false;
        if (Gate::check('view-offers-list')) {
            $returnAll = true;
        }

        $query = Offer::query()->when(!$returnAll, function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->when($search, function ($query) use ($search) {
                $query->search($search);
            })
            ->when(count($user_ids), function ($query) use ($user_ids) {
                $query->users($user_ids);
            })
            ->when(count($client_ids), function ($query) use ($client_ids) {
                $query->clients($client_ids);
            })
            ->when(count($statuses), function ($query) use ($statuses) {
                $query->statuses($statuses);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->dateFrom($date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->dateTo($date_to);
            })
            ->when($price_from, function ($query) use ($price_from) {
                $query->priceFrom($price_from);
            })
            ->when($price_to, function ($query) use ($price_to) {
                $query->priceTo($price_to);
            })
            ->orderBy($sort, $sort_direction);

        AppLog::info('Offers list viewed', 'Offers loaded');

        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getOffer($id, $log = true)
    {
        $offer = Offer::find($id);
        if (!$offer) throw new OfferManagementException('Offer not found');
        Gate::authorize('view-offer', $offer);

        $offer->load(
            'user',
            'client',
            'items.product',
            'items.product.category',
            'items.ingredients',
            'items.product.spec',
            'items.packing',
            'duplicateOf',
            'currency',
            'comments.user',
        );
        AppLog::info('Offer loaded', "Offer $offer->code loaded", $offer);
        return $offer;
    }

    public function createOffer($status, $clientId, $currencyId, $currencyRate, $offerItems, $duplicateOfId = null)
    {
        Gate::authorize('create-offers');
        $this->checkOfferItemsArray($offerItems);
        $code = $this->getNextOfferCode($duplicateOfId);

        $totalTonnage = 0;
        $totalPrice = 0;
        $totalBasePrice = 0;
        $totalFreightCost = 0;
        $totalPackingCost = 0;
        $totalSterilizationCost = 0;
        $totalAgentCommissionCost = 0;
        $totalInternalCost = 0;
        $totalCosts = 0;
        $totalProfit = 0;
        $profitPercentage = 0;

        foreach ($offerItems as $item) {
            $totalTonnage += $item['quantity_in_tons'];
            $totalPrice += $item['price'];
            $totalBasePrice += $item['base_cost_currency'];
            $totalFreightCost += $item['freight_total_cost'];
            $totalPackingCost += $item['total_packing_cost'];
            $totalSterilizationCost += $item['sterilization_total_cost'];
            $totalAgentCommissionCost += $item['agent_commission_total_cost'];
            $totalInternalCost += $item['internal_cost'];
            $totalCosts += $item['total_costs'];
            $totalProfit += $item['total_profit'];
        }

        $profitPercentage = ($totalProfit / $totalPrice) * 100;

        $offer = new Offer([
            'user_id' => Auth::id(),
            'status' => $status,
            'duplicate_of_id' => $duplicateOfId,
            'client_id' => $clientId,
            'currency_id' => $currencyId,
            'currency_rate' => $currencyRate,
            'code' => $code,
            'total_tonnage' => $totalTonnage,
            'total_price' => $totalPrice,
            'total_base_price' => $totalBasePrice,
            'total_freight_cost' => $totalFreightCost,
            'total_packing_cost' => $totalPackingCost,
            'total_sterilization_cost' => $totalSterilizationCost,
            'total_agent_commission_cost' => $totalAgentCommissionCost,
            'total_internal_cost' => $totalInternalCost,
            'total_costs' => $totalCosts,
            'total_profit' => $totalProfit,
            'profit_percentage' => $profitPercentage,
        ]);
        try {
            DB::transaction(function () use ($offer, $offerItems) {
                $offer->save();
                $offer->items()->createMany($offerItems);
                $offer->refresh();
                foreach ($offer->items as $item) {
                    if (isset($item['ingredients']) && is_array($item['ingredients']) && count($item['ingredients']) > 0) {
                        $item->ingredients()->createMany($item['ingredients']);
                    }
                }
            });
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to create offer');
        }

        return $offer;
    }

    public function setOfferStatus($id, $status)
    {
        $offer = $this->getOffer($id);
        Gate::authorize('update-offer', $offer);
        $offer->status = $status;
        try {
            $offer->save();
            AppLog::info('Offer status updated', "Offer $offer->code status updated to $status", $offer);
            return $offer;
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to update offer status');
        }
    }

    public function deleteOffer($id)
    {
        $offer = $this->getOffer($id);
        Gate::authorize('delete-offer', $offer);
        try {
            DB::transaction(function () use ($offer) {
                foreach ($offer->items as $item) {
                    $item->ingredients()->delete();
                    $item->delete();
                }
                $offer->comments()->delete();
                $offer->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to delete offer');
        }
        AppLog::info('Offer deleted', "Offer $offer->code deleted", $offer);
        return true;
    }

    private function checkOfferItemsArray($offerItems)
    {
        $requiredFields = OfferItem::REQUIRED_FIELDS;
        $i = 0;
        foreach ($offerItems as $item) {
            foreach ($requiredFields as $field) {
                if (!isset($item[$field])) {
                    throw new OfferManagementException("Field $field is required in offer item#$i ");
                }
            }
            $i++;
        }
    }

    private function getNextOfferCode($duplicateOfId = null)
    {
        if ($duplicateOfId) {
            $duplicateOf = $this->getOffer($duplicateOfId);
            $duplicationCode = str_pad($this->getDuplicateOffersCount($duplicateOfId) + 1, 2, '0', STR_PAD_LEFT);
            return $duplicateOf->code . '-' . $duplicationCode;
        } else {
            $latestOfferId = $this->getLatestOfferId();
            return "OF" . str_pad($latestOfferId + 1, 5, '0', STR_PAD_LEFT);
        }
    }

    private function getDuplicateOffersCount($duplicateOfId)
    {
        return Offer::where('duplicate_of_id', $duplicateOfId)->count();
    }

    private function getLatestOfferId()
    {
        return Offer::latest()->first()?->id ?? 0;
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OfferServiceProvider::class, function ($app) {
            return new OfferServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-offers-list', [OfferPolicy::class, 'viewAny']);
        Gate::define('view-offer', [OfferPolicy::class, 'view']);
        Gate::define('create-offers', [OfferPolicy::class, 'create']);
        Gate::define('update-offer', [OfferPolicy::class, 'update']);
        Gate::define('delete-offers', [OfferPolicy::class, 'delete']);
    }
}
