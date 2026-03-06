<div class="containerpric">

    <x-loading functionsList="" />

    <div class="w-full flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
        <x-secondary-hyperlink href="{{ route('reporttarimas') }}" target="" class="my-auto whitespace-nowrap w-fit">
            Volver
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
            <div class="flex justify-end mb-2">
                <x-button-primary class="w-fit h-fit" wire:click="printSelected">
                    Imprimir seleccionados
                </x-button-primary>
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
                                    wire:model="selectedReports"
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
                                                <td class="px-2 py-1 text-center">{{ $obs['thickness_in_microns'] }}</td>
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
