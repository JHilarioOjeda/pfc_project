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
use App\Models\Proccess;

class Storage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public function deleteTarima($id)
    {
        try {
            $tarima = Tarima::findOrFail($id);

            $tarimaNpIds = $tarima->tarimaNps()->pluck('id');
            $hasProcesses = Proccess::whereIn('id_tarima_np', $tarimaNpIds)->where('status', 'inprocess')->exists();

            if ($hasProcesses) {
                LivewireAlert::title('No se puede eliminar: la tarima tiene procesos iniciados.')
                    ->error()
                    ->show();
                return;
            }

            $tarima->delete();

            LivewireAlert::title('Tarima eliminada correctamente.')
                ->success()
                ->show();
        } catch (Throwable $e) {
            Log::error('Error al eliminar tarima: ' . $e->getMessage());
            LivewireAlert::title('Error al eliminar la tarima.')
                ->error()
                ->show();
        }
    }

    public function render()
    {
        $tarimas = Tarima::with(['customer', 'registeredBy'])
            ->where('serial_number', 'LIKE', '%' . $this->search . '%')
            ->orWhere('register_date', 'LIKE', '%' . $this->search . '%')
            ->orWhereHas('customer', function($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%');
            })
            ->orWhereHas('registeredBy', function($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy('register_date', 'DESC')
            ->paginate(25);
            
        return view('livewire.storage.storage', compact('tarimas'));
    }
}
