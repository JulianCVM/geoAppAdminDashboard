@extends('layouts.app')

@section('title', 'Relaciones entre Tablas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Relaciones entre Tablas</h5>
                    <a href="{{ route('database.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Esta página muestra todas las relaciones de clave foránea (foreign keys) entre tablas.
                    </div>
                    
                    @if(!empty($relations) && is_array($relations) && count($relations) > 0)
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Esquema</th>
                                        <th>Tabla</th>
                                        <th>Columna</th>
                                        <th>Referencia</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($relations as $relation)
                                        @if(is_array($relation) && isset($relation['table_schema']) && isset($relation['table_name']) && isset($relation['column_name']) && isset($relation['foreign_table_schema']) && isset($relation['foreign_table_name']) && isset($relation['foreign_column_name']))
                                        <tr>
                                            <td>{{ $relation['table_schema'] }}</td>
                                            <td>
                                                <a href="{{ route('database.table', [$relation['table_schema'], $relation['table_name']]) }}">
                                                    {{ $relation['table_name'] }}
                                                </a>
                                            </td>
                                            <td>{{ $relation['column_name'] }}</td>
                                            <td>
                                                <a href="{{ route('database.table', [$relation['foreign_table_schema'], $relation['foreign_table_name']]) }}">
                                                    {{ $relation['foreign_table_schema'] }}.{{ $relation['foreign_table_name'] }}.{{ $relation['foreign_column_name'] }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-search"></i> Explorar
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Diagrama visual de relaciones -->
                        <div class="mt-5">
                            <h4>Diagrama de Relaciones</h4>
                            <div id="relationDiagram" style="height: 600px; border: 1px solid #ddd; border-radius: 4px;"></div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No se encontraron relaciones entre tablas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/vis-network@9.1.0/dist/dist/vis-network.min.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.0/dist/vis-network.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Crear diagrama de relaciones si hay datos
        @if(!empty($relations) && is_array($relations) && count($relations) > 0)
            // Preparar datos para el diagrama
            const nodes = new vis.DataSet();
            const edges = new vis.DataSet();
            
            // Mapeo para evitar duplicados
            const addedNodes = {};
            
            @foreach($relations as $relation)
                @if(is_array($relation) && isset($relation['table_schema']) && isset($relation['table_name']) && isset($relation['column_name']) && isset($relation['foreign_table_schema']) && isset($relation['foreign_table_name']) && isset($relation['foreign_column_name']))
                // Nodo origen
                const sourceNodeId = '{{ $relation['table_schema'] }}.{{ $relation['table_name'] }}';
                if (!addedNodes[sourceNodeId]) {
                    nodes.add({
                        id: sourceNodeId,
                        label: '{{ $relation['table_name'] }}',
                        title: '{{ $relation['table_schema'] }}.{{ $relation['table_name'] }}',
                        group: '{{ $relation['table_schema'] }}'
                    });
                    addedNodes[sourceNodeId] = true;
                }
                
                // Nodo destino
                const targetNodeId = '{{ $relation['foreign_table_schema'] }}.{{ $relation['foreign_table_name'] }}';
                if (!addedNodes[targetNodeId]) {
                    nodes.add({
                        id: targetNodeId,
                        label: '{{ $relation['foreign_table_name'] }}',
                        title: '{{ $relation['foreign_table_schema'] }}.{{ $relation['foreign_table_name'] }}',
                        group: '{{ $relation['foreign_table_schema'] }}'
                    });
                    addedNodes[targetNodeId] = true;
                }
                
                // Relación
                edges.add({
                    from: sourceNodeId,
                    to: targetNodeId,
                    label: '{{ $relation['column_name'] }} → {{ $relation['foreign_column_name'] }}',
                    arrows: 'to',
                    title: '{{ isset($relation['constraint_name']) ? $relation['constraint_name'] : '' }}'
                });
                @endif
            @endforeach
            
            // Configuración del diagrama
            const container = document.getElementById('relationDiagram');
            const data = {
                nodes: nodes,
                edges: edges
            };
            const options = {
                nodes: {
                    shape: 'box',
                    margin: 10,
                    font: {
                        size: 14
                    }
                },
                edges: {
                    font: {
                        size: 12,
                        align: 'middle'
                    },
                    length: 250
                },
                physics: {
                    enabled: true,
                    stabilization: {
                        iterations: 200
                    },
                    barnesHut: {
                        gravitationalConstant: -10000,
                        springConstant: 0.002
                    }
                },
                layout: {
                    hierarchical: {
                        enabled: false
                    }
                },
                groups: {
                    'public': {
                        color: { background: '#4CAF50', border: '#2E7D32' }
                    },
                    'auth': {
                        color: { background: '#2196F3', border: '#0D47A1' }
                    },
                    'storage': {
                        color: { background: '#FF9800', border: '#E65100' }
                    }
                }
            };
            
            // Crear la red
            const network = new vis.Network(container, data, options);
            
            // Evento al hacer clic en un nodo
            network.on('doubleClick', function(params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    const [schema, table] = nodeId.split('.');
                    if (schema && table) {
                        window.location.href = '{{ url("dashboard/database") }}/' + schema + '/' + table;
                    }
                }
            });
        @endif
    });
</script>
@endsection 