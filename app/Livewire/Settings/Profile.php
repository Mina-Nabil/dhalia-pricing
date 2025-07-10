<?php

namespace App\Livewire\Settings;

use App\Exceptions\AppException;
use App\Exceptions\UserManagementException;
use App\Models\User;
use App\Providers\UserServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads, AlertFrontEnd; 

    /** @var UserServiceProvider */
    protected $userService;

    public $user;
    public $username;
    public $name;
    public $role;

    public $changes = false;

    // Change password modal properties
    public $changePasswordModal = false;
    public $newPassword;
    public $newPassword_confirmation;



    public function mount()
    {
        $this->userService = app(UserServiceProvider::class);
        $this->user = $this->userService->getUser(Auth::id());
        $this->username = $this->user->username;
        $this->name = $this->user->name;
        $this->role = $this->user->role; 
    }

    public function updatingUsername()
    {
        $this->changes = true;
    }

    public function updatingName()
    {
        $this->changes = true;
    }

    public function updatingUserImage()
    {
        $this->changes = true;
    }

    // Open change password modal
    public function openChangePass()
    {
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
            $this->userService->updateUserPassword($this->user, $this->newPassword);
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

    public function saveInfo()
    {
        try {
           $this->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $this->user->id,
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', User::ROLES),
           ]);
            
            
            $this->userService->updateUser($this->user, $this->username, $this->name, $this->role);

            $this->changes = false;
            $this->alertSuccess('Profile updated successfully');
        } catch (UserManagementException $e) {
            $this->alertError($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alertError('You are not authorized to update this user');
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update profile');
        }
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
