<div>
    {{-- Offers Management Page --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Offers Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create-offers')
                <button class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1" wire:click="goToOfferCreate">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Offer
                </button>
            @endcan
        </div>
    </div>

    <!-- Search Bar and Filters Toggle -->
    <x-card class="mb-6" :title="'Offers'">

        <div class="flex flex-wrap sm:flex-nowrap justify-between space-x-3 rtl:space-x-reverse mb-6">
            <div class="flex-1 w-full sm:w-auto mb-3 sm:mb-0">
                <div class="relative">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control pl-10" placeholder="Using code, client name, client phone or user name..."
                        wire:model.live.debounce.300ms="search">
                 
                </div>
            </div>
            <div class="flex space-x-2">
                <button wire:click="toggleFilters" 
                    class="btn inline-flex justify-center items-center {{ $showFilters ? 'btn-primary' : 'btn-outline-primary' }}">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:funnel"></iconify-icon>
                    Filters
                </button>
                @if (
                    $search ||
                        count($filterUserIds) ||
                        count($filterClientIds) ||
                        count($filterStatuses) ||
                        $filterDateFrom ||
                        $filterDateTo ||
                        $filterPriceFrom ||
                        $filterPriceTo)
                    <button wire:click="clearFilters" class="btn inline-flex justify-center btn-outline-danger items-center">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:x-mark"></iconify-icon>
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Filters Section -->
        @if ($showFilters)
            <div class="card mb-6">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Client Filter Modal -->
                        <div class="form-group">
                            <label class="form-label">Clients</label>
                            <livewire:components.select-clients-modal :selectedClientIds="$filterClientIds" />
                        </div>

                        <!-- User Filter Modal -->
                        <div class="form-group">
                            <label class="form-label">Users</label>
                            <livewire:components.select-users-modal :selectedUserIds="$filterUserIds" />
                        </div>

                        <!-- Status Filter Modal -->
                        <div class="form-group">
                            <label class="form-label">Statuses</label>
                            <livewire:components.select-statuses-modal :selectedStatuses="$filterStatuses" />
                        </div>

                        <!-- Sort Filter -->
                        <div class="form-group">
                            <label for="sort" class="form-label">Sort By</label>
                            <div class="flex space-x-2">
                                <select id="sort" class="form-control" wire:model.live="sort">
                                    @foreach ($sortFields as $field)
                                        <option value="{{ $field }}">{{ ucwords(str_replace('_', ' ', $field)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-control w-20" wire:model.live="sortDirection">
                                    <option value="asc">ASC</option>
                                    <option value="desc">DESC</option>
                                </select>
                            </div>
                        </div>

                        <!-- Date From Filter -->
                        <div class="form-group">
                            <label for="filterDateFrom" class="form-label">Date From</label>
                            <input type="date" id="filterDateFrom" class="form-control"
                                wire:model.live="filterDateFrom">
                        </div>

                        <!-- Date To Filter -->
                        <div class="form-group">
                        <label for="filterDateTo" class="form-label">Date To</label>
                            <input type="date" id="filterDateTo" class="form-control" wire:model.live="filterDateTo">
                        </div>

                        <!-- Price From Filter -->
                        <div class="form-group">
                            <label for="filterPriceFrom" class="form-label">Price From</label>
                            <input type="number" id="filterPriceFrom" class="form-control" step="0.01" 
                                placeholder="Min price" wire:model.live="filterPriceFrom">
                        </div>

                        <!-- Price To Filter -->
                        <div class="form-group">
                            <label for="filterPriceTo" class="form-label">Price To</label>
                            <input type="number" id="filterPriceTo" class="form-control" step="0.01" 
                                placeholder="Max price" wire:model.live="filterPriceTo">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Offers Table -->
        <div class="overflow-x-auto -mx-6">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th cursor-pointer" wire:click="sortBy('code')">
                                    <div class="flex items-center">
                                        Code
                                        @if ($sort === 'code')
                                            <iconify-icon
                                                icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"
                                                class="ml-1"></iconify-icon>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="table-th">Client</th>
                                <th scope="col" class="table-th">User</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th cursor-pointer"
                                    wire:click="sortBy('total_price')">
                                    <div class="flex items-center">
                                        Total Price
                                        @if ($sort === 'total_price')
                                            <iconify-icon
                                                icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"
                                                class="ml-1"></iconify-icon>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="table-th cursor-pointer"
                                    wire:click="sortBy('total_tonnage')">
                                    <div class="flex items-center">
                                        Tonnage
                                        @if ($sort === 'total_tonnage')
                                            <iconify-icon
                                                icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"
                                                class="ml-1"></iconify-icon>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="table-th cursor-pointer" wire:click="sortBy('created_at')">
                                    <div class="flex items-center">
                                        Created At
                                        @if ($sort === 'created_at')
                                            <iconify-icon
                                                icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"
                                                class="ml-1"></iconify-icon>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @forelse($offers as $offer)
                                <tr>
                                    <td class="table-td font-medium">{{ $offer->code }}</td>
                                    <td class="table-td">{{ $offer->client->name ?? 'N/A' }}</td>
                                    <td class="table-td">{{ $offer->user->name ?? 'N/A' }}</td>
                                    <td class="table-td">
                                        <span
                                            class="badge 
                                                @switch($offer->status)
                                                    @case('draft') bg-slate-900 text-white @break
                                                    @case('sent') bg-info-500 text-white @break
                                                    @case('accepted') bg-success-500 text-white @break
                                                    @case('rejected') bg-danger-500 text-white @break
                                                    @case('cancelled') bg-warning-500 text-white @break
                                                    @case('archived') bg-slate-500 text-white @break
                                                    @default bg-slate-900 text-white
                                                @endswitch
                                            ">
                                            {{ ucfirst($offer->status) }}
                                        </span>
                                    </td>
                                    <td class="table-td">${{ number_format($offer->total_price, 2) }}</td>
                                    <td class="table-td">{{ number_format($offer->total_tonnage, 2) }}</td>
                                    <td class="table-td">{{ $offer->created_at->format('M d, Y') }}</td>
                                    <td class="table-td">
                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                            <button wire:click="goToOfferShow({{ $offer->id }})"
                                                class="action-btn text-primary">
                                                <iconify-icon icon="heroicons:eye"></iconify-icon>
                                            </button>
                                            @can('update', $offer)
                                                <button class="action-btn text-primary">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                            @endcan
                                            @can('delete', $offer)
                                                <button wire:click="confirmDeleteOffer({{ $offer->id }})"
                                                    class="action-btn text-danger">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="table-td text-center">No offers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-6">
            {{ $offers->links('vendor.livewire.simple-bootstrap') }}
        </div>


    </x-card>

    <!-- Delete Confirmation Modal -->
    @if ($deleteConfirmationModal)
        <x-modal wire:model="deleteConfirmationModal">
            <x-slot name="title">
                Confirm Delete
            </x-slot>
            <x-slot name="content">
                <p>Are you sure you want to delete this offer? This action cannot be undone.</p>
                <div class="text-danger-500">
                    <p>Warning: Deleting this offer will also remove all associated items and comments.</p>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeDeleteConfirmationModal">
                    Cancel
                </x-secondary-button>
                <x-danger-button wire:click.prevent="confirmDelete" loadingFunction="confirmDelete">
                    Delete
                </x-danger-button>
            </x-slot>
        </x-modal>
    @endif
</div>
