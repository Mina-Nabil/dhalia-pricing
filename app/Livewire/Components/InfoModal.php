<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class InfoModal extends Component
{
    public $isInfoModalOpen = false;
    public $message = '';
    public $color;

    protected $listeners = ['showInfoModal'];

    #[On('showInfoModal')]
    public function showInfoModal($message, $color)
    {
        $this->message = $message;
        $this->color = $color;
        $this->isInfoModalOpen = true;
    }

    public function closeModal()
    {
        $this->isInfoModalOpen = false;
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.components.info-modal');
    }
}
