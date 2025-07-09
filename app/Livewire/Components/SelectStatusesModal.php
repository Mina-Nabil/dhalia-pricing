<?php

namespace App\Livewire\Components;

use App\Models\Offers\Offer;
use Livewire\Component;

class SelectStatusesModal extends Component
{
    public $showModal = false;
    public $selectedStatuses = [];
    public $originalSelectedStatuses = [];

    protected $listeners = ['clearStatusesSelection'];

    public function mount($selectedStatuses = [])
    {
        $this->selectedStatuses = $selectedStatuses;
        $this->originalSelectedStatuses = $selectedStatuses;
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->selectedStatuses = $this->originalSelectedStatuses;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedStatuses = $this->originalSelectedStatuses;
    }

    public function toggleStatus($status)
    {
        if (in_array($status, $this->selectedStatuses)) {
            $this->selectedStatuses = array_values(array_filter($this->selectedStatuses, function($s) use ($status) {
                return $s !== $status;
            }));
        } else {
            $this->selectedStatuses[] = $status;
        }
    }

    public function selectAll()
    {
        $this->selectedStatuses = Offer::STATUSES;
    }

    public function clearStatusesSelection()
    {
        $this->selectedStatuses = [];
    }

    public function applySelection()
    {
        $this->originalSelectedStatuses = $this->selectedStatuses;
        $this->dispatch('statusesSelected', $this->selectedStatuses);
        $this->closeModal();
    }

    public function isSelected($status)
    {
        return in_array($status, $this->selectedStatuses);
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'draft' => 'bg-slate-900 text-white',
            'sent' => 'bg-info-500 text-white',
            'accepted' => 'bg-success-500 text-white',
            'rejected' => 'bg-danger-500 text-white',
            'cancelled' => 'bg-warning-500 text-white',
            'archived' => 'bg-slate-500 text-white',
            default => 'bg-slate-900 text-white'
        };
    }

    public function render()
    {
        $statuses = Offer::STATUSES;
        
        return view('livewire.components.select-statuses-modal', [
            'statuses' => $statuses,
        ]);
    }
} 