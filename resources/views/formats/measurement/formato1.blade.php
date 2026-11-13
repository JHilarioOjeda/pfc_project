{{-- FORMATO 1 de reporte de medición: una sola tabla con todos los reportes
     (CÓDIGO / #ORDEN / NUM OF / CANTIDAD / NO. MEDICIÓN / ESPESOR / APARIENCIA).
     Parámetros: $reports, $customer, $folio, $backUrl, $pdfName, $pdfType --}}
<x-app-layout>
    @include('formats.measurement.partials.actions')

    @php
        $fmt = \App\Support\MeasurementReportFormat::class;
        $firstReport = $reports->first();

        $processTypes = $reports
            ->map(fn ($r) => optional(optional(optional($r->proccess)->tarimaNp)->numberPart)->process)
            ->filter()
            ->unique()
            ->implode(', ');

        // ===== PAGINACIÓN =====
        // Igual que en measurementreporttarimas-format.blade.php: cada página es un bloque
        // de alto carta. Si la tabla no cabe en una sola hoja se reparte en varias páginas
        // y se agrega una página final para la nota y la firma. Las filas de un mismo
        // reporte (rowspan) nunca se separan entre páginas.
        $maxRowsSinglePage   = 20; // filas que caben junto con datos generales + nota + firma
        $maxRowsPerTablePage = 34; // filas que caben en una página dedicada solo a la tabla
        $generalReserve      = 8;  // filas equivalentes que ocupan fecha/folio/datos generales

        $rowCount  = fn ($r) => max(1, $r->observations->count());
        $totalRows = $reports->sum($rowCount);

        $reportPages = collect();

        if ($reports->isEmpty() || $totalRows <= $maxRowsSinglePage) {
            $reportPages->push(['reports' => $reports, 'showGeneral' => true, 'showTable' => true, 'showClosing' => true]);
        } else {
            $chunk       = collect();
            $chunkRows   = 0;
            $isFirstPage = true;
            $capacity    = $maxRowsPerTablePage - $generalReserve;

            foreach ($reports as $report) {
                $rows = $rowCount($report);

                if ($chunkRows > 0 && ($chunkRows + $rows) > $capacity) {
                    $reportPages->push(['reports' => $chunk, 'showGeneral' => $isFirstPage, 'showTable' => true, 'showClosing' => false]);
                    $chunk       = collect();
                    $chunkRows   = 0;
                    $isFirstPage = false;
                    $capacity    = $maxRowsPerTablePage;
                }

                $chunk->push($report);
                $chunkRows += $rows;
            }

            if ($chunk->isNotEmpty()) {
                $reportPages->push(['reports' => $chunk, 'showGeneral' => $isFirstPage, 'showTable' => true, 'showClosing' => false]);
            }

            $reportPages->push(['reports' => collect(), 'showGeneral' => false, 'showTable' => false, 'showClosing' => true]);
        }

        $totalPages = $reportPages->count();
    @endphp

    <div class="w-full overflow-x-auto print-area">
        @foreach ($reportPages as $pageIndex => $page)
            <div class="bg-white rounded-lg mx-auto text-sm flex flex-col mt-4 report-page" style="min-height: 27.5cm; width: 21.59cm; padding: 1cm;">
                @include('formats.measurement.partials.header', ['format' => 1, 'pageNumber' => $pageIndex + 1, 'totalPages' => $totalPages])

                @if($page['showGeneral'])
                    @include('formats.measurement.partials.date-folio', ['date' => optional($firstReport)->register_date, 'folio' => $folio])

                    <!-- DATOS GENERALES -->
                    <div class="w-full mt-3">
                        <table class="w-full border-collapse border border-black text-[10px]">
                            <tr>
                                <td class="border border-black px-2 py-1 w-1/2" colspan="2"><span class="font-semibold">Cliente / Razón social:</span> {{ optional($customer)->name }}</td>
                                <td class="border border-black px-2 py-1 w-1/4"><span class="font-semibold">Teléfono / Celular:</span> {{ optional($customer)->telephone }}</td>
                                <td class="border border-black px-2 py-1 w-1/4"><span class="font-semibold">Email:</span> {{ optional($customer)->email ?: '-----' }}</td>
                            </tr>
                            <tr>
                                <td class="border border-black px-2 py-1"><span class="font-semibold">Unidades de medición:</span> Micras</td>
                                <td class="border border-black px-2 py-1"><span class="font-semibold">Colgado:</span> {{ optional($firstReport)->method === 'Colgado' ? 'SI' : '' }}</td>
                                <td class="border border-black px-2 py-1 font-semibold">Requisito</td>
                                <td class="border border-black px-2 py-1">{{ $fmt::number(optional($firstReport)->requirement) }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                @if($page['showTable'])
                    <!-- OBSERVACIONES / CONDICIONES DEL MATERIAL -->
                    <div class="w-full mt-3">
                        <table class="w-full border-collapse border border-black text-[10px]">
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold w-1/2">Observaciones / Condiciones del material</td>
                                <td class="border border-black px-2 py-1 font-semibold w-1/4">TIPO DE PROCESO:</td>
                                <td class="border border-black px-2 py-1 w-1/4">{{ $processTypes }}</td>
                            </tr>
                        </table>
                        <table class="w-full border-collapse border border-black text-[10px] mt-1">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-black px-2 py-1 text-center align-middle">CÓDIGO</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">#ORDEN</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">NUM OF</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">CANTIDAD</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">NO. MEDICIÓN</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">ESPESOR EN MICRAS</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">APARIENCIA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($page['reports'] as $report)
                                    @php
                                        $tarimaNpRow = optional($report->proccess)->tarimaNp;
                                        $numberPartRow = optional($tarimaNpRow)->numberPart;
                                        $observations = $report->observations->values();
                                        $span = max(1, $observations->count());
                                    @endphp
                                    @for ($i = 0; $i < $span; $i++)
                                        @php $observation = $observations->get($i); @endphp
                                        <tr>
                                            @if ($i === 0)
                                                <td class="border border-black px-2 py-1 text-center align-middle" rowspan="{{ $span }}">{{ optional($numberPartRow)->partnumber }}</td>
                                                <td class="border border-black px-2 py-1 text-center align-middle" rowspan="{{ $span }}">{{ optional($tarimaNpRow)->oc }}</td>
                                                <td class="border border-black px-2 py-1 text-center align-middle" rowspan="{{ $span }}">{{ optional($tarimaNpRow)->of }}</td>
                                                <td class="border border-black px-2 py-1 text-center align-middle" rowspan="{{ $span }}">{{ $fmt::number(optional($tarimaNpRow)->quantity, 2) }}</td>
                                            @endif
                                            @if ($observation)
                                                <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                                                <td class="border border-black px-2 py-1 text-center">{{ $fmt::number($observation->thickness_in_microns, 2) }}</td>
                                                <td class="border border-black px-2 py-1 text-center">{{ $observation->visual_appearance }}</td>
                                            @else
                                                <td colspan="3" class="border border-black px-2 py-1 text-center text-gray-500">Sin mediciones</td>
                                            @endif
                                        </tr>
                                    @endfor
                                @empty
                                    <tr>
                                        <td colspan="7" class="border border-black px-2 py-4 text-center text-gray-500">Sin mediciones registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($page['showClosing'])
                    @include('formats.measurement.partials.closing', ['notes' => optional($firstReport)->notes, 'extraText' => null])
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
