<?php

namespace App\Livewire\Clients;

use App\Exceptions\ClientManagementException;
use App\Models\Clients\Client;
use App\Providers\ClientServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class ClientShow extends Component
{
    use AlertFrontEnd;

    protected $clientService;

    public $client;

    // Client form properties
    public $clientName = '';
    public $clientPhone = '';
    public $clientAddress = '';
    public $clientEmail = '';
    public $clientNotes = '';

    // Edit mode flag
    public $editMode = false;

    public function boot()
    {
        $this->clientService = app(ClientServiceProvider::class);
    }

    public function mount($client_id)
    {
        $this->client = $this->clientService->getClient($client_id);
        $this->loadClientData();
    }

    public function loadClientData()
    {
        $this->clientName = $this->client->name;
        $this->clientPhone = $this->client->phone;
        $this->clientAddress = $this->client->address;
        $this->clientEmail = $this->client->email;
        $this->clientNotes = $this->client->notes;
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
            'clientPhone' => 'nullable|string|max:20',
            'clientAddress' => 'nullable|string|max:500',
            'clientEmail' => 'nullable|email|max:255',
            'clientNotes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->clientService->updateClient(
                $this->client,
                $this->clientName,
                $this->clientPhone,
                $this->clientAddress,
                $this->clientEmail,
                $this->clientNotes
            );

            // Refresh the client data
            $this->client = $this->clientService->getClient($this->client->id);
            $this->loadClientData();

            $this->editMode = false;
            $this->alert('success', 'Client updated successfully');
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->loadClientData();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.clients.client-show');
    }
}
