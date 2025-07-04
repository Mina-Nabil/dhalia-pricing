<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class ConfirmationModal extends Component
{
    public $isOpen = false;
    public $message = '';
    public $callback;
    public $color;
    public $callbackParams = [];

    protected $listeners = ['showConfirmation'];

    #[On('showConfirmation')]
    public function showConfirmation($message, $color ,$callback, ...$params)
    {
        $this->message = $message;
        $this->callback = $callback;
        $this->callbackParams = $params;
        $this->isOpen = true;
        $this->color = $color;
    }

    public function confirm()
    {
        $this->dispatch($this->callback, ...$this->callbackParams);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->message = '';
        $this->callback = '';
    }

    public function render()
    {
        return view('livewire.components.confirmation-modal');
    }
}
