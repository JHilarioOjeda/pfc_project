<x-app-layout>
    <!-- BOTONES SUPERIORES -->
    <div class="mx-auto text-sm overflow-y-hidden flex mt-4 no-print">
        <x-secondary-hyperlink href="{{ route('reporttarimas.detail', optional($tarima)->id) }}" class="!px-2 !py-1 !text-sm flex items-center ml-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                <path fill-rule="evenodd" d="M9.53 4.47a.75.75 0 0 1 0 1.06L5.81 9.25H20a.75.75 0 0 1 0 1.5H5.81l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
            </svg>
            Regresar
        </x-secondary-hyperlink>
        <x-secondary-button type="button" class="ml-auto !px-3 !py-1 !text-sm flex items-center mr-2" onclick="saveMeasurementTarimasPdf()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
                <path fill-rule="evenodd" d="M6.75 12a3.75 3.75 0 0 0-3.75 3.75v1.5A2.25 2.25 0 0 0 5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25v-1.5A3.75 3.75 0 0 0 17.25 12H6.75Zm1.5 2.25a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
            </svg>
            Guardar PDF
        </x-secondary-button>

        <x-button-primary type="button" class="mr-5 !px-3 !py-1 !text-sm flex items-center no-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
                <path fill-rule="evenodd" d="M6.75 12a3.75 3.75 0 0 0-3.75 3.75v1.5A2.25 2.25 0 0 0 5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25v-1.5A3.75 3.75 0 0 0 17.25 12H6.75Zm1.5 2.25a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
            </svg>
            Imprimir
        </x-button-primary>
    </div>

    <style>
        @media print {
            @page {
                size: letter;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .report-page {
                page-break-after: always;
            }

            .report-page:last-of-type {
                page-break-after: auto;
            }

            .report-page table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>

    @php
        $firstReport = $reports->first();
        $process = optional($firstReport)->proccess;
        $tarimaNp = optional($process)->tarimaNp;
        $tarimaReport = optional($tarimaNp)->tarima ?? $tarima;
        $customer = optional($tarimaReport)->customer;
        $numberPart = optional($tarimaNp)->numberPart;

        // ===== PAGINACIÓN DEL REPORTE =====
        // Igual que en processreport-format.blade.php: cada "página" es un bloque de
        // altura fija (carta, 27.94cm). Si la tabla de mediciones no cabe completa en
        // una sola hoja, se reparte en varias páginas de solo tabla y se agrega una
        // página final exclusiva para la nota de caducidad y la firma.
        //
        // Los valores de capacidad son aproximados (filas que caben en el alto
        // disponible de una hoja carta) y pueden ajustarse si cambia el tamaño de
        // fuente o el padding de las celdas.
        $maxRowsSinglePage   = 18; // filas que caben junto con datos generales + nota + firma en una sola página
        $maxRowsPerTablePage = 26; // filas que caben en una página dedicada solo a la tabla
        $generalReserve      = 8;  // filas de tabla "equivalentes" que ocupan encabezado/fecha/folio/datos generales en la primera página

        $totalRows = $reports->sum(fn ($r) => max(1, $r->observations->count()));

        $reportPages = collect();

        if ($reports->isEmpty() || $totalRows <= $maxRowsSinglePage) {
            $reportPages->push([
                'reports'     => $reports,
                'showGeneral' => true,
                'showTable'   => true,
                'showClosing' => true,
            ]);
        } else {
            $chunk       = collect();
            $chunkRows   = 0;
            $isFirstPage = true;
            $capacity    = max(5, $maxRowsPerTablePage - $generalReserve);

            foreach ($reports as $report) {
                $rowCount = max(1, $report->observations->count());

                if ($chunkRows > 0 && ($chunkRows + $rowCount) > $capacity) {
                    $reportPages->push([
                        'reports'     => $chunk,
                        'showGeneral' => $isFirstPage,
                        'showTable'   => true,
                        'showClosing' => false,
                    ]);
                    $chunk       = collect();
                    $chunkRows   = 0;
                    $isFirstPage = false;
                    $capacity    = $maxRowsPerTablePage;
                }

                $chunk->push($report);
                $chunkRows += $rowCount;
            }

            if ($chunk->isNotEmpty()) {
                $reportPages->push([
                    'reports'     => $chunk,
                    'showGeneral' => $isFirstPage,
                    'showTable'   => true,
                    'showClosing' => false,
                ]);
            }

            // Página final exclusiva para la nota de caducidad y la firma.
            $reportPages->push([
                'reports'     => collect(),
                'showGeneral' => false,
                'showTable'   => false,
                'showClosing' => true,
            ]);
        }

        $totalReportPages = $reportPages->count();
    @endphp

    <div class="w-full overflow-x-auto print-area">
        @foreach ($reportPages as $pageIndex => $page)
            <div class="bg-white rounded-lg mx-auto text-sm flex flex-col mt-4 report-page" style="min-height: 27.94cm; width: 21.59cm; padding: 1cm;">
                <!-- ENCABEZADO -->
                <div class="flex items-start justify-between border-b border-black pb-2">
                    <div class="flex items-center space-x-2 w-2/12">
                        <img src="/imgs/logos/principallogo.jpg" class="w-full" alt="Logo">
                    </div>

                    <div class="flex-1 text-center w-8/12 leading-tight">
                        <p class="font-bold text-base tracking-wide">RECUBRIMIENTOS INDUSTRIALES PFC S.A. DE C.V.</p>
                        <p class="font-semibold text-sm tracking-wide mt-1">REPORTE DE MEDICIONES</p>
                    </div>

                    <div class="text-[10px] border border-black px-2 py-1 leading-tight w-2/12">
                        <p><span class="font-semibold">Punto normativo:</span> 9.1.3 Análisis y Evaluación.</p>
                        <p><span class="font-semibold">Código:</span> FO-CA-CA-24</p>
                        <p><span class="font-semibold">Revisión:</span> 01</p>
                        @if($totalReportPages > 1)
                            <p><span class="font-semibold">Página:</span> {{ $pageIndex + 1 }} de {{ $totalReportPages }}</p>
                        @endif
                    </div>
                </div>

                @if($page['showGeneral'])
                    <!-- FECHA / FOLIO -->
                    <div class="flex justify-end mt-2">
                        <table class="text-[10px] border border-black border-collapse">
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold">FECHA</td>
                                <td class="border border-black px-2 py-1 text-center">{{ optional(optional($firstReport)->register_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold">FOLIO</td>
                                <td class="border border-black px-2 py-1 text-center">{{ optional($firstReport)->folio }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- DATOS GENERALES -->
                    <div class="w-full mt-4">
                        <table class="w-full border-collapse border border-black text-[11px]">
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold w-2/12">Cliente / Razón social</td>
                                <td class="border border-black px-2 py-1" colspan="3">{{ optional($customer)->name }}</td>
                            </tr>
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold">Unidades de medición</td>
                                <td class="border border-black px-2 py-1">Micras</td>
                                <td class="border border-black px-2 py-1 font-semibold">Cantidad total</td>
                                <td class="border border-black px-2 py-1">{{ $reports->count() }}</td>
                            </tr>
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold">Método</td>
                                <td class="border border-black px-2 py-1">{{ optional($firstReport)->method }}</td>
                                <td class="border border-black px-2 py-1 font-semibold">Requisito</td>
                                <td class="border border-black px-2 py-1">{{ optional($firstReport)->requirement }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                @if($page['showTable'])
                    <!-- OBSERVACIONES / CONDICIONES DEL MATERIAL -->
                    <div class="w-full mt-4">
                        <p class="font-semibold text-sm">Observaciones / Condiciones del material</p>
                        <p class="text-[11px] mb-1">TIPO DE PROCESO: {{ optional($numberPart)->process }}</p>
                        <table class="w-full border-collapse border border-black text-[11px]">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-black px-2 py-1 text-center align-middle">CÓDIGO</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle"># ORDEN</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">NUM</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">CANTIDAD</th>
                                    <th class="border border-black px-2 py-1 text-center align-middle">MEDICIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $hasObservations = false; @endphp
                                @foreach ($page['reports'] as $report)
                                    @php
                                        $processRow = $report->proccess;
                                        $tarimaNpRow = optional($processRow)->tarimaNp;
                                        $numberPartRow = optional($tarimaNpRow)->numberPart;

                                        $medCount = $report->observations->count();

                                        if ($medCount > 0) {
                                            $hasObservations = true;
                                        }
                                    @endphp

                                    <tr>
                                        <td class="border border-black px-2 py-1 text-center align-top">{{ optional($numberPartRow)->partnumber }}</td>
                                        <td class="border border-black px-2 py-1 text-center align-top">{{ optional($tarimaNpRow)->oc }}</td>
                                        <td class="border border-black px-2 py-1 text-center align-top">{{ optional($tarimaNpRow)->of }}</td>
                                        <td class="border border-black px-2 py-1 text-center align-top">{{ optional($tarimaNpRow)->quantity }}</td>
                                        <td class="border border-black px-0 py-0">
                                            <table class="w-full text-[10px] border-collapse">
                                                <thead>
                                                    <tr class="bg-gray-100">
                                                        <th class="px-1 py-0.5 text-center">No. medición</th>
                                                        <th class="px-1 py-0.5 text-center">Espesor</th>
                                                        <th class="px-1 py-0.5 text-center">Apariencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($report->observations as $index => $observation)
                                                        <tr>
                                                            <td class="px-1 py-0.5 text-center">{{ $index + 1 }}</td>
                                                            <td class="px-1 py-0.5 text-center">{{ round($observation->thickness_in_microns, 2) }}</td>
                                                            <td class="px-1 py-0.5 text-center">{{ $observation->visual_appearance }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="border border-black px-1 py-1 text-center text-gray-500">Sin mediciones</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach

                                @unless($hasObservations)
                                    <tr>
                                        <td colspan="7" class="border border-black px-2 py-4 text-center text-gray-500">Sin mediciones registradas</td>
                                    </tr>
                                @endunless
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($page['showClosing'])
                    <!-- NOTA Y CADUCIDAD -->
                    <div class="w-full mt-3 text-[10px]">
                        <p class="font-semibold">NOTA</p>
                        <p>*Se recomienda mantener el material en un lugar seco libre de humedad o líquidos (agua, soluciones, aceites, etc.) o solventes que emanen vapores.</p>
                        <p class="mt-1">Caducidad: 30 días a partir de la fecha de su tratamiento.</p>
                    </div>

                    <!-- FIRMA -->
                    <div class="w-full mt-10 flex flex-col items-center justify-end flex-1">
                        <img src="{{ asset('imgs/signs/firma_transparente_negro.png') }}" alt="Firma" class="h-16 object-contain">
                        <div class="w-1/2 border-t border-black mt-1 pt-1 text-center text-[11px]">
                            <p class="font-semibold">Erika Martínez</p>
                            <p>Nombre y firma del responsable de calidad</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
    <script>
        async function saveMeasurementTarimasPdf() {
            const element = document.querySelector('.print-area');
            if (!element || typeof html2pdf === 'undefined') {
                alert('No se pudo generar el PDF.');
                return;
            }

            const filename = `reporte-mediciones-tarima-{{ optional($tarima)->id }}.pdf`;

                const opt = {
                    margin:       0,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'letter', orientation: 'portrait' },
                pagebreak:    { mode: ['css', 'legacy'] }
            };

            try {
                const worker = html2pdf().set(opt).from(element);
                const pdfBlob = await worker.outputPdf('blob');

                const formData = new FormData();
                formData.append('file', pdfBlob, filename);
                formData.append('type', 'reporte-mediciones-tarimas');
                formData.append('filename', filename);

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('{{ route('pdf.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    body: formData
                });

                if (!response.ok) {
                    alert('Error al guardar el PDF en el servidor.');
                    return;
                }

                alert('PDF guardado correctamente en el servidor.');
            } catch (e) {
                console.error(e);
                alert('Ocurrió un error al generar el PDF.');
            }
        }
    </script>
