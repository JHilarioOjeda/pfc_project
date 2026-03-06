<div class="containerpric">

    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Tarimas con reporte de medición</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar por serie o cliente..." />
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-3 py-2">Número de tarima</th>
                        <th class="px-3 py-2">Cliente</th>
                        <th class="px-3 py-2 text-center">Fecha de recepción</th>
                        <th class="px-3 py-2 text-center"># Procesos terminados con reporte</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($tarimas->count() > 0)
                        @foreach ($tarimas as $tarima)
                            <tr class="border-b border-gray-200 text-sm">
                                <td class="px-3 py-2">
                                    #{{ $tarima->serial_number }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ optional($tarima->customer)->name }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ date('d/m/Y', strtotime($tarima->reception_date)) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-semibold bg-green-100 text-green-700">
                                        {{ $tarima->finished_processes_with_report_count }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <x-secondary-hyperlink href="{{ route('reporttarimas.detail', $tarima->id) }}" class="!px-2 !py-1 text-[10px]">
                                        Ver detalles
                                    </x-secondary-hyperlink>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-4">No se encontraron tarimas con procesos terminados y reporte de medición.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{ $tarimas->links() }}
        </div>
    </div>

</div>
