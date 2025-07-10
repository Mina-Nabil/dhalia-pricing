<div>
    <!-- Modal Trigger Button -->
    <button wire:click="openModal" 
        class="btn inline-flex justify-center items-center btn-outline-primary w-full">
        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:tag"></iconify-icon>
        Select Statuses
        @if(count($selectedStatuses) > 0)
            <span class="ml-2 badge bg-primary-500 text-white">{{ count($selectedStatuses) }}</span>
        @endif
    </button>
    <div class="flex space-x-2 mt-2">
        @foreach($selectedStatuses as $status)
            <span class="badge bg-primary-500 text-white">{{ $status }}</span>
        @endforeach
    </div>

    <!-- Modal -->
    <x-modal wire:model="showModal" maxWidth="2xl">
        <x-slot name="title">
            Select Offer Statuses
        </x-slot>
        
        <x-slot name="content">
            <!-- Action Buttons -->
            <div class="flex space-x-2 mb-4">
                <button wire:click="selectAll" 
                    class="btn btn-sm btn-outline-primary">
                    Select All
                </button>
                <button wire:click="clearStatusesSelection" 
                    class="btn btn-sm btn-outline-secondary">
                    Deselect All
                </button>
                <div class="flex-1"></div>
                <span class="text-sm text-slate-600">
                    {{ count($selectedStatuses) }} selected
                </span>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($statuses as $status)
                    <div class="border rounded-lg p-3 sm:p-4 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer {{ $this->isSelected($status) ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20' : '' }}"
                         wire:click="toggleStatus('{{ $status }}')">
                        <div class="flex items-start justify-between space-x-3">
                            <div class="flex items-start space-x-3 flex-1 min-w-0">
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($this->isSelected($status))
                                        <div class="w-5 h-5 sm:w-6 sm:h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                            <iconify-icon icon="heroicons:check" class="text-white text-xs sm:text-sm"></iconify-icon>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 sm:w-6 sm:h-6 border-2 border-slate-300 rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-slate-900 dark:text-slate-100 text-sm sm:text-base">
                                        {{ ucfirst($status) }}
                                    </h4>
                                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                        @switch($status)
                                            @case('draft')
                                                Offers that are being prepared
                                                @break
                                            @case('sent')
                                                Offers that have been sent to clients
                                                @break
                                            @case('accepted')
                                                Offers that clients have accepted
                                                @break
                                            @case('rejected')
                                                Offers that clients have rejected
                                                @break
                                            @case('cancelled')
                                                Offers that have been cancelled
                                                @break
                                            @case('archived')
                                                Offers that have been archived
                                                @break
                                            @default
                                                {{ ucfirst($status) }} offers
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge {{ $this->getStatusBadgeClass($status) }} text-xs">
                                    <span class="hidden sm:inline">{{ ucfirst($status) }}</span>
                                    <span class="sm:hidden">{{ strtoupper(substr($status, 0, 1)) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(count($selectedStatuses) > 0)
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2 text-sm sm:text-base">Selected Statuses:</h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedStatuses as $status)
                            <span class="badge {{ $this->getStatusBadgeClass($status) }} text-xs">
                                {{ ucfirst($status) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <span class="text-sm text-slate-600">
                    {{ count($selectedStatuses) }} status(es) selected
                </span>
                <div class="flex space-x-3">
                    <x-secondary-button wire:click="closeModal">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button wire:click="applySelection">
                        Apply Selection
                    </x-primary-button>
                </div>
            </div>
        </x-slot>
    </x-modal>
</div> 