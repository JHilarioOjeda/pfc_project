<div class="containerpric">
    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Reportes de procesos diarios</p>

    <div class="bg-white rounded-lg shadow-lg my-3 p-3 space-y-4">
        <div class="flex flex-col md:flex-row md:space-x-4 space-y-3 md:space-y-0">
            <div class="w-full md:w-1/3">
                <p class="text-secondarycolor">Fecha:</p>
                <input type="date" class="inputcatalogues w-full" wire:model="date" wire:change="loadData">
            </div>
            <div class="w-full md:w-1/3">
                <p class="text-secondarycolor">Líder de proceso:</p>
                <select class="inputcatalogues w-full" wire:model="leader_id" wire:change="loadData">
                    <option value="">Seleccionar...</option>
                    @foreach ($leaders as $leader)
                        <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/3 flex items-end justify-end">
                @if($date && $leader_id)
                    <x-primary-hyperlink href="{{ route('reportprocesses.print', ['date' => $date, 'leader' => $leader_id]) }}" target="_blank" class="w-fit !px-3 !py-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-2">
                            <path d="M6.75 3A2.25 2.25 0 0 0 4.5 5.25v3A2.25 2.25 0 0 0 6.75 10.5h10.5A2.25 2.25 0 0 0 19.5 8.25v-3A2.25 2.25 0 0 0 17.25 3h-10.5Z" />
                            <path fill-rule="evenodd" d="M6.75 12a3.75 3.75 0 0 0-3.75 3.75v1.5A2.25 2.25 0 0 0 5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25v-1.5A3.75 3.75 0 0 0 17.25 12H6.75Zm1.5 2.25a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd" />
                        </svg>
                        Imprimir
                    </x-primary-hyperlink>
                @endif
            </div>
        </div>

        @if($date && $leader_id)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="border rounded-lg p-3 bg-gray-50">
                    <p class="font-semibold text-primarycolor mb-2">Resumen del día</p>
                    <p class="text-sm"><span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                    @php
                        $leaderName = optional($leaders->firstWhere('id', (int) $leader_id))->name;
                    @endphp
                    <p class="text-sm"><span class="font-semibold">Líder:</span> {{ $leaderName }}</p>
                    <p class="text-sm"><span class="font-semibold">Procesos encontrados:</span> {{ collect($processes)->count() }}</p>
                    <p class="text-sm"><span class="font-semibold">Tiempo muerto total:</span> {{ number_format($totalDeadtime, 2) }} hrs</p>
                </div>

                <div class="border rounded-lg p-3 bg-gray-50 lg:col-span-2">
                    <p class="font-semibold text-primarycolor mb-2">Checklist de arranque</p>
                    @if($checklist)
                        <table class="w-full text-xs md:text-sm border border-gray-200 border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-200 px-2 py-1 text-left">Pregunta</th>
                                    <th class="border border-gray-200 px-2 py-1 text-center">Sí</th>
                                    <th class="border border-gray-200 px-2 py-1 text-center">No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ((array) $checklist->questions as $question)
                                    @php
                                        $answer = $question['answer'] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="border border-gray-200 px-2 py-1">{{ $question['label'] ?? $question['key'] ?? '' }}</td>
                                        <td class="border border-gray-200 px-2 py-1 text-center">@if($answer === true) X @endif</td>
                                        <td class="border border-gray-200 px-2 py-1 text-center">@if($answer === false) X @endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-sm text-gray-500">No se encontró checklist para esta fecha y líder.</p>
                    @endif
                </div>
            </div>

            <div class="mt-5">
                <p class="font-semibold text-primarycolor mb-2">Procesos del día</p>
                <div class="relative overflow-x-auto rounded-lg border border-gray-200">
                    <table class="table table-hover w-full text-left text-xs md:text-sm">
                        <thead>
                            <tr class="bg-gray-200 font-semibold">
                                <th class="px-2 py-2 text-center">ID</th>
                                <th class="px-2 py-2">Tarima</th>
                                <th class="px-2 py-2">NP</th>
                                <th class="px-2 py-2 hidden md:table-cell">OF</th>
                                <th class="px-2 py-2 hidden md:table-cell">Cliente</th>
                                <th class="px-2 py-2 hidden md:table-cell">Línea</th>
                                <th class="px-2 py-2 hidden md:table-cell">Pzas a procesar</th>
                                <th class="px-2 py-2 hidden md:table-cell">Pzas procesadas</th>
                                <th class="px-2 py-2 hidden md:table-cell">Tiempo muerto (hrs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($processes as $process)
                                @php
                                    $tarimaNp = optional($process->tarimaNp);
                                    $tarima = optional($tarimaNp->tarima);
                                    $numberPart = optional($tarimaNp->numberPart);
                                    $deadtimeHours = collect($process->timeouts)->sum('hours');
                                @endphp
                                <tr class="border-t border-gray-200">
                                    <td class="px-2 py-2 text-center">{{ $process->id }}</td>
                                    <td class="px-2 py-2">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                    <td class="px-2 py-2">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ optional($tarima->customer)->name }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ optional($process->line)->name ?? '' }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ round($tarimaNp->quantity ?? 0, 2) }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ round($process->pieces_alreadyproccess ?? 0, 2) }}</td>
                                    <td class="px-2 py-2 hidden md:table-cell">{{ number_format($deadtimeHours, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-2 py-4 text-center text-sm text-gray-500">No se encontraron procesos para la fecha y líder seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 mt-2">Selecciona una fecha y un líder de proceso para ver el reporte.</p>
        @endif
    </div>
</div>
