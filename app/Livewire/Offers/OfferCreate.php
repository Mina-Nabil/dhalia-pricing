<?php

namespace App\Livewire\Offers;

use App\Exceptions\OfferManagementException;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferItem;
use App\Providers\OfferServiceProvider;
use App\Providers\CurrencyServiceProvider;
use App\Providers\ProductServiceProvider;
use App\Providers\PackingServiceProvider;
use App\Providers\SpecServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class OfferCreate extends Component
{
    use AlertFrontEnd;

    /** @var OfferServiceProvider */
    protected $offerService;

    /** @var CurrencyServiceProvider */
    protected $currencyService;

    /** @var ProductServiceProvider */
    protected $productService;

    /** @var PackingServiceProvider */
    protected $packingService;

    /** @var SpecServiceProvider */
    protected $specService;

    // Main offer fields
    public $status = 'draft';
    public $client_id = '';
    public $currency_id = '';
    public $currency_rate = 1;
    public $notes = '';
    public $original_duplicate_of_id = null;
    public $duplicate_of_id = null;
    public $duplicate_of_code = null;

    // Offer items (dynamic array)
    public $offerItems = [];

    // Available dropdown data
    public $currencies = [];
    /** @var Collection<Product> */
    public $products;
    public $packings = [];
    public $statuses = [];
    public $calcTypes = [];
    public $categories = [];
    public $specs = [];

    protected $listeners = ['clientsSelected'];

    public function boot()
    {
        $this->offerService = app(OfferServiceProvider::class);
        $this->currencyService = app(CurrencyServiceProvider::class);
        $this->productService = app(ProductServiceProvider::class);
        $this->packingService = app(PackingServiceProvider::class);
        $this->specService = app(SpecServiceProvider::class);
    }

    public function clientsSelected($clientIds)
    {
        if (is_array($clientIds)) {
            $this->client_id = $clientIds[0];
        } else {
            $this->client_id = $clientIds;
        }
        if ($this->client_id && $this->original_duplicate_of_id) {
            $duplicate_offer = $this->offerService->getOffer($this->original_duplicate_of_id, false);
            if ($duplicate_offer->client_id != $this->client_id) {
                $this->resetDuplicateFields();
            } else {
                $this->setDuplicateFields($duplicate_offer);
            }
        }
    }

    protected function setDuplicateFields($duplicate_offer)
    {
        $this->duplicate_of_id = $duplicate_offer->id;
        $this->duplicate_of_code = $duplicate_offer->code;
    }

    protected function resetDuplicateFields()
    {
        $this->duplicate_of_id = null;
        $this->duplicate_of_code = null;
    }

    public function mount($duplicate_of_id = null)
    {
        $this->authorize('create-offers');
        // Load dropdown data
        $this->currencies = $this->currencyService->getCurrencies(paginate: false, forDropdown: true);
        $this->products = $this->productService->getProducts(paginate: false, forDropdown: true);
        $this->packings = $this->packingService->getPackings(paginate: false, forDropdown: true);
        $this->categories = $this->productService->getCategories(paginate: false, forDropdown: true);
        $this->specs = $this->specService->getSpecs(paginate: false, forDropdown: true);

        $this->statuses = Offer::STATUSES;
        $this->calcTypes = OfferItem::CALC_TYPES;

        if ($duplicate_of_id) {
            $offer = $this->offerService->getOffer($duplicate_of_id, false);
            while ($offer->duplicate_of_id) {
                $offer = $this->offerService->getOffer($offer->duplicate_of_id, false);
            }
            $this->original_duplicate_of_id = $offer->id;
            $this->duplicate_of_id = $offer->id;
            $this->duplicate_of_code = $offer->code;
            $this->loadFieldsFromOffer($offer);
        } else {
            // Add first offer item
            $this->addOfferItem();
        }
    }

    public function updatedCurrencyId()
    {
        if ($this->currency_id) {
            $currency = collect($this->currencies)->firstWhere('id', $this->currency_id);
            $this->currency_rate = $currency ? $currency->rate : 1;
            $this->recalculateAllItems();
        }
    }

    public function updatedCurrencyRate()
    {
        $this->recalculateAllItems();
    }

    public function addOfferItem()
    {
        $newIndex = count($this->offerItems);
        $this->offerItems[] = [
            'available_products' => [], // dynamic products array
            'product_id' => '',
            'category_id' => '',
            'spec_id' => '',
            'packing_id' => '',
            'quantity_in_kgs' => 0,
            'raw_ton_cost' => 0,
            'ingredients_cost' => 0,
            'internal_cost' => 0, // hidden, auto-calculated
            'kg_per_package' => 0,
            'one_package_cost' => 0,
            'total_packing_cost' => 0, // auto-calculated
            'base_cost_currency' => 0, // auto-calculated
            'profit_margain' => 0,
            'fob_price' => 0, // auto-calculated
            'freight_cost' => 0,
            'freight_type' => OfferItem::CALC_TYPE_FIXED,
            'freight_total_cost' => 0, // auto-calculated
            'sterilization_cost' => 0,
            'sterilization_type' => OfferItem::CALC_TYPE_FIXED,
            'sterilization_total_cost' => 0, // auto-calculated
            'agent_commission_cost' => 0,
            'agent_commission_type' => OfferItem::CALC_TYPE_FIXED,
            'agent_commission_total_cost' => 0, // auto-calculated
            'total_costs' => 0, // auto-calculated
            'total_profit' => 0, // auto-calculated
            'price' => 0, // auto-calculated
            'ingredients' => [], // dynamic ingredients array
        ];
    }

    public function removeOfferItem($index)
    {
        if (count($this->offerItems) > 1) {
            unset($this->offerItems[$index]);
            $this->offerItems = array_values($this->offerItems); // Reindex array
        }
    }

    public function categoryIdSelected($index)
    {
        if ($this->offerItems[$index]['spec_id'] && $this->offerItems[$index]['category_id']) {
            $this->offerItems[$index]['product_id'] = null;
            $this->offerItems[$index]['available_products'] = $this->products->where('product_category_id', $this->offerItems[$index]['category_id'])->where('spec_id', $this->offerItems[$index]['spec_id']);
        } else {
            $this->offerItems[$index]['available_products'] = [];
        }
    }

    public function specIdSelected($index)
    {
        if ($this->offerItems[$index]['category_id'] && $this->offerItems[$index]['spec_id']) {
            $this->offerItems[$index]['product_id'] = null;
            $this->offerItems[$index]['available_products'] = $this->products->where('product_category_id', $this->offerItems[$index]['category_id'])->where('spec_id', $this->offerItems[$index]['spec_id']);
        } else {
            $this->offerItems[$index]['available_products'] = [];
        }
    }

    public function productIdSelected($index)
    {
        if ($this->offerItems[$index]['product_id']) {
            $product = $this->productService->getSelectedProduct($this->offerItems[$index]['product_id']);
            if ($product) {

                // Load product ingredients if any
                $this->loadProductIngredients($index, $product);
                $this->recalculateOfferItem($index);
            }
        }
    }

    public function addIngredient($itemIndex)
    {
        $this->offerItems[$itemIndex]['ingredients'][] = [
            'name' => '',
            'total_cost' => 0,
            'cost' => 0,
            'percentage' => 0,
        ];
    }

    public function removeIngredient($itemIndex, $ingredientIndex)
    {
        unset($this->offerItems[$itemIndex]['ingredients'][$ingredientIndex]);
        $this->offerItems[$itemIndex]['ingredients'] = array_values($this->offerItems[$itemIndex]['ingredients']);
        $this->recalculateOfferItem($itemIndex);
    }

    public function updatedOfferItemsIngredients($value, $key)
    {
        // Parse key like "0.ingredients.1.cost"
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $itemIndex = $parts[0];
            $this->recalculateOfferItem($itemIndex);
        }
    }

    public function forceRecalculateOfferItem($index)
    {
        $this->recalculateOfferItem($index, true);
    }

    public function packingChanged($itemIndex)
    {
        $packing = $this->packingService->getPacking($this->offerItems[$itemIndex]['packing_id'], false);
        if ($packing) {
            $this->offerItems[$itemIndex]['one_package_cost'] = $packing->cost;
        } else {
            $this->offerItems[$itemIndex]['one_package_cost'] = 0;
        }
        $this->recalculateOfferItem($itemIndex);
    }

    private function loadProductIngredients($itemIndex, $product)
    {
        $this->offerItems[$itemIndex]['ingredients'] = [];
        $ingredientCount = count($product->ingredients);

        if ($ingredientCount > 0) {
            foreach ($product->ingredients as $ingredient) {
                $this->offerItems[$itemIndex]['ingredients'][] = [
                    'name' => $ingredient->name,
                    'cost' => $ingredient->cost,
                    'percentage' => (100 / $ingredientCount),
                    'total_cost' => $ingredient->cost * (100 / $ingredientCount) / 100,
                ];
            }
        }
    }

    private function recalculateInternalCost($index)
    {
        if (isset($this->offerItems[$index]['product_id']) && $this->offerItems[$index]['product_id']) {
            $product = $this->products->firstWhere('id', $this->offerItems[$index]['product_id']);
            if ($product) {
                $this->offerItems[$index]['internal_cost'] = $product->calculateBaseCost($this->offerItems[$index]['quantity_in_kgs'] / 1000);
            }
        }
    }

    private function recalculateOfferItem($index, $forced = false)
    {
        if (!isset($this->offerItems[$index]['product_id']) || !$this->offerItems[$index]['product_id']) return;


        $item = &$this->offerItems[$index];
        // Calculate total packing cost
        $quantityInTons = isset($item['quantity_in_kgs']) && is_numeric($item['quantity_in_kgs']) ? ($item['quantity_in_kgs'] / 1000) : 0;
        $kgPerPackage = isset($item['kg_per_package']) && is_numeric($item['kg_per_package']) ? $item['kg_per_package'] : 1;
        $onePackageCost = isset($item['one_package_cost']) && is_numeric($item['one_package_cost']) ? $item['one_package_cost'] : 0;

        if (!Gate::check('view-product-costs') && $forced) {
            $this->resetErrorBag();
            if (!$quantityInTons) {
                $this->addError('offerItems.' . $index . '.quantity_in_kgs', 'Quantity in Kgs is required');
                return;
            }
            if (!$kgPerPackage) {
                $this->addError('offerItems.' . $index . '.kg_per_package', 'Kg per package is required');
                return;
            }
            if (!$onePackageCost) {
                $this->addError('offerItems.' . $index . '.one_package_cost', 'One package cost is required');
                return;
            }
            if (!$item['product_id']) {
                $this->addError('offerItems.' . $index . '.product_id', 'Product is required');
                return;
            }
            if (!$item['packing_id']) {
                $this->addError('offerItems.' . $index . '.packing_id', 'Packing is required');
                return;
            }
        } elseif (!Gate::check('view-product-costs')) {
            return;
        }

        $this->recalculateInternalCost($index);
        
        if ($kgPerPackage > 0 && $quantityInTons > 0) {
            $totalPackages = ($quantityInTons * 1000) / $kgPerPackage; // Convert tons to kg
            $item['total_packing_cost'] = ($totalPackages * $onePackageCost) / $quantityInTons;
        } else {
            $item['total_packing_cost'] = 0;
        }
        
        // Calculate sum of ingredients cost
        $ingredientsCost = 0;
        if (isset($item['ingredients']) && is_array($item['ingredients'])) {
            foreach ($item['ingredients'] as &$ingredient) {
                $ingredient['total_cost'] = ($ingredient['cost'] ?? 0) * ($ingredient['percentage'] ?? 0) / 100;
                $ingredientsCost += $ingredient['total_cost'];
            }
        }
        $item['ingredients_cost'] = $ingredientsCost;
        $totalPackingCost = $item['total_packing_cost'] ?? 0;

        $product = $this->products->firstWhere('id', $this->offerItems[$index]['product_id']);

        $item['internal_cost'] = $product->calculateFinalCost($quantityInTons, $totalPackingCost, $ingredientsCost);
        
        $item['raw_ton_cost'] = $ingredientsCost + $item['internal_cost'];
        
        $internalCurrencyCost = $item['internal_cost'] / $this->currency_rate;
        $currencyRate = $this->currency_rate ?: 1;
        // Calculate base_cost_currency
        $item['base_cost_currency'] = $internalCurrencyCost + (($totalPackingCost + $ingredientsCost) / $currencyRate);


        // Calculate FOB price
        $profitMargin = $item['profit_margain'] ?? 0;
        $baseCostCurrency = $item['base_cost_currency'] ?? 0;
        $item['fob_price'] = $baseCostCurrency * (100 + $profitMargin) / 100;

        // Calculate freight total cost
        $this->calculateCostByType(
            $item,
            'freight_cost',
            'freight_type',
            'freight_total_cost',
            $quantityInTons,
            $item['fob_price']
        );

        // Calculate sterilization total cost
        $this->calculateCostByType(
            $item,
            'sterilization_cost',
            'sterilization_type',
            'sterilization_total_cost',
            $quantityInTons,
            $item['fob_price']
        );

        // Calculate agent commission total cost
        $this->calculateCostByType(
            $item,
            'agent_commission_cost',
            'agent_commission_type',
            'agent_commission_total_cost',
            $quantityInTons,
            $item['fob_price']
        );


        // Calculate total costs
        $item['total_costs'] = $baseCostCurrency +
            ($item['freight_total_cost'] ?? 0) +
            ($item['sterilization_total_cost'] ?? 0) +
            ($item['agent_commission_total_cost'] ?? 0);

        // Set price FOB + total costs
        $item['price'] = ($item['fob_price'] ?? 0) + ($item['freight_total_cost'] ?? 0) +
            ($item['sterilization_total_cost'] ?? 0) +
            ($item['agent_commission_total_cost'] ?? 0);

        // Calculate total profit
        $item['total_profit'] = ($item['price'] ?? 0) - ($item['total_costs'] ?? 0);

        // Calculate total price
        $item['total_price'] = ($item['price'] ?? 0) * $quantityInTons;
    }

    private function calculateCostByType(&$item, $costField, $typeField, $totalField, $quantityInTons, $fobPrice)
    {
        $cost = $item[$costField] ?? 0;
        $type = $item[$typeField] ?? OfferItem::CALC_TYPE_FIXED;

        switch ($type) {
            case OfferItem::CALC_TYPE_FIXED:
                $item[$totalField] = $quantityInTons > 0 ? $cost / $quantityInTons : 0;
                break;
            case OfferItem::CALC_TYPE_PER_TON:
                $item[$totalField] = $cost;
                break;
            case OfferItem::CALC_TYPE_PERCENTAGE:
                $item[$totalField] = ($cost / 100) * $fobPrice;
                break;
            default:
                $item[$totalField] = $cost;
                break;
        }
    }

    private function recalculateAllItems()
    {
        foreach ($this->offerItems as $index => $item) {
            $this->recalculateOfferItem($index);
        }
    }

    public function recalculate($itemIndex = null)
    {
        // This method will be called by wire:change
        // If specific item index is provided, recalculate only that item
        // Otherwise recalculate all items
        if ($itemIndex !== null && isset($this->offerItems[$itemIndex])) {
            $this->recalculateOfferItem($itemIndex);
        } else {
            $this->recalculateAllItems();
        }
    }

    private function loadFieldsFromOffer($offer)
    {
        $this->status = Offer::STATUS_DRAFT;
        $this->client_id = $offer->client_id;
        $this->currency_id = $offer->currency_id;
        $this->currency_rate = $offer->currency_rate;
        $this->notes = $offer->notes;
        $this->offerItems = $offer->items->map(function ($item) {
            return [
                'available_products' => $this->products->where('product_category_id', $item->product->product_category_id)->where('spec_id', $item->product->spec_id),
                'internal_cost' => $item->internal_cost,
                'category_id' => $item->product->product_category_id,
                'spec_id' => $item->product->spec_id,
                'product_id' => $item->product_id,
                'packing_id' => $item->packing_id,
                'quantity_in_kgs' => $item->quantity_in_kgs,
                'kg_per_package' => $item->kg_per_package,
                'one_package_cost' => $item->one_package_cost,
                'profit_margain' => $item->profit_margain,
                'freight_cost' => $item->freight_cost,
                'freight_type' => $item->freight_type,
                'freight_total_cost' => $item->freight_total_cost,
                'sterilization_cost' => $item->sterilization_cost,
                'sterilization_type' => $item->sterilization_type,
                'sterilization_total_cost' => $item->sterilization_total_cost,
                'agent_commission_cost' => $item->agent_commission_cost,
                'agent_commission_type' => $item->agent_commission_type,
                'agent_commission_total_cost' => $item->agent_commission_total_cost,
                'total_costs' => $item->total_costs,
                'total_profit' => $item->total_profit,
                'price' => $item->price,
                'ingredients' => $item->ingredients->map(function ($ingredient) {
                    return [
                        'name' => $ingredient->name,
                        'cost' => $ingredient->cost,
                        'percentage' => $ingredient->percentage,
                        'total_cost' => $ingredient->percentage / 100 * $ingredient->cost,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $this->recalculateAllItems();
    }

    public function saveOffer()
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'currency_id' => 'required|exists:currencies,id',
            'currency_rate' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', Offer::STATUSES),
            'notes' => 'nullable|string',
            'offerItems' => 'required|array|min:1',
            'offerItems.*.ingredients' => 'present|array',
            'offerItems.*.ingredients.*.name' => 'required|string|max:255',
            'offerItems.*.ingredients.*.cost' => 'required|numeric|min:0',
            'offerItems.*.ingredients.*.percentage' => 'required|numeric|min:1|max:100',
            'offerItems.*.product_id' => 'required|exists:products,id',
            'offerItems.*.packing_id' => 'required|exists:packings,id',
            'offerItems.*.quantity_in_kgs' => 'required|numeric|min:1',
            'offerItems.*.kg_per_package' => 'required|numeric|min:1',
            'offerItems.*.one_package_cost' => 'required|numeric|min:0',
            'offerItems.*.profit_margain' => 'required|numeric|min:0',
            'offerItems.*.freight_cost' => 'required|numeric|min:0',
            'offerItems.*.freight_type' => 'required|in:' . implode(',', OfferItem::CALC_TYPES),
            'offerItems.*.sterilization_cost' => 'required|numeric|min:0',
            'offerItems.*.sterilization_type' => 'required|in:' . implode(',', OfferItem::CALC_TYPES),
            'offerItems.*.agent_commission_cost' => 'required|numeric|min:0',
            'offerItems.*.agent_commission_type' => 'required|in:' . implode(',', OfferItem::CALC_TYPES),
        ], [
            'client_id.required' => 'Please select a client',
            'client_id.exists' => 'Client not found',
            'currency_id.required' => 'Required',
            'currency_id.exists' => 'Currency not found',
            'currency_rate.required' => 'Required',
            'currency_rate.numeric' => 'Not a number',
            'currency_rate.min' => 'Not greater than 0',
            'status.required' => 'Required',
            'status.in' => 'Invalid status',
            'offerItems.*.product_id.required' => 'Required',
            'offerItems.*.product_id.exists' => 'Product not found',
            'offerItems.*.packing_id.required' => 'Required',
            'offerItems.*.packing_id.exists' => 'Packing not found',
            'offerItems.*.quantity_in_kgs.required' => 'Required',
            'offerItems.*.quantity_in_kgs.numeric' => 'Not a number',
            'offerItems.*.quantity_in_kgs.min' => 'Not greater than 0',
            'offerItems.*.kg_per_package.required' => 'Required',
            'offerItems.*.kg_per_package.numeric' => 'Not a number',
            'offerItems.*.kg_per_package.min' => 'Not greater than 0',
            'offerItems.*.one_package_cost.required' => 'Required',
            'offerItems.*.one_package_cost.numeric' => 'Not a number',
            'offerItems.*.one_package_cost.min' => 'Not greater than 0',
            'offerItems.*.profit_margain.required' => 'Required',
            'offerItems.*.profit_margain.numeric' => 'Not a number',
            'offerItems.*.freight_cost.required' => 'Required',
            'offerItems.*.freight_cost.numeric' => 'Not a number',
            'offerItems.*.freight_type.required' => 'Required',
            'offerItems.*.freight_type.in' => 'Invalid freight type',
            'offerItems.*.sterilization_cost.required' => 'Required',
            'offerItems.*.sterilization_cost.numeric' => 'Not a number',
            'offerItems.*.sterilization_type.required' => 'Required',
            'offerItems.*.sterilization_type.in' => 'Invalid sterilization type',
            'offerItems.*.agent_commission_cost.required' => 'Required',
            'offerItems.*.agent_commission_cost.numeric' => 'Not a number',
            'offerItems.*.agent_commission_type.required' => 'Required',
            'offerItems.*.agent_commission_type.in' => 'Invalid agent commission type',
            'offerItems.*.ingredients.*.name.required' => 'Required',
            'offerItems.*.ingredients.*.name.string' => 'Not a string',
            'offerItems.*.ingredients.*.name.max' => 'Too long',
            'offerItems.*.ingredients.*.cost.required' => 'Required',
            'offerItems.*.ingredients.*.cost.numeric' => 'Not a number',
            'offerItems.*.ingredients.*.cost.min' => 'Not greater than 0',
            'offerItems.*.ingredients.*.percentage.required' => 'Required',
            'offerItems.*.ingredients.*.percentage.numeric' => 'Not a number',
            'offerItems.*.ingredients.*.percentage.min' => 'Not greater than 0',
            'offerItems.*.ingredients.*.percentage.max' => 'Not less than 100',
        ]);

        // if (!$this->validateIngredientsTotalPercentage()) return;

        try {
            $offer = $this->offerService->createOffer(
                $this->status,
                $this->client_id,
                $this->currency_id,
                $this->currency_rate,
                $this->offerItems,
                $this->duplicate_of_id,
                $this->notes
            );

            $this->alert('success', 'Offer created successfully!');
            return redirect()->route('offers.show', $offer->id);
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to create offer: ' . $e->getMessage());
        }
    }

    private function validateIngredientsTotalPercentage()
    {


        foreach ($this->offerItems as $item) {
            if (!isset($item['ingredients']) || count($item['ingredients']) == 0) {
                continue;
            }
            $ingredientsTotalPercentage = 100;
            foreach ($item['ingredients'] as $ingredient) {
                $ingredientsTotalPercentage -= $ingredient['percentage'];
            }
            if ($ingredientsTotalPercentage != 0) {
                $this->alertError('Ingredients total percentage Not 100%');
                return false;
            }
        }
        return true;
    }

    public function render()
    {
        return view('livewire.offers.offer-create');
    }
}
