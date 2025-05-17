@extends('layouts.app')

@section('title', 'Estadísticas')

@section('styles')
<style>
    .stat-card {
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .tab-pane {
        padding: 20px 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Panel de Estadísticas</h1>
        <div>
            <button id="descargarInforme" class="btn" style="background-color: #2E8B57; color: white;">
                <i class="fas fa-download me-1"></i> Descargar Informe
            </button>
        </div>
    </div>
    
    <!-- Cards de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #F5F5DC; border-left: 5px solid #2E8B57;">
                <h2 style="color: #2E8B57; font-size: 2rem;">{{ $estadisticas['total_reportes'] }}</h2>
                <p style="color: #1B5E20;">Total de reportes</p>
                <i class="fas fa-clipboard-list" style="font-size: 2rem; color: #8FBC8F; position: absolute; top: 15px; right: 15px; opacity: 0.3;"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #FFF3E0; border-left: 5px solid #FFB74D;">
                <h2 style="color: #E65100; font-size: 2rem;">{{ $estadisticas['reportes_pendientes'] }}</h2>
                <p style="color: #E65100;">Reportes pendientes</p>
                <i class="fas fa-clock" style="font-size: 2rem; color: #FFB74D; position: absolute; top: 15px; right: 15px; opacity: 0.3;"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #E1F5FE; border-left: 5px solid #64B5F6;">
                <h2 style="color: #0277BD; font-size: 2rem;">{{ $estadisticas['reportes_en_proceso'] }}</h2>
                <p style="color: #0277BD;">Reportes en proceso</p>
                <i class="fas fa-spinner" style="font-size: 2rem; color: #64B5F6; position: absolute; top: 15px; right: 15px; opacity: 0.3;"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #E8F5E9; border-left: 5px solid #4CAF50;">
                <h2 style="color: #2E7D32; font-size: 2rem;">{{ $estadisticas['reportes_resueltos'] }}</h2>
                <p style="color: #2E7D32;">Reportes resueltos</p>
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #4CAF50; position: absolute; top: 15px; right: 15px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <!-- Pestañas de gráficos -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: #F5F5DC;">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="estados-tab" data-bs-toggle="tab" data-bs-target="#estados" type="button" role="tab" aria-controls="estados" aria-selected="true" style="color: #2E8B57;">
                        Estado de reportes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="importancia-tab" data-bs-toggle="tab" data-bs-target="#importancia" type="button" role="tab" aria-controls="importancia" aria-selected="false" style="color: #2E8B57;">
                        Importancia
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tendencia-tab" data-bs-toggle="tab" data-bs-target="#tendencia" type="button" role="tab" aria-controls="tendencia" aria-selected="false" style="color: #2E8B57;">
                        Tendencia temporal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tipos-tab" data-bs-toggle="tab" data-bs-target="#tipos" type="button" role="tab" aria-controls="tipos" aria-selected="false" style="color: #2E8B57;">
                        Tipos de reporte
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body" style="background-color: #F9F6F0;">
            <div class="tab-content" id="myTabContent">
                <!-- Gráfico de estados -->
                <div class="tab-pane fade show active" id="estados" role="tabpanel" aria-labelledby="estados-tab">
                    <div class="chart-container">
                        <canvas id="chartEstados"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #2E8B57;">Distribución por estado</h5>
                            <p>Este gráfico muestra la distribución de reportes según su estado actual.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <span class="badge me-2" style="background-color: #FFB74D; color: #212121;">Pendiente: {{ $estadisticas['reportes_pendientes'] }}</span>
                                <span class="badge me-2" style="background-color: #64B5F6; color: white;">En proceso: {{ $estadisticas['reportes_en_proceso'] }}</span>
                                <span class="badge me-2" style="background-color: #4CAF50; color: white;">Resuelto: {{ $estadisticas['reportes_resueltos'] }}</span>
                                <span class="badge" style="background-color: #E57373; color: white;">Cancelado: {{ $estadisticas['reportes_cancelados'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de importancia -->
                <div class="tab-pane fade" id="importancia" role="tabpanel" aria-labelledby="importancia-tab">
                    <div class="chart-container">
                        <canvas id="chartImportancia"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #2E8B57;">Distribución por importancia</h5>
                            <p>Este gráfico muestra la distribución de reportes según su nivel de importancia.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de tendencia temporal -->
                <div class="tab-pane fade" id="tendencia" role="tabpanel" aria-labelledby="tendencia-tab">
                    <div class="chart-container">
                        <canvas id="chartTendencia"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #2E8B57;">Tendencia de reportes mensuales</h5>
                            <p>Este gráfico muestra la evolución de reportes a lo largo del tiempo.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de tipos de reporte -->
                <div class="tab-pane fade" id="tipos" role="tabpanel" aria-labelledby="tipos-tab">
                    <div class="chart-container">
                        <canvas id="chartTipos"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #2E8B57;">Distribución por tipo de reporte</h5>
                            <p>Este gráfico muestra la cantidad de reportes según su tipo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" style="background-color: #F5F5DC;">
            <small class="text-muted">Datos actualizados al {{ date('d/m/Y H:i') }}</small>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración común para los gráficos
        Chart.defaults.color = '#212121';
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        
        // Gráfico de estados
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($datosEstados['labels']) !!},
                datasets: [{
                    data: {!! json_encode($datosEstados['data']) !!},
                    backgroundColor: {!! json_encode($datosEstados['colors']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Gráfico de importancia
        const ctxImportancia = document.getElementById('chartImportancia').getContext('2d');
        new Chart(ctxImportancia, {
            type: 'bar',
            data: {
                labels: {!! json_encode($datosImportancia['labels']) !!},
                datasets: [{
                    label: 'Cantidad de reportes',
                    data: {!! json_encode($datosImportancia['data']) !!},
                    backgroundColor: {!! json_encode($datosImportancia['colors']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de tendencia
        const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
        new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: {!! json_encode($datosTendencia['labels']) !!},
                datasets: [{
                    label: 'Reportes por mes',
                    data: {!! json_encode($datosTendencia['data']) !!},
                    backgroundColor: {!! json_encode($datosTendencia['color']) !!},
                    borderColor: {!! json_encode($datosTendencia['color']) !!},
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de tipos
        const ctxTipos = document.getElementById('chartTipos').getContext('2d');
        new Chart(ctxTipos, {
            type: 'pie',
            data: {
                labels: {!! json_encode($datosTipos['labels']) !!},
                datasets: [{
                    data: {!! json_encode($datosTipos['data']) !!},
                    backgroundColor: {!! json_encode($datosTipos['colors']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Botón para descargar informe
        document.getElementById('descargarInforme').addEventListener('click', function() {
            window.location.href = '{{ route("estadisticas.informe.json") }}';
        });
    });
</script>
@endsection 