<x-app-layout>
    <!-- BOTONES SUPERIORES -->
    <div class="mx-auto text-sm overflow-y-hidden flex mt-4 no-print">
        <x-secondary-hyperlink href="{{ route('storage') }}" class="!px-2 !py-1 !text-sm flex items-center ml-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
                <path fill-rule="evenodd" d="M9.53 4.47a.75.75 0 0 1 0 1.06L5.81 9.25H20a.75.75 0 0 1 0 1.5H5.81l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
            </svg>
            Regresar
        </x-secondary-hyperlink>
    </div>

    <div class="w-full overflow-x-auto">
        <div class="bg-white rounded-lg mx-auto text-sm overflow-y-hidden flex flex-col mt-4 print-area" style="height: 21.59cm; width: 27.94cm; padding: 1cm;">
            <!-- ENCABEZADO -->
            <div class="flex items-start justify-between border-b border-black pb-2">
                <div class="flex items-center space-x-2 w-2/12">
                    <img src="/imgs/logos/principallogo.jpg" class="w-full" alt="Logo">
                </div>

                <div class="flex-1 text-center w-8/12">
                    <p class="font-bold text-base tracking-wide">HOJA DE IDENTIFICACION MC</p>
                </div>

                <div class="text-[10px] border border-black px-2 py-1 leading-tight w-2/12">
                    <p><span class="font-semibold">Código:</span> FO-AL-AL-04</p>
                    <p><span class="font-semibold">Revisión:</span> 01</p>
                    <p><span class="font-semibold">Fecha:</span> 26/Nov/2025</p>
                </div>
            </div>

            <!-- TABLA PRINCIPAL -->
            <div class="w-full mt-4">
                <table class="w-full border-collapse border border-black text-[11px]">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-1 py-1 text-center align-middle">FECHA RECEPCIÓN</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">TARIMA</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">ORDEN DE<br>FABRICACIÓN</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">N° DE PARTE</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">CANTIDAD</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">ACABADO</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">CONTEO<br>ALMACÉN</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">CONTEO<br>PROD</th>
                            <th class="border border-black px-1 py-1 text-center align-middle">CONTEO<br>CALIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tarima->tarimaNps as $item)
                            <tr>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ optional($tarima->register_date)->format('d-M-y') }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ $tarima->serial_number }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ $item->of }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ optional($item->numberPart)->partnumber }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ round($item->quantity, 2) }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center">
                                    {{ optional($item->numberPart)->process }}
                                </td>
                                <td class="border border-black px-1 py-1 text-center"></td>
                                <td class="border border-black px-1 py-1 text-center"></td>
                                <td class="border border-black px-1 py-1 text-center"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- FIRMAS -->
            <div class="w-full mt-10">
                <table class="w-full border-collapse border border-black text-[11px]">
                    <tr>
                        <td class="border border-black h-16 align-bottom text-center">FIRMA ALMACEN</td>
                        <td class="border border-black h-16 align-bottom text-center">FIRMA LIDER DE LINEA</td>
                        <td class="border border-black h-16 align-bottom text-center">FIRMA CALIDAD</td>
                    </tr>
                </table>
            </div>

            <!-- NUMERO DE TARIMA GRANDE -->
            <div class="flex-1 flex items-center justify-center">
                <p class="text-5xl font-bold">
                    No. {{ $tarima->serial_number }}
                </p>
            </div>
        </div>
    </div>

    <!-- BOTONES FLOTANTES IMPRESIÓN / PDF (pensado para tablet) -->
    <x-secondary-button type="button" class="fixed bottom-4 right-32 z-50 px-4 py-3 text-sm flex items-center shadow-lg no-print" onclick="saveTarimaPdf()">
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
        async function saveTarimaPdf() {
            const element = document.querySelector('.print-area');
            if (!element || typeof html2pdf === 'undefined') {
                alert('No se pudo generar el PDF.');
                return;
            }

            const filename = `tarima-{{ $tarima->serial_number }}.pdf`;

                const opt = {
				margin:       0,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            try {
                const worker = html2pdf().set(opt).from(element);
                const pdfBlob = await worker.outputPdf('blob');

                const formData = new FormData();
                formData.append('file', pdfBlob, filename);
                formData.append('type', 'tarima');
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