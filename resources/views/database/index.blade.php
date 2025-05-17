@extends('layouts.app')

@section('title', 'Explorador de Base de Datos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Explorador de Base de Datos</h5>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <!-- Alerta para la función execute_sql -->
                    @if(session('execute_sql_error') || (isset($error) && strpos($error, 'execute_sql') !== false))
                    <div class="alert mb-4" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #EF9A9A;">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error: Función execute_sql no encontrada</h5>
                        <p>Se requiere la función <code>execute_sql</code> en la base de datos Supabase para usar el explorador de base de datos.</p>
                        <hr>
                        <p class="mb-0">
                            <a href="{{ route('database.connection.test') }}" class="alert-link" style="color: #B71C1C; text-decoration: underline;">
                                Haz clic aquí para ver instrucciones de cómo crear esta función
                            </a>
                        </p>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mb-4">
                        <h5 style="color: #2E8B57;">Esquemas Disponibles</h5>
                        <a href="{{ route('database.query') }}" class="btn" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-terminal"></i> Consulta SQL
                        </a>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="list-group">
                                @forelse($schemas as $schema)
                                    <a href="{{ route('database.schema', $schema['table_schema']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                                        <span style="color: #1B5E20;">
                                            <i class="fas fa-database me-2" style="color: #2E8B57;"></i> 
                                            {{ $schema['table_schema'] }}
                                        </span>
                                        <span class="badge" style="background-color: #2E8B57; color: white;">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </a>
                                @empty
                                    <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No se encontraron esquemas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card" style="border-color: #8FBC8F;">
                                <div class="card-header" style="background-color: #A5D6A7; color: #1B5E20;">
                                    <h5 class="mb-0">Información</h5>
                                </div>
                                <div class="card-body" style="background-color: #F9F6F0;">
                                    <p style="color: #1B5E20;">Selecciona un esquema para ver sus tablas.</p>
                                    <p style="color: #2E8B57; font-weight: bold;">Esquemas principales:</p>
                                    <ul style="color: #1B5E20;">
                                        <li><strong>public</strong>: Contiene las tablas principales de la aplicación</li>
                                        <li><strong>auth</strong>: Contiene las tablas de autenticación y usuarios</li>
                                        <li><strong>storage</strong>: Contiene las tablas de almacenamiento de archivos</li>
                                    </ul>
                                    
                                    <div class="mt-4 d-grid">
                                        <a href="{{ route('database.relations') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                                            <i class="fas fa-project-diagram"></i> Ver Relaciones entre Tablas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background-color: #F5F5DC;">
                    <a href="{{ route('dashboard') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 