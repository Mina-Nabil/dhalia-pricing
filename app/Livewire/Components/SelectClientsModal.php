<?php

namespace App\Livewire\Components;

use App\Providers\ClientServiceProvider;
use Livewire\Component;
use Livewire\WithPagination;

class SelectClientsModal extends Component
{
    use WithPagination;

    public $showModal = false;
    public $search = '';
    public $selectedClientIds = [];
    public $originalSelectedClientIds = [];
    public $selectedClientNames = [];
    public $mode = 'multiple';

    protected $clientService;
    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['clearClientsSelection'];

    public function boot()
    {
        $this->clientService = app(ClientServiceProvider::class);
    }

    public function mount($selectedClientIds = [])
    {
        $this->selectedClientIds = $selectedClientIds;
        $this->originalSelectedClientIds = $selectedClientIds;
        $this->selectedClientNames = $this->clientService->getClientsByIds($this->selectedClientIds)->pluck('name')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->selectedClientIds = $this->originalSelectedClientIds;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedClientIds = $this->originalSelectedClientIds;
        $this->search = '';
        $this->resetPage();
    }

    public function toggleClient($clientId)
    {
        if ($this->mode == 'single') {
            $this->selectedClientIds = [];
        }

        if (in_array($clientId, $this->selectedClientIds)) {
            $this->selectedClientIds = array_values(array_filter($this->selectedClientIds, function ($id) use ($clientId) {
                return $id != $clientId;
            }));
        } else {
            $this->selectedClientIds[] = $clientId;
        }
    }

    public function selectAll()
    {
        if ($this->mode == 'single') {
            $this->selectedClientIds = [];
        }

        $clients = $this->clientService->getClients($this->search, paginate: false);
        foreach ($clients as $client) {
            if (!in_array($client->id, $this->selectedClientIds)) {
                $this->selectedClientIds[] = $client->id;
            }
        }
    }

    public function clearClientsSelection()
    {
        $this->selectedClientIds = [];
        $this->selectedClientNames = [];
    }

    public function applySelection()
    {
        $this->originalSelectedClientIds = $this->selectedClientIds;
        $this->selectedClientNames = $this->clientService->getClientsByIds($this->selectedClientIds)->pluck('name')->toArray();
        if ($this->mode == 'single' && count($this->selectedClientIds) === 1) {
            $this->dispatch('clientsSelected', $this->selectedClientIds[0]);
        } else {
            $this->dispatch('clientsSelected', $this->selectedClientIds);
        }
        $this->closeModal();
    }

    public function isSelected($clientId)
    {
        return in_array($clientId, $this->selectedClientIds);
    }

    public function render()
    {
        $clients = $this->clientService->getClients($this->search, paginate: 10);

        return view('livewire.components.select-clients-modal', [
            'clients' => $clients,
        ]);
    }
}
