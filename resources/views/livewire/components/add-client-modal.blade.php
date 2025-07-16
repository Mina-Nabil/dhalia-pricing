<div>
    @if ($newClientModal)
        <x-modal wire:model="newClientModal">
            <x-slot name="title">
                Create New Client
            </x-slot>
            <x-slot name="content">
                <div class="space-y-4">
                    <x-text-input wire:model="clientName" label="Client Name *"
                        wire:change.debounce.100ms="checkNameExists" errorMessage="{{ $errors->first('clientName') }}" />

                    <x-text-input wire:model="clientCode" label="Client Code (Optional)"
                        errorMessage="{{ $errors->first('clientCode') }}" placeholder="e.g., CLT001" />

                    @if($clientNameExists)
                        <div class="text-danger-500">
                            <p>Client name already exists, how to proceed?</p>
                        </div>

                        <div class="radio-group">
                            <label for="existingClient">
                                <input type="radio" wire:model="proceedType" value="update" id="existingClient">
                                Update Existing Client
                            </label>
                        </div>

                        <div class="radio-group">
                            <label for="newClient">
                                <input type="radio" wire:model="proceedType" value="create" id="newClient">
                                Create New Client
                            </label>
                        </div>
                    @endif

                    <x-text-input wire:model="clientPhone" label="Phone Number"
                        errorMessage="{{ $errors->first('clientPhone') }}" placeholder="e.g., +1 234 567 8900" />

                    <x-text-input wire:model="clientEmail" label="Email Address" type="email"
                        errorMessage="{{ $errors->first('clientEmail') }}" placeholder="e.g., john@example.com" />

                    <x-textarea wire:model="clientAddress" label="Address" rows="3"
                        placeholder="Enter client address..." errorMessage="{{ $errors->first('clientAddress') }}" />

                    <x-textarea wire:model="clientNotes" label="Notes (Optional)" rows="3"
                        placeholder="Any additional notes about the client..."
                        errorMessage="{{ $errors->first('clientNotes') }}" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeNewClientSec">
                    Cancel
                </x-secondary-button>
                <x-primary-button wire:click.prevent="addNewClient" loadingFunction="addNewClient">
                    Create Client
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif
</div>
