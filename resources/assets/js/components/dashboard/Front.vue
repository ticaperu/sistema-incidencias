<template>
    <div>
        <div class="d-flex justify-content-center mt-5">
            <div class="col-md-6">
                <h4 class="card-header">Métricas</h4>
                <canvas id="metricas-chart-pie"></canvas>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <div class="col-md-6">
                <h4 class="card-header">Usuarios</h4>
                <canvas id="users-chart-bar"></canvas>
            </div>
            <div class="col-md-6">
                <h4 class="card-header">Incidentes</h4>
                <canvas id="incidentes-chart-bar"></canvas>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <div class="col-md-6">
                <h4 class="card-header">Usuarios</h4>
                <canvas id="users-chart-pie"></canvas>
            </div>
            <div class="col-md-6">
                <h4 class="card-header">Incidentes</h4>
                <canvas id="incidentes-chart-pie"></canvas>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <div class="col-md-6">
                <h4 class="card-header">Usuarios</h4>
                <canvas id="users-chart-doughnut"></canvas>
            </div>
            <div class="col-md-6">
                <h4 class="card-header">Incidentes</h4>
                <canvas id="incidentes-chart-doughnut"></canvas>
            </div>
        </div>
    </div>
    
</template>

<script>
    import Chart from 'chart.js';

    export default {
        data() {
            return {
                // 
            }
        },
        mounted() {
            axios.get(route("reportes.metricas")).then((response) => {
                var labelMetricas = response.data.labels;
                var dataMetricas = response.data.data;
                var loading_bar = this.createDataChart(labelMetricas, dataMetricas, "Métricas", "bar");
                this.createChart('metricas-chart-pie', loading_bar);
            });
            
            axios.get(route("reportes.total-usuarios-por-tipo")).then((response) => {
                var labelUsuarios = response.data.labels;
                var dataUsuarios = response.data.data;
                var loading_bar = this.createDataChart(labelUsuarios, dataUsuarios, "Usuarios", "bar");
                this.createChart('users-chart-bar', loading_bar);

                var loading_pie = this.createDataChart(labelUsuarios, dataUsuarios, "Usuarios", "pie");
                this.createChart('users-chart-pie', loading_pie);

                var loading_doughnut = this.createDataChart(labelUsuarios, dataUsuarios, "Usuarios", "doughnut");
                this.createChart('users-chart-doughnut', loading_doughnut);
            });

            axios.get(route("reportes.total-incidentes-por-estado-incidente")).then((response) => {
                var labelIncidentes = response.data.labels;
                var dataIncidentes = response.data.data;
                var loading_bar = this.createDataChart(labelIncidentes, dataIncidentes, "Incidentes", "bar");
                this.createChart("incidentes-chart-bar", loading_bar);

                var loading_pie = this.createDataChart(labelIncidentes, dataIncidentes, "Incidentes", "pie");
                this.createChart('incidentes-chart-pie', loading_pie);

                var loading_doughnut = this.createDataChart(labelIncidentes, dataIncidentes, "Incidentes", "doughnut");
                this.createChart('incidentes-chart-doughnut', loading_doughnut);
            });
        },
        methods: {
            createDataChart(paramLabels, paramData, labelTitle, type) {
                var chartData = {
                    type: type,
                    data: {
                        labels: paramLabels,
                        datasets: [
                            {
                                label: labelTitle,
                                data: paramData,
                                backgroundColor: [
                                    '#00b3e7',
                                    "#d2e5a7",
                                    "#f9f9f9",
                                    "#2d2a36",
                                    "#31a34a",
                                ],
                                borderColor: [
                                    '#00b3e7',
                                    "#d2e5a7",
                                    "#f9f9f9",
                                    "#2d2a36",
                                    "#31a34a",
                                ],
                                borderWidth: 3
                            }
                        ]
                    },
                    options: {
                        legend: {
                            display: true,
                            labels: {
                                fontColor: 'rgb(255, 99, 132)'
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                }
                // console.log("chartData", chartData);
                return chartData;
            },
            createChart(chartId, chartData) {
                const ctx = document.getElementById(chartId);
                const myChart = new Chart(ctx, {
                    type: chartData.type,
                    data: chartData.data,
                    options: chartData.options
                });
            }
        }
    }
</script>