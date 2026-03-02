<?php

namespace App\Livewire\Processes;

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
use App\Models\TarimaNp;
use App\Models\User;
use App\Models\Proccess;
use App\Models\WorkLine;

class Process extends Component
{
    use WithFileUploads;
    use WithPagination;

    public  $idprocess, $process_selected;
    public $lines = [], $id_line;
    public $operator_name;

    public function mount($idprocess){
        $this->process_selected = Proccess::find($this->idprocess);
        $this->lines = WorkLine::all();

        if($this->process_selected->start_date == null){
            $this->process_selected->start_date = now();
            $this->process_selected->status = 'inprocess';
            $this->process_selected->save();
        }
    }

    public function render(){

        return view('livewire.processes.process');
    }
}
