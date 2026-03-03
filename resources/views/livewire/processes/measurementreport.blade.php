<div class="containerpric">
    <x-loading functionsList="" />

    <div class="w-full flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
        <x-secondary-hyperlink href="{{ route('processes') }}" target="" class="my-auto whitespace-nowrap w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Volver
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-lg sm:text-2xl font-bold leading-snug">Reporte de medición para el proceso {{ $process_selected->id ?? 'N/A' }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg my-3 p-4 lg:space-y-10">
        <div class="flex flex-col w-full space-y-5 lg:flex-row lg:space-y-0 lg:space-x-10">
            <div class="flex flex-col lg:w-1/2 space-y-2">
                <div class="flex space-x-4 sm:space-x-6 text-xs sm:text-sm">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cliente</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->tarima->customer->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Órden de compra</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->oc ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Órden de fabricación</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->of ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:w-1/2 space-y-2">
                <div class="flex space-x-4 sm:space-x-6 text-xs sm:text-sm">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Número de parte</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->partnumber ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Proceso/acabado</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->process ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Unidades de medición</span>
                        <span class="text-base sm:text-lg font-medium">Micras</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cantidad</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->quantity ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex w-full flex-col space-y-5 lg:flex-row lg:space-y-0 lg:space-x-10 mt-5">
            <div class="w-full lg:w-1/2 flex flex-col space-y-3 lg:flex-row lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-1/2">
                    <p class="text-secondarycolor">Método:</p>
                    <select wire:model="method" class="inputcatalogues w-full">
                        <option value="">Seleccionar...</option>
                        <option value="Colgado">Colgado</option>
                        <option value="Barril">Barril</option>
                    </select>
                    <span class="text-red-500 text-xs italic">
                        @error('method')
                            {{$message}}
                        @enderror
                    </span>
                </div>
                <div class="w-full lg:w-1/2">
                    <p class="text-secondarycolor">Requisito:</p>
                    <textarea wire:model="requirement" class="inputcatalogues w-full"></textarea>
                    <span class="text-red-500 text-xs italic">
                        @error('requirement')
                            {{$message}}
                        @enderror
                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-col space-y-2 mt-5">
            <p class="font-semibold text-primarycolor">Observaciones / Condiciones del material</p>
            <div class="w-full lg:w-1/2 flex space-x-3">
                <div class="sm:w-1/3">
                        <p class="text-secondarycolor">Apariencia visual</p>
                        <select wire:model="visual_appearance" class="inputcatalogues w-full">
                            <option value="">Seleccionar...</option>
                            <option value="1">Aceptable</option>
                            <option value="2">Denegado</option>
                        </select>
                        <span class="text-red-500 text-xs italic">
                            @error('visual_appearance')
                                {{$message}}
                            @enderror
                        </span>
                    </div>
                    <div class="sm:w-1/3">
                            <p class="text-secondarycolor">Espesor en micras:</p>
                            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-2">
                                <div class="flex-1 sm:min-w-[10rem]">
                                    <input wire:model="thickness_in_microns" type="number" class="inputcatalogues w-full">
                                    <span class="text-red-500 text-xs italic">
                                        @error('thickness_in_microns')
                                            {{$message}}
                                        @enderror
                                    </span>
                                </div>

                                <x-secondary-button class="w-fit mt-2 self-end sm:mt-0 sm:self-auto h-[2rem]" wire:click="addDeadtime">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-2">
                                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                    </svg>
                                    Agregar
                                </x-secondary-button>
                            </div>
                        </div>
                    
            </div>
            @if(count($deadTimesList) > 0)
                    <div class="w-full lg:w-1/2 mt-4">
                        <p class="font-semibold text-primarycolor">Mediciones</p>
                        <div class="w-full rounded-lg border-2 border-dashed border-gray-200 p-2">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs md:text-sm">
                                    <thead class="bg-gray-100 text-gray-600">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Número de medición</th>
                                            <th class="px-2 py-1 text-center">Espesor micras</th>
                                            <th class="px-2 py-1 text-center">Apariencia visual</th>
                                            <th class="px-2 py-1 text-left">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deadTimesList as $index => $item)
                                            <tr class="border-b last:border-b-0">
                                                <td class="px-1 py-2">{{ $loop->iteration }}</td>
                                                <td class="px-1 py-2 text-center">{{ $item['thickness_in_microns'] }}</td>
                                                <td class="px-1 py-2">{{ $item['visual_appearance'] }}</td>
                                                <td class="px-1 py-2">
                                                    <x-buttondelete class="!px-2 !py-1 text-xs" wire:click="removeDeadtime({{ $index }})">
                                                        Eliminar
                                                    </x-buttondelete>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            @endif
        </div>

        <div class="flex flex-row space-x-3 justify-end mt-12">
            <x-secondary-button class="w-fit h-fit" onclick="confirmUpdateProcess()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                </svg>

                Actualizar datos
            </x-secondary-button>

            @if($process_selected && $process_selected->tarimaNp && $process_selected->tarimaNp->quantity == $quantity_processed && $process_selected->status != 'finished')
                <x-button-primary class="w-fit h-fit" onclick="confirmFinishProcess()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                        <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                    </svg>


                    Terminar proceso
                </x-button-primary>
            @endif
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

    function initDeadtimeSelect() {
        if (typeof SlimSelect === 'undefined') return;

        if (window.deadtimeSlim) {
            window.deadtimeSlim.destroy();
        }

        const el = document.getElementById('deadtime_select');
        if (!el) return;

        window.deadtimeSlim = new SlimSelect({
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
            initDeadtimeSelect();
        };
    })(window.refreshSlimSelects);

    // Disparo inicial
    window.refreshSlimSelects();

    function confirmUpdateProcess() {
        Swal.fire({
            title: '¿Deseas actualizar los datos del proceso?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Si, actualizar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('updateProcessData');
            }
        });
    }

    function confirmFinishProcess() {
        Swal.fire({
            title: '¿Deseas terminar este proceso?',
            text: 'Se marcará como Terminado y ya no podrás editarlo.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Si, terminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('finishProcess');
            }
        });
    }
</script>
@endpush

