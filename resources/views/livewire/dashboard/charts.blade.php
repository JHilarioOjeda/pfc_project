<div class="containerpric">
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">
        <p class="text-secondarycolor text-2xl font-bold">Dashboard</p>

        <div class="mt-4 flex flex-col md:flex-row md:space-x-4">
            <div class="w-full md:w-1/3">
                <p class="text-secondarycolor">Desde:</p>
                <input wire:model.live="fromDate" type="date" class="inputcatalogues w-full">
            </div>
            <div class="w-full md:w-1/3 mt-3 md:mt-0">
                <p class="text-secondarycolor">Hasta:</p>
                <input wire:model.live="toDate" type="date" class="inputcatalogues w-full">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Tarimas ingresadas por día</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="tarimasByDayChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-3">
            <p class="text-secondarycolor font-semibold">Procesos por estatus</p>
            <div class="mt-3 h-72" wire:ignore>
                <canvas id="processesByStatusChart"></canvas>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            window.dashboardChartsInitialData = {
                tarimas: @js($tarimasChart),
                processes: @js($processesByStatusChart),
            };

            function upsertLineChart(chartInstance, canvasId, payload) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof window.Chart === 'undefined' || !payload) return chartInstance;

                const labels = Array.isArray(payload.labels) ? payload.labels : [];
                const data = Array.isArray(payload.data) ? payload.data : [];

                if (chartInstance) {
                    chartInstance.data.labels = labels;
                    chartInstance.data.datasets[0].data = data;
                    chartInstance.update();
                    return chartInstance;
                }

                return new window.Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tarimas',
                            data: data,
                            tension: 0.3,
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
                    chartInstance.data.labels = labels;
                    chartInstance.data.datasets[0].data = data;
                    chartInstance.update();
                    return chartInstance;
                }

                return new window.Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Procesos',
                            data: data,
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
                };
            })(window.refreshDashboardCharts);

            window.refreshDashboardCharts();

            document.addEventListener('livewire:init', () => {
                if (window._dashboardChartsHooked) return;
                window._dashboardChartsHooked = true;

                Livewire.on('dashboard-charts-updated', (payload) => {
                    if (!payload) return;

                    if (payload.tarimas) {
                        window._tarimasByDayChart = upsertLineChart(
                            window._tarimasByDayChart,
                            'tarimasByDayChart',
                            payload.tarimas
                        );
                    }

                    if (payload.processes) {
                        window._processesByStatusChart = upsertDoughnutChart(
                            window._processesByStatusChart,
                            'processesByStatusChart',
                            payload.processes
                        );
                    }
                });
            });
        </script>
    @endpush
</div>
