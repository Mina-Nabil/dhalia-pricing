<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                {{ __('users.users_management') }}
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\User::class)
                <button wire:click="openNewUserSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    {{ __('users.create_user') }}
                </button>
            @endcan

        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="{{ __('users.search') }}"
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

    @can('create', App\Models\Users\User::class)
        @if ($setUserSec)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
                tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ __('users.create_new_user') }}
                                </h3>
                                <button wire:click="closeSetUserSec" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="username" class="form-label">Username</label>
                                        <input id="username" type="text"
                                            class="form-control @error('username') !border-danger-500 @enderror"
                                            wire:model="username" autocomplete="off">
                                    </div>
                                    @error('username')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text"
                                            class="form-control @error('name') !border-danger-500 @enderror"
                                            wire:model="name" autocomplete="off">
                                    </div>

                                    @error('name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="from-group">
                                    <label for="newType" class="form-label">Role</label>
                                    <select name="newType" id="newType"
                                        class="form-control w-full mt-2 @error('type') !border-danger-500 @enderror"
                                        wire:model="type" autocomplete="off">
                                        <option>None</option>
                                        @foreach ($TYPES as $type)
                                            <option value="{{ $type }}">
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('newType')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>


                                @if ($setUserSec === true)
                                    <div class="from-group">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                            <div class="input-area">
                                                <label for="newPassword"
                                                    class="form-label">{{ __('users.password') }}</label>
                                                <input id="newPassword" type="password"
                                                    class="form-control @error('password') !border-danger-500 @enderror"
                                                    wire:model="password" autocomplete="off">
                                            </div>
                                            <div class="input-area">
                                                <label for="password_confirmation"
                                                    class="form-label">{{ __('users.confirm_password') }}</label>
                                                <input type="password"
                                                    class="form-control @error('password_confirmation') !border-danger-500 @enderror"
                                                    autocomplete="off" wire:model="password_confirmation">
                                            </div>
                                        </div>
                                        @error('password')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror

                                        @error('password_confirmation')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeSetUserSec" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    {{ __('users.close') }}
                                </button>
                                <button
                                    @if ($setUserSec === true) wire:click="addNewUser" @elseif(is_numeric($setUserSec)) wire:click="EditUser()" @endif
                                    data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addNewUser">{{ __('users.submit') }}</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addNewUser"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    <!-- Change Password Modal -->
    @if ($changePasswordModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="change_password_modal" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Change Password
                            </h3>
                            <button wire:click="closeChangePasswordModal" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class="input-area">
                                        <label for="changePassword" class="form-label">New Password</label>
                                        <input id="changePassword" type="password"
                                            class="form-control @error('newPassword') !border-danger-500 @enderror"
                                            wire:model="newPassword" autocomplete="off">
                                    </div>
                                    <div class="input-area">
                                        <label for="newPassword_confirmation" class="form-label">Confirm New
                                            Password</label>
                                        <input id="newPassword_confirmation" type="password"
                                            class="form-control @error('newPassword_confirmation') !border-danger-500 @enderror"
                                            autocomplete="off" wire:model="newPassword_confirmation">
                                    </div>
                                </div>
                                @error('newPassword')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="changeUserPassword" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="changeUserPassword">Change Password</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="changeUserPassword"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
