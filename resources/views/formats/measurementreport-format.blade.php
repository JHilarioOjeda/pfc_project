<x-app-layout>
    <!-- BOTONES SUPERIORES -->
    <div class="mx-auto text-sm overflow-y-hidden flex mt-4 no-print">
        <x-secondary-hyperlink href="{{ route('measurementreports', optional($report->proccess)->id) }}" class="!px-2 !py-1 !text-sm flex items-center ml-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                <path fill-rule="evenodd" d="M9.53 4.47a.75.75 0 0 1 0 1.06L5.81 9.25H20a.75.75 0 0 1 0 1.5H5.81l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
            </svg>
            Regresar
        </x-secondary-hyperlink>
    </div>

    <div class="w-full overflow-x-auto">
        <div class="bg-white rounded-lg mx-auto text-sm overflow-y-hidden flex flex-col mt-4 print-area" style="height: 27.94cm; width: 21.59cm; padding: 1cm;">
            @php
                $process = $report->proccess;
                $tarimaNp = optional($process)->tarimaNp;
                $tarima = optional($tarimaNp)->tarima;
                $customer = optional($tarima)->customer;
                $numberPart = optional($tarimaNp)->numberPart;
            @endphp

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
                    <p><span class="font-semibold">Código:</span> FO-CA-CA-14</p>
                    <p><span class="font-semibold">Revisión:</span> 01</p>
                </div>
            </div>

            <!-- FECHA / FOLIO -->
            <div class="flex justify-end mt-2">
                <table class="text-[10px] border border-black border-collapse">
                    <tr>
                        <td class="border border-black px-2 py-1 font-semibold">FECHA</td>
                        <td class="border border-black px-2 py-1 text-center">{{ optional($report->register_date)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black px-2 py-1 font-semibold">FOLIO</td>
                        <td class="border border-black px-2 py-1 text-center">{{ $report->folio }}</td>
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
                        <td class="border border-black px-2 py-1 font-semibold">Código / Nombre de la pieza</td>
                        <td class="border border-black px-2 py-1">{{ optional($numberPart)->partnumber }}</td>
                        <td class="border border-black px-2 py-1 font-semibold">Requisito</td>
                        <td class="border border-black px-2 py-1">{{ round(optional($report)->requirement, 4) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black px-2 py-1 font-semibold">Unidades de medición</td>
                        <td class="border border-black px-2 py-1">Micras</td>
                        <td class="border border-black px-2 py-1 font-semibold">Cantidad</td>
                        <td class="border border-black px-2 py-1">{{ round(optional($tarimaNp)->quantity, 4) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black px-2 py-1 font-semibold"># Orden</td>
                        <td class="border border-black px-2 py-1">{{ optional($tarimaNp)->oc }}</td>
                        <td class="border border-black px-2 py-1 font-semibold">NUM</td>
                        <td class="border border-black px-2 py-1">{{ optional($tarimaNp)->of }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black px-2 py-1 font-semibold">Método</td>
                        <td class="border border-black px-2 py-1">{{ $report->method }}</td>
                        <td class="border border-black px-2 py-1 font-semibold">Colgado</td>
                        <td class="border border-black px-2 py-1">{{ $report->method === 'Colgado' ? 'SI' : '' }}</td>
                    </tr>
                </table>
            </div>

            <!-- OBSERVACIONES / CONDICIONES DEL MATERIAL -->
            <div class="w-full mt-4">
                <p class="font-semibold text-sm">Observaciones / Condiciones del material</p>
                <p class="text-[11px] mb-1">TIPO DE PROCESO: {{ optional($numberPart)->process }}</p>
                <table class="w-full border-collapse border border-black text-[11px]">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-2 py-1 text-center align-middle w-2/12">No. de medición</th>
                            <th class="border border-black px-2 py-1 text-center align-middle w-5/12">Espesor en Micras</th>
                            <th class="border border-black px-2 py-1 text-center align-middle w-5/12">Apariencia Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->observations as $index => $observation)
                            <tr>
                                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                                <td class="border border-black px-2 py-1 text-center">{{ round($observation->thickness_in_microns, 2) }}</td>
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

            <!-- NOTA Y CADUCIDAD -->
            <div class="w-full mt-3 text-[10px]">
                <p class="font-semibold">NOTA</p>
                @if($report->notes)
                    <p>{{ $report->notes }}</p>
                @else
                    <p>*Se recomienda mantener el material en un lugar seco libre de humedad o líquidos (agua, soluciones, aceites, etc.) o solventes que emanen vapores.</p>
                @endif
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
        </div>
    </div>

    <!-- BOTONES FLOTANTES IMPRESIÓN / PDF -->
    <x-secondary-button type="button" class="fixed bottom-4 right-40 z-50 px-4 py-3 text-sm flex items-center shadow-lg no-print" onclick="saveMeasurementReportPdf()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
            <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
            <path fill-rule="evenodd" d="M6.75 12a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
        </svg>
        Guardar PDF
    </x-secondary-button>

    <x-button-primary type="button" class="fixed bottom-4 right-4 z-50 px-4 py-3 text-sm flex items-center shadow-lg no-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
            <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
            <path fill-rule="evenodd" d="M6.75 12a3.75 3.75 0 0 0-3.75 3.75v1.5A2.25 2.25 0 0 0 5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25v-1.5A3.75 3.75 0 0 0 17.25 12H6.75Zm1.5 2.25a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
        </svg>
        Imprimir
     </x-button-primary>

    <script>
        async function saveMeasurementReportPdf() {
            const element = document.querySelector('.print-area');
            if (!element || typeof html2pdf === 'undefined') {
                alert('No se pudo generar el PDF.');
                return;
            }

            const filename = `reporte-mediciones-{{ $report->id }}.pdf`;

                const opt = {
				margin:       0,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'letter', orientation: 'portrait' }
            };

            try {
                const worker = html2pdf().set(opt).from(element);
                const pdfBlob = await worker.outputPdf('blob');

                const formData = new FormData();
                formData.append('file', pdfBlob, filename);
                formData.append('type', 'reporte-mediciones');
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
</x-app-layout>
