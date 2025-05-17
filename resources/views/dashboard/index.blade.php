@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Panel de Control</h1>
    
    <!-- Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary-soft">
                <div class="card-body">
                    <h2>{{ $estadisticas['total_reportes'] }}</h2>
                    <p>Total de reportes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning-soft">
                <div class="card-body">
                    <h2>{{ $estadisticas['reportes_pendientes'] }}</h2>
                    <p>Reportes pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info-soft">
                <div class="card-body">
                    <h2>{{ $estadisticas['reportes_en_proceso'] }}</h2>
                    <p>Reportes en proceso</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success-soft">
                <div class="card-body">
                    <h2>{{ $estadisticas['reportes_resueltos'] }}</h2>
                    <p>Reportes resueltos</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimos reportes -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Últimos reportes</h5>
            <a href="{{ route('dashboard.reportes') }}" class="btn btn-sm btn-primary">Ver todos</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
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
                                    <span class="badge bg-secondary">{{ $reporte['tipo'] }}</span>
                                </td>
                                <td>
                                    @if($reporte['estado'] == 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($reporte['estado'] == 'en_proceso')
                                        <span class="badge bg-info">En proceso</span>
                                    @elseif($reporte['estado'] == 'resuelto')
                                        <span class="badge bg-success">Resuelto</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td>{{ number_format($reporte['latitud'], 6) }}, {{ number_format($reporte['longitud'], 6) }}</td>
                                <td>{{ date('d/m/Y H:i', strtotime($reporte['created_at'])) }}</td>
                                <td>
                                    <a href="{{ route('dashboard.reportes.show', $reporte['id']) }}" class="btn btn-sm btn-primary">
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
    </div>
</div>
@endsection 