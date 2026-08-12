<div class="containerpric">
    <x-loading functionsList="addCharge, deleteCharge, liberateCharge, confirmCharge, addDeadtime, removeDeadtime, updateProcessData, finishProcess" />

    <div class="w-full flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
        <x-secondary-button onclick="window.history.back()" class="my-auto whitespace-nowrap w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            regresar
        </x-secondary-button>
        <p class="text-secondarycolor text-lg sm:text-2xl font-bold leading-snug">Proceso para la parte {{ $process_selected->tarimaNp->numberPart->partnumber ?? 'N/A' }} de la tarima {{ $process_selected->tarimaNp->tarima->serial_number ?? 'N/A' }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg my-3 p-4 space-y-6">

        {{-- InformaciÃ³n general y de NP --}}
        <div class="flex flex-col w-full space-y-5 lg:flex-row lg:space-y-0 lg:space-x-10">
            <div class="flex flex-col lg:w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Información general</span>
                <div class="flex space-x-4 sm:space-x-6 text-xs sm:text-sm">
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Número de tarima</span>
                        <span class="text-base sm:text-lg font-medium">#{{ $process_selected->tarimaNp->tarima->serial_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Orden de compra</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->oc ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Orden de fabricación</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->of ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Cliente</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->tarima->customer->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Información de NP</span>
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
                        <span class="text-secondarycolor text-sm">Decímetros</span>
                        <span class="text-base sm:text-lg font-medium">{{ round($process_selected->tarimaNp->numberPart->decimeters ?? 0, 5) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-secondarycolor text-sm">Detalles</span>
                        <span class="text-base sm:text-lg font-medium">{{ $process_selected->tarimaNp->numberPart->details ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Línea, operador y piezas --}}
        <div class="flex w-full flex-col space-y-5 lg:flex-row lg:space-y-0 lg:space-x-10">
            <div class="w-full lg:w-1/2 flex flex-col space-y-3 lg:flex-row lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-1/2">
                    <p class="text-secondarycolor">Línea:</p>
                    <select wire:model="id_line" id="id_line" class="inputcatalogues w-full" @if((!in_array(Auth::user()->user_type, ['1', '3', '6'])) || $process_selected->status === 'finished') disabled @endif>
                        <option value="">Seleccionar...</option>
                        @foreach ($lines as $line)
                            <option @if($id_line == $line->id) selected @endif value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs italic">@error('id_line') {{ $message }} @enderror</span>
                </div>
                <div class="w-full lg:w-1/2">
                    <p class="text-secondarycolor">Nombre(s) de operador(es):</p>
                    <textarea wire:model="operator_name" class="inputcatalogues w-full" @if((!in_array(Auth::user()->user_type, ['1', '3', '6'])) || $process_selected->status === 'finished') disabled @endif></textarea>
                </div>
            </div>

            <div class="flex flex-col w-full lg:w-1/2 space-y-2">
                <span class="text-primarycolor font-semibold">Piezas del proceso</span>
                <div class="flex space-x-10">
                    <div class="flex flex-col w-full lg:w-1/2 items-center">
                        <span class="text-secondarycolor text-xs sm:text-sm text-center">Total a procesar</span>
                        <span class="text-lg sm:text-xl font-medium text-center">{{ round($piezasTotales, 2) }}</span>
                    </div>
                    <div class="flex flex-col w-full lg:w-1/2 items-center">
                        <span class="text-secondarycolor text-xs sm:text-sm text-center">Piezas restantes</span>
                        <span class="text-lg sm:text-xl font-bold text-center {{ $piezasRestantes > 0 ? 'text-orange-500' : 'text-green-600' }}">
                            {{ round($piezasRestantes, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARGAS --}}
        <div class="flex flex-col space-y-3">
            <p class="font-semibold text-primarycolor text-base">Cargas</p>

            {{-- Agregar nueva carga --}}
            @if($process_selected->status !== 'finished' && $piezasRestantes > 0)
                @if(in_array(Auth::user()->user_type, ['1', '3', '6']))
                <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-3 space-y-2 sm:space-y-0 w-full lg:w-1/2">
                    <div class="flex-1">
                        <p class="text-secondarycolor text-sm">Piezas por carga:</p>
                        <input type="number" min="1" wire:model="new_charge_quantity" class="inputcatalogues w-full">
                        <span class="text-red-500 text-xs italic">@error('new_charge_quantity') {{ $message }} @enderror</span>
                    </div>
                    <x-secondary-button class="w-fit h-fit" wire:click="addCharge">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                        </svg>
                        Agregar carga
                    </x-secondary-button>
                </div>
                @endif
            @endif

            {{-- Lista de cargas --}}
            @forelse($chargesList as $ci => $charge)
                @php
                    $statusLabel = match($charge['status']) {
                        'created'   => ['texto' => 'Creada',    'class' => 'bg-gray-100 text-gray-700'],
                        'liberated' => ['texto' => 'Liberada',  'class' => 'bg-blue-100 text-blue-700'],
                        'confirmed' => ['texto' => 'Confirmada','class' => 'bg-green-100 text-green-700'],
                        default     => ['texto' => $charge['status'], 'class' => 'bg-gray-100 text-gray-700'],
                    };
                @endphp
                <div class="border rounded-lg p-3 space-y-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-semibold text-secondarycolor">Carga #{{ $ci + 1 }}</span>
                        <span class="text-sm">{{ $charge['quantity_pieces'] }} piezas</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusLabel['class'] }}">{{ $statusLabel['texto'] }}</span>

                        <div class="ml-auto flex flex-wrap gap-2">
                            {{-- Botón Liberar --}}
                            @if($charge['status'] === 'created' && $process_selected->status !== 'finished' && in_array(Auth::user()->user_type, ['1', '3', '6']))
                                <button onclick="confirmLiberateCharge({{ $ci }})" class="px-3 py-1 text-xs rounded-md bg-blue-500 text-white hover:bg-blue-600 transition uppercase font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 inline mr-1">
                                        <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
                                    </svg>

                                    Liberar
                                </button>
                            @endif

                            {{-- Botón Confirmar --}}
                            @if($charge['status'] === 'liberated' && $process_selected->status !== 'finished' && in_array(Auth::user()->user_type, ['1', '4', '6']))
                                <button onclick="confirmConfirmCharge({{ $ci }})"
                                        class="px-3 py-1 text-xs rounded-md bg-green-600 text-white hover:bg-green-400 transition uppercase font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 inline mr-1">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                        </svg>
                                    Confirmar conteo
                                </button>
                            @endif

                            {{-- Tiempos muertos --}}
                            <button wire:click="toggleDeadtimePanel({{ $ci }})"
                                    class="px-3 py-1 text-xs rounded-md bg-yellow-200 text-yellow-800 hover:bg-yellow-300 transition uppercase font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 inline mr-1">
                                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                                </svg>
                                T. Muertos ({{ count($charge['timeouts'] ?? []) }})
                            </button>

                            {{-- Eliminar (solo si está creada) --}}
                            @if($charge['status'] === 'created' && $process_selected->status !== 'finished' && in_array(Auth::user()->user_type, ['1', '3']))
                                <x-buttondelete class="!px-2 !py-1 text-xs" onclick="confirmDeleteCharge({{ $ci }})">
                                    Eliminar
                                </x-buttondelete>
                            @endif
                        </div>
                    </div>

                    {{-- Panel de tiempos muertos --}}
                    @if($deadtimeOpenCharge === $ci)
                        <div class="border-t pt-3 mt-2 space-y-3">
                            <p class="text-sm font-semibold text-primarycolor">Tiempos muertos</p>

                            @if($process_selected->status !== 'finished' && $charge['status'] !== 'confirmed')
                                <div class="flex flex-col sm:flex-row sm:items-end gap-2 w-full lg:w-2/3">
                                    <div class="sm:w-1/2">
                                        <p class="text-secondarycolor text-sm">Razón</p>
                                        <select wire:model="deadtime.{{ $ci }}" id="deadtime_select_{{ $ci }}" class="inputcatalogues w-full">
                                            <option value="">Seleccionar...</option>
                                            <option value="1">Secado deficiente</option>
                                            <option value="2">Mesa llena de piezas</option>
                                            <option value="3">Falta de material</option>
                                            <option value="4">Falta de personal</option>
                                            <optgroup label="Reproceso de piezas">
                                                <option value="15">Desprendimiento</option>
                                                <option value="16">Bajo micraje</option>
                                                <option value="17">Cobre expuesto</option>
                                                <option value="18">Mancha</option>
                                            </optgroup>
                                            <option value="6">Tiempo excesivo desengrase</option>
                                            <option value="7">Ajuste de soluciones</option>
                                            <option value="8">Cambio de soluciones</option>
                                            <option value="9">Evento social</option>
                                            <option value="10">Falla de equipo</option>
                                            <option value="11">Falta de energía eléctrica</option>
                                            <option value="12">Llenado de tinas</option>
                                            <option value="13">Limpieza de piezas</option>
                                            <option value="14">Cambio de tina y/o ganchos</option>
                                        </select>
                                        <span class="text-red-500 text-xs italic">@error("deadtime.{$ci}") {{ $message }} @enderror</span>
                                    </div>
                                    <div class="sm:w-1/3">
                                        <p class="text-secondarycolor text-sm">Horas:</p>
                                        <input wire:model="deadtime_hours.{{ $ci }}" type="number" step="0.01" class="inputcatalogues w-full">
                                        <span class="text-red-500 text-xs italic">@error("deadtime_hours.{$ci}") {{ $message }} @enderror</span>
                                    </div>
                                    <x-secondary-button class="w-fit h-[2rem]" wire:click="addDeadtime({{ $ci }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                        </svg>
                                        Agregar
                                    </x-secondary-button>
                                </div>
                            @endif

                            @if(count($charge['timeouts'] ?? []) > 0)
                                <div class="w-full lg:w-2/3 rounded-lg border-2 border-dashed border-gray-200 p-2">
                                    <table class="min-w-full text-xs md:text-sm">
                                        <thead class="bg-gray-100 text-gray-600">
                                            <tr>
                                                <th class="px-2 py-1 text-left">Razón</th>
                                                <th class="px-2 py-1 text-center">Horas</th>
                                                @if($process_selected->status !== 'finished' && $charge['status'] !== 'confirmed')
                                                    <th class="px-2 py-1 text-left">Acciones</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($charge['timeouts'] ?? [] as $ti => $timeout)
                                                <tr class="border-b last:border-b-0">
                                                    <td class="px-1 py-2">{{ $timeout['type'] }}</td>
                                                    <td class="px-1 py-2 text-center">{{ round($timeout['hours'], 2) }}</td>
                                                    @if($process_selected->status !== 'finished' && $charge['status'] !== 'confirmed' && in_array(Auth::user()->user_type, ['1', '4', '6']))
                                                        <td class="px-1 py-2">
                                                            <x-buttondelete class="!px-2 !py-1 text-xs" wire:click="removeDeadtime({{ $ci }}, {{ $ti }})">
                                                                Eliminar
                                                            </x-buttondelete>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Sin tiempos muertos registrados.</p>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 italic">No hay cargas registradas para este proceso.</p>
            @endforelse
        </div>

        {{-- Acciones --}}
        <div class="flex flex-row space-x-3 justify-end">
            @if($process_selected->status !== 'finished')
                @if((in_array(Auth::user()->user_type, ['1', '3', '6'])))
                <x-secondary-button class="w-fit h-fit" onclick="confirmUpdateProcess()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                        <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                        <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                    </svg>
                    Guardar datos
                </x-secondary-button>
                @endif

                @if($piezasRestantes == 0 && collect($chargesList)->isNotEmpty() && collect($chargesList)->every(fn($c) => $c['status'] === 'confirmed') && (in_array(Auth::user()->user_type, ['1', '4', '6'])))
                    <x-button-primary class="w-fit h-fit" onclick="confirmFinishProcess()">
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                        </svg> -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                        </svg>

                        Terminar proceso
                    </x-button-primary>
                @endif
            @endif
        </div>

    </div>
</div>

@push('js')
<script>
    function initLineSelect() {
        if (typeof SlimSelect === 'undefined') return;
        if (window.lineSlim) window.lineSlim.destroy();
        const el = document.getElementById('id_line');
        if (!el) return;
        window.lineSlim = new SlimSelect({
            select: el,
            settings: { placeholderText: 'Seleccionar...', searchPlaceholder: 'Buscar', searchText: 'No se encontraron resultados' },
            events: { afterChange: () => el.dispatchEvent(new Event('change', { bubbles: true })) },
        });
    }

    window.refreshSlimSelects = ((previous) => {
        return function () {
            if (typeof previous === 'function') previous();
            initLineSelect();
        };
    })(window.refreshSlimSelects);

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
        }).then((result) => { if (result.isConfirmed) @this.call('updateProcessData'); });
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
        }).then((result) => { if (result.isConfirmed) @this.call('finishProcess'); });
    }

    function confirmLiberateCharge(index) {
        Swal.fire({
            title: '¿Liberar esta carga?',
            text: 'La carga pasará a estado Liberada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Sí, liberar',
            cancelButtonText: 'Cancelar',
        }).then((result) => { if (result.isConfirmed) @this.call('liberateCharge', index); });
    }

    function confirmConfirmCharge(index) {
        Swal.fire({
            title: '¿Confirmar conteo de esta carga?',
            text: 'La carga pasará a estado Confirmada y no se podrán modificar sus tiempos muertos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => { if (result.isConfirmed) @this.call('confirmCharge', index); });
    }

    function confirmDeleteCharge(index) {
        Swal.fire({
            title: '¿Eliminar esta carga?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => { if (result.isConfirmed) @this.call('deleteCharge', index); });
    }
</script>
@endpush
