<?php

namespace App\Livewire\Storage;

use Livewire\Component;
use DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage as StorageDisk;
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
use App\Models\Tarima as TarimaModel;
use App\Models\Customer;
use App\Models\NumberPart;
use App\Models\User;

class Tarima extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $customers, $number_parts;
    public $serial_number, $id_customer, $id_number_part;

    public function mount(){
        $this->customers = Customer::where('active', true)->orderBy('name')->get();
        $this->number_parts = NumberPart::where('active', true)->orderBy('partnumber')->get();
    }
    public function render(){
        return view('livewire.storage.tarima');
    }
}
