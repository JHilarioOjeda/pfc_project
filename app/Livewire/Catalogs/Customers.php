<?php

namespace App\Livewire\Catalogs;

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
use App\Models\Customer;
use Termwind\Components\Li;

class Customers extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '', $modalcecustomers = false, $customerselected = null;

    public $name, $company_name, $address, $zip_code, $telephone, $email, $rfc, $line, $active;

    protected $listeners = ['changeCustomerStatus'];

    protected $rules = [];
    protected $validationAttributes  = [
        'name' => 'Nombre',
        'company_name' => 'Razón social',
        'address' => 'Dirección',
        'zip_code' => 'Código postal',
        'telephone' => 'Teléfono',
        'email' => 'Correo electrónico',
        'rfc' => 'RFC',
        'line' => 'Giro',
        'active' => 'Activo',
    ];

    public function render(){
        $customers = Customer::where('company_name', 'LIKE', '%' . $this->search . '%')
            ->orWhere('zip_code', 'LIKE', '%' . $this->search . '%')
            ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ->orWhere('rfc', 'LIKE', '%' . $this->search . '%')
            ->orderBy('company_name', 'ASC')
            ->get();

        return view('livewire.catalogs.customers')->with('customers', $customers);
    }

    public function scmodalcustomers($idcustomer){
        if ($this->modalcecustomers == true) {
            $this->modalcecustomers = false;
            $this->reset(['name', 'company_name', 'address', 'zip_code', 'telephone', 'email', 'rfc', 'line', 'active', 'modalcecustomers', 'customerselected']);
        } else {
            $this->modalcecustomers = true;
            $this->customerselected = Customer::where('id', $idcustomer)->first();
            if($this->customerselected != null){
                $this->name = $this->customerselected->name;
                $this->company_name = $this->customerselected->company_name;
                $this->address = $this->customerselected->address;
                $this->zip_code = $this->customerselected->zip_code;
                $this->telephone = $this->customerselected->telephone;
                $this->email = $this->customerselected->email;
                $this->rfc = $this->customerselected->rfc;
                $this->line = $this->customerselected->line;
                $this->active = $this->customerselected->active;
            }
        }
    }

    public  function createUpdateCustomer(){
        
        $this->rules = [
            'company_name' => 'required',
        ];

        $this->validate();

        try{

            $customer = ($this->customerselected != null) ? Customer::find($this->customerselected->id) : new Customer();

            $customer->name = $this->company_name;
            $customer->company_name = $this->company_name;
            $customer->address = $this->address;
            $customer->zip_code = $this->zip_code;
            $customer->telephone = $this->telephone;
            $customer->email = $this->email;
            $customer->rfc = $this->rfc;
            $customer->line = $this->line;
            $customer->active = $this->customerselected ? $this->active : true;
            $customer->save();

            $message = ($this->customerselected != null) ? 'Cliente actualizado con éxito.' : 'Cliente creado con éxito.';
            LivewireAlert::title($message)
                    ->success()
                    ->show();

        }catch(Throwable $e){
            Log::error('Error al crear/actualizar cliente: ' . $e->getMessage());
            LivewireAlert::title('Error al crear/actualizar cliente.')
                    ->error()
                    ->show();
            return;
        }
    }

    public function changeCustomerStatus($idcustomer){

        try{
            $aux_customer = Customer::where('id', $idcustomer)->first();
            if($aux_customer != null){
                $new_status = !$aux_customer->active;
                $aux_customer->active = $new_status;
                $aux_customer->save();

                $message = $new_status ? 'Activado con éxito.' : 'Desactivado con éxito.';
                LivewireAlert::title($message)
                    ->success()
                    ->show();
            } else {
                LivewireAlert::title('Cliente no encontrado.')
                    ->error()
                    ->show();
            } 
 
        }catch(Throwable $exception) {
            Log::error('Error al cambiar el estado del cliente: ' . $exception->getMessage());
            LivewireAlert::title('Error al cambiar el estado del cliente.')
                ->error()
                ->show();
        }
    }
}
