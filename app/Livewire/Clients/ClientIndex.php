<?php

namespace App\Livewire\Clients;

use App\Exceptions\ClientManagementException;
use App\Models\Clients\Client;
use App\Providers\ClientServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class ClientIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    protected $clientService;

    public $search = '';
    
    // Modal states
    public $newClientModal = false;
    public $deleteConfirmationModal = false;
    
    // Selected items
    public $selectedClient;
    
    // Client form properties
    public $clientName = '';
    public $clientPhone = '';
    public $clientAddress = '';
    public $clientEmail = '';
    public $clientNotes = '';
    
    // Delete confirmation
    public $itemIdToDelete = null;

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deleteClient', 'refreshClientList'];

    public function boot()
    {
        $this->clientService = app(ClientServiceProvider::class);
    }

    public function mount()
    {
        $this->authorize('view-client-any');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function goToClientShow($clientId)
    {
        return redirect()->route('clients.show', $clientId);
    }

    public function confirmDeleteClient($clientId)
    {
        $this->itemIdToDelete = $clientId;
        $this->deleteConfirmationModal = true;
    }

    public function deleteClient($clientId)
    {
        try {
            $client = $this->clientService->getClient($clientId);
            $this->clientService->deleteClient($client);
            $this->alert('success', 'Client deleted successfully');
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    // Modal control methods
    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemIdToDelete = null;
    }

    public function confirmDelete()
    {
        $this->deleteClient($this->itemIdToDelete);
        $this->closeDeleteConfirmationModal();
    }

    public function refreshClientList()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clients = $this->clientService->getClients($this->search, 10);

        return view('livewire.clients.client-index', [
            'clients' => $clients,
        ]);
    }
}
