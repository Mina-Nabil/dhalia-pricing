<div>
    <!-- Modal Trigger Button -->
    <button wire:click="openModal" class="btn inline-flex justify-center items-center btn-outline-primary w-full">
        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:users"></iconify-icon>
        @if ($mode == 'single')
            @if (count($selectedClientIds) == 1)
                @foreach ($selectedClientNames as $clientName)
                    <span class="badge bg-primary-500 text-white">{{ $clientName }}</span>
                @endforeach
            @else
                Select Client
            @endif
        @else
            @if (count($selectedClientIds) > 0 && count($selectedClientNames) <= 4)
                @foreach ($selectedClientNames as $clientName)
                    <span class="badge bg-primary-500 text-white mr-2">{{ $clientName }}</span>
                @endforeach
            @elseif(count($selectedClientNames) > 4)
                <span class="badge bg-primary-500 text-white mr-2">{{ count($selectedClientNames) }} Clients</span>
            @else
                Select Clients
            @endif
        @endif
    </button>

    <!-- Modal -->
    <x-modal wire:model="showModal" maxWidth="4xl">
        <x-slot name="title">
            Select Clients
        </x-slot>

        <x-slot name="content">
            <!-- Search Bar -->
            <div class="mb-4">
                <label for="clientSearch" class="form-label">Search Clients</label>
                <div class="relative">
                    <input type="text" id="clientSearch" class="form-control pl-10"
                        placeholder="Search by name, phone, or email..." wire:model.live.debounce.300ms="search">
                
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 mb-4">
                @if ($mode == 'multiple')
                    <button wire:click="selectAll" class="btn btn-sm btn-outline-primary">
                        Select All
                    </button>
                @endif
                <button wire:click="clearClientsSelection" class="btn btn-sm btn-outline-secondary">
                    Deselect All
                </button>
                <div class="flex-1"></div>
                <span class="text-sm text-slate-600">
                    {{ count($selectedClientIds) }} selected
                </span>
            </div>

            <!-- Clients Table -->
            <div class="overflow-x-auto max-h-96 -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700" style="min-width: 600px;">
                        <thead class="bg-slate-200 dark:bg-slate-700 sticky top-0">
                            <tr>
                                <th scope="col" class="table-th w-16 whitespace-nowrap">Select</th>
                                <th scope="col" class="table-th whitespace-nowrap">Name</th>
                                <th scope="col" class="table-th whitespace-nowrap">Phone</th>
                                <th scope="col" class="table-th whitespace-nowrap">Email</th>
                                <th scope="col" class="table-th whitespace-nowrap">Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @forelse($clients as $client)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <td class="table-td text-center whitespace-nowrap bg-white dark:bg-slate-800">
                                        <button wire:click="toggleClient({{ $client->id }})"
                                            class="btn btn-sm {{ $this->isSelected($client->id) ? 'btn-primary' : 'btn-outline-secondary' }}">
                                            @if ($this->isSelected($client->id))
                                                <iconify-icon icon="heroicons:check"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="table-td font-medium whitespace-nowrap bg-white dark:bg-slate-800">{{ $client->name }}</td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">{{ $client->phone ?? 'N/A' }}</td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">{{ $client->email ?? 'N/A' }}</td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">{{ Str::limit($client->address ?? 'N/A', 30) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="table-td text-center text-slate-500 whitespace-nowrap bg-white dark:bg-slate-800">
                                        @if ($search)
                                            No clients found matching "{{ $search }}"
                                        @else
                                            No clients available
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($clients->hasPages())
                <div class="mt-4">
                    {{ $clients->links('vendor.livewire.simple-bootstrap') }}
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <span class="text-sm text-slate-600">
                    {{ count($selectedClientIds) }} client(s) selected
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
