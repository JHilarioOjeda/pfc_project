<div class="containerpric">

    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Procesos</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-4 py-2">Tarima</th>
                        <th class="px-4 py-2">Número de parte</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Orden de compra</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Orden de fabricación</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Cliente</th>
                        <th class="px-4 py-2 text-center">Estatus</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($processes) != 0)
                        @foreach ($processes as $process)
                            <tr class="border-b border-gray-200 text-sm">
                                <td class="px-4 py-2">
                                    {{$process->tarimaNp->tarima->serial_number}}
                                </td>
                                <td scope="row" class="px-4 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold text-sm">{{$process->tarimaNp->numberPart->partnumber}}</span>
                                    
                                    <!-- Datos extra en móvil -->
                                    <div class="block lg:hidden mt-2 text-gray-500 text-sm">
                                        <p><span class="font-semibold">Orden de compra:</span> {{$process->tarimaNp->oc}}</p>
                                        <p><span class="font-semibold">Orden de fabricación:</span> {{$process->tarimaNp->of}}</p>
                                        <p><span class="font-semibold">Cliente:</span> {{$process->tarimaNp->tarima->customer->name}}</p>
                                    </div>
                                </td>
                                
                                <!-- Columnas visibles solo en pantallas grandes o más grandes -->
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->oc}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->of}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$process->tarimaNp->tarima->customer->name}}
                                </td>
                                @switch($process->status)
                                    @case('pending')
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-gray-200 text-gray-600">sin comenzar</span>
                                        </td>
                                        <td class="px-4 py-2 text-center space-y-3">
                                            <x-button-primary class="!px-2 !py-1 text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
                                                </svg>
                                                Comenzar proceso
                                            </x-button-primary>
                                        </td>
                                        @break
                                    @case('inprocess')
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-yellow-200 text-yellow-600">En proceso</span>
                                        </td>

                                        <td class="px-4 py-2 text-center space-y-3">
                                            <x-secondary-button class="!px-2 !py-1 text-[10px]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                </svg>
                                                Detalles
                                            </x-secondary-button>
                                        </td>
                                        @break
                                    @case('finished')
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-green-200 text-green-600">Completado</span>
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
                            <td colspan="5" class="text-center py-4">No se encontraron procesos.</td>
                        </tr>
                    @endif
                </tbody>
           </table>
           {{ $processes->links() }}
        </div>
    </div>

</div>