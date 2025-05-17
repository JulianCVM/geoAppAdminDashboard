@extends('layouts.app')

@section('title', 'Explorador de Base de Datos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Explorador de Base de Datos</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <h5>Esquemas Disponibles</h5>
                        <a href="{{ route('database.query') }}" class="btn btn-primary">
                            <i class="fas fa-terminal"></i> Consulta SQL
                        </a>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="list-group">
                                @forelse($schemas as $schema)
                                    <a href="{{ route('database.schema', $schema['table_schema']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-database me-2"></i> 
                                            {{ $schema['table_schema'] }}
                                        </span>
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </a>
                                @empty
                                    <div class="alert alert-warning">
                                        No se encontraron esquemas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Información</h5>
                                </div>
                                <div class="card-body">
                                    <p>Selecciona un esquema para ver sus tablas.</p>
                                    <p>Esquemas principales:</p>
                                    <ul>
                                        <li><strong>public</strong>: Contiene las tablas principales de la aplicación</li>
                                        <li><strong>auth</strong>: Contiene las tablas de autenticación y usuarios</li>
                                        <li><strong>storage</strong>: Contiene las tablas de almacenamiento de archivos</li>
                                    </ul>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('database.relations') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-project-diagram"></i> Ver Relaciones entre Tablas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 