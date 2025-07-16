<?php

namespace App\Livewire\Clients;

use App\Exceptions\ClientManagementException;
use App\Models\Clients\Client;
use App\Providers\ClientServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

class ClientShow extends Component
{
    use AlertFrontEnd;

    protected $clientService;

    public $client;

    // Client form properties
    public $clientName = '';
    public $clientCode = '';
    public $clientPhone = '';
    public $clientAddress = '';
    public $clientEmail = '';
    public $clientNotes = '';
    public $clientCountryName = '';

    // Edit mode flag
    public $editMode = false;

    // User management properties
    public $selectedUserIds = [];
    public $userToRemove = null;
    public $removeUserConfirmationModal = false;

    protected $listeners = ['usersSelected'];

    public function boot()
    {
        $this->clientService = app(ClientServiceProvider::class);
    }

    public function mount($client_id)
    {
        $this->client = $this->clientService->getClient($client_id);
        $this->loadClientData();
        $this->loadCurrentUserIds();
    }

    public function loadClientData()
    {
        $this->clientName = $this->client->name;
        $this->clientCode = $this->client->code;
        $this->clientPhone = $this->client->phone;
        $this->clientAddress = $this->client->address;
        $this->clientEmail = $this->client->email;
        $this->clientNotes = $this->client->notes;
        $this->clientCountryName = $this->client->country_name;
    }

    public function loadCurrentUserIds()
    {
        $this->selectedUserIds = $this->client->users->pluck('id')->toArray();
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;

        if (!$this->editMode) {
            // Reset form data when exiting edit mode
            $this->loadClientData();
            $this->resetValidation();
        }
    }

    public function updateClient()
    {
        $this->validate([
            'clientName' => 'required|string|max:255',
            'clientCode' => 'nullable|string|max:50',
            'clientPhone' => 'nullable|string|max:20',
            'clientAddress' => 'nullable|string|max:500',
            'clientEmail' => 'nullable|email|max:255',
            'clientNotes' => 'nullable|string|max:1000',
            'clientCountryName' => 'nullable|string|max:255',
        ]);

        try {
            $this->clientService->updateClient(
                $this->client,
                $this->clientName,
                $this->clientCode,
                $this->clientPhone,
                $this->clientAddress,
                $this->clientEmail,
                $this->clientNotes,
                $this->clientCountryName
            );

            // Refresh the client data
            $this->client = $this->clientService->getClient($this->client->id);
            $this->loadClientData();

            $this->editMode = false;
            $this->alert('success', 'Client updated successfully');
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->loadClientData();
        $this->resetValidation();
    }

    public function usersSelected($userIds)
    {
        try {
            $this->clientService->addUsersToClient($this->client, $userIds);

            // Refresh the client data
            $this->client = $this->clientService->getClient($this->client->id);
            $this->loadCurrentUserIds();

            $this->alert('success', 'Users updated successfully');
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function confirmRemoveUser($userId)
    {
        $this->userToRemove = $userId;
        $this->removeUserConfirmationModal = true;
    }

    public function removeUser()
    {
        if ($this->userToRemove) {
            try {
                $this->clientService->removeUserFromClient($this->client, $this->userToRemove);

                // Refresh the client data
                $this->client = $this->clientService->getClient($this->client->id);
                $this->loadCurrentUserIds();

                $this->alert('success', 'User removed successfully');
            } catch (ClientManagementException $e) {
                $this->alert('error', $e->getMessage());
            } catch (AuthorizationException $e) {
                $this->alert('error', $e->getMessage());
            } catch (Exception $e) {
                report($e);
                $this->alert('error', 'An unexpected error occurred');
            }
        }

        $this->closeRemoveUserConfirmationModal();
    }

    public function closeRemoveUserConfirmationModal()
    {
        $this->removeUserConfirmationModal = false;
        $this->userToRemove = null;
    }

    public function render()
    {
        return view('livewire.clients.client-show');
    }
}
