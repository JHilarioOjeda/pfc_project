<div class="containerpric">

    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Procesos</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex flex-col md:flex-row lg:flex-row flex-wrap gap-3 md:items-center">
            <x-search-input class="w-full md:flex-1 lg:w-1/3 min-w-0" wireModel="search" placeholder="Buscar..." />

            <select wire:model.live="filterProcess" class="inputcatalogues w-full md:flex-1 lg:w-1/3 min-w-0">
                <option value="">Todos los procesos de NP</option>
                @foreach ($processOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>

            <input type="text" wire:model.live.debounce.300ms="filterTarima" placeholder="Número de tarima..." class="inputcatalogues w-full md:flex-1 lg:w-1/3 min-w-0">
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-1 py-2 text-center">ID Proceso</th>
                        <th class="px-1 py-2 hidden lg:table-cell"># Tarima</th>
                        <th class="px-3 py-2">Número de parte</th>
                        <th class="px-3 py-2 hidden lg:table-cell">Proceso</th>
                        {{-- <th class="px-1 py-2 hidden lg:table-cell">Orden de compra</th>
                        <th class="px-1 py-2 hidden lg:table-cell">Orden de fabricación</th> --}}
                        <th class="px-1 py-2 hidden lg:table-cell">Cliente</th>
                        <th class="px-1 py-2 hidden lg:table-cell">Quien realizo</th>
                        <th class="px-1 py-2 hidden lg:table-cell">Fecha de inicio</th>
                        <th class="px-1 py-2 text-center">Estatus</th>
                        <th class="px-1 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($processes) != 0)
                        @foreach ($processes as $process)
                            <tr class="border-b border-gray-200 text-sm">
                                <td class="px-1 py-2 text-center">
                                    {{$process->id}}
                                </td>
                                <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->tarima->serial_number}}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold text-sm">{{$process->tarimaNp->numberPart->partnumber}}</span>
                                    
                                    <!-- Datos extra en móvil -->
                                    <div class="block lg:hidden mt-2 text-gray-500 text-sm">
                                        <p><span class="font-semibold text-black"># Tarima:</span> {{$process->tarimaNp->tarima->serial_number}}</p>
                                        <p><span class="font-semibold">Proceso:</span> {{$process->tarimaNp->numberPart->process}}</p>
                                        {{-- <p><span class="font-semibold">Orden de compra:</span> {{$process->tarimaNp->oc}}</p>
                                        <p><span class="font-semibold">Orden de fabricación:</span> {{$process->tarimaNp->of}}</p> --}}
                                        <p><span class="font-semibold">Cliente:</span> {{$process->tarimaNp->tarima->customer->name}}</p>
                                        <p><span class="font-bold">Quien realizo:</span> {{$process->whomade->name ?? 'N/A'}}</p>
                                        <p><span class="font-bold">Fecha de inicio:</span> {{$process->start_date ? $process->start_date->format('d/m/Y') : 'N/A'}}</p>
                                    </div>
                                </td>

                                <!-- Columnas visibles solo en pantallas grandes o más grandes -->
                                <td class="px-3 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->numberPart->process}}
                                </td>
                                {{-- <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->oc}}
                                </td>
                                <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->of}}
                                </td> --}}
                                <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->tarima->customer->name}}
                                </td>
                                <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->whomade->name ?? 'N/A'}}
                                </td>
                                <td class="px-1 py-2 hidden lg:table-cell">
                                    {{$process->start_date ? $process->start_date->format('d/m/Y') : 'N/A'}}
                                </td>
                                @switch($process->status)
                                    @case('pending')
                                        <td class="px-2 py-2 text-center">
                                            <span class="px-1 py-1 rounded-lg text-[9px] uppercase font-semibold bg-gray-200 text-gray-600">sin comenzar</span>
                                        </td>
                                        <td class="px-2 py-2 text-right space-y-3">
                                            @if(in_array(Auth::user()->user_type, ['1', '3', '6']))
                                            <x-button-primary onclick="startProcess({{$process->id}})" class="!px-2 !py-1 text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3 mr-1">
                                                    <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
                                                </svg>

                                                Comenzar proceso
                                            </x-button-primary>
                                            @endif
                                        </td>
                                        @break
                                    @case('inprocess')
                                        <td class="px-2 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-yellow-200 text-yellow-600">En proceso</span>
                                        </td>

                                        <td class="px-2 py-2 text-right space-y-3">
                                            <x-secondary-hyperlink href="{{ route('processes.process', $process->id) }}" target="" class="!px-2 !py-1 text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                </svg>
                                                Detalles
                                            </x-secondary-hyperlink>
                                        </td>
                                        @break
                                    @case('finished')
                                        <td class="px-2 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-green-200 text-green-600">Completado</span>
                                        </td>

                                        <td class="px-2 py-2 text-right space-y-3 space-x-3">
                                            <x-secondary-hyperlink href="{{ route('processes.process', $process->id) }}" target="" class="!px-2 !py-1 text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                </svg>
                                                Detalles
                                            </x-secondary-hyperlink>

                                            @if(in_array(Auth::user()->user_type, ['1', '5', '6']))
                                            <x-secondary-hyperlink href="{{ route('measurementreports', $process->id) }}" target="" class="!px-2 !py-1 text-[9.9px] lg:text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                                    <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875ZM9.75 17.25a.75.75 0 0 0-1.5 0V18a.75.75 0 0 0 1.5 0v-.75Zm2.25-3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3a.75.75 0 0 1 .75-.75Zm3.75-1.5a.75.75 0 0 0-1.5 0V18a.75.75 0 0 0 1.5 0v-5.25Z" clip-rule="evenodd" />
                                                    <path d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
                                                </svg>

                                                Reporte de medición
                                            </x-secondary-hyperlink>
                                            @endif
                                        </td>
                                        @break
                                    @default
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase old bg-gray-100 text-gray-800">{{$process->status}}</span>
                                        </td>
                                        <td class="px-4 py-2 text-center space-y-3">
                                            
                                        </td>
                                        
                                @endswitch
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4">No se encontraron procesos.</td>
                        </tr>
                    @endif
                </tbody>
           </table>
            {{ $processes->links() }}
        </div>
    </div>

</div>

@push('js')
<script>
    function startProcess(idprocess){
        Swal.fire({
            title: '¿Estás seguro?',
            html: "Al comenzar el proceso, <span style='font-weight: bold;'>SE CONFIRMARÁ EL CONTEO DE PIEZAS</span>, y no se podrá modificar la cantidad de piezas a procesar. Además, el proceso se marcará como 'En proceso' y no se podrá eliminar. Asegúrate de que toda la información sea correcta antes de continuar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Sí, comenzar proceso',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    {
                        title: '¡Comenzado!',
                        text: 'El proceso ha comenzado.',
                        icon: 'success',
                        showConfirmButton: false,
                    }
                )

                window.location.href = '/process/' + idprocess;
            }
        });
    }
</script>
@endpush