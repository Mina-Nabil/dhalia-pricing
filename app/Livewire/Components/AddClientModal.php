<?php

namespace App\Livewire\Components;

use App\Exceptions\ClientManagementException;
use App\Providers\ClientServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class AddClientModal extends Component
{
    use AlertFrontEnd;

    public $newClientModal = false;
    public $clientName = '';
    public $clientPhone = '';
    public $clientAddress = '';
    public $clientEmail = '';
    public $clientNotes = '';
    public $clientNameExists = false;
    public $proceedType = '';

    protected $clientService;

    protected $listeners = ['openNewClient'];

    protected function rules()
    {
        $rules = [];
        $this->checkNameExists();
        // Client validation rules
        if ($this->newClientModal) {
            $rules = array_merge($rules, [
                'clientName' => 'required|string|max:255',
                'clientPhone' => 'nullable|string|max:20',
                'clientAddress' => 'nullable|string|max:500',
                'clientEmail' => 'nullable|email|max:255',
                'clientNotes' => 'nullable|string|max:1000',
                'proceedType' => 'required_if:clientNameExists,true|string|in:create,update',
            ]);
        }

        return $rules;
    }

    public function openNewClient()
    {
        $this->clientService = app(ClientServiceProvider::class);
        $this->resetClientFormFields();
        $this->newClientModal = true;
    }

    public function checkNameExists()
    {
        $this->validate([
            'clientName' => 'required|string|max:255',
        ]);
        $this->clientService = app(ClientServiceProvider::class);

        $this->clientNameExists = $this->clientService->checkClientNameExists($this->clientName);
    }


    public function addNewClient()
    {
        $this->validate();
        $this->clientService = app(ClientServiceProvider::class);

        try {
            $client = $this->clientService->getClientByName($this->clientName);
            if ($client && $this->proceedType == 'update') {
                $this->clientService->updateClient(
                    $client,
                    $this->clientName,
                    $this->clientPhone,
                    $this->clientAddress,
                    $this->clientEmail,
                    $this->clientNotes
                );
            } else {
                $this->clientService->createClient(
                    $this->clientName,
                    $this->clientPhone,
                    $this->clientAddress,
                    $this->clientEmail,
                    $this->clientNotes
                );
            }

            $this->alert('success', 'Client created successfully');
            $this->resetClientFormFields();
            $this->dispatch('refreshClientList');
            $this->closeNewClientSec();
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    // Form reset methods
    private function resetClientFormFields()
    {
        $this->clientName = '';
        $this->clientPhone = '';
        $this->clientAddress = '';
        $this->clientEmail = '';
        $this->clientNotes = '';
        $this->proceedType = '';
        $this->clientNameExists = false;
        $this->resetValidation();
    }

    public function closeNewClientSec()
    {
        $this->newClientModal = false;
        $this->resetClientFormFields();
    }

    public function render()
    {
        return view('livewire.components.add-client-modal');
    }
}
