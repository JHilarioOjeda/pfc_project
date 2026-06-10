<?php

namespace App\Livewire\Catalogs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\NumberPart;
use App\Models\NumberPartPrice;
use App\Support\NpImportFileParser;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class Nps extends Component
{
    private const PREVIEW_LIMIT = 10;

    use WithFileUploads;

    public $search = '', $modalcenps = false, $npselected = null;

    public $modalImportNps = false, $importFile = null;

    public $importPreview = [], $importPreparedRows = [];

    public $importNewCount = 0, $importUpdateCount = 0;

    public $importIsAnalyzed = false, $analyzedImportSignature = null;

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
        'importFile' => 'Archivo de importacion',
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
            $this->npselected = ($idnp > 0) ? NumberPart::where('id', $idnp)->first() : null;
            if ($this->npselected != null) {
                $this->partnumber = $this->npselected->partnumber;
                $this->process = $this->npselected->process;
                $this->details = $this->npselected->details;
                $this->microns = round($this->npselected->microns,2);
                $this->inches = round($this->npselected->inches,2);
                $this->decimeters = round($this->npselected->decimeters,2);
                $this->active = $this->npselected->active;
                $this->loadPrices();
            }
            $this->newPriceDate = date('Y-m-d');
        }
    }

    public function scmodalImportNps()
    {
        if ($this->modalImportNps) {
            $this->modalImportNps = false;
            $this->resetImportState();
            return;
        }

        $this->modalImportNps = true;
        $this->resetImportState();
    }

    public function updatedImportFile(): void
    {
        // Si cambia el archivo, invalida el análisis previo.
        $this->importPreparedRows = [];
        $this->importPreview = [];
        $this->importNewCount = 0;
        $this->importUpdateCount = 0;
        $this->importIsAnalyzed = false;
        $this->analyzedImportSignature = null;
    }

    public function analyzeImportFile()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $this->resetValidation('importFile');
        $this->importPreparedRows = [];
        $this->importPreview = [];
        $this->importNewCount = 0;
        $this->importUpdateCount = 0;
        $this->importIsAnalyzed = false;
        $this->analyzedImportSignature = null;

        try {
            $parser = app(NpImportFileParser::class);
            $rows = $parser->parse($this->importFile->getRealPath(), strtolower($this->importFile->getClientOriginalExtension()));

            if (count($rows) === 0) {
                LivewireAlert::title('El archivo no contiene registros validos para importar.')
                    ->warning()
                    ->show();
                return;
            }

            $partnumbers = collect($rows)
                ->pluck('partnumber')
                ->filter()
                ->unique()
                ->values();

            $existing = NumberPart::whereIn('partnumber', $partnumbers)
                ->get()
                ->keyBy('partnumber');

            foreach ($rows as $row) {
                $existingNp = $existing->get($row['partnumber']);
                if ($existingNp) {
                    $this->importUpdateCount++;
                } else {
                    $this->importNewCount++;
                }

                $this->importPreparedRows[] = [
                    'id' => $existingNp?->id,
                    'partnumber' => $row['partnumber'],
                    'process' => $row['process'],
                    'microns' => $row['microns'],
                    'inches' => $row['inches'],
                    'price' => $row['price'],
                ];
            }

            $this->importPreview = array_slice($this->importPreparedRows, 0, self::PREVIEW_LIMIT);
            $this->importIsAnalyzed = true;
            $this->analyzedImportSignature = $this->buildImportFileSignature();
        } catch (Throwable $e) {
            Log::error('Error al analizar archivo de importacion de NP: ' . $e->getMessage());
            LivewireAlert::title('No se pudo analizar el archivo. Verifica el formato.')
                ->error()
                ->show();
        }
    }

    public function processImport()
    {
        if (!$this->importIsAnalyzed || !$this->importFile) {
            LivewireAlert::title('Primero analiza el archivo para continuar.')
                ->warning()
                ->show();
            return;
        }

        if ($this->buildImportFileSignature() !== $this->analyzedImportSignature) {
            LivewireAlert::title('El archivo cambió. Analiza de nuevo antes de importar.')
                ->warning()
                ->show();
            return;
        }

        if (count($this->importPreparedRows) === 0) {
            LivewireAlert::title('No hay registros preparados para importar.')
                ->warning()
                ->show();
            return;
        }

        DB::beginTransaction();

        $created = 0;
        $updated = 0;
        $pricesCreated = 0;

        try {
            $partnumbers = collect($this->importPreparedRows)
                ->pluck('partnumber')
                ->filter()
                ->unique()
                ->values();

            $existingNps = NumberPart::whereIn('partnumber', $partnumbers)
                ->get()
                ->keyBy('partnumber');

            $today = now()->toDateString();
            $existingPricesByNp = NumberPartPrice::whereIn('id_np', $existingNps->pluck('id')->values())
                ->whereDate('price_date', $today)
                ->get(['id_np', 'price'])
                ->groupBy('id_np')
                ->map(function ($group) {
                    return $group
                        ->pluck('price')
                        ->map(fn($value) => (string) $value)
                        ->toArray();
                });

            foreach ($this->importPreparedRows as $row) {
                $np = $existingNps->get($row['partnumber']);

                if (!$np) {
                    $np = new NumberPart();
                    $np->partnumber = $row['partnumber'];
                    $np->active = true;
                    $created++;
                } else {
                    $updated++;
                }

                $np->process = $row['process'];
                $np->microns = $row['microns'];
                $np->inches = $row['inches'];
                $np->decimeters = $this->calculateDecimetersFromInches($row['inches']);
                $np->save();

                $existingNps->put($np->partnumber, $np);

                if ($row['price'] !== null) {
                    $priceValue = (string) $row['price'];
                    $pricesForNp = $existingPricesByNp->get($np->id, []);
                    $existsPrice = in_array($priceValue, $pricesForNp, true);

                    if (!$existsPrice) {
                        NumberPartPrice::create([
                            'id_np' => $np->id,
                            'price' => $row['price'],
                            'price_date' => $today,
                        ]);
                        $pricesCreated++;

                        $pricesForNp[] = $priceValue;
                        $existingPricesByNp->put($np->id, $pricesForNp);
                    }
                }
            }

            DB::commit();

            $this->modalImportNps = false;
            $this->resetImportState();

            LivewireAlert::title('Importacion completada. Nuevos: ' . $created . ' | Actualizados: ' . $updated . ' | Precios agregados: ' . $pricesCreated)
                ->success()
                ->show();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al procesar importacion de NP: ' . $e->getMessage());
            LivewireAlert::title('Error al importar los NPs.')
                ->error()
                ->show();
        }
    }

    private function resetImportState(): void
    {
        $this->reset(['importFile', 'importPreview', 'importPreparedRows', 'importNewCount', 'importUpdateCount', 'importIsAnalyzed', 'analyzedImportSignature']);
    }

    private function buildImportFileSignature(): ?string
    {
        if (!$this->importFile) {
            return null;
        }

        $realPath = $this->importFile->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            return null;
        }

        return md5_file($realPath) . '|' . $this->importFile->getClientOriginalName() . '|' . $this->importFile->getSize();
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
            $np->microns = $this->microns;
            $np->inches = $this->inches;
            $np->decimeters = $this->decimeters;
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

    public function changeNpStatus($idnp){
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

    public function calculateDecimeters()
    {
        $this->decimeters = $this->calculateDecimetersFromInches($this->inches);
    }

    private function calculateDecimetersFromInches(mixed $inches): ?float
    {
        if ($inches !== null && is_numeric($inches)) {
            return round(((float) $inches) * 0.065, 4);
        }

        return null;
    }
}
