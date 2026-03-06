<x-app-layout>

    <div class="no-print flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-secondarycolor">Impresión de reportes de medición</h1>
        <div class="space-x-2">
            <x-secondary-hyperlink href="{{ url()->previous() }}">Cancelar</x-secondary-hyperlink>
            <x-button-primary onclick="window.print()">Imprimir</x-button-primary>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: letter;
                margin: 1cm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="space-y-10">
        @foreach ($reports as $report)
            <div class="report-page break-after-page border border-gray-300 rounded-lg p-6 text-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-secondarycolor mb-2">Reporte de medición</h2>
                    <p><span class="font-semibold">Tarima:</span> #{{ $tarima->serial_number ?? 'N/A' }}</p>
                    <p><span class="font-semibold">Cliente:</span> {{ $tarima->customer->name ?? 'N/A' }}</p>
                    <p><span class="font-semibold">Fecha de recepción:</span> {{ isset($tarima->register_date) ? date('d/m/Y', strtotime($tarima->register_date)) : 'N/A' }}</p>
                </div>

                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><span class="font-semibold">Número de parte:</span> {{ optional(optional(optional($report->proccess)->tarimaNp)->numberPart)->partnumber ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Proceso / acabado:</span> {{ optional(optional(optional($report->proccess)->tarimaNp)->numberPart)->process ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Orden de compra:</span> {{ optional(optional($report->proccess)->tarimaNp)->oc ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Orden de fabricación:</span> {{ optional(optional($report->proccess)->tarimaNp)->of ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p><span class="font-semibold">Método:</span> {{ $report->method ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Requisito:</span> {{ $report->requirement ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Fecha de reporte:</span> {{ $report->register_date ? $report->register_date->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="font-semibold text-primarycolor mb-2">Mediciones (observaciones)</p>
                    @if ($report->observations->count() > 0)
                        <table class="w-full text-xs border border-gray-300">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="border border-gray-300 px-2 py-1 text-left">#</th>
                                    <th class="border border-gray-300 px-2 py-1 text-center">Espesor (micras)</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Apariencia visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report->observations as $index => $obs)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1">{{ $index + 1 }}</td>
                                        <td class="border border-gray-300 px-2 py-1 text-center">{{ $obs->thickness_in_microns }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ $obs->visual_appearance }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-xs text-gray-500">No hay observaciones registradas para este reporte.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</x-app-layout>
