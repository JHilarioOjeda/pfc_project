<?php

namespace App\Livewire\Catalogs;

use Livewire\Component;
use App\Models\NumberPart;
use App\Models\NumberPartPrice;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Throwable;
use Log;

class Nps extends Component
{
    public $search = '', $modalcenps = false, $npselected = null;

    public $partnumber, $process, $details, $microns, $inches, $decimeters, $active;

    // Propiedades para precios
    public $newPrice, $newPriceDate, $npPrices = [];

    protected $listeners = ['changeNpStatus', 'deletePrice'];

    protected $rules = [];
    protected $validationAttributes  = [
        'partnumber' => 'NP',
        'process' => 'Proceso',
        'details' => 'Detalles',
        'microns' => 'Micras',
        'inches' => 'Pulgadas',
        'decimeters' => 'Decímetros',
        'active' => 'Activo',
    ];

    public function render()
    {
        $nps = NumberPart::with('latestPrice')
            ->where('partnumber', 'LIKE', '%' . $this->search . '%')
            ->orWhere('process', 'LIKE', '%' . $this->search . '%')
            ->orWhere('details', 'LIKE', '%' . $this->search . '%')
            ->orderBy('partnumber', 'ASC')
            ->get();

        return view('livewire.catalogs.nps')->with('nps', $nps);
    }

    public function scmodalnps($idnp)
    {
        if ($this->modalcenps == true) {
            $this->modalcenps = false;
            $this->reset(['partnumber', 'process', 'details', 'microns', 'inches', 'decimeters', 'active', 'modalcenps', 'npselected', 'newPrice', 'newPriceDate', 'npPrices']);
        } else {
            $this->modalcenps = true;
            $this->npselected = NumberPart::where('id', $idnp)->first();
            if ($this->npselected != null) {
                $this->partnumber = $this->npselected->partnumber;
                $this->process = $this->npselected->process;
                $this->details = $this->npselected->details;
                $this->microns = round($this->npselected->microns);
                $this->inches = round($this->npselected->inches);
                $this->decimeters = round($this->npselected->decimeters);
                $this->active = $this->npselected->active;
                $this->loadPrices();
            }
            $this->newPriceDate = date('Y-m-d');
        }
    }

    public function loadPrices()
    {
        if ($this->npselected) {
            $this->npPrices = NumberPartPrice::where('id_np', $this->npselected->id)
                ->orderBy('price_date', 'desc')
                ->get()
                ->toArray();
        }
    }

    public function addPrice()
    {
        $this->validate([
            'newPrice' => 'required|numeric|min:0',
            // 'newPriceDate' => 'required|date',
        ], [], [
            'newPrice' => 'Precio',
            // 'newPriceDate' => 'Fecha',
        ]);

        if ($this->npselected == null) {
            LivewireAlert::title('Primero guarda el NP antes de agregar precios.')
                ->warning()
                ->show();
            return;
        }

        try {
            NumberPartPrice::create([
                'id_np' => $this->npselected->id,
                'price' => $this->newPrice,
                'price_date' => $this->newPriceDate ?? date('Y-m-d'),
            ]);

            $this->reset(['newPrice', 'newPriceDate']);
            $this->newPriceDate = date('Y-m-d');
            $this->loadPrices();

            LivewireAlert::title('Precio agregado con éxito.')
                ->success()
                ->show();
        } catch (Throwable $e) {
            Log::error('Error al agregar precio: ' . $e->getMessage());
            LivewireAlert::title('Error al agregar precio.')
                ->error()
                ->show();
        }
    }

    public function deletePrice($priceId)
    {
        try {
            $price = NumberPartPrice::find($priceId);
            if ($price) {
                $price->delete();
                $this->loadPrices();
                LivewireAlert::title('Precio eliminado con éxito.')
                    ->success()
                    ->show();
            }
        } catch (Throwable $e) {
            Log::error('Error al eliminar precio: ' . $e->getMessage());
            LivewireAlert::title('Error al eliminar precio.')
                ->error()
                ->show();
        }
    }

    public function createUpdateNp()
    {
        $this->rules = [
            'partnumber' => 'required',
            'process' => 'nullable',
            'details' => 'nullable',
            'microns' => 'nullable|numeric',
            'inches' => 'nullable|numeric',
            'decimeters' => 'nullable|numeric',
        ];

        $this->validate();

        try {
            $np = ($this->npselected != null) ? NumberPart::find($this->npselected->id) : new NumberPart();

            $np->partnumber = $this->partnumber;
            $np->process = $this->process;
            $np->details = $this->details;
            $np->microns = blank($this->microns) ? null : $this->microns;
            $np->inches = blank($this->inches) ? null : $this->inches;
            $np->decimeters = blank($this->decimeters) ? null : $this->decimeters;
            $np->active = $this->npselected ? $this->active : true;
            $np->save();

            $message = ($this->npselected != null) ? 'NP actualizado con éxito.' : 'NP creado con éxito.';

            LivewireAlert::title($message)
                ->success()
                ->show();
        } catch (Throwable $e) {
            Log::error('Error al crear/actualizar NP: ' . $e->getMessage());
            LivewireAlert::title('Error al crear/actualizar NP.')
                ->error()
                ->show();
            return;
        }
    }

    public function changeNpStatus($idnp)
    {
        try {
            $aux_np = NumberPart::where('id', $idnp)->first();
            if ($aux_np != null) {
                $new_status = !$aux_np->active;
                $aux_np->active = $new_status;
                $aux_np->save();

                $message = $new_status ? 'Activado con éxito.' : 'Desactivado con éxito.';
                LivewireAlert::title($message)
                    ->success()
                    ->show();
            } else {
                LivewireAlert::title('NP no encontrado.')
                    ->error()
                    ->show();
            }
        } catch (Throwable $exception) {
            Log::error('Error al cambiar el estado del NP: ' . $exception->getMessage());
            LivewireAlert::title('Error al cambiar el estado del NP.')
                ->error()
                ->show();
        }
    }
}
