<div class="containerpric">
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="mt-4 flex flex-col md:flex-row md:space-x-4">
            <div class="w-full md:w-1/3">
                <p class="text-secondarycolor">Desde:</p>
                <input wire:model.live="fromDate" type="date" class="inputcatalogues w-full">
            </div>
            <div class="w-full md:w-1/3 mt-3 md:mt-0">
                <p class="text-secondarycolor">Hasta:</p>
                <input wire:model.live="toDate" type="date" class="inputcatalogues w-full">
            </div>
            <div class="w-full md:w-1/3 mt-3 md:mt-0">
                <p class="text-secondarycolor">Línea:</p>
                <select wire:model.live="lineId" class="inputcatalogues w-full">
                    <option value="all">Todas las líneas</option>
                    @foreach ($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <p class="mt-5 italic text-sm text-gray-500">
            Cambia el rango de fechas o la línea para actualizar los reportes. Por defecto se muestran los últimos 7 días y todas las líneas.
        </p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-lg shadow-lg p-4">
            <p class="text-sm text-gray-500">Tarimas ingresadas</p>
            <p class="text-2xl font-semibold text-secondarycolor mt-1">{{ number_format($totalTarimas) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <p class="text-sm text-gray-500">Procesos totales</p>
            <p class="text-2xl font-semibold text-secondarycolor mt-1">{{ number_format($totalProcesses) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <p class="text-sm text-gray-500">Piezas trabajadas</p>
            <p class="text-2xl font-semibold text-secondarycolor mt-1">{{ number_format($totalPieces) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <p class="text-sm text-gray-500">Decímetros trabajados</p>
            <p class="text-2xl font-semibold text-secondarycolor mt-1">{{ number_format($totalDecimeters, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Procesos por estatus</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="processesByStatusChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Tarimas ingresadas por día</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="tarimasByDayChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Piezas trabajadas por día</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="piecesByDayChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Decímetros trabajados por día</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="decimetersByDayChart"></canvas>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            window.dashboardChartsInitialData = {
                tarimas: @js($tarimasChart),
                processes: @js($processesByStatusChart),
                piecesDecimeters: @js($piecesDecimetersChart),
            };

            function upsertLineChart(chartInstance, canvasId, payload) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof window.Chart === 'undefined' || !payload) return chartInstance;

                const labels = Array.isArray(payload.labels) ? payload.labels : [];
                const data = Array.isArray(payload.data) ? payload.data : [];

                if (chartInstance) {
                    try {
                        chartInstance.destroy();
                    } catch (e) {
                        console.warn('[Dashboard] Error al destruir chart de tarimas', e);
                    }
                }

                return new window.Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tarimas',
                            data: data,
                            tension: 0.3,
                            borderColor: '#F27D16',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });
            }

            function upsertBarChart(chartInstance, canvasId, payload, { label, dataKey, color }) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof window.Chart === 'undefined' || !payload) return chartInstance;

                const labels = Array.isArray(payload.labels) ? payload.labels : [];
                const data = Array.isArray(payload[dataKey]) ? payload[dataKey] : [];

                if (chartInstance) {
                    try {
                        chartInstance.destroy();
                    } catch (e) {
                        console.warn(`[Dashboard] Error al destruir chart ${canvasId}`, e);
                    }
                }

                return new window.Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: color,
                            borderRadius: 4,
                            maxBarThickness: 24,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });
            }

            function upsertDoughnutChart(chartInstance, canvasId, payload) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof window.Chart === 'undefined' || !payload) return chartInstance;

                const labels = Array.isArray(payload.labels) ? payload.labels : [];
                const data = Array.isArray(payload.data) ? payload.data : [];

                if (chartInstance) {
                    try {
                        chartInstance.destroy();
                    } catch (e) {
                        console.warn('[Dashboard] Error al destruir chart de procesos', e);
                    }
                }

                return new window.Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Procesos',
                            data: data,
                            backgroundColor: Array.isArray(payload.colors) ? payload.colors : [],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });
            }

            // Registra un refresco global (el layout lo puede invocar tras updates/navegación Livewire v3)
            window.refreshDashboardCharts = ((previous) => {
                return function () {
                    if (typeof previous === 'function') previous();

                    if (!window.dashboardChartsInitialData) return;

                    console.log('[Dashboard] Carga inicial charts', window.dashboardChartsInitialData);

                    window._tarimasByDayChart = upsertLineChart(
                        window._tarimasByDayChart,
                        'tarimasByDayChart',
                        window.dashboardChartsInitialData.tarimas
                    );

                    window._processesByStatusChart = upsertDoughnutChart(
                        window._processesByStatusChart,
                        'processesByStatusChart',
                        window.dashboardChartsInitialData.processes
                    );

                    window._piecesByDayChart = upsertBarChart(
                        window._piecesByDayChart,
                        'piecesByDayChart',
                        window.dashboardChartsInitialData.piecesDecimeters,
                        { label: 'Piezas trabajadas', dataKey: 'pieces', color: '#60A5FA' }
                    );

                    window._decimetersByDayChart = upsertBarChart(
                        window._decimetersByDayChart,
                        'decimetersByDayChart',
                        window.dashboardChartsInitialData.piecesDecimeters,
                        { label: 'Decímetros trabajados', dataKey: 'decimeters', color: '#F27D16' }
                    );
                };
            })(window.refreshDashboardCharts);

            window.refreshDashboardCharts();

            document.addEventListener('livewire:init', () => {
                if (window._dashboardChartsHooked) return;
                window._dashboardChartsHooked = true;

                // En Livewire v3, `$this->dispatch()` dispara un browser event.
                // Los datos vienen en `event.detail`.
                window.addEventListener('dashboard-charts-updated', (event) => {
                    if (!event || !event.detail) return;

                    const { tarimas, processes, piecesDecimeters } = event.detail;

                    console.log('[Dashboard] Evento dashboard-charts-updated recibido', {
                        tarimas,
                        processes,
                        piecesDecimeters,
                    });

                    // Mantener sincronizada la data global usada por refreshDashboardCharts
                    window.dashboardChartsInitialData = {
                        tarimas,
                        processes,
                        piecesDecimeters,
                    };

                    if (tarimas) {
                        window._tarimasByDayChart = upsertLineChart(
                            window._tarimasByDayChart,
                            'tarimasByDayChart',
                            tarimas
                        );
                    }

                    if (processes) {
                        window._processesByStatusChart = upsertDoughnutChart(
                            window._processesByStatusChart,
                            'processesByStatusChart',
                            processes
                        );
                    }

                    if (piecesDecimeters) {
                        window._piecesByDayChart = upsertBarChart(
                            window._piecesByDayChart,
                            'piecesByDayChart',
                            piecesDecimeters,
                            { label: 'Piezas trabajadas', dataKey: 'pieces', color: '#60A5FA' }
                        );

                        window._decimetersByDayChart = upsertBarChart(
                            window._decimetersByDayChart,
                            'decimetersByDayChart',
                            piecesDecimeters,
                            { label: 'Decímetros trabajados', dataKey: 'decimeters', color: '#F27D16' }
                        );
                    }
                });
            });
        </script>
    @endpush
</div>
