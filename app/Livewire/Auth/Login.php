<?php

namespace App\Livewire\Auth;

use App\Providers\AuthServiceProvider;
use App\Traits\AlertFrontEnd;
use Illuminate\Validation\UnauthorizedException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('components.layouts.base')]
class Login extends Component
{
    use AlertFrontEnd;

    private $authService;

    public $username;
    public $password;

    public function mount()
    {
        $this->authService = app(AuthServiceProvider::class);
    }

    public function checkUser()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $this->authService->login($this->username, $this->password);
            $this->alertSuccess('Login successful');
            return redirect('/');
        } catch (NotFoundHttpException $e) {
            $this->alertError($e->getMessage());
        } catch (UnauthorizedException $e) {
            $this->alertError($e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.auth.login');
    }
}
