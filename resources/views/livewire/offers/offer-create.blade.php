<div>
    {{-- Main Offer Card --}}
    <x-card title="Create New Offer {{ $duplicate_of_code ? ' - Duplicate of ' . $duplicate_of_code : '' }}">

        <x-slot name="tools">
            <button wire:click="saveOffer" type="button" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Offer
            </button>
        </x-slot>

        <div class="grid grid-cols-3 gap-4">

            <div class="col-span-3">
                <livewire:components.select-clients-modal :mode="'single'" :selectedClientIds="[$client_id]" />
                @error('client_id')
                    <span class="text-danger-500 small">{{ $message }}</span>
                @enderror
            </div>

            <x-select wire:model="status" id="status" name="status" class="form-control" :label="__('Status')"
                errorMessage="{{ $errors->first('status') }}">
                <option value="">Select Status</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                @endforeach
            </x-select>


            {{-- <x-select wire:model.live="client_id" id="client_id" name="client_id" class="form-control" :label="__('Client')"
                errorMessage="{{ $errors->first('client_id') }}">
                <option value="">Select Client</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </x-select> --}}

            <x-select wire:model.live="currency_id" id="currency_id" name="currency_id" class="form-control"
                :label="__('Currency')" errorMessage="{{ $errors->first('currency_id') }}">
                <option value="">Select Currency</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                @endforeach
            </x-select>

            <x-text-input wire:model.live="currency_rate" id="currency_rate" name="currency_rate" type="number"
                step="0.001" min="0" class="form-control" :label="__('Currency Rate')"
                errorMessage="{{ $errors->first('currency_rate') }}" />

        </div>
    </x-card>

    {{-- Offer Items Cards --}}
    <div class="flex justify-between items-center mb-3 mt-4">
        <h4>Offer Items</h4>
        <button wire:click="addOfferItem" type="button" class="btn btn-success">
            <i class="fa fa-plus"></i> Add Product
        </button>
    </div>

    @foreach ($offerItems as $index => $item)
        <x-card title="Product #{{ $index + 1 }}">
            <x-slot name="tools">
                @if (count($offerItems) > 1)
                    <button wire:click="removeOfferItem({{ $index }})" type="button"
                        class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                @endif
            </x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Left Column: Product & Ingredients --}}
                <div class="lg:col-span-1">
                    <div class="border border-blue-200 rounded-lg p-4 h-full bg-blue-50">
                        <h6 class="text-blue-600 mb-3 font-semibold"><i class="fa fa-cube"></i> Product & Ingredients
                        </h6>

                        {{-- Product Selection --}}
                        <x-select wire:model="offerItems.{{ $index }}.category_id"
                            wire:change="categoryIdSelected({{ $index }})"
                            id="offerItems.{{ $index }}.category_id" class="form-control mb-3" :label="__('Category')"
                            errorMessage="{{ $errors->first('offerItems.' . $index . '.category_id') }}">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-select>
                        {{-- Product Selection --}}
                        <x-select wire:model="offerItems.{{ $index }}.spec_id"
                            wire:change="specIdSelected({{ $index }})"
                            id="offerItems.{{ $index }}.spec_id" class="form-control mb-3" :label="__('Spec')"
                            errorMessage="{{ $errors->first('offerItems.' . $index . '.spec_id') }}">
                            <option value="">Select Specs</option>
                            @foreach ($specs as $spec)
                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                            @endforeach
                        </x-select>
                        {{-- Product Selection --}}
                        <x-select wire:model="offerItems.{{ $index }}.product_id"
                            wire:change="productIdSelected({{ $index }})"
                            id="offerItems.{{ $index }}.product_id" class="form-control mb-3" :label="__('Product')"
                            errorMessage="{{ $errors->first('offerItems.' . $index . '.product_id') }}">
                            <option value="">Select Product</option>
                            @foreach ($offerItems[$index]['available_products'] as $product)
                                <option value="{{ $product->id }}">({{ $product->category->name }} -
                                    {{ $product->spec->name }}) - {{ $product->name }} </option>
                            @endforeach
                        </x-select>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            @can('view-product-costs')
                                {{-- Internal Cost --}}
                                <x-text-input wire:model="offerItems.{{ $index }}.internal_cost"
                                    id="offerItems.{{ $index }}.internal_cost" type="number" step="0.001"
                                    min="0" class="form-control mb-3" :label="__('Internal Cost per Ton')" readonly />
                            @endcan

                            {{-- Quantity in Tons --}}
                            <x-text-input wire:model="offerItems.{{ $index }}.quantity_in_kgs"
                                wire:change="recalculate({{ $index }})"
                                id="offerItems.{{ $index }}.quantity_in_kgs" type="number" step="0.001"
                                min="0" class="form-control" :label="__('Quantity (KGs)')"
                                errorMessage="{{ $errors->first('offerItems.' . $index . '.quantity_in_kgs') }}" />
                        </div>
                        {{-- Ingredients --}}
                        <div class="mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Ingredients</small>
                                <button wire:click="addIngredient({{ $index }})" type="button"
                                    class="btn btn-xs btn-outline-primary">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>

                            @if (isset($item['ingredients']) && count($item['ingredients']) > 0)
                                @foreach ($item['ingredients'] as $ingredientIndex => $ingredient)
                                    <div class="grid grid-cols-1 gap-2 border border-gray-200 rounded-lg p-2 mb-2">
                                        <div class="flex gap-2 mb-2">
                                            <div class="flex-1">
                                                <x-text-input
                                                    wire:model="offerItems.{{ $index }}.ingredients.{{ $ingredientIndex }}.name"
                                                    wire:change="recalculate({{ $index }})" placeholder="Name"
                                                    class="form-control form-control-sm w-full"
                                                    errorMessage="{{ $errors->first('offerItems.' . $index . '.ingredients.' . $ingredientIndex . '.name') }}" />
                                            </div>
                                            <div class="flex-1">
                                                <x-text-input
                                                    wire:model="offerItems.{{ $index }}.ingredients.{{ $ingredientIndex }}.total_cost"
                                                    wire:change="recalculate({{ $index }})"
                                                    placeholder="Total Cost"
                                                    class="form-control form-control-sm w-full" readonly />
                                            </div>
                                        </div>
                                        <div class="flex gap-2 mb-2">
                                            <div class="flex-1">
                                                <x-text-input
                                                    wire:model="offerItems.{{ $index }}.ingredients.{{ $ingredientIndex }}.cost"
                                                    wire:change="recalculate({{ $index }})" placeholder="Cost"
                                                    type="number" step="0.01" min="0"
                                                    class="form-control form-control-sm w-full"
                                                    errorMessage="{{ $errors->first('offerItems.' . $index . '.ingredients.' . $ingredientIndex . '.cost') }}" />
                                            </div>
                                            <div class="flex-1">
                                                <x-text-input
                                                    wire:model="offerItems.{{ $index }}.ingredients.{{ $ingredientIndex }}.percentage"
                                                    wire:change="recalculate({{ $index }})"
                                                    placeholder="Percentage" type="number" step="0.01"
                                                    min="0" class="form-control form-control-sm w-full"
                                                    errorMessage="{{ $errors->first('offerItems.' . $index . '.ingredients.' . $ingredientIndex . '.percentage') }}" />
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button
                                                    wire:click="removeIngredient({{ $index }}, {{ $ingredientIndex }})"
                                                    type="button" class="btn btn-xs btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            @else
                                <small class="text-muted">No ingredients</small>
                            @endif
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <strong class="text-gray-600 mb-2">Raw costs per Ton</strong>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Internal Cost:</small>
                                    <strong
                                        class="text-red-600">{{ number_format($item['internal_cost'] ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Ingredients Cost:</small>
                                    <strong
                                        class="text-red-600">{{ number_format($item['ingredients_cost'] ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Total Costs:</small>
                                    <strong
                                        class="text-red-600">{{ number_format($item['raw_ton_cost'] ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Middle Column: Packing & Pricing --}}
                <div class="lg:col-span-1">
                    <div class="border border-green-200 rounded-lg p-4 h-full bg-green-50">
                        <h6 class="text-green-600 mb-3 font-semibold"><i class="fa fa-box"></i> Packing & Pricing</h6>

                        {{-- Packing Info --}}
                        <x-select wire:model="offerItems.{{ $index }}.packing_id"
                            wire:change="packingChanged({{ $index }})"
                            id="offerItems.{{ $index }}.packing_id" class="form-control mb-3"
                            :label="__('Packing Type')"
                            errorMessage="{{ $errors->first('offerItems.' . $index . '.packing_id') }}">
                            <option value="">Select Packing</option>
                            @foreach ($packings as $packing)
                                <option value="{{ $packing->id }}">{{ $packing->name }}</option>
                            @endforeach
                        </x-select>

                        <div class="grid grid-cols-2 gap-3 mb-3">

                            <x-text-input wire:model="offerItems.{{ $index }}.kg_per_package"
                                wire:change="recalculate({{ $index }})"
                                id="offerItems.{{ $index }}.kg_per_package" type="number" step="0.001"
                                min="0" class="form-control w-full" :label="__('KG/Package')"
                                errorMessage="{{ $errors->first('offerItems.' . $index . '.kg_per_package') }}" />

                            <x-text-input wire:model="offerItems.{{ $index }}.one_package_cost"
                                wire:change="recalculate({{ $index }})"
                                id="offerItems.{{ $index }}.one_package_cost" type="number" step="0.01"
                                min="0" class="form-control w-full" :label="__('Cost/Package')"
                                errorMessage="{{ $errors->first('offerItems.' . $index . '.one_package_cost') }}" />
                        </div>

                        @can('view-product-costs')
                            <x-text-input :label="__('Ton Packing Cost')"
                                value="{{ number_format($item['total_packing_cost'] ?? 0, 2) }}" readonly
                                class="form-control bg-light mb-3" />
                        @endcan
                        @can('view-product-costs')
                            {{-- Pricing --}}
                            <x-text-input :label="__('Ton Base Cost (Currency)')"
                                value="{{ number_format($item['base_cost_currency'] ?? 0, 2) }}" readonly
                                class="form-control bg-light mb-3" />
                        @endcan


                        <x-text-input wire:model="offerItems.{{ $index }}.profit_margain"
                            wire:change="recalculate({{ $index }})"
                            id="offerItems.{{ $index }}.profit_margain" type="number" step="0.01"
                            min="0" class="form-control" :label="__('Profit Margin (%)')"
                            errorMessage="{{ $errors->first('offerItems.' . $index . '.profit_margain') }}" />

                        <x-text-input :label="__('Ton FOB Price')" value="{{ number_format($item['fob_price'] ?? 0, 2) }}"
                            readonly class="form-control bg-light mb-3" />
                    </div>
                </div>

                {{-- Right Column: Additional Costs & Summary --}}
                <div class="lg:col-span-1">
                    <div class="border border-yellow-200 rounded-lg p-4 h-full bg-yellow-50">
                        <h6 class="text-yellow-600 mb-3 font-semibold"><i class="fa fa-calculator"></i> Costs &
                            Summary</h6>

                        {{-- Freight --}}
                        <div class="mb-2">
                            <small class="text-muted">Freight</small>
                            <div class="grid grid-cols-3 gap-1">
                                <div>
                                    <x-text-input wire:model="offerItems.{{ $index }}.freight_cost"
                                        wire:change="recalculate({{ $index }})" placeholder="Cost"
                                        type="number" step="0.01" min="0"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.freight_cost') }}" />

                                </div>
                                <div>
                                    <x-select wire:model="offerItems.{{ $index }}.freight_type"
                                        wire:change="recalculate({{ $index }})"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.freight_type') }}">
                                        @foreach ($calcTypes as $type)
                                            <option value="{{ $type }}">
                                                {{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                        @endforeach
                                    </x-select>

                                </div>
                                @can('view-product-costs')
                                    <div>
                                        <x-text-input value="{{ number_format($item['freight_total_cost'] ?? 0, 2) }}"
                                            readonly class="form-control form-control-sm bg-light w-full" />
                                    </div>
                                @endcan
                            </div>
                        </div>

                        {{-- Sterilization --}}
                        <div class="mb-2">
                            <small class="text-muted">Sterilization</small>
                            <div class="grid grid-cols-3 gap-1">
                                <div>
                                    <x-text-input wire:model="offerItems.{{ $index }}.sterilization_cost"
                                        wire:change="recalculate({{ $index }})" placeholder="Cost"
                                        type="number" step="0.01" min="0"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.sterilization_cost') }}" />

                                </div>
                                <div>
                                    <x-select wire:model="offerItems.{{ $index }}.sterilization_type"
                                        wire:change="recalculate({{ $index }})"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.sterilization_type') }}">
                                        @foreach ($calcTypes as $type)
                                            <option value="{{ $type }}">
                                                {{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                        @endforeach
                                    </x-select>

                                </div>
                                @can('view-product-costs')
                                    <div>
                                        <x-text-input
                                            value="{{ number_format($item['sterilization_total_cost'] ?? 0, 2) }}"
                                            readonly class="form-control form-control-sm bg-light w-full" />
                                    </div>
                                @endcan
                            </div>
                        </div>

                        {{-- Agent Commission --}}
                        <div class="mb-3">
                            <small class="text-muted">Agent Commission</small>
                            <div class="grid grid-cols-3 gap-1">
                                <div>
                                    <x-text-input wire:model="offerItems.{{ $index }}.agent_commission_cost"
                                        wire:change="recalculate({{ $index }})" placeholder="Cost"
                                        type="number" step="0.01" min="0"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.agent_commission_cost') }}" />

                                </div>
                                <div>
                                    <x-select wire:model="offerItems.{{ $index }}.agent_commission_type"
                                        wire:change="recalculate({{ $index }})"
                                        class="form-control form-control-sm w-full"
                                        errorMessage="{{ $errors->first('offerItems.' . $index . '.agent_commission_type') }}">
                                        @foreach ($calcTypes as $type)
                                            <option value="{{ $type }}">
                                                {{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                        @endforeach
                                    </x-select>

                                </div>
                                @can('view-product-costs')
                                    <div>
                                        <x-text-input
                                            value="{{ number_format($item['agent_commission_total_cost'] ?? 0, 2) }}"
                                            readonly class="form-control form-control-sm bg-light w-full" />
                                    </div>
                                @endcan
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="border-t pt-2">
                            <strong class="text-gray-600 mb-5">Summary</strong>
                            @can('view-product-costs')
                                <div class="mb-2">
                                    <div class="flex justify-between">
                                        <small>Total Ton Costs:</small>
                                        <strong
                                            class="text-red-600">{{ number_format($item['total_costs'] ?? 0, 2) }}</strong>
                                    </div>
                                </div>
                            @endcan
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Tons:</small>
                                    <strong
                                        class="text-red-600">{{ number_format($item['quantity_in_kgs'] / 1000 ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Profit per Ton:</small>
                                    <strong
                                        class="text-green-600">{{ number_format($item['total_profit'] ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between">
                                    <small>Total Price:</small>
                                    <strong
                                        class="text-blue-600">{{ number_format($item['price'] ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    @endforeach

    <div class="mt-5">
        {{-- Overall Summary Card --}}
        <x-card title="Offer Summary">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Items Total</h5>
                    <h3 class="text-blue-600 text-2xl font-bold">{{ count($offerItems) }}</h3>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Tons Total</h5>
                    <h3 class="text-teal-600 text-2xl font-bold">
                        {{ number_format(array_sum(array_column($offerItems, 'quantity_in_kgs')), 2) }}</h3>
                </div>
                @can('view-product-costs')
                    <div class="text-center">
                        <h5 class="text-gray-600 text-sm">Costs Total</h5>
                        <h3 class="text-yellow-600 text-2xl font-bold">
                            {{ number_format(array_sum(array_column($offerItems, 'total_costs')), 2) }}</h3>
                    </div>
                @endcan
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Price Total</h5>
                    <h3 class="text-green-600 text-2xl font-bold">
                        {{ number_format(array_sum(array_column($offerItems, 'price')), 2) }}
                    </h3>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Save Button at Bottom --}}
    <div class="text-right mt-4">
        <button wire:click="saveOffer" type="button" class="btn btn-primary btn-lg">
            <i class="fa fa-save"></i> Save Offer
        </button>
    </div>
</div>
