<?php

namespace App\Livewire\Settings;

use App\Exceptions\PackingManagementException;
use App\Models\Packing;
use App\Providers\PackingServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class PackingsIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    protected $packingService;

    public $search = '';
    public $setPackingSec = false;
    public $editMode = false;
    public $selectedPacking;

    // Form properties
    public $name = '';
    public $cost = '';

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deletePacking'];

    public function __construct()
    {
        $this->authorize('viewAny', Packing::class);
        $this->packingService = app(PackingServiceProvider::class);
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ];

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openNewPackingSec()
    {
        $this->resetFormFields();
        $this->editMode = false;
        $this->setPackingSec = true;
    }

    public function updateThisPacking($packingId)
    {
        try {
            $packing = $this->packingService->getPacking($packingId);
            
            $this->selectedPacking = $packing;
            $this->name = $packing->name;
            $this->cost = $packing->cost;
            $this->editMode = true;
            $this->setPackingSec = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to load packing data');
        }
    }

    public function addNewPacking()
    {
        $this->validate();

        try {
            $packingService = app(PackingServiceProvider::class);
            
            if ($this->editMode) {
                $packingService->updatePacking($this->selectedPacking, $this->name, $this->cost);
                $this->alert('success', 'Packing updated successfully');
            } else {
                $packingService->createPacking($this->name, $this->cost);
                $this->alert('success', 'Packing created successfully');
            }

            $this->resetFormFields();
            $this->setPackingSec = false;
        } catch (PackingManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function togglePackingStatus($packingId)
    {
        try {
            $packing = $this->packingService->getPacking($packingId);
            $newStatus = !$packing->is_active;
            $this->packingService->setPackingStatus($packing, $newStatus);
            
            $statusText = $newStatus ? 'activated' : 'deactivated';
            $this->alert('success', "Packing {$statusText} successfully");
        } catch (PackingManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deletePacking($packingId)
    {
        try {
            $packing = $this->packingService->getPacking($packingId);
            $this->packingService->deletePacking($packing);
            $this->alert('success', 'Packing deleted successfully');
        } catch (PackingManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    private function resetFormFields()
    {
        $this->name = '';
        $this->cost = '';
        $this->selectedPacking = null;
        $this->resetValidation();
    }

    public function render()
    {
        $packings = $this->packingService->getPackings($this->search);

        return view('livewire.settings.packings-index', [
            'packings' => $packings
        ]);
    }
}
