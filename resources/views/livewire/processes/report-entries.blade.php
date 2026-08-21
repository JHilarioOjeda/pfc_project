<div class="containerpric">
    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Control de entradas y salidas</p>

    <div class="bg-white rounded-lg shadow-lg my-3 p-3 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4">
            <div class="w-full">
                <p class="text-secondarycolor">Desde:</p>
                <input wire:model.live="fromDate" type="date" class="inputcatalogues w-full">
            </div>
            <div class="w-full">
                <p class="text-secondarycolor">Hasta:</p>
                <input wire:model.live="toDate" type="date" class="inputcatalogues w-full">
            </div>
            <div class="w-full xl:col-span-1">
                <p class="text-secondarycolor">Buscar:</p>
                <x-search-input wireModel="search" placeholder="Tarima, cliente, NP, OC u OF..." />
            </div>
            <div class="w-full flex items-end">
                <x-button-primary class="w-full justify-center !py-2 !text-sm !bg-[#217346] hover:!bg-[#1a5c38] focus:!ring-[#217346]" wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv" title="Descargar el reporte del rango seleccionado en formato CSV">
                    <svg class="size-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 16a1 1 0 0 1-1-1V5.414L8.707 7.707a1 1 0 0 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0l4 4a1 1 0 1 1-1.414 1.414L13 5.414V15a1 1 0 0 1-1 1Z"/>
                        <path d="M4 14a1 1 0 0 1 1 1v3h14v-3a1 1 0 1 1 2 0v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1Z"/>
                    </svg>
                    <span wire:loading.remove wire:target="exportCsv">Descargar CSV</span>
                    <span wire:loading wire:target="exportCsv">Generando...</span>
                </x-button-primary>
            </div>
        </div>

        <p class="text-sm text-gray-500">
            {{ $totalRows }} registro(s) encontrado(s) del {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}.
        </p>

        <div class="relative overflow-x-auto rounded-lg border border-gray-200">
            <table class="table table-hover w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-200 font-semibold whitespace-nowrap">
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['folio'] }}</th>
                        <th class="px-2 py-2">{{ \App\Livewire\Processes\ReportEntries::HEADERS['estatus'] }}</th>
                        <th class="px-2 py-2">{{ \App\Livewire\Processes\ReportEntries::HEADERS['fecha_recepcion'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['hora_recepcion'] }}</th>
                        <th class="px-2 py-2">{{ \App\Livewire\Processes\ReportEntries::HEADERS['cliente'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['oc'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['of'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['urgencia'] }}</th>
                        <th class="px-2 py-2">{{ \App\Livewire\Processes\ReportEntries::HEADERS['tarima'] }}</th>
                        <th class="px-2 py-2">{{ \App\Livewire\Processes\ReportEntries::HEADERS['numero_parte'] }}</th>
                        <th class="px-2 py-2 text-center">{{ \App\Livewire\Processes\ReportEntries::HEADERS['cantidad_pzas'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['acabado'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['personal_recibe'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['fecha_produccion'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['personal_libera_pt'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['reporte_calidad'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['fecha_compromiso'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['fecha_salida'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['numero_remision'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['numero_factura'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['personal_entrega'] }}</th>
                        <th class="px-2 py-2 hidden md:table-cell">{{ \App\Livewire\Processes\ReportEntries::HEADERS['observaciones'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t border-gray-200">
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['folio'] ?? '—' }}</td>
                            <td class="px-2 py-2">
                                @php
                                    $badgeClasses = match($row['estatus']) {
                                        'Terminado' => 'bg-green-100 text-green-700',
                                        'En proceso' => 'bg-yellow-100 text-yellow-700',
                                        'Pendiente' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-lg font-semibold whitespace-nowrap {{ $badgeClasses }}">{{ $row['estatus'] }}</span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $row['fecha_recepcion'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell whitespace-nowrap">{{ $row['hora_recepcion'] ?? '—' }}</td>
                            <td class="px-2 py-2">{{ $row['cliente'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['oc'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['of'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['urgencia'] ?? '—' }}</td>
                            <td class="px-2 py-2">#{{ $row['tarima'] ?? '—' }}</td>
                            <td class="px-2 py-2">{{ $row['numero_parte'] ?? '—' }}</td>
                            <td class="px-2 py-2 text-center">{{ $row['cantidad_pzas'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['acabado'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['personal_recibe'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell whitespace-nowrap">{{ $row['fecha_produccion'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['personal_libera_pt'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['reporte_calidad'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['fecha_compromiso'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['fecha_salida'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['numero_remision'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['numero_factura'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell text-gray-400">{{ $row['personal_entrega'] ?? '—' }}</td>
                            <td class="px-2 py-2 hidden md:table-cell">{{ $row['observaciones'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="22" class="px-2 py-4 text-center text-sm text-gray-500">No se encontraron entradas para el rango de fechas y filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $paginated->links() }}

        <p class="italic text-xs text-gray-500">
            Las columnas en gris (Urgencia, Fecha compromiso, Fecha de salida, N° de remisión, N° factura y Personal que entrega) corresponden al formato original de almacén y no se capturan actualmente en el sistema, por lo que se muestran vacías. La columna "Folio" muestra el ID interno del proceso en el sistema.
        </p>
    </div>
</div>
