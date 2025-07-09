<?php

namespace App\Livewire\Components;

use App\Providers\UserServiceProvider;
use Livewire\Component;
use Livewire\WithPagination;

class SelectUsersModal extends Component
{
    use WithPagination;

    public $showModal = false;
    public $search = '';
    public $selectedUserIds = [];
    public $selectedUserNames = [];
    public $originalSelectedUserIds = [];

    protected $userService;
    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['clearUsersSelection'];

    public function boot()
    {
        $this->userService = app(UserServiceProvider::class);
    }

    public function mount($selectedUserIds = [])
    {
        $this->selectedUserIds = $selectedUserIds;
        $this->originalSelectedUserIds = $selectedUserIds;
        $this->selectedUserNames = $this->userService->getUsersByIds($this->selectedUserIds)->pluck('name')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->selectedUserIds = $this->originalSelectedUserIds;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUserIds = $this->originalSelectedUserIds;
        $this->search = '';
        $this->resetPage();
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUserIds)) {
            $this->selectedUserIds = array_values(array_filter($this->selectedUserIds, function($id) use ($userId) {
                return $id != $userId;
            }));
        } else {
            $this->selectedUserIds[] = $userId;
        }
    }

    public function selectAll()
    {
        $users = $this->userService->getUsers(paginate: null, search: $this->search);
        foreach ($users as $user) {
            if (!in_array($user->id, $this->selectedUserIds)) {
                $this->selectedUserIds[] = $user->id;
            }
        }
    }

    public function clearUsersSelection()
    {
        $this->selectedUserIds = [];
        $this->selectedUserNames = [];
    }

    public function applySelection()
    {
        $this->originalSelectedUserIds = $this->selectedUserIds;
        $this->selectedUserNames = $this->userService->getUsersByIds($this->selectedUserIds)->pluck('name')->toArray();
        $this->dispatch('usersSelected', $this->selectedUserIds);
        $this->closeModal();
    }

    public function isSelected($userId)
    {
        return in_array($userId, $this->selectedUserIds);
    }

    public function render()
    {
        $users = $this->userService->getUsers(paginate: 10, search: $this->search);
        
        return view('livewire.components.select-users-modal', [
            'users' => $users,
        ]);
    }
}
