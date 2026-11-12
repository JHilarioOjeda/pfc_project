<div class="containerpric">

    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Almacén</p>

    @php
        $tabs = [
            'toprocess' => 'Listo para proceso',
            'todeliver' => 'Listos para entrega',
            'delivered' => 'Entregados',
        ];
        $statusBadges = [
            'inprocess'      => ['texto' => 'En proceso de entrada', 'class' => 'bg-yellow-200 text-yellow-600'],
            'finished'       => ['texto' => 'Confirmada', 'class' => 'bg-green-200 text-green-600'],
            'readytodeliver' => ['texto' => 'Listo para entrega', 'class' => 'bg-blue-200 text-blue-600'],
            'delivered'      => ['texto' => 'Entregado', 'class' => 'bg-purple-200 text-purple-600'],
        ];
        $isDeliveredTab = $tab === 'delivered';
    @endphp

    <!-- Navbar de estatus -->
    <nav class="flex flex-wrap gap-2 mt-3">
        @foreach ($tabs as $key => $label)
            <button type="button" wire:click="$set('tab', '{{ $key }}')"
                class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ $tab === $key ? 'bg-primarycolor text-white' : 'bg-white text-gray-500 shadow hover:text-white hover:bg-primarycolor' }}">
                {{ $label }}
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold {{ $tab === $key ? 'bg-white text-primarycolor' : 'bg-gray-200 text-gray-600' }}">{{ $tabCounts[$key] ?? 0 }}</span>
            </button>
        @endforeach
    </nav>

    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
            @if ($tab === 'toprocess')
            <x-primary-hyperlink href="{{ route('storage.tarima', ['id' => 0]) }}" target="" class="my-auto ml-auto whitespace-nowrap">
                <svg class="size-6 mr-2 font-semibold" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
                    <path fill="currentColor" d="M440 328h-240v-80h240v-240h80v240h240v80h-240v240h-80v-240z"></path>
                 </svg>
                Registrar entrada
            </x-primary-hyperlink>
            @endif
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-4 py-2">Tarima</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Fecha y hora de recepción</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Cliente</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Quien registro</th>
                        @if ($isDeliveredTab)
                            <th class="px-4 py-2 hidden lg:table-cell">Fecha y hora de entrega</th>
                            <th class="px-4 py-2 hidden lg:table-cell">Quien entregó</th>
                        @endif
                        <th class="px-4 py-2 hidden lg:table-cell">Estatus</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($tarimas) != 0)
                        @foreach ($tarimas as $tarima)
                            @php
                                $badge = $statusBadges[$tarima->status] ?? ['texto' => 'Desconocido', 'class' => 'bg-gray-200 text-gray-600'];
                            @endphp
                            <tr class="border-b border-gray-200 text-sm">
                                <!-- Nombre -->
                                <td scope="row" class="px-4 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold text-sm">{{$tarima->serial_number}}</span>
                                    <!-- Datos extra en móvil -->
                                    <div class="block lg:hidden mt-2 text-gray-500 text-sm">
                                        <p class="text-secondarycolor"><span class="font-semibold">Fecha de recepción:</span> {{ date('d/m/Y H:i', strtotime($tarima->register_date)) }}</p>
                                        <p><span class="font-semibold">Cliente:</span> {{$tarima->customer->name}}</p>
                                        <p>
                                            <span class="font-semibold">Quien registró:</span> {{$tarima->registeredBy->name}}
                                        </p>
                                        @if ($isDeliveredTab)
                                            <p class="text-secondarycolor"><span class="font-semibold">Fecha de entrega:</span> {{ $tarima->delivered_date ? $tarima->delivered_date->format('d/m/Y H:i') : '-' }}</p>
                                            <p><span class="font-semibold">Quien entregó:</span> {{ optional($tarima->deliveredBy)->name ?? '-' }}</p>
                                        @endif
                                        <p>
                                            <span class="font-semibold">Estatus:</span>
                                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold {{ $badge['class'] }}">{{ $badge['texto'] }}</span>
                                        </p>
                                    </div>
                                </td>

                                <!-- Columnas visibles solo en pantallas grandes o más grandes -->
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ date('d/m/Y H:i', strtotime($tarima->register_date)) }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$tarima->customer->name}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$tarima->registeredBy->name}}
                                </td>
                                @if ($isDeliveredTab)
                                    <td class="px-4 py-2 hidden lg:table-cell">
                                        {{ $tarima->delivered_date ? $tarima->delivered_date->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 hidden lg:table-cell">
                                        {{ optional($tarima->deliveredBy)->name ?? '-' }}
                                    </td>
                                @endif
                                <td class="px-4 py-2 text-center hidden lg:table-cell">
                                    <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold {{ $badge['class'] }}">{{ $badge['texto'] }}</span>
                                </td>
                                <td class="px-4 py-2 text-center space-y-3">
                                    <x-secondary-hyperlink class="!px-2 !py-1 text-xs" href="{{ route('storage.tarima', ['id' => $tarima->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                            <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                            <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                        </svg>
                                        Detalles
                                    </x-secondary-hyperlink>
                                    @if($tarima->status !== 'inprocess')
                                    <x-secondary-hyperlink class="!px-2 !py-1 text-xs" href="{{ route('storage.tarima.document', ['tarima' => $tarima->id]) }}" target="">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                            <path fill-rule="evenodd" d="M10.5 3A1.501 1.501 0 0 0 9 4.5h6A1.5 1.5 0 0 0 13.5 3h-3Zm-2.693.178A3 3 0 0 1 10.5 1.5h3a3 3 0 0 1 2.694 1.678c.497.042.992.092 1.486.15 1.497.173 2.57 1.46 2.57 2.929V19.5a3 3 0 0 1-3 3H6.75a3 3 0 0 1-3-3V6.257c0-1.47 1.073-2.756 2.57-2.93.493-.057.989-.107 1.487-.15Z" clip-rule="evenodd" />
                                        </svg>
                                        Documento
                                    </x-secondary-hyperlink>
                                    @endif
                                    @if($tarima->status === 'readytodeliver')
                                        <x-buttonact onclick="confirmDeliverTarima({{ $tarima->id }})">
                                            Marcar como entregado
                                        </x-buttonact>
                                    @endif
                                    @if(in_array($tarima->status, ['inprocess', 'finished']))
                                        <x-buttondelete onclick="confirmDeleteTarima({{ $tarima->id }})">
                                            Eliminar
                                        </x-buttondelete>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $isDeliveredTab ? 8 : 6 }}" class="text-center py-4">No se encontraron tarimas.</td>
                        </tr>
                    @endif
                </tbody>
           </table>
           {{ $tarimas->links() }}
        </div>
    </div>

    @push('js')
        <script>
            function confirmDeleteTarima(id) {
                Swal.fire({
                    title: '¿Seguro que deseas eliminar esta tarima?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deleteTarima', id);
                    }
                })
            }

            function confirmDeliverTarima(id) {
                Swal.fire({
                    title: '¿Marcar esta tarima como entregada?',
                    text: 'Se registrará la fecha y el usuario que entrega.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16A34A',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, entregar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('markAsDelivered', id);
                    }
                })
            }
        </script>
    @endpush

</div>
