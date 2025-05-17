@extends('layouts.app')

@section('title', 'Diagnóstico de Conexión Secundaria')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Diagnóstico de Conexión a Base de Datos Secundaria</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta herramienta verifica la conexión a la base de datos secundaria y detecta los nombres de las tablas.
                    </div>
                    
                    <h6 class="fw-bold">Información de Conexión:</h6>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            URL de Supabase Secundario
                            <span>{{ $secondary_url }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            API Key (longitud)
                            <span>{{ $key_length }} caracteres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Service Key (longitud)
                            <span>{{ $service_key_length }} caracteres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Estado de Conexión
                            @if($connectionResult['connection'] ?? false)
                                <span class="badge bg-success">Conectado</span>
                            @else
                                <span class="badge bg-danger">Error de Conexión</span>
                            @endif
                        </li>
                    </ul>
                    
                    @if(isset($connectionResult['error']))
                        <div class="alert alert-danger">
                            <strong>Error:</strong> {{ $connectionResult['error'] }}
                        </div>
                    @endif
                    
                    @if(isset($connectionResult['schemas']) && count($connectionResult['schemas']) > 0)
                        <h6 class="fw-bold">Esquemas Detectados:</h6>
                        <ul class="list-group mb-4">
                            @foreach($connectionResult['schemas'] as $schema)
                                <li class="list-group-item">{{ $schema }}</li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @if(isset($tableNames) && is_array($tableNames))
                        <h6 class="fw-bold">Tablas Detectadas:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo de Tabla</th>
                                        <th>Esquema</th>
                                        <th>Nombre de Tabla</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableNames as $type => $info)
                                        <tr>
                                            <td>{{ ucfirst($type) }}</td>
                                            <td>{{ $info['schema'] ?? 'public' }}</td>
                                            <td>{{ $info['table'] ?? 'No detectado' }}</td>
                                            <td>
                                                @if($info['found'] ?? false)
                                                    <span class="badge bg-success">Encontrada</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">No encontrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    @if(isset($connectionResult['tables']) && is_array($connectionResult['tables']))
                        <h6 class="fw-bold">Pruebas de Acceso:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tabla</th>
                                        <th>Acceso con Token Usuario</th>
                                        <th>Acceso con Service Key</th>
                                        <th>Políticas RLS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($connectionResult['tables'] as $table => $info)
                                        <tr>
                                            <td>{{ $table }}</td>
                                            <td>
                                                @if($info['accessible_with_user_token'] ?? false)
                                                    <span class="badge bg-success">Accesible</span>
                                                    ({{ $info['rows_with_user_token'] ?? 0 }} filas)
                                                @else
                                                    <span class="badge bg-danger">No accesible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($info['accessible_with_service_key'] ?? false)
                                                    <span class="badge bg-success">Accesible</span>
                                                    ({{ $info['rows_with_service_key'] ?? 0 }} filas)
                                                @else
                                                    <span class="badge bg-danger">No accesible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($info['has_rls_policies'] ?? false)
                                                    <span class="badge bg-primary">{{ $info['policies_count'] ?? 0 }} políticas</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Sin políticas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                    </a>
                    <button class="btn btn-success ms-2" id="refreshBtn">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar Diagnóstico
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