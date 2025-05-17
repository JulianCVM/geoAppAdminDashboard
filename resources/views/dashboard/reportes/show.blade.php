@extends('layouts.app')

@section('title', 'Detalle de Reporte')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalle del Reporte #{{ $reporte['id'] }}</h1>
        <a href="{{ route('dashboard.reportes') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>ID:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $reporte['id'] }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipo:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $reporte['tipo'] }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Estado actual:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($reporte['estado'] == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($reporte['estado'] == 'en_proceso')
                                <span class="badge bg-info">En proceso</span>
                            @elseif($reporte['estado'] == 'resuelto')
                                <span class="badge bg-success">Resuelto</span>
                            @else
                                <span class="badge bg-danger">Cancelado</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Importancia:</strong>
                        </div>
                        <div class="col-md-8">
                            @for($i = 0; $i < $reporte['importancia']; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor
                            @for($i = $reporte['importancia']; $i < 5; $i++)
                                <i class="far fa-star text-warning"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Ubicación:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <span>Lat: {{ number_format($reporte['latitud'], 6) }}, Long: {{ number_format($reporte['longitud'], 6) }}</span>
                                <a href="https://www.google.com/maps?q={{ $reporte['latitud'] }},{{ $reporte['longitud'] }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                    <i class="fas fa-map-marker-alt"></i> Ver en mapa
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Fecha de creación:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ date('d/m/Y H:i', strtotime($reporte['created_at'])) }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Última actualización:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ date('d/m/Y H:i', strtotime($reporte['updated_at'])) }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Usuario:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $reporte['email'] ?: 'Anónimo' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Vistas:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $reporte['vistas'] }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tags de tipo:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($reporte['tipo_tags'])
                                @foreach(explode(',', $reporte['tipo_tags']) as $tag)
                                    <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Sin tags</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tags de ubicación:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($reporte['ubicacion_tags'])
                                @foreach(explode(',', $reporte['ubicacion_tags']) as $tag)
                                    <span class="badge bg-info me-1">{{ trim($tag) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Sin tags</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    <p>{{ $reporte['descripcion'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Imagen</h5>
                </div>
                <div class="card-body text-center">
                    @if($reporte['imagen'])
                        <img src="{{ $reporte['imagen'] }}" alt="Imagen del reporte" class="img-fluid rounded" style="max-height: 300px;">
                        <div class="mt-2">
                            <a href="{{ $reporte['imagen'] }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Ver imagen completa
                            </a>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>No hay imagen disponible</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actualizar Estado</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.reportes.update-estado', $reporte['id']) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado del reporte</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="pendiente" {{ $reporte['estado'] == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ $reporte['estado'] == 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                                <option value="resuelto" {{ $reporte['estado'] == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                <option value="cancelado" {{ $reporte['estado'] == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar Estado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 