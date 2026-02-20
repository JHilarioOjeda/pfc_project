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
use App\Models\Tarima;
use App\Models\Customer;
use App\Models\User;

class Storage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public function render()
    {
        $tarimas = Tarima::with(['customer', 'who_register'])
            ->where('serial_number', 'LIKE', '%' . $this->search . '%')
            ->whereHas('customer', function($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%');
            })
            ->orWhereHas('who_register', function($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy('register_date', 'DESC')
            ->paginate(25);
            
        return view('livewire.storage.storage', compact('tarimas'));
    }
}
