<div class="containerpric">
    <x-loading functionsList="" />

    <p class="text-secondarycolor text-2xl font-bold">Reporte de procesos diarios</p>

    <div class="bg-white rounded-lg shadow-lg my-3 p-3 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 md:gap-4">
            <div class="w-full"
                wire:ignore
                data-initdates="{{ json_encode(array_column($availableDates, 'date')) }}"
                x-data="{
                    fp: null,
                    datesWithProcesses: [],
                    initFlatpickr() {
                        if (this.fp) this.fp.destroy();
                        const wire = this.$wire;
                        const self = this;
                        this.fp = flatpickr(this.$refs.dateinput, {
                            dateFormat: 'Y-m-d',
                            monthSelectorType: 'static',
                            defaultDate: '{{ $date ?? '' }}',
                            locale: {
                                firstDayOfWeek: 1,
                                weekdays: {
                                    shorthand: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
                                    longhand: ['Domingo','Lunes','Martes','Mi\u00e9rcoles','Jueves','Viernes','S\u00e1bado']
                                },
                                months: {
                                    shorthand: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                                    longhand: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
                                }
                            },
                            onDayCreate: function(dObj, dStr, fp, dayElem) {
                                const d = dayElem.dateObj;
                                const s = d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
                                if (self.datesWithProcesses.includes(s)) dayElem.classList.add('has-processes');
                            },
                            onChange: function(selectedDates, dateStr) {
                                if (dateStr) wire.selectDate(dateStr);
                            }
                        });
                    },
                    init() {
                        this.datesWithProcesses = JSON.parse(this.$el.dataset.initdates || '[]');
                        if (typeof flatpickr !== 'undefined') {
                            this.initFlatpickr();
                        } else {
                            window.addEventListener('flatpickr-ready', () => this.initFlatpickr(), { once: true });
                        }
                    }
                }"
                x-on:dates-updated.window="datesWithProcesses = $event.detail.dates; if (fp) fp.redraw();"
            >
                <p class="text-secondarycolor">Fecha:</p>
                <input x-ref="dateinput" type="text" class="inputcatalogues w-full cursor-pointer" readonly placeholder="Seleccionar fecha...">
            </div>
            <div class="w-full">
                <p class="text-secondarycolor">Líder de proceso:</p>
                @if(Auth::user()->user_type === '3')
                    <input type="text" class="inputcatalogues w-full bg-gray-100 cursor-not-allowed" value="{{ Auth::user()->name }}" disabled>
                @else
                <select class="inputcatalogues w-full" wire:model="leader_id" wire:change="loadData">
                    <option value="">Seleccionar...</option>
                    <option value="all">Todos los líderes</option>
                    @foreach ($leaders as $leader)
                        <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div class="w-full md:col-span-2 xl:col-span-1 flex items-end xl:justify-end">
                @if($date && $leader_id && (string)$leader_id !== 'all')
                    <x-primary-hyperlink href="{{ route('reportprocesses.print', ['date' => $date, 'leader' => $leader_id]) }}" target="" class="w-fit !px-3 !py-2 flex items-center">
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
            @if((string)$leader_id === 'all')
                {{-- ===== VISTA: TODOS LOS LÍDERES ===== --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <p class="font-semibold text-primarycolor mb-2">Resumen del día</p>
                        <p class="text-sm"><span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                        <p class="text-sm"><span class="font-semibold">Líderes con procesos:</span> {{ count($processesGrouped) }}</p>
                        <p class="text-sm"><span class="font-semibold">Total de procesos:</span> {{ $processCountAll }}</p>
                        <p class="text-sm"><span class="font-semibold">Tiempo muerto total:</span> {{ number_format($totalDeadtimeAll, 2) }} hrs</p>
                    </div>
                    @if(count($processesGrouped) > 0)
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <p class="font-semibold text-primarycolor mb-2">Tiempo muerto por líder</p>
                            @foreach($processesGrouped as $group)
                                <div class="flex justify-between text-sm py-1 border-b border-gray-100">
                                    <span>{{ $group['leader_name'] }}</span>
                                    <span class="font-semibold">{{ number_format($group['deadtime'], 2) }} hrs</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @forelse($processesGrouped as $group)
                    <div class="mt-5">
                        <p class="font-semibold text-primarycolor mb-2">
                            {{ $group['leader_name'] }}
                            <span class="ml-2 text-xs font-normal text-gray-500">
                                ({{ collect($group['processes'])->count() }} proceso(s) &middot; {{ number_format($group['deadtime'], 2) }} hrs TM)
                            </span>
                        </p>
                        <div class="relative overflow-x-auto rounded-lg border border-gray-200">
                            <table class="table table-hover w-full text-left text-xs md:text-sm">
                                <thead>
                                    <tr class="bg-gray-200 font-semibold">
                                        <th class="px-2 py-2 text-center">ID Proceso</th>
                                        <th class="px-2 py-2 text-center">Carga #</th>
                                        <th class="px-2 py-2">Tarima</th>
                                        <th class="px-2 py-2">NP</th>
                                        <th class="px-2 py-2 hidden md:table-cell">Decímetros</th>
                                        <th class="px-2 py-2 hidden md:table-cell">OF</th>
                                        <th class="px-2 py-2 hidden md:table-cell">Cliente</th>
                                        <th class="px-2 py-2 hidden md:table-cell">Línea</th>
                                        <th class="px-2 py-2 hidden md:table-cell text-center">Pzas carga</th>
                                        <th class="px-2 py-2 hidden md:table-cell text-center">Pzas totales proceso</th>
                                        <th class="px-2 py-2 hidden md:table-cell text-center">Decímetros trabajados</th>
                                        <th class="px-2 py-2 hidden md:table-cell text-center">Estatus carga</th>
                                        <th class="px-2 py-2 hidden md:table-cell text-center">T. muerto (hrs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($group['processes'] as $process)
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
                                            <tr class="border-t border-gray-200">
                                                <td class="px-2 py-2 text-center">{{ $process->id }}</td>
                                                <td class="px-2 py-2 text-center text-gray-400 italic">Sin cargas</td>
                                                <td class="px-2 py-2">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                                <td class="px-2 py-2">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell">{{ round($decimeters, 4) }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell">{{ optional($process->line)->name ?? '' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center">0</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center">0.00</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                            </tr>
                                        @else
                                            @foreach($charges as $ci => $charge)
                                                @php
                                                    $chargeDeadtime = $charge->timeouts->sum('hours');
                                                    $statusLabel = match($charge->status) {
                                                        'created'   => 'Creada',
                                                        'liberated' => 'Liberada',
                                                        'confirmed' => 'Confirmada',
                                                        default     => $charge->status,
                                                    };
                                                @endphp
                                                <tr class="border-t border-gray-200 {{ $ci % 2 === 1 ? 'bg-gray-50' : '' }}">
                                                    @if($ci === 0)
                                                        <td class="px-2 py-2 text-center font-semibold" rowspan="{{ $charges->count() }}">{{ $process->id }}</td>
                                                    @endif
                                                    <td class="px-2 py-2 text-center">#{{ $ci + 1 }}</td>
                                                    @if($ci === 0)
                                                        <td class="px-2 py-2" rowspan="{{ $charges->count() }}">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                                        <td class="px-2 py-2" rowspan="{{ $charges->count() }}">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                                        <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ round($decimeters, 4) }}</td>
                                                        <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                                        <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                                        <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ optional($process->line)->name ?? '' }}</td>
                                                    @endif
                                                    <td class="px-2 py-2 hidden md:table-cell text-center">{{ round($charge->quantity_pieces, 2) }}</td>
                                                    @if($ci === 0)
                                                        <td class="px-2 py-2 hidden md:table-cell text-center" rowspan="{{ $charges->count() }}">{{ round($totalProcessPieces, 2) }}</td>
                                                        <td class="px-2 py-2 hidden md:table-cell text-center" rowspan="{{ $charges->count() }}">{{ round($totalDecimetersWorked, 4) }}</td>
                                                    @endif
                                                    <td class="px-2 py-2 hidden md:table-cell text-center">{{ $statusLabel }}</td>
                                                    <td class="px-2 py-2 hidden md:table-cell text-center">{{ number_format($chargeDeadtime, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="13" class="px-2 py-4 text-center text-sm text-gray-500">Sin procesos.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 mt-2">No se encontraron procesos para la fecha seleccionada.</p>
                @endforelse

            @else
            {{-- ===== VISTA: LÍDER INDIVIDUAL ===== --}}
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
                                <th class="px-2 py-2 text-center">ID Proceso</th>
                                <th class="px-2 py-2 text-center">Carga #</th>
                                <th class="px-2 py-2">Tarima</th>
                                <th class="px-2 py-2">NP</th>
                                <th class="px-2 py-2 hidden md:table-cell">Decímetros</th>
                                <th class="px-2 py-2 hidden md:table-cell">OF</th>
                                <th class="px-2 py-2 hidden md:table-cell">Cliente</th>
                                <th class="px-2 py-2 hidden md:table-cell">Línea</th>
                                <th class="px-2 py-2 hidden md:table-cell text-center">Pzas carga</th>
                                <th class="px-2 py-2 hidden md:table-cell text-center">Pzas totales proceso</th>
                                <th class="px-2 py-2 hidden md:table-cell text-center">Decímetros trabajados</th>
                                <th class="px-2 py-2 hidden md:table-cell text-center">Estatus carga</th>
                                <th class="px-2 py-2 hidden md:table-cell text-center">T. muerto (hrs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($processes as $process)
                                @php
                                    $tarimaNp  = optional($process->tarimaNp);
                                    $tarima    = optional($tarimaNp->tarima);
                                    $numberPart = optional($tarimaNp->numberPart);
                                    $charges   = $process->charges;
                                    $decimeters = $numberPart->decimeters ?? 0;
                                    $totalProcessPieces = $charges->sum('quantity_pieces');
                                    $totalDecimetersWorked = $totalProcessPieces * $decimeters;
                                @endphp
                                @if($charges->isEmpty())
                                    <tr class="border-t border-gray-200">
                                        <td class="px-2 py-2 text-center">{{ $process->id }}</td>
                                        <td class="px-2 py-2 text-center text-gray-400 italic">Sin cargas</td>
                                        <td class="px-2 py-2">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                        <td class="px-2 py-2">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                        <td class="px-2 py-2 hidden md:table-cell">{{ round($decimeters, 4) }}</td>
                                        <td class="px-2 py-2 hidden md:table-cell">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                        <td class="px-2 py-2 hidden md:table-cell">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                        <td class="px-2 py-2 hidden md:table-cell">{{ optional($process->line)->name ?? '' }}</td>
                                        <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                        <td class="px-2 py-2 hidden md:table-cell text-center">0</td>
                                        <td class="px-2 py-2 hidden md:table-cell text-center">0</td>
                                        <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                        <td class="px-2 py-2 hidden md:table-cell text-center">—</td>
                                    </tr>
                                @else
                                    @foreach($charges as $ci => $charge)
                                        @php
                                            $chargeDeadtime = $charge->timeouts->sum('hours');
                                            $statusLabel = match($charge->status) {
                                                'created'   => 'Creada',
                                                'liberated' => 'Liberada',
                                                'confirmed' => 'Confirmada',
                                                default     => $charge->status,
                                            };
                                        @endphp
                                        <tr class="border-t border-gray-200 {{ $ci % 2 === 1 ? 'bg-gray-50' : '' }}">
                                            @if($ci === 0)
                                                <td class="px-2 py-2 text-center font-semibold" rowspan="{{ $charges->count() }}">{{ $process->id }}</td>
                                            @endif
                                            <td class="px-2 py-2 text-center">#{{ $ci + 1 }}</td>
                                            @if($ci === 0)
                                                <td class="px-2 py-2" rowspan="{{ $charges->count() }}">#{{ $tarima->serial_number ?? 'N/A' }}</td>
                                                <td class="px-2 py-2" rowspan="{{ $charges->count() }}">{{ $numberPart->partnumber ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ round($decimeters, 4) }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ $tarimaNp->of ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ optional($tarima->customer)->name ?? 'N/A' }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell" rowspan="{{ $charges->count() }}">{{ optional($process->line)->name ?? '' }}</td>
                                            @endif
                                            <td class="px-2 py-2 hidden md:table-cell text-center">{{ round($charge->quantity_pieces, 2) }}</td>
                                            @if($ci === 0)
                                                <td class="px-2 py-2 hidden md:table-cell text-center" rowspan="{{ $charges->count() }}">{{ round($totalProcessPieces, 2) }}</td>
                                                <td class="px-2 py-2 hidden md:table-cell text-center" rowspan="{{ $charges->count() }}">{{ round($totalDecimetersWorked, 4) }}</td>
                                            @endif
                                            <td class="px-2 py-2 hidden md:table-cell text-center">{{ $statusLabel }}</td>
                                            <td class="px-2 py-2 hidden md:table-cell text-center">{{ number_format($chargeDeadtime, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="13" class="px-2 py-4 text-center text-sm text-gray-500">No se encontraron procesos para la fecha y líder seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @else
            <p class="text-sm text-gray-500 mt-2">Selecciona una fecha y un líder de proceso para ver el reporte.</p>
        @endif
    </div>
</div>

