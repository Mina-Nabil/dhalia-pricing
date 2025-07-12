<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Packings Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create-packing')
                <button wire:click="openNewPackingSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Packing
                </button>
            @endcan
        </div>
    </div>

    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search packings..."
                wire:model.live.debounce.500ms="search">
        </header>

        <div class="card-body px-6 pb-6">
            <div class="-mx-6 overflow-x-auto">
                <div class="inline-block min-w-full align-middle">

                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700" >
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th whitespace-nowrap">
                                    Name
                                </th>

                                <th scope="col" class="table-th whitespace-nowrap">
                                    Cost
                                </th>

                                <th scope="col" class="table-th whitespace-nowrap">
                                    Status
                                </th>

                                <th scope="col" class="table-th whitespace-nowrap">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                            @foreach ($packings as $packing)
                                <tr>
                                    <td class="table-td flex items-center whitespace-nowrap">
                                        <div class="rounded-full flex-shrink-0 ltr:mr-[10px] rtl:ml-[10px]">
                                            <span
                                                class="block w-8 h-8 lg:w-8 lg:h-8 object-cover text-center text-lg leading-8 user-initial">
                                                {{ strtoupper(substr($packing->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span>{{ $packing->name }}</span>
                                    </td>

                                    <td class="table-td whitespace-nowrap">
                                        <span class="font-medium">{{ number_format($packing->cost, 2) }}EGP</span>
                                    </td>

                                    <td class="table-td whitespace-nowrap">
                                        @if ($packing->is_active)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap">
                                        <div class="dropstart relative">
                                            <button class="inline-flex justify-center items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                    icon="heroicons-outline:dots-vertical"></iconify-icon>
                                            </button>
                                            <ul
                                                class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[29990] float-left list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                                @can('update-packing', $packing)
                                                    <li wire:click="updateThisPacking({{ $packing->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                                            <span>Edit</span>
                                                        </span>
                                                    </li>

                                                    <li wire:click="togglePackingStatus({{ $packing->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            @if ($packing->is_active)
                                                                <iconify-icon icon="lucide:eye-off"></iconify-icon>
                                                                <span>Deactivate</span>
                                                            @else
                                                                <iconify-icon icon="lucide:eye"></iconify-icon>
                                                                <span>Activate</span>
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endcan

                                                @can('delete-packing', $packing)
                                                    <li wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this packing?',color:'danger',callback:'deletePacking',params:{{ $packing->id }}})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                            <span>Delete</span>
                                                        </span>
                                                    </li>
                                                @endcan

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    @if ($packings->isEmpty())
                        {{-- START: empty filter result --}}
                        <div class="card m-5 p-5" style="min-height: 400px;">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                        No packings found</h2>
                                    <p class="card-text">Try changing your search criteria or create a new packing
                                    </p>
                                    @can('create-packing')
                                        <button wire:click="openNewPackingSec"
                                            class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">
                                            Create Packing
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        {{-- END: empty filter result --}}
                    @endif

                </div>
                <div class="mt-6">
                    {{ $packings->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Packing Modal --}}
    @can('create-packing')
        @if ($setPackingSec)
            <x-modal wire:model="setPackingSec">
                <x-slot name="title">
                    {{ $editMode ? 'Edit Packing' : 'Create New Packing' }}
                </x-slot>
                <x-slot name="content">

                    <x-text-input wire:model="name" label="Packing Name" errorMessage="{{ $errors->first('name') }}" />

                    <x-text-input wire:model="cost" label="Packing Cost" type="number" step="0.01" min="0"
                        errorMessage="{{ $errors->first('cost') }}" placeholder="e.g., 10.00" />

                    <div class="text-sm text-slate-500 mt-2">
                        <p>* Cost should be in your base currency</p>
                    </div>

                </x-slot>
                <x-slot name="footer">
                    <x-primary-button wire:click.prevent="addNewPacking" loadingFunction="addNewPacking">
                        {{ $editMode ? 'Update Packing' : 'Create Packing' }}
                    </x-primary-button>
                </x-slot>
            </x-modal>
        @endif
    @endcan
</div>
