@extends('layouts.app')

@section('title', 'Diagnóstico de Conexión Secundaria')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Diagnóstico de Conexión a Base de Datos Secundaria</h5>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <div class="alert" style="background-color: #A5D6A7; color: #1B5E20; border: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta herramienta verifica la conexión a la base de datos secundaria y detecta los nombres de las tablas.
                    </div>
                    
                    <h6 class="fw-bold" style="color: #2E8B57;">Información de Conexión:</h6>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                            URL de Supabase Secundario
                            <span>{{ $secondary_url }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                            API Key (longitud)
                            <span>{{ $key_length }} caracteres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                            Service Key (longitud)
                            <span>{{ $service_key_length }} caracteres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                            Estado de Conexión
                            @if($connectionResult['connection'] ?? false)
                                <span class="badge" style="background-color: #4CAF50; color: white;">Conectado</span>
                            @else
                                <span class="badge" style="background-color: #E57373; color: white;">Error de Conexión</span>
                            @endif
                        </li>
                    </ul>
                    
                    @if(isset($connectionResult['error']))
                        <div class="alert" style="background-color: #FFF3E0; color: #E57373; border: 1px solid #E57373;">
                            <strong>Error:</strong> {{ $connectionResult['error'] }}
                        </div>
                    @endif
                    
                    @if(isset($connectionResult['schemas']) && count($connectionResult['schemas']) > 0)
                        <h6 class="fw-bold" style="color: #2E8B57;">Esquemas Detectados:</h6>
                        <ul class="list-group mb-4">
                            @foreach($connectionResult['schemas'] as $schema)
                                <li class="list-group-item" style="background-color: #F9F6F0; border-color: #8FBC8F;">{{ $schema }}</li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @if(isset($tableNames) && is_array($tableNames))
                        <h6 class="fw-bold" style="color: #2E8B57;">Tablas Detectadas:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead style="background-color: #A5D6A7;">
                                    <tr>
                                        <th>Tipo de Tabla</th>
                                        <th>Esquema</th>
                                        <th>Nombre de Tabla</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color: #F9F6F0;">
                                    @foreach($tableNames as $type => $info)
                                        <tr>
                                            <td>{{ ucfirst($type) }}</td>
                                            <td>{{ $info['schema'] ?? 'public' }}</td>
                                            <td>{{ $info['table'] ?? 'No detectado' }}</td>
                                            <td>
                                                @if($info['found'] ?? false)
                                                    <span class="badge" style="background-color: #4CAF50; color: white;">Encontrada</span>
                                                @else
                                                    <span class="badge" style="background-color: #FFB74D; color: #212121;">No encontrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    @if(isset($connectionResult['tables']) && is_array($connectionResult['tables']))
                        <h6 class="fw-bold" style="color: #2E8B57;">Pruebas de Acceso:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead style="background-color: #A5D6A7;">
                                    <tr>
                                        <th>Tabla</th>
                                        <th>Acceso con Token Usuario</th>
                                        <th>Acceso con Service Key</th>
                                        <th>Políticas RLS</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color: #F9F6F0;">
                                    @foreach($connectionResult['tables'] as $table => $info)
                                        <tr>
                                            <td>{{ $table }}</td>
                                            <td>
                                                @if($info['accessible_with_user_token'] ?? false)
                                                    <span class="badge" style="background-color: #4CAF50; color: white;">Accesible</span>
                                                    ({{ $info['rows_with_user_token'] ?? 0 }} filas)
                                                @else
                                                    <span class="badge" style="background-color: #E57373; color: white;">No accesible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($info['accessible_with_service_key'] ?? false)
                                                    <span class="badge" style="background-color: #4CAF50; color: white;">Accesible</span>
                                                    ({{ $info['rows_with_service_key'] ?? 0 }} filas)
                                                @else
                                                    <span class="badge" style="background-color: #E57373; color: white;">No accesible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($info['has_rls_policies'] ?? false)
                                                    <span class="badge" style="background-color: #64B5F6; color: white;">{{ $info['policies_count'] ?? 0 }} políticas</span>
                                                @else
                                                    <span class="badge" style="background-color: #FFB74D; color: #212121;">Sin políticas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer" style="background-color: #F5F5DC;">
                    <a href="{{ route('dashboard') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                    </a>
                    <button class="btn ms-2" id="refreshBtn" style="background-color: #2E8B57; color: white;">
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