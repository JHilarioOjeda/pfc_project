{{-- FORMATO 3 de reporte de medición: una hoja por reporte, con lote, fecha de
     proceso (semana) y leyenda de liberación al 5%.
     Parámetros: $reports, $customer, $folio, $backUrl, $pdfName, $pdfType --}}
<x-app-layout>
    @include('formats.measurement.partials.actions')

    @php
        $fmt = \App\Support\MeasurementReportFormat::class;
        $totalPages = max(1, $reports->count());
    @endphp

    <div class="w-full overflow-x-auto print-area">
        @foreach ($reports as $report)
            @php
                $process = $report->proccess;
                $tarimaNp = optional($process)->tarimaNp;
                $numberPart = optional($tarimaNp)->numberPart;
                $processDate = optional($process)->start_date ?? $report->register_date;
            @endphp
            <div class="bg-white rounded-lg mx-auto text-sm flex flex-col mt-4 report-page" style="min-height: 27.5cm; width: 21.59cm; padding: 1cm;">
                @include('formats.measurement.partials.header', ['format' => 3, 'pageNumber' => $loop->iteration, 'totalPages' => $totalPages])

                @include('formats.measurement.partials.date-folio', ['date' => $report->register_date, 'folio' => $folio])

                <!-- DATOS GENERALES -->
                <div class="w-full mt-3">
                    <table class="w-full border-collapse border border-black text-[10px] table-fixed">
                        <tr>
                            <td class="border border-black px-2 py-1" colspan="2"><span class="font-semibold">Cliente / Razón social:</span> {{ optional($customer)->name }}</td>
                            <td class="border border-black px-2 py-1"><span class="font-semibold">Teléfono / Celular:</span> {{ optional($customer)->telephone }}</td>
                            <td class="border border-black px-2 py-1 break-words"><span class="font-semibold">Email:</span> {{ optional($customer)->email }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-semibold">Código / Nombre de la pieza</td>
                            <td class="border border-black px-2 py-1">
                                {{ optional($numberPart)->partnumber }}
                                @if(optional($numberPart)->details)
                                    <br>{{ $numberPart->details }}
                                @endif
                            </td>
                            <td class="border border-black px-2 py-1 font-semibold">Requisito</td>
                            <td class="border border-black px-2 py-1">{{ $fmt::number($report->requirement) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1"><span class="font-semibold">Unidades de medición:</span> Micras</td>
                            <td class="border border-black px-2 py-1"><span class="font-semibold">Nº ORDEN:</span> {{ optional($tarimaNp)->oc }}</td>
                            <td class="border border-black px-2 py-1" colspan="2"><span class="font-semibold">LOTE:</span></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1"><span class="font-semibold">Cantidad:</span> {{ $fmt::pieces(optional($tarimaNp)->quantity) }}</td>
                            <td class="border border-black px-2 py-1"><span class="font-semibold">NUM OF:</span> {{ optional($tarimaNp)->of ?: 'N/A' }}</td>
                            <td class="border border-black px-2 py-1 font-semibold">Barril</td>
                            <td class="border border-black px-2 py-1">{{ $report->method === 'Barril' ? 'SI' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1" colspan="4">
                                <span class="font-semibold">Fecha de proceso:</span>
                                {{ $fmt::processWeek($processDate) }}
                                <span class="inline-block w-24 border-b border-black ml-2">&nbsp;</span> carga
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- OBSERVACIONES / CONDICIONES DEL MATERIAL -->
                <div class="w-full mt-3">
                    <table class="w-full border-collapse border border-black text-[10px]">
                        <tr>
                            <td class="border border-black px-2 py-1 font-semibold w-1/2">Observaciones / Condiciones del material</td>
                            <td class="border border-black px-2 py-1 font-semibold w-1/4">TIPO DE PROCESO:</td>
                            <td class="border border-black px-2 py-1 w-1/4">{{ optional($numberPart)->process }}</td>
                        </tr>
                    </table>
                    <table class="w-full border-collapse border border-black text-[10px] mt-1">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-black px-2 py-1 text-center align-middle w-2/12">No. de medición</th>
                                <th class="border border-black px-2 py-1 text-center align-middle w-5/12">Espesor en micras</th>
                                <th class="border border-black px-2 py-1 text-center align-middle w-5/12">Apariencia visual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report->observations as $index => $observation)
                                <tr>
                                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-black px-2 py-1 text-center">{{ $fmt::number($observation->thickness_in_microns, 2) }}</td>
                                    <td class="border border-black px-2 py-1 text-center">{{ $observation->visual_appearance }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="border border-black px-2 py-4 text-center text-gray-500">Sin mediciones registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('formats.measurement.partials.closing', ['notes' => $report->notes, 'extraText' => 'LIBERADO CON UNA INSPECCIÓN AL 5%'])
            </div>
        @endforeach
    </div>
</x-app-layout>
