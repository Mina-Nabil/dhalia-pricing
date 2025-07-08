<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Specs Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create-spec')
                <button wire:click="openNewSpecSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Spec
                </button>
            @endcan
        </div>
    </div>

    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search specs..."
                wire:model.live.debounce.500ms="search">
        </header>

        <div class="card-body px-6 pb-6">
            <div class=" -mx-6">
                <div class="inline-block min-w-full align-middle">

                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class=" table-th ">
                                    Name
                                </th>

                                <th scope="col" class=" table-th ">
                                    Products Count
                                </th>

                                <th scope="col" class=" table-th ">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                            @foreach ($specs as $spec)
                                <tr>
                                    <td class="table-td flex items-center">
                                        <div class="rounded-full flex-shrink-0 ltr:mr-[10px] rtl:ml-[10px]">
                                            <span
                                                class="block w-8 h-8 lg:w-8 lg:h-8 object-cover text-center text-lg leading-8 user-initial">
                                                {{ strtoupper(substr($spec->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span>{{ $spec->name }}</span>
                                    </td>

                                    <td class="table-td">
                                        <span
                                            class="badge bg-primary-500 text-primary-500 bg-opacity-30 capitalize rounded-3xl">
                                            {{ $spec->products()->count() }} product(s)
                                        </span>
                                    </td>

                                    <td>
                                        <div class="dropstart relative">
                                            <button class="inline-flex justify-center items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                    icon="heroicons-outline:dots-vertical"></iconify-icon>
                                            </button>
                                            <ul
                                                class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[29990] float-left list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                                @can('update-spec', $spec)
                                                    <li wire:click="updateThisSpec({{ $spec->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                                            <span>Edit</span>
                                                        </span>
                                                    </li>
                                                @endcan

                                                @can('delete-spec', $spec)
                                                    <li wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this spec? This will remove it from all associated products.',color:'danger',callback:'deleteSpec',params:{{ $spec->id }}})">
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

                    @if ($specs->isEmpty())
                        {{-- START: empty filter result --}}
                        <div class="card m-5 p-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                        No specs found</h2>
                                    <p class="card-text">Try changing your search criteria or create a new spec
                                    </p>
                                    @can('create-spec')
                                        <button wire:click="openNewSpecSec"
                                            class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">
                                            Create Spec
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        {{-- END: empty filter result --}}
                    @endif

                </div>
                <div class="mt-6">
                    {{ $specs->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Spec Modal --}}
    @can('create-spec')
        @if ($setSpecSec)
            <x-modal wire:model="setSpecSec">
                <x-slot name="title">
                    {{ $editMode ? 'Edit Spec' : 'Create New Spec' }}
                </x-slot>
                <x-slot name="content">

                    <x-text-input wire:model="name" label="Spec Name" errorMessage="{{ $errors->first('name') }}" />

                    <div class="text-sm text-slate-500 mt-2">
                        <p>* Specs can be associated with multiple products</p>
                    </div>

                </x-slot>
                <x-slot name="footer">
                    <x-primary-button wire:click.prevent="addNewSpec" loadingFunction="addNewSpec">
                        {{ $editMode ? 'Update Spec' : 'Create Spec' }}
                    </x-primary-button>
                </x-slot>
            </x-modal>
        @endif
    @endcan
</div>
