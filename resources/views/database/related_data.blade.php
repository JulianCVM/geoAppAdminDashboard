@extends('layouts.app')

@section('title', 'Datos Relacionados')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Datos Relacionados - {{ $schema }}.{{ $table }}</h5>
                    <a href="{{ route('database.table', [$schema, $table]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Mostrando datos relacionados para {{ $schema }}.{{ $table }} donde {{ $idColumn }} = {{ $idValue }}
                    </div>
                    
                    <!-- Datos de la entidad principal -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Datos Principales</h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($mainData) && is_array($mainData) && count($mainData) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Campo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mainData[0] as $field => $value)
                                                <tr>
                                                    <td><strong>{{ $field }}</strong></td>
                                                    <td>
                                                        @if(is_array($value) || (is_string($value) && strlen($value) > 100))
                                                            <button type="button" class="btn btn-sm btn-outline-primary json-viewer" data-json="{{ htmlspecialchars(json_encode($value)) }}">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </button>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No se encontraron datos para esta entidad.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Datos relacionados -->
                    <h4 class="mt-4 mb-3">
                        <i class="fas fa-project-diagram"></i> Tablas Relacionadas
                    </h4>
                    
                    <!-- Tablas que referencian a esta tabla -->
                    @if(!empty($relatedData) && count($relatedData) > 0)
                        <ul class="nav nav-tabs" id="relatedTabs" role="tablist">
                            @foreach($relatedData as $relatedTable => $tableData)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                            id="tab-{{ $relatedTable }}" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#content-{{ $relatedTable }}" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="content-{{ $relatedTable }}" 
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $relatedTable }}
                                        <span class="badge bg-secondary">{{ count($tableData['data']) }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="relatedTabsContent">
                            @foreach($relatedData as $relatedTable => $tableData)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                    id="content-{{ $relatedTable }}" 
                                    role="tabpanel" 
                                    aria-labelledby="tab-{{ $relatedTable }}">
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5>
                                            {{ $tableData['schema'] }}.{{ $tableData['table'] }}
                                            <small class="text-muted">
                                                ({{ $tableData['relation_type'] }} {{ $schema }}.{{ $table }})
                                            </small>
                                        </h5>
                                        <a href="{{ route('database.table', [$tableData['schema'], $tableData['table']]) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-table"></i> Ver Tabla Completa
                                        </a>
                                    </div>
                                    
                                    <div class="alert alert-secondary">
                                        Relación: {{ $tableData['local_column'] }} → {{ $tableData['foreign_column'] }}
                                        <br>
                                        Mostrando {{ count($tableData['data']) }} registros relacionados
                                    </div>
                                    
                                    @if(count($tableData['data']) > 0)
                                        @php
                                            // Extraer todas las claves
                                            $allKeys = [];
                                            foreach ($tableData['data'] as $row) {
                                                foreach (array_keys($row) as $key) {
                                                    if (!in_array($key, $allKeys)) {
                                                        $allKeys[] = $key;
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        @foreach($allKeys as $key)
                                                            <th>{{ $key }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tableData['data'] as $index => $row)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            @foreach($allKeys as $key)
                                                                <td>
                                                                    @if(array_key_exists($key, $row))
                                                                        @if(is_array($row[$key]) || (is_string($row[$key]) && strlen($row[$key]) > 100))
                                                                            <button type="button" class="btn btn-sm btn-outline-primary json-viewer" data-json="{{ htmlspecialchars(json_encode($row[$key])) }}">
                                                                                <i class="fas fa-eye"></i> Ver
                                                                            </button>
                                                                        @else
                                                                            {{ $row[$key] }}
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">NULL</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            No hay registros relacionados en esta tabla.
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No se encontraron tablas relacionadas con este registro.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar JSON -->
<div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsonModalLabel">Visualizador de Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar la visualización de JSON
        document.querySelectorAll('.json-viewer').forEach(function(button) {
            button.addEventListener('click', function() {
                const jsonData = this.getAttribute('data-json');
                try {
                    const parsedData = JSON.parse(jsonData);
                    document.getElementById('jsonContent').textContent = JSON.stringify(parsedData, null, 2);
                } catch (e) {
                    document.getElementById('jsonContent').textContent = jsonData;
                }
                
                const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
                modal.show();
            });
        });
    });
</script>
@endsection 