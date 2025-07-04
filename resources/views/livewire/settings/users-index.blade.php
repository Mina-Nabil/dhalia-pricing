<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Users Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\User::class)
                <button wire:click="openNewUserSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create User
                </button>
            @endcan

        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
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
                                    Username
                                </th>

                                <th scope="col" class=" table-th ">
                                    Role
                                </th>

                                <th scope="col" class=" table-th ">
                                    Status
                                </th>

                                <th scope="col" class=" table-th ">
                                    Action
                                </th>


                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                            @foreach ($users as $user)
                                <tr>

                                    <td class="table-td flex items-center">
                                        <div class="rounded-full flex-shrink-0 ltr:mr-[10px] rtl:ml-[10px]">
                                            <span
                                                class="block w-8 h-8 lg:w-8 lg:h-8 object-cover text-center text-lg leading-8 user-initial">
                                                {{ strtoupper(substr($user->username, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </td>



                                    <td class="table-td">
                                        {{ $user->username }}
                                    </td>

                                    <td class="table-td ">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="badge bg-info-500 text-slate-900 bg-opacity-30 capitalize rounded-3xl">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                                        </div>
                                    </td>

                                    <td class="table-td">
                                        @if ($user->is_active)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Active</span>
                                        @else
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Deactivated</span>
                                        @endif

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

                                                <li wire:click="updateThisUser({{ $user->id }})">
                                                    <span
                                                        class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                                        <span>Edit</span></span>
                                                </li>

                                                <li wire:click="openChangePasswordModal({{ $user->id }})">
                                                    <span
                                                        class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                        <iconify-icon icon="lucide:key"></iconify-icon>
                                                        <span>Change Password</span></span>
                                                </li>

                                                @if ($user->is_active)
                                                    <li wire:click="toggleUserStatus({{ $user->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="ant-design:stop-twotone"></iconify-icon>
                                                            <span>Set as Deactivated</span></span>
                                                    </li>
                                                @else
                                                    <li wire:click="toggleUserStatus({{ $user->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon
                                                                icon="teenyicons:tick-circle-outline"></iconify-icon>
                                                            <span>Set as Active</span></span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>


                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    @if ($users->isEmpty())
                        {{-- START: empty filter result --}}
                        <div class="card m-5 p-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                        No users with filters</h2>
                                    <p class="card-text">Try changing filters
                                    </p>
                                    <a href="{{ url('/users') }}"
                                        class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                        all users</a>
                                </div>
                            </div>
                        </div>
                        {{-- END: empty filter result --}}
                    @endif


                </div>
                <div class="mt-6">
                    {{ $users->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    @can('create', App\Models\User::class)
        @if ($setUserSec)
            <x-modal wire:model="setUserSec">
                <x-slot name="title">
                    Create New User
                </x-slot>
                <x-slot name="content">

                    <x-text-input wire:model="username" label="Username" errorMessage="{{ $errors->first('username') }}" />

                    <x-text-input wire:model="name" label="Name" errorMessage="{{ $errors->first('name') }}" />



                    <x-select wire:model="role" label="Role" errorMessage="{{ $errors->first('role') }}">
                        @foreach ($TYPES as $type)
                            <option value="{{ $type }}">
                                {{ $type }}</option>
                        @endforeach
                    </x-select>

                    <x-text-input wire:model="password" label="Password" type="password" errorMessage="{{ $errors->first('password') }}" />

                    <x-text-input wire:model="password_confirmation" label="Confirm Password"
                        type="password"
                        errorMessage="{{ $errors->first('password_confirmation') }}" />


                </x-slot>
                <x-slot name="footer">
                    <x-primary-button wire:click.prevent="addNewUser" loadingFunction="addNewUser">Create
                        User</x-primary-button>
                </x-slot>
            </x-modal>
        @endif
    @endcan

    <!-- Change Password Modal -->
    @if ($changePasswordModal)
        <x-modal wire:model="changePasswordModal">
            <x-slot name="title">
                Change Password
            </x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <div class="from-group">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        <x-text-input wire:model="newPassword" label="New Password"
                            errorMessage="{{ $errors->first('newPassword') }}" />
                        <x-text-input wire:model="newPassword_confirmation" label="Confirm New Password"
                            errorMessage="{{ $errors->first('newPassword_confirmation') }}" />
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <x-primary-button wire:click.prevent="changeUserPassword" loadingFunction="changeUserPassword">Change
                    Password
                </x-primary-button>
            </x-slot>

        </x-modal>
    @endif
</div>
