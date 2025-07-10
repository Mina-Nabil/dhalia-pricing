<?php

namespace App\Livewire\Settings;

use App\Exceptions\UserManagementException;
use App\Models\User;
use App\Providers\UserServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination, AlertFrontEnd, WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    /** @var UserServiceProvider */
    protected $userService;

    public $search;

    public $setUserSec;
    public $loadedUser;
    public $username;
    public $name;
    public $role = User::ROLE_USER;
    public $password;
    public $password_confirmation;
    public $user;

    // Change password modal properties
    public $changePasswordModal = false;
    public $selectedUserId;
    public $newPassword;
    public $newPassword_confirmation;

    public function boot()
    {
        $this->userService = app(UserServiceProvider::class);
    }

    public function mount()
    {
        $this->authorize('viewAny', User::class);
    }

    public function updateThisUser($id)
    {
        $this->setUserSec = $id;
        $this->loadedUser = User::find($id);
        $this->user = $this->loadedUser;
        $this->username = $this->loadedUser->username;
        $this->name = $this->loadedUser->name;
        $this->role = $this->loadedUser->role;
    }

    public function toggleUserStatus($id)
    {
        try {
            $this->userService->setUserStatus(User::find($id), $this->user->is_active ? 0 : 1);
            $this->alertSuccess('User status updated successfully');
        } catch (UserManagementException $e) {
            $this->alertError($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alertError('You are not authorized to update this user');
        } catch (Exception $e) {
            report($e);
            $this->alertError('Internal server error');
        }
    }

    public function closeSetUserSec()
    {
        $this->reset(['setUserSec', 'username', 'name', 'role', 'password', 'password_confirmation']);
    }

    // Open change password modal
    public function openChangePasswordModal($id)
    {
        $this->selectedUserId = $id;
        $this->changePasswordModal = true;
        $this->resetPasswordFields();
    }

    // Close change password modal
    public function closeChangePasswordModal()
    {
        $this->changePasswordModal = false;
        $this->resetPasswordFields();
    }

    // Reset password fields
    private function resetPasswordFields()
    {
        $this->reset(['newPassword', 'newPassword_confirmation']);
    }

    // Change password
    public function changeUserPassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|confirmed',
        ], [
            'newPassword.required' => 'Password is required',
            'newPassword.min' => 'Password must be at least 8 characters',
            'newPassword.confirmed' => 'Passwords do not match',
        ]);

        try {
            $this->userService->updateUserPassword(User::find($this->selectedUserId), $this->newPassword);
            $this->closeChangePasswordModal();
            $this->alertSuccess('Password changed successfully');
        } catch (UserManagementException $e) {
            $this->alertError($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alertError('You are not authorized to update this user');
        } catch (Exception $e) {
            report($e);
            $this->alertError('Internal server error');
        }
    }

    public function EditUser()
    {
        $currentUserId = $this->setUserSec;
        $this->validate([
            'username' => [
                'required',
                'max:255',
                'unique:users,username,' . $currentUserId,
            ],
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', User::ROLES),
        ]);

        try {
            $this->userService->updateUser(User::find($currentUserId), $this->username, $this->name, $this->type);
            $this->closeSetUserSec();
            $this->alertSuccess('User updated successfuly!');
        } catch (UserManagementException $e) {
            $this->alertError($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alertError('You are not authorized to update this user');
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update user');
        }
    }

    public function openNewUserSec()
    {
        $this->setUserSec = true;
    }


    public function addNewUser()
    {
        $this->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', User::ROLES),
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->userService->createUser($this->username, $this->name, $this->password, $this->role);
            $this->alertSuccess('User added successfuly!');
        } catch (UserManagementException $e) {
            $this->alertError($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alertError('You are not authorized to create a user');
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to create user');
        }
    }

    public function render()
    {
        $users = $this->userService->getUsers(paginate: 15, search: $this->search);

        return view('livewire.settings.users-index', [
            'users' => $users,
            'TYPES' => User::ROLES,
            'usersIndex' => 'active'
        ]);
    }
}
