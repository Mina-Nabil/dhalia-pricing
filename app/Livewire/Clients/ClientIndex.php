<?php

namespace App\Livewire\Clients;

use App\Exceptions\ClientManagementException;
use App\Models\Clients\Client;
use App\Providers\ClientServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ClientIndex extends Component
{
    use WithPagination, AlertFrontEnd, WithFileUploads;

    protected $clientService;

    public $search = '';
    
    // File upload for import
    public $importFile;
    
    // Modal states
    public $newClientModal = false;
    public $deleteConfirmationModal = false;
    public $importModal = false;
    
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

    public function openImportModal()
    {
        $this->importModal = true;
    }

    public function closeImportModal()
    {
        $this->importModal = false;
        $this->importFile = null;
    }

    public function exportClients()
    {
        try {
            $this->authorize('view-client-any');
            
            $filePath = $this->clientService->exportClientsToExcel(
                search: $this->search ?: null,
                filename: 'clients_export_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
            
            $this->alert('success', 'Clients exported successfully');
            
            return response()->download($filePath)->deleteFileAfterSend(true);
            
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function importClients()
    {
        try {
            $this->authorize('create', Client::class);
            
            // Validate the uploaded file
            $this->validate([
                'importFile' => 'required|file|mimes:xlsx,xls|max:10240'
            ]);
            
            if (!$this->importFile) {
                $this->alert('error', 'Please select a file to import');
                return;
            }
            
            // Get the temporary file path
            $filePath = $this->importFile->getRealPath();
            
            // Import the clients
            $result = $this->clientService->importClientsFromExcel($filePath);
            
            // Generate success message with details
            $message = "Import completed successfully! ";
            $message .= "Created: {$result['imported']}, Updated: {$result['updated']}";
            
            if ($result['skipped'] > 0) {
                $message .= ", Skipped: {$result['skipped']}";
            }
            
            $this->alert('success', $message);
            
            // Show errors if any
            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->alert('warning', $error);
                }
            }
            
            // Reset the file input
            $this->importFile = null;
            
            // Refresh the list
            $this->refreshClientList();
            
            // Close the modal
            $this->closeImportModal();
            
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (ClientManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Import failed: ' . $e->getMessage());
        }
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
