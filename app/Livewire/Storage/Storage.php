<?php

namespace App\Livewire\Storage;

use Livewire\Component;
use DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage as StorageDisk;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
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

    #[Url(history: true)]
    public $tab = 'toprocess';

    // Estatus de tarima que se muestran en cada pestaña
    protected const TAB_STATUSES = [
        'toprocess' => [Tarima::STATUS_INPROCESS, Tarima::STATUS_FINISHED],
        'todeliver' => [Tarima::STATUS_READYTODELIVER],
        'delivered' => [Tarima::STATUS_DELIVERED],
    ];

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function markAsDelivered($id)
    {
        if (!in_array(Auth::user()->user_type, ['1', '2'])) {
            return;
        }

        try {
            $tarima = Tarima::findOrFail($id);

            if ($tarima->status !== Tarima::STATUS_READYTODELIVER) {
                LivewireAlert::title('Solo se pueden entregar tarimas listas para entrega.')
                    ->warning()
                    ->show();
                return;
            }

            $tarima->status = Tarima::STATUS_DELIVERED;
            $tarima->delivered_date = now();
            $tarima->who_delivered = Auth::id();
            $tarima->save();

            LivewireAlert::title('Tarima marcada como entregada.')
                ->success()
                ->show();
        } catch (Throwable $e) {
            Log::error('Error al marcar tarima como entregada: ' . $e->getMessage());
            LivewireAlert::title('Error al marcar la tarima como entregada.')
                ->error()
                ->show();
        }
    }

    public function deleteTarima($id)
    {
        try {
            $tarima = Tarima::findOrFail($id);

            if (in_array($tarima->status, [Tarima::STATUS_READYTODELIVER, Tarima::STATUS_DELIVERED])) {
                LivewireAlert::title('No se puede eliminar: la tarima ya está lista para entrega o entregada.')
                    ->error()
                    ->show();
                return;
            }

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
        if (!array_key_exists($this->tab, self::TAB_STATUSES)) {
            $this->tab = 'toprocess';
        }

        $tarimas = Tarima::with(['customer', 'registeredBy', 'deliveredBy'])
            ->whereIn('status', self::TAB_STATUSES[$this->tab])
            ->where(function($query) {
                $query->where('serial_number', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('register_date', 'LIKE', '%' . $this->search . '%')
                    ->orWhereHas('customer', function($q) {
                        $q->where('name', 'LIKE', '%' . $this->search . '%');
                    })
                    ->orWhereHas('registeredBy', function($q) {
                        $q->where('name', 'LIKE', '%' . $this->search . '%');
                    });
            })
            ->when($this->tab === 'delivered', function($query) {
                $query->orderBy('delivered_date', 'DESC');
            })
            ->orderBy('register_date', 'DESC')
            ->paginate(25);

        $tabCounts = [];
        foreach (self::TAB_STATUSES as $key => $statuses) {
            $tabCounts[$key] = Tarima::whereIn('status', $statuses)->count();
        }

        return view('livewire.storage.storage', compact('tarimas', 'tabCounts'));
    }
}
