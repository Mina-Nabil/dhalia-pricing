<div>

    @if ($changes)
        <div class="app-header z-[999] bg-white dark:bg-slate-800 shadow dark:shadow-slate-700 save-section">
            <div class="flex justify-between items-center h-full  float-right">
                <div class="flex items-center md:space-x-4 space-x-2 xl:space-x-0 rtl:space-x-reverse vertical-box">
                    <x-primary-button wire:click.prevent="saveInfo" loadingFunction="saveInfo">Save</x-primary-button>
                </div>
                <!-- end nav tools -->
            </div>
        </div>
    @endif

    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">
        <div
            class="profiel-wrap px-[35px] pb-10 md:pt-[84px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0
                space-y-6 justify-between items-end relative z-[1]">
            <div
                class="bg-slate-900 dark:bg-slate-700 absolute left-0 top-0 md:h-1/2 h-[150px] w-full z-[-1] rounded-t-lg p-5">
                <div>
                    <div class="dropdown relative ">
                        <button
                            class="btn inline-flex justify-center btn-light items-center cursor-default relative !pr-14 btn-sm float-right"
                            type="button" id="lightsplitDropdownMenuButton" data-bs-toggle="dropdown"
                            aria-expanded="true">
                            {{ __('users.more_actions') }}
                            <span
                                class="cursor-pointer absolute ltr:border-l rtl:border-r border-slate-100 h-full ltr:right-0 rtl:left-0 px-2 flex
                                        items-center justify-center leading-none">
                                <iconify-icon class="leading-none text-xl" icon="ic:round-keyboard-arrow-down">
                                </iconify-icon>
                            </span>
                        </button>
                        <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                            data-popper-placement="bottom-start"
                            style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 50px);">
                            <li class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cusor-pointer"
                                wire:click="openChangePass">

                                Change Password</a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">

                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ $user->full_name }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400">
                            {{ __('users.' . $user->type) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div>
            <div class="card h-full">
                <header class="card-header flex justify-between">
                    <h4 class="card-title">{{ __('users.info') }}</h4>
                </header>
                <div class="card-body p-6">
                    <ul class="list space-y-8">
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="solar:user-broken"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Username
                                </div>
                                <input type="text" wire:model.live="username"
                                    class="form-control @error('username') !border-danger-500 @enderror">
                                @error('username')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="icon-park-outline:edit-name"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Name
                                </div>
                                <input type="text" class="form-control @error('name') !border-danger-500 @enderror"
                                    wire:model.live="name">
                                @error('name')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </li>


                    </ul>
                </div>
            </div>
        </div>


        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Change Password Modal -->
    @if ($changePasswordModal)
        <x-modal wire:model="changePasswordModal">
            <x-slot name="title">
                Change Password
            </x-slot>
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
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
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
