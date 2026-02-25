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
use App\Models\TarimaNp;
use App\Models\User;

class Tarima extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $customers, $number_parts, $tarima_selected, $idstorage;
    public $serial_number, $id_customer;
    public $id_number_part, $quantity_np, $oc_np, $of_np;

    public $numberPartsList = [];

    protected $validationAttributes  = [
        'id_number_part' => 'Número de parte',
        'quantity_np' => 'Cantidad',
        'oc_np' => 'OC',
        'of_np' => 'OF',
        'serial_number' => 'Número de tarima',
        'id_customer' => 'Cliente',
    ];

    public function mount(){
        $this->customers = Customer::where('active', true)->orderBy('name')->get();
        $this->number_parts = NumberPart::where('active', true)->orderBy('partnumber')->get();

        $this->tarima_selected = TarimaModel::find($this->idstorage);
        if($this->tarima_selected){
            $this->serial_number = $this->tarima_selected->serial_number;
            $this->id_customer = $this->tarima_selected->id_customer;
            foreach($this->tarima_selected->tarimaNps as $item){
                $this->numberPartsList[] = [
                    'id_tarima_np' => $item->id,
                    'id_number_part' => $item->id_np,
                    'partnumber' => $item->numberPart ? $item->numberPart->partnumber : null,
                    'status_cont' => $item->status_cont,
                    'quantity' => $item->quantity,
                    'oc' => $item->oc,
                    'of' => $item->of,
                ];
            }
        } else {
            $this->serial_number = $this->lastTarimaRegisterNumber() + 1;
        }
    }
    public function render(){
        return view('livewire.storage.tarima');
    }

    public function addNumberPart()
    {
        $this->validate([
            'id_number_part' => 'required|exists:number_parts,id',
            'quantity_np' => 'required|numeric|min:1',
            'oc_np' => 'required|string',
            'of_np' => 'required|string',
        ]);

        $numberPart = $this->number_parts->firstWhere('id', $this->id_number_part);

        $this->numberPartsList[] = [
            'id_tarima_np' => null,
            'id_number_part' => $this->id_number_part,
            'partnumber' => $numberPart ? $numberPart->partnumber : null,
            'status_cont' => false,
            'quantity' => $this->quantity_np,
            'oc' => $this->oc_np,
            'of' => $this->of_np,
        ];

        $this->reset(['id_number_part', 'quantity_np', 'oc_np', 'of_np']);
    }

    public function saveTarima()
    {
        $this->validate([
            'serial_number' => 'required|unique:tarima,serial_number,' . ($this->tarima_selected ? $this->tarima_selected->id : 'NULL'),
            'id_customer' => 'required|exists:customers,id',
            'numberPartsList' => 'required|array|min:1',
            'numberPartsList.*.id_number_part' => 'required|exists:number_parts,id',
            'numberPartsList.*.quantity' => 'required|numeric|min:1',
            'numberPartsList.*.oc' => 'required|string',
            'numberPartsList.*.of' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $isUpdate = $this->tarima_selected ? true : false;

            if ($this->tarima_selected) {
                // Actualizar tarima existente
                $tarima = $this->tarima_selected;
                $tarima->serial_number = $this->serial_number;
                $tarima->id_customer = $this->id_customer;
                $tarima->save();
            } else {
                // Crear nueva tarima
                $tarima = TarimaModel::create([
                    'serial_number' => $this->serial_number,
                    'id_customer' => $this->id_customer,
                    'who_register' => Auth::id(),
                    'register_date' => now(),
                ]);
                $this->tarima_selected = $tarima;
            }

            // Crear o actualizar NPs asociados
            foreach ($this->numberPartsList as $part) {
                if (!empty($part['id_tarima_np'])) {
                    // Actualizar registro existente
                    $tarimaNp = TarimaNp::find($part['id_tarima_np']);
                    if ($tarimaNp) {
                        $tarimaNp->update([
                            'id_np' => $part['id_number_part'],
                            'quantity' => $part['quantity'],
                            'oc' => $part['oc'],
                            'of' => $part['of'],
                            'status_cont' => $part['status_cont'] ?? false,
                        ]);
                        continue;
                    }
                }

                // Crear nuevo registro si no existe o no se encontró
                $tarima->tarimaNps()->create([
                    'id_np' => $part['id_number_part'],
                    'quantity' => $part['quantity'],
                    'oc' => $part['oc'],
                    'of' => $part['of'],
                    'status_cont' => $part['status_cont'] ?? false,
                ]);
            }

            DB::commit();

            $message = $isUpdate ? 'Entrada actualizada correctamente.' : 'Entrada creada correctamente.';

            LivewireAlert::title($message)
                    ->success()
                    ->show();
            if (!$isUpdate) {
                return redirect()->route('storage.tarima', ['id' => $tarima->id]);
            }
            
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear/editar entrada: '.$e->getMessage());
            LivewireAlert::title('Error al crear/editar entrada.')
                    ->error()
                    ->show();
        }
    }   

    public function confirmCount($index)
    {
        if (!isset($this->numberPartsList[$index])) {
            return;
        }

        $item = $this->numberPartsList[$index];

        // Actualizar en base de datos si es una tarima existente y el NP proviene de BD
        if ($this->tarima_selected && !empty($item['id_tarima_np'])) {
            $tarimaNp = TarimaNp::find($item['id_tarima_np']);
            if ($tarimaNp) {
                $tarimaNp->update(['status_cont' => true]);
            }
        }

        // Actualizar en la lista para reflejar el cambio en la vista
        $this->numberPartsList[$index]['status_cont'] = true;
    }

    public function removeNumberPart($index)
    {
        if (!isset($this->numberPartsList[$index])) {
            return;
        }

        $item = $this->numberPartsList[$index];

        // Si existe en BD, eliminar también el registro relacionado
        if (!empty($item['id_tarima_np'])) {
            $tarimaNp = TarimaNp::find($item['id_tarima_np']);
            if ($tarimaNp) {
                $tarimaNp->delete();
            }
        }

        unset($this->numberPartsList[$index]);
        $this->numberPartsList = array_values($this->numberPartsList);
    }

    public function lastTarimaRegisterNumber(): int
    {
        return TarimaModel::lastRegisterId();
    }

    public function getAllConfirmedProperty(): bool
    {
        if (empty($this->numberPartsList)) {
            return false;
        }

        foreach ($this->numberPartsList as $item) {
            if (empty($item['status_cont'])) {
                return false;
            }
        }

        return true;
    }
}
