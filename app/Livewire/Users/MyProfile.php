<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class MyProfile extends Component
{
    public $name;
    public $email;
    public $title_job;
    public $user_type;
    public $password;
    public $password_confirmation;

    public function mount(): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->title_job = $user->title_job;
        $this->user_type = $user->user_type;
    }

    protected function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nombre completo',
        'email' => 'Correo electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmación de contraseña',
    ];

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $user->name = $this->name;
        $user->email = $this->email;

        if (!blank($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        LivewireAlert::title('Perfil actualizado con éxito.')
            ->success()
            ->show();
    }

    public function render()
    {
        return view('livewire.users.myprofile');
    }
}
