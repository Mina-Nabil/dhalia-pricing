<div>
    {{-- Main Offer Card --}}
    <x-card title="{{ $offer->code ?? 'N/A' }}">

        <x-slot name="tools">
            <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                @can('update-offer', $offer)
                    <div class="flex items-center gap-2">
                        <select wire:change="updateOfferStatus($event.target.value)" 
                                class="btn btn-outline-primary btn-sm w-full sm:w-auto" 
                                style="background: white; border: 1px solid #007bff; color: #007bff; padding: 0.375rem 0.75rem; border-radius: 0.375rem;">
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}" 
                                        {{ $offer->status === $statusOption ? 'selected' : '' }}>
                                    {{ ucfirst($statusOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endcan
                
                <div class="flex flex-col sm:flex-row gap-2">
                    @can('create-offers')
                        <button wire:click="duplicateOffer" type="button" class="btn btn-warning btn-sm">
                            <i class="fa fa-copy"></i> <span class="hidden sm:inline">Duplicate</span>
                        </button>
                    @endcan
                    
                    @can('delete-offer', $offer)
                        <button wire:click="$dispatch('showConfirmationModal', {
                            title: 'Delete Offer',
                            message: 'Are you sure you want to delete this offer? This action cannot be undone.',
                            callback: 'deleteOffer'
                        })" type="button" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> <span class="hidden sm:inline">Delete</span>
                        </button>
                    @endcan
                    
                    <a href="{{ route('offers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> <span class="hidden sm:inline">Back</span>
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <x-input-label :value="__('Status')" />
                <span class="badge badge-{{ $offer->status === 'draft' ? 'secondary' : ($offer->status === 'sent' ? 'primary' : ($offer->status === 'accepted' ? 'success' : 'danger')) }}">
                    {{ ucfirst($offer->status) }}
                </span>
            </div>

            <div>
                <x-input-label :value="__('Client')" />
                <p class="form-control-plaintext mb-0">{{ $offer->client->name ?? 'N/A' }}</p>
            </div>

            <div>
                <x-input-label :value="__('Currency')" />
                <p class="form-control-plaintext mb-0">{{ $offer->currency->code ?? 'N/A' }}</p>
            </div>

            <div>
                <x-input-label :value="__('Rate')" />
                <p class="form-control-plaintext mb-0">{{ number_format($offer->currency_rate ?? 0, 3) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 pt-2 border-t">
            <div>
                <x-input-label :value="__('Created By')" />
                <p class="form-control-plaintext mb-0">{{ $offer->user->name ?? 'N/A' }}</p>
            </div>

            <div>
                <x-input-label :value="__('Created At')" />
                <p class="form-control-plaintext mb-0">{{ $offer->created_at ? $offer->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
            </div>

            <div>
                <x-input-label :value="__('Updated At')" />
                <p class="form-control-plaintext mb-0">{{ $offer->updated_at ? $offer->updated_at->format('Y-m-d H:i') : 'N/A' }}</p>
            </div>
        </div>
    </x-card>

    {{-- Notes Section --}}
    @can('update-offer-notes', $offer)
        <x-card title="Notes">
            <div class="mb-3">
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea wire:model="notes" id="notes" name="notes" rows="3" 
                    class="form-control" placeholder="Enter any additional notes or comments..."></textarea>
                @error('notes')
                    <span class="text-danger-500 small">{{ $message }}</span>
                @enderror
            </div>
            <div class="text-right">
                <button wire:click="updateNotes" type="button" class="btn btn-primary btn-sm">
                    <i class="fa fa-save"></i> Save Notes
                </button>
            </div>
        </x-card>
    @else
        @if($offer->notes)
            <x-card title="Notes">
                <div class="mb-0">
                    <x-input-label :value="__('Notes')" />
                    <p class="form-control-plaintext">{{ $offer->notes }}</p>
                </div>
            </x-card>
        @endif
    @endcan

    {{-- Offer Items Cards --}}
    <div class="flex justify-between items-center mb-3 mt-4">
        <h4>Offer Items ({{ $offer->items->count() }})</h4>
    </div>

    @foreach ($offer->items as $index => $item)
        <x-card title="Product #{{ $index + 1 }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Left Column: Product & Ingredients --}}
                <div class="lg:col-span-1">
                    <div class="border border-blue-200 rounded-lg p-2 sm:p-4 h-full bg-blue-50">
                        <h6 class="text-blue-600 mb-3 font-semibold"><i class="fa fa-cube"></i> Product & Ingredients</h6>

                        {{-- Product Information --}}
                        <div class="mb-3">
                            <x-input-label :value="__('Product')" />
                            <p class="form-control-plaintext">
                                @if($item->product)
                                    ({{ $item->product->category->name ?? 'N/A' }} - {{ $item->product->spec->name ?? 'N/A' }}) - {{ $item->product->name }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>

                        {{-- Internal Cost --}}
                        <div class="mb-3">
                            <x-input-label :value="__('Internal Cost per Ton')" />
                            <p class="form-control-plaintext">{{ number_format($item->internal_cost ?? 0, 3) }}</p>
                        </div>

                        {{-- Quantity in Tons --}}
                        <div class="mb-3">
                            <x-input-label :value="__('Quantity (KGs)')" />
                            <p class="form-control-plaintext">{{ number_format($item->quantity_in_kgs ?? 0, 3) }}</p>
                        </div>

                        {{-- Ingredients --}}
                        <div>
                            <x-input-label :value="__('Ingredients')" />
                            @if ($item->ingredients && $item->ingredients->count() > 0)
                                @foreach ($item->ingredients as $ingredient)
                                    <div class="border border-gray-200 rounded-lg p-2 mb-2 bg-white">
                                        <div class="grid grid-cols-3 gap-2 text-sm">
                                            <div>
                                                <small class="text-muted">Name:</small>
                                                <p class="mb-1">{{ $ingredient->name }}</p>
                                            </div>
                                            <div>
                                                <small class="text-muted">Cost:</small>
                                                <p class="mb-1">{{ number_format($ingredient->cost ?? 0, 2) }}</p>
                                            </div>
                                            <div>
                                                <small class="text-muted">Percentage:</small>
                                                <p class="mb-1">{{ number_format($ingredient->percentage ?? 0, 2) }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No ingredients</p>
                            @endif
                        </div>

                        {{-- Raw costs per Ton section --}}
                        <div class="border-t pt-2 mt-2">
                            <strong class="text-gray-600 mb-2">Raw costs per Ton</strong>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Internal Cost:</small>
                                    <strong class="text-red-600">{{ number_format($item->internal_cost ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Ingredients Cost:</small>
                                    <strong class="text-red-600">{{ number_format($item->ingredients_cost ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Total Costs:</small>
                                    <strong class="text-red-600">{{ number_format($item->raw_ton_cost ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Middle Column: Packing & Pricing --}}
                <div class="lg:col-span-1">
                    <div class="border border-green-200 rounded-lg p-2 sm:p-4 h-full bg-green-50">
                        <h6 class="text-green-600 mb-3 font-semibold"><i class="fa fa-box"></i> Packing & Pricing</h6>

                        {{-- Packing Info --}}
                        <div class="mb-3">
                            <x-input-label :value="__('Packing Type')" />
                            <p class="form-control-plaintext">{{ $item->packing->name ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input-label :value="__('KG/Package')" />
                                <p class="form-control-plaintext">{{ number_format($item->kg_per_package ?? 0, 3) }}</p>
                            </div>

                            <div>
                                <x-input-label :value="__('Cost/Package')" />
                                <p class="form-control-plaintext">{{ number_format($item->one_package_cost ?? 0, 2) }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label :value="__('Total Packing Cost')" />
                            <p class="form-control-plaintext font-bold">{{ number_format($item->total_packing_cost ?? 0, 2) }}</p>
                        </div>

                        {{-- Pricing --}}
                        <div class="mb-3">
                            <x-input-label :value="__('Base Cost per Ton (Currency)')" />
                            <p class="form-control-plaintext">{{ number_format($item->base_cost_currency ?? 0, 2) }} {{ $offer->currency->code ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <x-input-label :value="__('Profit Margin (%)')" />
                            <p class="form-control-plaintext">{{ number_format($item->profit_margain ?? 0, 2) }}%</p>
                        </div>

                        <div>
                            <x-input-label :value="__('Ton FOB Price')" />
                            <p class="form-control-plaintext font-bold text-green-600">{{ number_format($item->fob_price ?? 0, 2) }} {{ $offer->currency->code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Additional Costs & Summary --}}
                <div class="lg:col-span-1">
                    <div class="border border-yellow-200 rounded-lg p-2 sm:p-4 h-full bg-yellow-50">
                        <h6 class="text-yellow-600 mb-3 font-semibold"><i class="fa fa-calculator"></i> Costs & Summary</h6>

                        {{-- Freight --}}
                        <div class="mb-2">
                            <small class="text-muted font-semibold">Freight</small>
                            <div class="grid grid-cols-3 gap-1 text-sm">
                                <div>
                                    <small class="text-muted">Cost:</small>
                                    <p>{{ number_format($item->freight_cost ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Type:</small>
                                    <p>{{ ucfirst(str_replace('_', ' ', $item->freight_type ?? 'fixed')) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Total:</small>
                                    <p class="font-bold">{{ number_format($item->freight_total_cost ?? 0, 2) }} {{ $offer->currency->code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sterilization --}}
                        <div class="mb-2">
                            <small class="text-muted font-semibold">Sterilization</small>
                            <div class="grid grid-cols-3 gap-1 text-sm">
                                <div>
                                    <small class="text-muted">Cost:</small>
                                    <p>{{ number_format($item->sterilization_cost ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Type:</small>
                                    <p>{{ ucfirst(str_replace('_', ' ', $item->sterilization_type ?? 'fixed')) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Total:</small>
                                    <p class="font-bold">{{ number_format($item->sterilization_total_cost ?? 0, 2) }} {{ $offer->currency->code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Agent Commission --}}
                        <div class="mb-3">
                            <small class="text-muted font-semibold">Agent Commission</small>
                            <div class="grid grid-cols-3 gap-1 text-sm">
                                <div>
                                    <small class="text-muted">Cost:</small>
                                    <p>{{ number_format($item->agent_commission_cost ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Type:</small>
                                    <p>{{ ucfirst(str_replace('_', ' ', $item->agent_commission_type ?? 'fixed')) }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Total:</small>
                                    <p class="font-bold">{{ number_format($item->agent_commission_total_cost ?? 0, 2) }} {{ $offer->currency->code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="border-t pt-2">
                            <strong class="text-gray-600 mb-5">Summary</strong>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Total Ton Costs:</small>
                                    <strong class="text-red-600">{{ number_format($item->total_costs ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Tons:</small>
                                    <strong class="text-red-600">{{ number_format($item->quantity_in_kgs / 1000 ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between">
                                    <small>Profit per Ton:</small>
                                    <strong class="text-green-600">{{ number_format($item->total_profit ?? 0, 2) }}</strong>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between">
                                    <small>Total Price:</small>
                                    <strong class="text-blue-600">{{ number_format($item->price ?? 0, 2) }}</strong>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Items Total</h5>
                    <h3 class="text-blue-600 text-2xl font-bold">{{ $offer->items->count() }}</h3>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Tons Total</h5>
                    <h3 class="text-teal-600 text-2xl font-bold">
                        {{ number_format($offer->items->sum('quantity_in_kgs'), 2) }}
                    </h3>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Costs Total</h5>
                    <h3 class="text-yellow-600 text-2xl font-bold">
                        {{ number_format($offer->total_costs ?? 0, 2) }}
                    </h3>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Price Total</h5>
                    <h3 class="text-green-600 text-2xl font-bold">
                        {{ number_format($offer->total_price ?? 0, 2) }}
                    </h3>
                </div>
            </div>

            {{-- Additional Summary Information --}}
            <div class="grid grid-cols-3 sm:grid-cols-3 gap-4 mt-4 pt-4 border-t">
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Total Profit</h5>
                    <h4 class="text-green-600 text-xl font-bold">{{ number_format($offer->total_profit ?? 0, 2) }}</h4>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Profit Percentage</h5>
                    <h4 class="text-green-600 text-xl font-bold">{{ number_format($offer->profit_percentage ?? 0, 2) }}%</h4>
                </div>
                <div class="text-center">
                    <h5 class="text-gray-600 text-sm">Currency</h5>
                    <h4 class="text-blue-600 text-xl font-bold">{{ $offer->currency->code ?? 'N/A' }}</h4>
                </div>
            </div>
        </x-card>
    </div>
</div>
