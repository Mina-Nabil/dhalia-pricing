<?php

namespace App\Livewire\Settings;

use App\Exceptions\SpecManagementException;
use App\Models\Spec;
use App\Providers\SpecServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class SpecsIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    protected $specService;

    public $search = '';
    public $setSpecSec = false;
    public $editMode = false;
    public $selectedSpec;

    // Form properties
    public $name = '';

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deleteSpec'];

    public function __construct()
    {
        $this->authorize('viewAny', Spec::class);
        $this->specService = app(SpecServiceProvider::class);
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openNewSpecSec()
    {
        $this->resetFormFields();
        $this->editMode = false;
        $this->setSpecSec = true;
    }

    public function updateThisSpec($specId)
    {
        try {
            $spec = $this->specService->getSpec($specId);
            
            $this->selectedSpec = $spec;
            $this->name = $spec->name;
            $this->editMode = true;
            $this->setSpecSec = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to load spec data');
        }
    }

    public function addNewSpec()
    {
        $this->validate();

        try {
            $specService = app(SpecServiceProvider::class);
            
            if ($this->editMode) {
                $specService->updateSpec($this->selectedSpec, $this->name);
                $this->alert('success', 'Spec updated successfully');
            } else {
                $specService->createSpec($this->name);
                $this->alert('success', 'Spec created successfully');
            }

            $this->resetFormFields();
            $this->setSpecSec = false;
        } catch (SpecManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deleteSpec($specId)
    {
        try {
            $spec = $this->specService->getSpec($specId);
            $this->specService->deleteSpec($spec);
            $this->alert('success', 'Spec deleted successfully');
        } catch (SpecManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    private function resetFormFields()
    {
        $this->name = '';
        $this->selectedSpec = null;
        $this->resetValidation();
    }

    public function render()
    {
        $specs = $this->specService->getSpecs($this->search);

        return view('livewire.settings.specs-index', [
            'specs' => $specs
        ]);
    }
}
