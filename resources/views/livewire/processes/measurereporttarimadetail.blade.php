<div class="containerpric">

    <x-loading functionsList="" />

    <div class="w-full flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
        <x-secondary-hyperlink href="{{ route('reporttarimas') }}" target="" class="my-auto whitespace-nowrap w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Regresar
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-lg sm:text-2xl font-bold leading-snug">
            Reportes de medición de la tarima #{{ $tarima->serial_number ?? 'N/A' }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-lg my-3 p-4 space-y-6">
        <div class="flex flex-col space-y-1 text-sm">
            <span><span class="font-semibold">Cliente:</span> {{ $tarima->customer->name ?? 'N/A' }}</span>
            <span><span class="font-semibold">Fecha de recepción:</span> {{ isset($tarima->register_date) ? date('d/m/Y', strtotime($tarima->register_date)) : 'N/A' }}</span>
        </div>

        @if (count($reports) > 0)
            <div class="flex flex-col gap-4 mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-col gap-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-sm text-secondarycolor">
                        <input type="checkbox" class="size-5 rounded border-2 border-primarycolor text-primarycolor focus:ring-primarycolor focus:ring-offset-0" wire:model.live="selectAll">
                        Seleccionar todos
                    </label>

                    @if ($lastFolioUsed)
                        <p class="text-xs text-gray-500">
                            Último folio usado: <span class="font-semibold text-gray-700">{{ $lastFolioUsed }}</span>
                        </p>
                    @endif
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    @if ($requiresFolio)
                        <div class="w-full sm:w-72">
                            <label class="block text-sm text-secondarycolor mb-1">Folio para reporte:</label>
                            <input wire:model="folio" type="text" class="inputcatalogues w-full" placeholder="Ingresa el folio">
                            <p class="mt-1 text-xs text-amber-600">
                                Esta combinación de reportes nunca se ha impreso junta. Ingresa un folio nuevo para poder imprimir.
                            </p>
                            @error('folio')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <x-button-primary class="w-full sm:w-fit h-fit whitespace-nowrap shrink-0" wire:click="printSelected">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-1">
                            <path fill-rule="evenodd" d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z" clip-rule="evenodd" />
                        </svg>

                        Imprimir seleccionados
                    </x-button-primary>
                </div>
            </div>

            @foreach ($reports as $report)
                <div class="border border-gray-200 rounded-lg p-3 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start text-sm">
                        <div class="space-y-1">
                            <p><span class="font-semibold">Número de parte:</span> {{ $report['partnumber'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Orden de compra:</span> {{ $report['oc'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Orden de fabricación:</span> {{ $report['of'] ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1 sm:text-right mt-2 sm:mt-0">
                            <p><span class="font-semibold">Método:</span> {{ $report['method'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Requisito:</span> {{ $report['requirement'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Fecha de registro:</span> {{ $report['register_date'] ? $report['register_date']->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="flex justify-end">
                            <label class="inline-flex items-center cursor-pointer select-none bg-white rounded-full px-3 py-1 text-xs font-semibold text-gray-600">
                                <span class="mr-2 uppercase tracking-wide">Imprimir</span>
                                <input
                                    type="checkbox"
                                    class="size-6 rounded border-2 border-primarycolor text-primarycolor focus:ring-primarycolor focus:ring-offset-0"
                                    wire:model.live="selectedReports"
                                    value="{{ $report['id'] }}"
                                >
                            </label>
                        </div>
                    </div>

                    @if (count($report['observations']) > 0)
                        <div class="mt-3">
                            <p class="font-semibold text-primarycolor mb-2">Mediciones (observaciones)</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs md:text-sm">
                                    <thead class="bg-gray-100 text-gray-600">
                                        <tr>
                                            <th class="px-2 py-1 text-left">#</th>
                                            <th class="px-2 py-1 text-center">Espesor (micras)</th>
                                            <th class="px-2 py-1 text-left">Apariencia visual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report['observations'] as $index => $obs)
                                            <tr class="border-b last:border-b-0">
                                                <td class="px-2 py-1">{{ $index + 1 }}</td>
                                                <td class="px-2 py-1 text-center">{{ round($obs['thickness_in_microns'], 2) }}</td>
                                                <td class="px-2 py-1">{{ $obs['visual_appearance'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500 mt-2">Este reporte no tiene observaciones registradas.</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center text-sm text-gray-500">No se encontraron reportes de medición para esta tarima.</p>
        @endif
    </div>

</div>
