<x-app-layout>
    <!-- BOTONES SUPERIORES -->
    <div class="mx-auto text-sm overflow-y-hidden flex mt-4 no-print">
        <x-secondary-hyperlink href="/reportprocesses" class="!px-2 !py-1 !text-sm flex items-center ml-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                <path fill-rule="evenodd" d="M9.53 4.47a.75.75 0 0 1 0 1.06L5.81 9.25H20a.75.75 0 0 1 0 1.5H5.81l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
            </svg>
            Regresar
        </x-secondary-hyperlink>
        <x-secondary-button type="button" class="ml-auto !px-3 !py-1 !text-sm flex items-center mr-2" onclick="saveProcessReportPdf()">
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
        // ===== PAGINACIÓN DEL REPORTE =====
        // Cada "página" es un bloque de altura fija (carta, 27.94cm). Si la tabla de
        // procesos no cabe completa en una sola hoja, se reparte en varias páginas de
        // solo tabla y se agrega una página final exclusiva para "Causas de tiempo
        // muerto" y la firma, para evitar que el contenido se recorte por el
        // overflow-y-hidden de cada página.
        //
        // Los valores de capacidad son aproximados (filas de tabla que caben en el
        // alto disponible de una hoja carta con 1cm de margen) y pueden ajustarse
        // si cambia el tamaño de fuente o el padding de las celdas.
        $maxRowsSinglePage   = 20; // filas que caben junto con checklist + causas + firma en una sola página
        $maxRowsPerTablePage = 26; // filas que caben en una página dedicada solo a la tabla
        $checklistReserve    = 9;  // filas de tabla "equivalentes" que ocupa el checklist en la primera página

        $totalRows = $processes->sum(fn ($p) => max(1, $p->charges->count()));

        $reportPages = collect();

        if ($processes->isEmpty() || $totalRows <= $maxRowsSinglePage) {
            $reportPages->push([
                'processes'     => $processes,
                'showChecklist' => true,
                'showTable'     => true,
                'showClosing'   => true,
            ]);
        } else {
            $chunk       = collect();
            $chunkRows   = 0;
            $isFirstPage = true;
            $capacity    = max(5, $maxRowsPerTablePage - $checklistReserve);

            foreach ($processes as $process) {
                $rowCount = max(1, $process->charges->count());

                if ($chunkRows > 0 && ($chunkRows + $rowCount) > $capacity) {
                    $reportPages->push([
                        'processes'     => $chunk,
                        'showChecklist' => $isFirstPage,
                        'showTable'     => true,
                        'showClosing'   => false,
                    ]);
                    $chunk       = collect();
                    $chunkRows   = 0;
                    $isFirstPage = false;
                    $capacity    = $maxRowsPerTablePage;
                }

                $chunk->push($process);
                $chunkRows += $rowCount;
            }

            if ($chunk->isNotEmpty()) {
                $reportPages->push([
                    'processes'     => $chunk,
                    'showChecklist' => $isFirstPage,
                    'showTable'     => true,
                    'showClosing'   => false,
                ]);
            }

            // Página final exclusiva para causas de tiempo muerto y firma.
            $reportPages->push([
                'processes'     => collect(),
                'showChecklist' => false,
                'showTable'     => false,
                'showClosing'   => true,
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
                        <p class="font-semibold text-sm tracking-wide mt-1">CONTROL DE PRODUCCIÓN - REPORTE DIARIO</p>
                    </div>

                    <div class="text-[10px] border border-black px-2 py-1 leading-tight w-2/12">
                        <p><span class="font-semibold">Código:</span> FO-PR-PR-01</p>
                        <p><span class="font-semibold">Revisión:</span> 01</p>
                        <p><span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse('21-10-25')->format('d/m/Y') }}</p>
                        @if($totalReportPages > 1)
                            <p><span class="font-semibold">Página:</span> {{ $pageIndex + 1 }} de {{ $totalReportPages }}</p>
                        @endif
                    </div>
                </div>

                @if($page['showChecklist'])
                    <!-- CHECKLIST DE ARRANQUE -->
                    <div class="w-full mt-4">
                        <p class="font-semibold text-sm mb-1">Checklist de arranque del día</p>
                        @if($checklist)
                            <table class="w-full border-collapse border border-black text-[11px]">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-black px-2 py-1 text-left">Pregunta</th>
                                        <th class="border border-black px-2 py-1 text-center">Sí</th>
                                        <th class="border border-black px-2 py-1 text-center">No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ((array) $checklist->questions as $question)
                                        @php
                                            $answer = $question['answer'] ?? null;
                                        @endphp
                                        <tr>
                                            <td class="border border-black px-2 py-1">{{ $question['label'] ?? $question['key'] ?? '' }}</td>
                                            <td class="border border-black px-2 py-1 text-center">@if($answer === true) X @endif</td>
                                            <td class="border border-black px-2 py-1 text-center">@if($answer === false) X @endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-[11px] text-gray-600 italic">No se encontró checklist capturado para esta fecha y líder.</p>
                        @endif
                    </div>
                @endif

                @if($page['showTable'])
                    <!-- PLAN Y REPORTE DE PRODUCCIÓN -->
                    <div class="w-full mt-4">
                        <p class="font-semibold text-sm mb-1">Plan y reporte de producción</p>
                        @if($pageIndex === 0)
                            @php
                                $firstProcess = $processes->first();
                            @endphp
                            <div class="py-2">
                                <div class="flex space-x-4">
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Líder de proceso:</span> {{ $leader->name }}</p>
                                </div>
                                <div class="flex space-x-4">
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Línea:</span> {{ optional(optional($firstProcess)->line)->name ?? 'N/A' }}</p>
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Operador(es):</span> {{ optional($firstProcess)->operator_name ?? 'N/A' }}</p>
                                </div>
                                <div class="flex space-x-4">
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Piezas totales trabajadas:</span> {{ round($totalPieces, 2) }}</p>
                                    <p class="text-[11px] mb-1"><span class="font-semibold">Decímetros totales trabajados:</span> {{ round($totalDecimeters, 4) }}</p>
                                </div>
                            </div>
                        @endif
                        <table class="w-full border-collapse border border-black text-[10px]">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-black px-1 py-1 text-center">ID Proceso</th>
                                    <th class="border border-black px-1 py-1 text-center">Carga</th>
                                    <th class="border border-black px-1 py-1 text-center">Tarima</th>
                                    <th class="border border-black px-1 py-1 text-center">Cliente</th>
                                    <th class="border border-black px-1 py-1 text-center">OF</th>
                                    <th class="border border-black px-1 py-1 text-center">No. de parte</th>
                                    <th class="border border-black px-1 py-1 text-center">Pzas carga</th>
                                    <th class="border border-black px-1 py-1 text-center">Pzas totales proceso</th>
                                    <th class="border border-black px-1 py-1 text-center">Decímetros trabajados</th>
                                    <th class="border border-black px-1 py-1 text-center">Inicio</th>
                                    <th class="border border-black px-1 py-1 text-center">Fin</th>
                                    <th class="border border-black px-1 py-1 text-center">T. muerto (hrs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($page['processes'] as $process)
                                    @php
                                        $tarimaNp   = optional($process->tarimaNp);
                                        $tarima     = optional($tarimaNp->tarima);
                                        $numberPart = optional($tarimaNp->numberPart);
                                        $decimeters = $tarimaNp->numberPart->decimeters ?? 0;
                                        $charges    = $process->charges;
                                        $totalProcessPieces = $charges->sum('quantity_pieces');
                                        $totalDecimetersWorked = $totalProcessPieces * $decimeters;
                                    @endphp
                                    @if($charges->isEmpty())
                                        <tr>
                                            <td class="border border-black px-1 py-1 text-center">{{ $process->id }}</td>
                                            <td class="border border-black px-1 py-1 text-center text-gray-400 italic">—</td>
                                            <td class="border border-black px-1 py-1 text-center">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                            <td class="border border-black px-1 py-1 text-center">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                            <td class="border border-black px-1 py-1 text-center">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                            <td class="border border-black px-1 py-1 text-center">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                            <td class="border border-black px-1 py-1 text-center">—</td>
                                            <td class="border border-black px-1 py-1 text-center">0</td>
                                            <td class="border border-black px-1 py-1 text-center">0</td>
                                            <td class="border border-black px-1 py-1 text-center">{{ optional($process->start_date)->format('H:i') }}</td>
                                            <td class="border border-black px-1 py-1 text-center">{{ optional($process->finished_date)->format('H:i') }}</td>
                                            <td class="border border-black px-1 py-1 text-center">0</td>
                                        </tr>
                                    @else
                                        @foreach($charges as $ci => $charge)
                                            @php
                                                $totalProcessDeadtime = $charges->sum(fn($c) => $c->timeouts->sum('hours'));
                                            @endphp
                                            <tr>
                                                @if($ci === 0)
                                                    <td class="border border-black px-1 py-1 text-center font-semibold" rowspan="{{ $charges->count() }}">{{ $process->id }}</td>
                                                @endif
                                                <td class="border border-black px-1 py-1 text-center">#{{ $ci + 1 }}</td>
                                                @if($ci === 0)
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                                @endif
                                                <td class="border border-black px-1 py-1 text-center">{{ round($charge->quantity_pieces, 2) }}</td>
                                                @if($ci === 0)
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ round($totalProcessPieces, 2) }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ round($totalDecimetersWorked, 4) }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ optional($process->start_date)->format('H:i') }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ optional($process->finished_date)->format('H:i') }}</td>
                                                    <td class="border border-black px-1 py-1 text-center" rowspan="{{ $charges->count() }}">{{ number_format($totalProcessDeadtime, 2) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="12" class="border border-black px-2 py-4 text-center text-gray-500">No se encontraron procesos para la fecha y líder seleccionados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($page['showClosing'])
                    <!-- CAUSAS DE TIEMPO MUERTO -->
                    <div class="w-full mt-4 text-[10px]">
                        <p class="font-semibold text-sm mb-1">Causas de tiempo muerto</p>
                        @if(!empty($deadtimeByType))
                            <table class="w-full border-collapse border border-black">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-black px-2 py-1 text-left">Razón</th>
                                        <th class="border border-black px-2 py-1 text-center">Horas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deadtimeByType as $type => $hours)
                                        <tr>
                                            <td class="border border-black px-2 py-1">{{ $type }}</td>
                                            <td class="border border-black px-2 py-1 text-center">{{ round($hours, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="mt-1"><span class="font-semibold">Total tiempo muerto:</span> {{ round($totalDeadtime, 2) }} hrs</p>
                        @else
                            <p class="italic text-gray-600">No hay tiempos muertos registrados para los procesos seleccionados.</p>
                        @endif
                    </div>

                    <!-- FIRMA -->
                    <div class="w-full mt-8 flex flex-col items-center justify-end flex-1">
                        <div class="w-1/2 border-t border-black mt-8 pt-1 text-center text-[11px]">
                            Nombre y firma del líder de proceso
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
    <script>
        async function saveProcessReportPdf() {
            const element = document.querySelector('.print-area');
            if (!element || typeof html2pdf === 'undefined') {
                alert('No se pudo generar el PDF.');
                return;
            }

            const filename = `reporte-procesos-{{ $leader->id }}-{{ \Illuminate\Support\Str::slug($date) }}.pdf`;

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
                formData.append('type', 'reporte-procesos');
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
