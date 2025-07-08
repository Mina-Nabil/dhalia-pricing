<div>
    <div class="flex justify-between flex-wrap items-center mb-6">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Client Details
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <a href="{{ route('clients.index') }}"
                class="btn inline-flex justify-center btn-outline-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:arrow-left"></iconify-icon>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Two Cards Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Client Information Card -->
        <div class="lg:col-span-2">
            <x-card title="Client Information">
                <x-slot name="tools">
                    @if (!$editMode)
                        @can('update-client', $client)
                            <button wire:click="toggleEditMode"
                                class="btn btn-sm text-white hover:bg-white hover:text-slate-800">
                                <iconify-icon class="text-lg" icon="heroicons:pencil-square" />
                            </button>
                        @endcan
                    @endif
                </x-slot>

                @if ($editMode)
                    <form wire:submit.prevent="updateClient">
                        <div class="space-y-6">
                            <x-text-input wire:model="clientName" label="Client Name"
                                errorMessage="{{ $errors->first('clientName') }}" />

                            <x-text-input wire:model="clientPhone" label="Phone Number"
                                errorMessage="{{ $errors->first('clientPhone') }}" 
                                placeholder="e.g., +1 234 567 8900" />

                            <x-text-input wire:model="clientEmail" label="Email Address" type="email"
                                errorMessage="{{ $errors->first('clientEmail') }}" 
                                placeholder="e.g., john@example.com" />

                            <x-textarea wire:model="clientAddress" label="Address" rows="3"
                                placeholder="Enter client address..." 
                                errorMessage="{{ $errors->first('clientAddress') }}" />

                            <x-textarea wire:model="clientNotes" label="Notes" rows="3"
                                placeholder="Any additional notes about the client..." 
                                errorMessage="{{ $errors->first('clientNotes') }}" />
                        </div>

                        <div class="flex space-x-3 mt-8">
                            <x-primary-button type="submit" loadingFunction="updateClient">
                                Update Client
                            </x-primary-button>
                            <x-secondary-button wire:click="cancelEdit" type="button">
                                Cancel
                            </x-secondary-button>
                        </div>
                    </form>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Client
                                Name</label>
                            <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">{{ $client->name }}</p>
                        </div>
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Phone
                                Number</label>
                            <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">
                                {{ $client->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Email
                                Address</label>
                            <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">
                                {{ $client->email ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Client
                                ID</label>
                            <p class="text-slate-500 dark:text-slate-400 text-lg mt-1">#{{ $client->id }}</p>
                        </div>
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Created
                                By</label>
                            <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">
                                {{ $client->createdBy->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Created
                                Date</label>
                            <p class="text-slate-500 dark:text-slate-400 text-lg mt-1">
                                {{ $client->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    @if ($client->address)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Address</label>
                            <p class="text-slate-600 dark:text-slate-400 mt-2 text-base">
                                {{ $client->address }}</p>
                        </div>
                    @endif

                    @if ($client->notes)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <label
                                class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Notes</label>
                            <p class="text-slate-600 dark:text-slate-400 mt-2 text-base">
                                {{ $client->notes }}</p>
                        </div>
                    @endif

                    @if (!$editMode)
                        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <div class="text-center py-6">
                                <div class="mb-4">
                                    <iconify-icon class="text-4xl text-slate-400 dark:text-slate-600"
                                        icon="heroicons:user-circle"></iconify-icon>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 mb-4 text-base">
                                    @can('update-client', $client)
                                        Click the "Edit" button to update this client's information.
                                    @else
                                        You don't have permission to edit this client.
                                    @endcan
                                </p>
                                <div class="text-sm text-slate-400 dark:text-slate-500">
                                    <p>Current values are displayed above.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </x-card>
        </div>

        <!-- Right Column: Users Table Card -->
        <div class="lg:col-span-1">
            <x-card title="Associated Users">
                <div class="space-y-4">
                    @if ($client->users->count() > 0)
                        <div class="space-y-3">
                            <p
                                class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-4 uppercase tracking-wide">
                                Users with access to this client:</p>
                            @foreach ($client->users as $user)
                                <div
                                    class="flex justify-between items-center py-3 px-4 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-slate-900 dark:text-white font-medium">{{ $user->name }}</p>
                                            <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $user->username }}</p>
                                            <p class="text-slate-500 dark:text-slate-400 text-sm">
                                                {{ $user->pivot->offers_count }} offers
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if ($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($user->role === 'manager') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg font-bold text-slate-900 dark:text-white">Total Users:</span>
                                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $client->users->count() }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <iconify-icon class="text-4xl text-slate-400 dark:text-slate-600"
                                    icon="heroicons:users"></iconify-icon>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">
                                No users are currently associated with this client.
                            </p>
                            <p class="text-slate-400 dark:text-slate-500 text-xs mt-2">
                                Users can be assigned to clients to give them access.
                            </p>
                        </div>
                    @endif

                    @if ($client->createdBy)
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                            <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3 uppercase tracking-wide">
                                Created By:</p>
                            <div class="flex items-center space-x-3 py-2 px-3 bg-slate-100 dark:bg-slate-700 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-slate-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ strtoupper(substr($client->createdBy->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-slate-900 dark:text-white font-medium">{{ $client->createdBy->name }}
                                    </p>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm">
                                        {{ $client->createdBy->username }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
