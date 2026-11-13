{{-- Botones Regresar / Guardar PDF / Imprimir, estilos de impresión y script de PDF.
     Parámetros: $backUrl, $pdfName, $pdfType --}}
<div class="mx-auto text-sm overflow-y-hidden flex mt-4 no-print">
    <x-secondary-hyperlink href="{{ $backUrl }}" class="!px-2 !py-1 !text-sm flex items-center ml-5">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mr-1">
            <path fill-rule="evenodd" d="M9.53 4.47a.75.75 0 0 1 0 1.06L5.81 9.25H20a.75.75 0 0 1 0 1.5H5.81l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
        </svg>
        Regresar
    </x-secondary-hyperlink>
    <x-secondary-button type="button" class="ml-auto !px-3 !py-1 !text-sm flex items-center mr-2" onclick="saveMeasurementFormatPdf()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
            <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
            <path fill-rule="evenodd" d="M6.75 12a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
        </svg>
        Guardar PDF
    </x-secondary-button>
    <x-button-primary type="button" class="mr-5 !px-3 !py-1 !text-sm flex items-center" onclick="window.print()">
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
            padding: 0;
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

<script>
    async function saveMeasurementFormatPdf() {
        const element = document.querySelector('.print-area');
        if (!element || typeof html2pdf === 'undefined') {
            alert('No se pudo generar el PDF.');
            return;
        }

        const filename = @json($pdfName);

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
            formData.append('type', @json($pdfType));
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
