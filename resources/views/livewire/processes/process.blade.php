<div class="containerpric">
    <x-loading functionsList="saveTarima, addNumberPart" />

    <div class="w-full flex space-x-4">
        <x-secondary-hyperlink href="{{ route('processes') }}" target="" class="my-auto whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Volver
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-2xl font-bold">Proceso para {{ $process_selected->tarimaNp->tarima->serial_number ?? 'N/A' }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg my-3 p-4 space-y-10">
        <div class="flex w-full space-x-10">
            <div class="flex flex-col w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Información general</span>
                <div class="flex space-x-6">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Número de tarima</span>
                        <span class="text-lg font-medium">#{{ $process_selected->tarimaNp->tarima->serial_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Órden de compra</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->oc ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Órden de fabricación</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->of ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cliente</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->tarima->customer->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Información de NP</span>
                <div class="flex space-x-6">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Número de parte</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->partnumber ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Proceso/acabado</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->process ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Detalles</span>
                        <span class="text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->details ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex w-full space-x-10">
            <div class="w-1/2 flex space-x-4">
                <div class="w-full md:w-1/2">
                    <p class="text-secondarycolor">Línea:</p>
                    <select wire:model="id_line" id="id_line" class="inputcatalogues w-full">
                        <option value="">Seleccionar...</option>
                        @foreach ($lines as $line)
                            <option value="{{$line->id}}">{{$line->name}}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs italic">
                        @error('id_line')
                            {{$message}}
                        @enderror
                    </span>
                </div>
                <div class="w-full md:w-1/2">
                    <p class="text-secondarycolor">Nombre(s) de operador(es):</p>
                    <textarea wire:model="operator_name" class="inputcatalogues w-full"></textarea>
                    <span class="text-red-500 text-xs italic">
                        @error('operator_name')
                            {{$message}}
                        @enderror
                    </span>
                </div>
            </div>
            <div class="flex flex-col w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Información del proceso</span>
                <div class="flex space-x-10">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cantidad de piezas a procesar</span>
                        <span class="text-lg font-medium text-center">{{ $process_selected->tarimaNp->quantity ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cantidad de piezas procesadas:</span>
                        <input type="number" min="0" max="{{ $process_selected->tarimaNp->quantity ?? 0 }}" wire:model="quantity_processed" class="inputcatalogues w-full">
                        <span class="text-red-500 text-xs italic">
                            @error('quantity_processed')
                                {{$message}}
                            @enderror
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex w-full space-x-10">
            
        </div>    
    </div>
</div>

@push('js')
<script>
    function initLineSelect() {
        if (typeof SlimSelect === 'undefined') return;

        if (window.lineSlim) {
            window.lineSlim.destroy();
        }

        const el = document.getElementById('id_line');
        if (!el) return;

        window.lineSlim = new SlimSelect({
            select: el,
            settings: {
                placeholderText: 'Seleccionar...',
                searchPlaceholder: 'Buscar',
                searchText: 'No se encontraron resultados',
            },
            events: {
                afterChange: () => {
                    // Asegura que Livewire capture el cambio
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                },
            },
        });
    }

    // Registra un refresco global (el layout lo invoca tras updates de Livewire v3)
    window.refreshSlimSelects = ((previous) => {
        return function () {
            if (typeof previous === 'function') previous();
            initLineSelect();
        };
    })(window.refreshSlimSelects);

    // Disparo inicial
    window.refreshSlimSelects();
</script>
@endpush
