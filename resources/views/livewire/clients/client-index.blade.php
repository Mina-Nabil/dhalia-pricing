<div>
    {{-- Client Management --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Clients Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create-client')
                <button wire:click="$dispatch('openNewClient')"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Client
                </button>
                <button wire:click="openImportModal"
                    class="btn inline-flex justify-center btn-primary dark:bg-blue-600 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold"></iconify-icon>
                    Import
                </button>
            @endcan
            @can('view-client-any')
                <button wire:click="exportClients"
                    class="btn inline-flex justify-center btn-success dark:bg-green-600 dark:text-slate-300 m-1"
                    wire:loading.attr="disabled" wire:target="exportClients">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:download-bold" wire:loading.remove
                        wire:target="exportClients"></iconify-icon>
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 animate-spin" icon="ph:spinner-bold" wire:loading
                        wire:target="exportClients"></iconify-icon>
                    <span wire:loading.remove wire:target="exportClients">Export</span>
                    <span wire:loading wire:target="exportClients">Exporting...</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Search Bar -->
    <div class="flex flex-wrap sm:flex-nowrap justify-between space-x-3 rtl:space-x-reverse mb-6">
        <div class="flex-1 w-full sm:w-auto mb-3 sm:mb-0">
            <div class="relative">
                <input type="text" class="form-control pl-10" placeholder="Search by name, code, phone, or email..."
                    wire:model.live.debounce.300ms="search">
                <span class="absolute right-0 top-0 w-9 h-full flex items-center justify-center text-slate-400">
                    <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                </span>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card">
        <header class="card-header noborder">
            <h4 class="card-title">Clients</h4>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Name</th>
                                    <th scope="col" class="table-th">Code</th>
                                    <th scope="col" class="table-th">Phone</th>
                                    <th scope="col" class="table-th">Email</th>
                                    <th scope="col" class="table-th">Address</th>
                                    <th scope="col" class="table-th">Created By</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($clients as $client)
                                    <tr>
                                        <td class="table-td whitespace-nowrap hover:underline"
                                            wire:click="goToClientShow({{ $client->id }})">
                                            <div class="flex items-center">
                                                <div
                                                    class="font-medium text-slate-600 dark:text-slate-300 hover:cursor-pointer">
                                                    {{ $client->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-td">
                                            {{ $client->code ?: '-' }}
                                        </td>
                                        <td class="table-td">
                                            {{ $client->phone ?: '-' }}
                                        </td>
                                        <td class="table-td">
                                            {{ $client->email ?: '-' }}
                                        </td>
                                        <td class="table-td">
                                            <div class="max-w-xs truncate" title="{{ $client->address }}">
                                                {{ $client->address ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="table-td">
                                            {{ $client->createdBy->name ?? '-' }}
                                        </td>
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                @can('view-client', $client)
                                                    <button wire:click="goToClientShow({{ $client->id }})"
                                                        class="action-btn text-primary" title="View Client">
                                                        <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                    </button>
                                                @endcan
                                                @can('delete-client', $client)
                                                    <button wire:click="confirmDeleteClient({{ $client->id }})"
                                                        class="action-btn text-danger" title="Delete Client">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="table-td text-center">
                                            <div class="flex flex-col items-center justify-center py-8">
                                                <iconify-icon icon="heroicons:users"
                                                    class="text-slate-300 text-6xl mb-4"></iconify-icon>
                                                <p class="text-slate-500 dark:text-slate-400">No clients found</p>
                                                @if (empty($search))
                                                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Start by
                                                        creating your first client</p>
                                                @else
                                                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Try
                                                        adjusting your search criteria</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($clients->hasPages())
                <div class="mt-6">
                    {{ $clients->links('vendor.livewire.simple-bootstrap') }}
                </div>
            @endif
        </div>
    </div>


    <!-- Import Modal -->
    @if ($importModal)
        <x-modal wire:model="importModal">
            <x-slot name="title">
                Import Clients from Excel
            </x-slot>
            <x-slot name="content">
                <div class="space-y-4">
                    <div>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">
                            Upload an Excel file to import clients. The file should have the following columns:
                        </p>
                        <div class="bg-slate-100 dark:bg-slate-700 p-3 rounded text-sm">
                            <strong>Required format:</strong><br>
                            A: Client Name | B: Code | C: Phone | D: Email | E: Address | F: Country | G: Notes
                        </div>
                    </div>
                    
                    <div>
                        <label for="import-file-modal" class="form-label">Select Excel File</label>
                        <input type="file" wire:model="importFile" id="import-file-modal" 
                            class="form-control" 
                            accept=".xlsx,.xls">
                        @error('importFile') 
                            <span class="text-danger-500 text-xs mt-1">{{ $message }}</span> 
                        @enderror
                    </div>
                    
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        <p><strong>Note:</strong> Existing clients (matched by name) will be updated. New clients will be created.</p>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeImportModal">
                    Cancel
                </x-secondary-button>
                <button wire:click="importClients"
                    class="btn inline-flex justify-center btn-primary"
                    wire:loading.attr="disabled" wire:target="importClients"
                    {{ !$importFile ? 'disabled' : '' }}>
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold" wire:loading.remove
                        wire:target="importClients"></iconify-icon>
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 animate-spin" icon="ph:spinner-bold" wire:loading
                        wire:target="importClients"></iconify-icon>
                    <span wire:loading.remove wire:target="importClients">Import Clients</span>
                    <span wire:loading wire:target="importClients">Importing...</span>
                </button>
            </x-slot>
        </x-modal>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($deleteConfirmationModal)
        <x-modal wire:model="deleteConfirmationModal">
            <x-slot name="title">
                Confirm Delete
            </x-slot>
            <x-slot name="content">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <iconify-icon icon="heroicons:exclamation-triangle"
                            class="text-danger-500 text-6xl"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-slate-600 dark:text-slate-300 mb-2">
                            Are you sure you want to delete this client? This action cannot be undone.
                        </p>
                        <div class="text-danger-500 text-sm">
                            <p>Warning: This will permanently remove the client and all associated data.</p>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeDeleteConfirmationModal">
                    Cancel
                </x-secondary-button>
                <x-danger-button wire:click.prevent="confirmDelete" loadingFunction="confirmDelete">
                    Delete Client
                </x-danger-button>
            </x-slot>
        </x-modal>
    @endif
</div>
