<div>
    <!-- Modal Trigger Button -->
    @if ($iconButton)
        <button wire:click="openModal" class="btn inline-flex justify-center items-center text-white w-full">
            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:user-plus"></iconify-icon>
        </button>
    @else
        <button wire:click="openModal" class="btn inline-flex justify-center items-center btn-outline-primary w-full">
            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:user-group"></iconify-icon>
            Select Users
            @if (count($selectedUserIds) > 0)
                <span class="ml-2 badge bg-primary-500 text-white">{{ count($selectedUserIds) }}</span>
            @endif
        </button>

        <div class="flex space-x-2 mt-2">
            @foreach ($selectedUserNames as $userName)
                <span class="badge bg-primary-500 text-white">{{ $userName }}</span>
            @endforeach
        </div>
    @endif

    <!-- Modal -->
    <x-modal wire:model="showModal" maxWidth="4xl">
        <x-slot name="title">
            Select Users
        </x-slot>

        <x-slot name="content">
            <!-- Search Bar -->
            <div class="mb-4">
                <label for="userSearch" class="form-label">Search Users</label>
                <div class="relative">
                    <input type="text" id="userSearch" class="form-control pl-10"
                        placeholder="Search by name or username..." wire:model.live.debounce.300ms="search">

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 mb-4">
                <button wire:click="selectAll" class="btn btn-sm btn-outline-primary">
                    Select All
                </button>
                <button wire:click="clearUsersSelection" class="btn btn-sm btn-outline-secondary">
                    Deselect All
                </button>
                <div class="flex-1"></div>
                <span class="text-sm text-slate-600">
                    {{ count($selectedUserIds) }} selected
                </span>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto max-h-96 -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-200 dark:bg-slate-700 sticky top-0">
                            <tr>
                                <th scope="col" class="table-th w-16 whitespace-nowrap">Select</th>
                                <th scope="col" class="table-th whitespace-nowrap">Name</th>
                                <th scope="col" class="table-th whitespace-nowrap">Username</th>
                                <th scope="col" class="table-th whitespace-nowrap">Role</th>
                                <th scope="col" class="table-th whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <td class="table-td text-center whitespace-nowrap bg-white dark:bg-slate-800">
                                        <button wire:click="toggleUser({{ $user->id }})"
                                            class="btn btn-sm {{ $this->isSelected($user->id) ? 'btn-primary' : 'btn-outline-secondary' }}">
                                            @if ($this->isSelected($user->id))
                                                <iconify-icon icon="heroicons:check"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="table-td font-medium whitespace-nowrap bg-white dark:bg-slate-800">
                                        {{ $user->name }}</td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">
                                        {{ $user->username }}</td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">
                                        <span
                                            class="badge {{ $user->role === 'admin' ? 'bg-purple-500' : 'bg-blue-500' }} text-white">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="table-td whitespace-nowrap bg-white dark:bg-slate-800">
                                        <span
                                            class="badge {{ $user->is_active ? 'bg-success-500' : 'bg-danger-500' }} text-white">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="table-td text-center text-slate-500 whitespace-nowrap bg-white dark:bg-slate-800">
                                        @if ($search)
                                            No users found matching "{{ $search }}"
                                        @else
                                            No users available
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="mt-4">
                    {{ $users->links('vendor.livewire.simple-bootstrap') }}
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <span class="text-sm text-slate-600">
                    {{ count($selectedUserIds) }} user(s) selected
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
