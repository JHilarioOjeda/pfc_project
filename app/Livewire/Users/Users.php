<?php

namespace App\Livewire\Users;

use Livewire\Component;
use DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use RealRashid\SweetAlert\Facades\Alert;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Auth;
use Throwable;
use Log;
use App\Models\User;

class Users extends Component
{
    use WithFileUploads;
    use WithPagination;
 
    public $search = '', $modalceusers = false, $userselected = null;

    public $name, $email, $title_job, $user_type, $active, $password;

    public $sign_photo_path, $old_sign_photo_path;
    public $img = '/imgs/user.png';
    public $imgflag = false;

    protected $listeners = ['changeUserStatus'];

    protected $rules = [];
    protected $validationAttributes  = [
        'name' => 'Nombre completo',
        'title_job' => 'Puesto',
        'email' => 'Correo electrónico',
        'user_type' => 'Tipo de usuario',
        'password' => 'Contraseña',
    ];

    public function mount(){
    }

    public function render()
    {
        $users = User::where('name', 'LIKE', '%' . $this->search . '%')
            ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ->orWhere('title_job', 'LIKE', '%' . $this->search . '%')
            ->orderBy('name', 'ASC')
            ->get();

        return view('livewire.users.users')->with('users', $users);
    }

    public function scmodalusers($iduser){
        if ($this->modalceusers == true) {
            $this->modalceusers = false;
            $this->reset(['name', 'email', 'title_job', 'user_type', 'active', 'password', 'modalceusers', 'userselected']);
        } else {
            $this->modalceusers = true;
            $this->userselected = User::where('id', $iduser)->first();
            if($this->userselected != null){
                $this->name = $this->userselected->name;
                $this->email = $this->userselected->email;
                $this->title_job = $this->userselected->title_job;
                $this->user_type = $this->userselected->user_type;
                $this->active = $this->userselected->active;
            }
        }
    }

    public function createUpdateUser(){
        // Reiniciamos reglas en cada petición
        $this->rules = [
            'name' => 'required|max:255',
            'user_type' => 'required',
        ];

        // Regla unique diferente para crear vs actualizar
        if ($this->userselected !== null) {
            // Ignora el id del usuario actual al validar el email
            $this->rules['email'] = 'required|email|unique:users,email,' . $this->userselected->id;
        } else {
            $this->rules['email'] = 'required|email|unique:users,email';
        }

        if (!blank($this->password)) {
            $this->rules +=[
                'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            ];
        }

        $this->validate();

        try{

            $aux_user = ($this->userselected != null) ? $this->userselected : new User();
            $aux_user->name = $this->name;
            $aux_user->title_job = $this->title_job;
            $aux_user->email = $this->email;
            $aux_user->user_type = $this->user_type;
            $aux_user->active = ($this->userselected != null) ? $this->userselected->active : true;
            if (!blank($this->password)) {
                $aux_user->password = Hash::make($this->password);
            }
            $aux_user->save();

            LivewireAlert::title('Usuario creado con éxito.')
                    ->success()
                    ->show();
            
            $this->reset(['name', 'email', 'title_job', 'user_type', 'active', 'password', 'modalceusers', 'userselected']);

        }catch(Throwable $exception) {
            Log::error('Error al crear el usuario: ' . $exception->getMessage());
            LivewireAlert::title('Error al crear el usuario.')
                ->error()
                ->show();
        }
    }

    public function desactivateuser($idUser){

        try{
            User::where('id', $idUser)->update([
                'active' => false,
                'updated_at' => date('Y-m-d H:m'),
            ]);

            LivewireAlert::title('Desactivado con éxito.')
                ->success()
                ->show();
 
        }catch(\Exception $exception) {
            $this->alert('error', 'No se puede desactivar.', [
             'position' => 'center',
             'timer' => 1500,
             'toast' => true,
            ]);
        }
    }

    public function activateuser($idUser){

        try{
            User::where('id', $idUser)->update([
                'active' => true,
                'updated_at' => date('Y-m-d H:m'),
            ]);


            LivewireAlert::title('Activado con éxito.')
                ->success()
                ->show();
 
        }catch(\Exception $exception) {
            $this->alert('error', 'No se puede activar.', [
             'position' => 'center',
             'timer' => 1500,
             'toast' => true,
            ]);
        }
    }

}
