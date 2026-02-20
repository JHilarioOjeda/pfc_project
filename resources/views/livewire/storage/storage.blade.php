<div class="containerpric">

    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Almacén</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
            <x-primary-hyperlink href="{{ route('storage.tarima', ['id' => 0]) }}" target=" " class="my-auto ml-auto whitespace-nowrap">
                <svg class="size-6 mr-2 font-semibold" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
                    <path fill="currentColor" d="M440 328h-240v-80h240v-240h80v240h240v80h-240v240h-80v-240z"></path>
                 </svg>
                Registrar entrada
            </x-primary-hyperlink>
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-4 py-2">Tarima</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Fecha y hora de recepción</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Cliente</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Quien registro</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($tarimas) != 0)
                        @foreach ($tarimas as $tarima)
                            <tr class="border-b border-gray-200 text-sm">
                                <!-- Nombre -->
                                <td scope="row" class="px-4 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold">{{$tarima->serial_number}}</span>
                                    <!-- Datos extra en móvil -->
                                    <div class="block lg:hidden mt-2 text-gray-500 text-xs">
                                        <p class="text-secondarycolor"><span class="font-semibold">Fecha de recepción:</span> {{$tarima->register_date}}</p>
                                        <p><span class="font-semibold">Cliente:</span> {{$tarima->customer->name}}</p>
                                        <p>
                                            <span class="font-semibold">Quien registró:</span> {{$tarima->who_register->name}}
                                        </p>
                                    </div>
                                </td>
                                
                                <!-- Columnas visibles solo en pantallas grandes o más grandes -->
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$tarima->register_date}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$tarima->customer->name}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$tarima->who_register->name}}
                                </td>
                                <td class="px-4 py-2 text-center">

                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-4">No se encontraron tarimas.</td>
                        </tr>
                    @endif
                </tbody>
           </table>
           {{ $tarimas->links() }}
        </div>
    </div>

</div>