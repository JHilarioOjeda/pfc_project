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
use Livewire\Attributes\Url;
use Auth;
use Throwable;
use Log;
use App\Models\Tarima;
use App\Models\TarimaNp;
use App\Models\Customer;
use App\Models\User;
use App\Models\Proccess;
use App\Models\NumberPart;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Livewire\Component;
use Carbon\Carbon;

class Processes extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    #[Url(history: true)]
    public $filterProcess = '';

    #[Url(history: true)]
    public $filterTarima = '';

    public function updatedFilterProcess(): void
    {
        $this->resetPage();
    }

    // Rango de fechas de registro de entrada de la tarima (tarima.register_date).
    // Ambos son opcionales: vacíos no filtran.
    #[Url(history: true)]
    public $fromDate = '';

    #[Url(history: true)]
    public $toDate = '';

    public function updatedFilterTarima(): void
    {
        $this->resetPage();
    }

    public function updatedFromDate(): void
    {
        $this->resetPage();
    }

    public function updatedToDate(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterProcess', 'filterTarima', 'fromDate', 'toDate']);
        $this->resetPage();
    }

    // Normaliza el rango: inicio/fin de día y, si vienen invertidas, las intercambia.
    protected function entryDateRange(): array
    {
        $from = $this->parseDate($this->fromDate)?->startOfDay();
        $to = $this->parseDate($this->toDate)?->endOfDay();

        if ($from && $to && $from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    protected function parseDate($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable $e) {
            return null;
        }
    }

    // Revierte un proceso iniciado por error a "sin comenzar"
    public function revertProcess($idprocess): void
    {
        if (!in_array(Auth::user()->user_type, ['1', '3', '6'])) {
            return;
        }

        $process = Proccess::find($idprocess);

        if (!$process || $process->status !== 'inprocess') {
            LivewireAlert::title('Solo se pueden revertir procesos que estén en proceso.')->warning()->show();
            return;
        }

        if ($process->hasRegisteredInfo()) {
            LivewireAlert::title('No se puede revertir: el proceso ya tiene información registrada (línea, cargas, piezas, reportes o documento).')->warning()->show();
            return;
        }

        try {
            $process->status = 'pending';
            $process->start_date = null;
            $process->who_made = null;
            $process->save();

            Log::info("Proceso {$process->id} revertido a 'pending' por el usuario " . Auth::id());

            LivewireAlert::title('El proceso se revirtió correctamente.')->success()->show();
        } catch (Throwable $e) {
            Log::error('Error al revertir el proceso: ' . $e->getMessage());
            LivewireAlert::title('Error al revertir el proceso.')->error()->show();
        }
    }

    public function render(){

        [$entryFrom, $entryTo] = $this->entryDateRange();

        $procesos = Proccess::with(['tarimaNp.tarima', 'tarimaNp.numberPart'])
            ->withCount(['charges', 'meditionsReports'])
            ->when(Auth::user()->user_type == '5', function($query) {
                $query->where('status', 'finished');
            })
            ->when($this->filterProcess !== '', function($query) {
                $query->whereHas('tarimaNp.numberPart', function($q) {
                    $q->where('process', 'LIKE', '%' . $this->filterProcess . '%');
                });
            })
            ->when($this->filterTarima !== '', function($query) {
                $query->whereHas('tarimaNp.tarima', function($q) {
                    $q->where('serial_number', 'LIKE', '%' . $this->filterTarima . '%');
                });
            })
            ->when($entryFrom || $entryTo, function($query) use ($entryFrom, $entryTo) {
                $query->whereHas('tarimaNp.tarima', function($q) use ($entryFrom, $entryTo) {
                    $q->when($entryFrom, fn ($q) => $q->where('register_date', '>=', $entryFrom))
                      ->when($entryTo, fn ($q) => $q->where('register_date', '<=', $entryTo));
                });
            })
            ->where(function($query) {
                $query->whereHas('tarimaNp', function($q) {
                    $q->where('oc', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('of', 'LIKE', '%' . $this->search . '%')
                      ->orWhereHas('tarima', function($q) {
                          $q->where('serial_number', 'LIKE', '%' . $this->search . '%');
                      });
                })
                ->orWhereHas('tarimaNp.numberPart', function($q) {
                    $q->where('partnumber', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(25);

        $processOptions = NumberPart::whereNotNull('process')
            ->where('process', '<>', '')
            ->distinct()
            ->orderBy('process')
            ->pluck('process');

        return view('livewire.processes.processes', [
            'processes' => $procesos,
            'processOptions' => $processOptions,
            'entryFrom' => $entryFrom,
            'entryTo' => $entryTo,
        ]);
    }
}
