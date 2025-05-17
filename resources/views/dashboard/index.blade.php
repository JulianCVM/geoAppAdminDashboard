@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4" style="color: #1B5E20;">Panel de Control</h1>
    
    <!-- Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card mb-4" style="border-color: #A5D6A7; border-left: 4px solid #2E8B57;">
                <div class="card-body" style="background-color: #F9F6F0;">
                    <h2 style="color: #2E8B57;">{{ $estadisticas['total_reportes'] }}</h2>
                    <p style="color: #1B5E20;">Total de reportes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card mb-4" style="border-color: #A5D6A7; border-left: 4px solid #FFB74D;">
                <div class="card-body" style="background-color: #F9F6F0;">
                    <h2 style="color: #FFB74D;">{{ $estadisticas['reportes_pendientes'] }}</h2>
                    <p style="color: #1B5E20;">Reportes pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card mb-4" style="border-color: #A5D6A7; border-left: 4px solid #64B5F6;">
                <div class="card-body" style="background-color: #F9F6F0;">
                    <h2 style="color: #64B5F6;">{{ $estadisticas['reportes_en_proceso'] }}</h2>
                    <p style="color: #1B5E20;">Reportes en proceso</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card mb-4" style="border-color: #A5D6A7; border-left: 4px solid #4CAF50;">
                <div class="card-body" style="background-color: #F9F6F0;">
                    <h2 style="color: #4CAF50;">{{ $estadisticas['reportes_resueltos'] }}</h2>
                    <p style="color: #1B5E20;">Reportes resueltos</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimos reportes -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
            <h5 class="mb-0" style="color: #1B5E20;">Últimos reportes</h5>
            <a href="{{ route('dashboard.reportes') }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">Ver todos</a>
        </div>
        <div class="card-body" style="background-color: #F9F6F0;">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr style="background-color: #A5D6A7;">
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($reportes, 0, 5) as $reporte)
                            <tr>
                                <td>{{ $reporte['id'] }}</td>
                                <td>
                                    <span class="badge" style="background-color: #66BB6A; color: white;">{{ $reporte['tipo'] }}</span>
                                </td>
                                <td>
                                    @if($reporte['estado'] == 'pendiente')
                                        <span class="badge" style="background-color: #FFB74D; color: #212121;">Pendiente</span>
                                    @elseif($reporte['estado'] == 'en_proceso')
                                        <span class="badge" style="background-color: #64B5F6; color: white;">En proceso</span>
                                    @elseif($reporte['estado'] == 'resuelto')
                                        <span class="badge" style="background-color: #4CAF50; color: white;">Resuelto</span>
                                    @else
                                        <span class="badge" style="background-color: #E57373; color: white;">Cancelado</span>
                                    @endif
                                </td>
                                <td>{{ number_format($reporte['latitud'], 6) }}, {{ number_format($reporte['longitud'], 6) }}</td>
                                <td>{{ date('d/m/Y H:i', strtotime($reporte['created_at'])) }}</td>
                                <td>
                                    <a href="{{ route('dashboard.reportes.show', $reporte['id']) }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay reportes disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" style="background-color: #F5F5DC;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge me-2" style="background-color: #FFB74D; color: #212121;">Pendiente</span>
                    <span class="badge me-2" style="background-color: #64B5F6; color: white;">En proceso</span>
                    <span class="badge me-2" style="background-color: #4CAF50; color: white;">Resuelto</span>
                    <span class="badge" style="background-color: #E57373; color: white;">Cancelado</span>
                </div>
                <div>
                    <button id="refreshBtn" class="btn" style="background-color: #2E8B57; color: white;">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón para actualizar la página
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.location.reload();
        });
    });
</script>
@endsection 