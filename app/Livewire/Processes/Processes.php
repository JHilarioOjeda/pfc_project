<?php

namespace App\Livewire\Processes;
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
use App\Models\Tarima;
use App\Models\TarimaNp;
use App\Models\Customer;
use App\Models\User;
use App\Models\Proccess;

use Livewire\Component;

class Processes extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public function render(){

        $procesos = Proccess::with(['tarimaNp.tarima', 'tarimaNp.numberPart'])
            ->whereHas('tarimaNp', function($query) {
                $query->where('oc', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('of', 'LIKE', '%' . $this->search . '%')
                      ->orWhereHas('tarima', function($query) {
                          $query->where('serial_number', 'LIKE', '%' . $this->search . '%');
                      });
            })
            ->orWhereHas('tarimaNp.numberPart', function($query) {
                $query->where('partnumber', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(25);

        return view('livewire.processes.processes', ['processes' => $procesos]);
    }
}
