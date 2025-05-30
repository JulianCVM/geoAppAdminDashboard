@extends('layouts.app')

@section('title', 'Tabla: ' . $table)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Tabla: {{ $schema }}.{{ $table }}</h5>
                    <div>
                        <a href="{{ route('database.schema', $schema) }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body" style="background-color: #F9F6F0;">
                    <ul class="nav nav-tabs" id="tableTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="structure-tab" data-bs-toggle="tab" data-bs-target="#structure" type="button" role="tab" aria-controls="structure" aria-selected="true" style="color: #2E8B57; border-color: #A5D6A7;">
                                <i class="fas fa-sitemap"></i> Estructura
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab" aria-controls="data" aria-selected="false" style="color: #2E8B57; border-color: #A5D6A7;">
                                <i class="fas fa-table"></i> Datos
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3" id="tableTabContent">
                        <!-- Estructura de la tabla -->
                        <div class="tab-pane fade show active" id="structure" role="tabpanel" aria-labelledby="structure-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead style="background-color: #A5D6A7;">
                                        <tr>
                                            <th style="color: #1B5E20;">Columna</th>
                                            <th style="color: #1B5E20;">Tipo de Dato</th>
                                            <th style="color: #1B5E20;">Nullable</th>
                                            <th style="color: #1B5E20;">Valor por Defecto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($structure as $column)
                                            <tr>
                                                <td>
                                                    <strong>{{ $column['column_name'] }}</strong>
                                                </td>
                                                <td>{{ $column['data_type'] }}</td>
                                                <td>
                                                    @if($column['is_nullable'] === 'YES')
                                                        <span class="badge" style="background-color: #4CAF50; color: white;">SI</span>
                                                    @else
                                                        <span class="badge" style="background-color: #E57373; color: white;">NO</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($column['column_default']))
                                                        <code>{{ $column['column_default'] }}</code>
                                                    @else
                                                        <span class="text-muted">NULL</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No se encontró información de la estructura.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Datos de la tabla -->
                        <div class="tab-pane fade" id="data" role="tabpanel" aria-labelledby="data-tab">
                            @if(!empty($data) && is_array($data) && count($data) > 0)
                                <div class="mt-3 mb-3">
                                    <div class="alert" style="background-color: #E8F5E9; border: 1px solid #A5D6A7; color: #1B5E20;">
                                        <i class="fas fa-info-circle"></i> Mostrando {{ count($data) }} registros (limitado a 1000 registros máximo)
                                    </div>
                                    
                                    @php
                                        // Extraer todas las claves de los registros
                                        $allKeys = [];
                                        foreach ($data as $row) {
                                            foreach (array_keys($row) as $key) {
                                                if (!in_array($key, $allKeys)) {
                                                    $allKeys[] = $key;
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover" style="font-size: 0.9rem;">
                                            <thead style="background-color: #A5D6A7;">
                                                <tr>
                                                    <th style="color: #1B5E20;">#</th>
                                                    @foreach($allKeys as $key)
                                                        <th style="color: #1B5E20;">{{ $key }}</th>
                                                    @endforeach
                                                    <th style="color: #1B5E20;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $index => $row)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        @foreach($allKeys as $key)
                                                            <td>
                                                                @if(array_key_exists($key, $row))
                                                                    @if(is_array($row[$key]) || (is_string($row[$key]) && strlen($row[$key]) > 100))
                                                                        <button type="button" class="btn btn-sm json-viewer" style="border-color: #2E8B57; color: #2E8B57;" data-json="{{ htmlspecialchars(json_encode($row[$key])) }}">
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
                                                        <td>
                                                            @php
                                                                // Identificar posible columna PK
                                                                $idColumn = null;
                                                                $idValue = null;
                                                                
                                                                if (array_key_exists('id', $row)) {
                                                                    $idColumn = 'id';
                                                                    $idValue = $row['id'];
                                                                } elseif (array_key_exists($table . '_id', $row)) {
                                                                    $idColumn = $table . '_id';
                                                                    $idValue = $row[$idColumn];
                                                                } else {
                                                                    // Buscar primera columna que termine en _id
                                                                    foreach ($allKeys as $key) {
                                                                        if (substr($key, -3) === '_id') {
                                                                            $idColumn = $key;
                                                                            $idValue = $row[$key];
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            
                                                            @if($idColumn && $idValue)
                                                                <a href="{{ route('database.related_data', ['schema' => $schema, 'table' => $table, 'id_column' => $idColumn, 'id_value' => $idValue]) }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                                                                    <i class="fas fa-project-diagram"></i> Relacionados
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                    <i class="fas fa-exclamation-triangle"></i> No hay datos para mostrar en esta tabla.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar JSON -->
<div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F5F5DC;">
                <h5 class="modal-title" id="jsonModalLabel" style="color: #1B5E20;">Visualizador de Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #F9F6F0;">
                <pre id="jsonContent" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto; border: 1px solid #A5D6A7;"></pre>
            </div>
            <div class="modal-footer" style="background-color: #F5F5DC;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #8FBC8F; color: white;">Cerrar</button>
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