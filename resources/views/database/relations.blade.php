@extends('layouts.app')

@section('title', 'Relaciones entre Tablas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Relaciones entre Tablas</h5>
                    <a href="{{ route('database.index') }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <!-- Alerta para la función execute_sql -->
                    @if(session('execute_sql_error') || (isset($error) && strpos($error, 'execute_sql') !== false))
                    <div class="alert mb-4" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #EF9A9A;">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error: Función execute_sql no encontrada</h5>
                        <p>Se requiere la función <code>execute_sql</code> en la base de datos Supabase para visualizar las relaciones entre tablas.</p>
                        <hr>
                        <p class="mb-0">
                            <a href="{{ route('database.connection.test') }}" class="alert-link" style="color: #B71C1C; text-decoration: underline;">
                                Haz clic aquí para ver instrucciones de cómo crear esta función
                            </a>
                        </p>
                    </div>
                    @endif

                    <div class="alert" style="background-color: #E8F5E9; border: 1px solid #A5D6A7; color: #1B5E20;">
                        <i class="fas fa-info-circle"></i> Esta página muestra todas las relaciones de clave foránea (foreign keys) entre tablas.
                    </div>
                    
                    @if(!empty($relations) && is_array($relations) && count($relations) > 0)
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: #A5D6A7;">
                                    <tr>
                                        <th style="color: #1B5E20;">Esquema</th>
                                        <th style="color: #1B5E20;">Tabla</th>
                                        <th style="color: #1B5E20;">Columna</th>
                                        <th style="color: #1B5E20;">Referencia</th>
                                        <th style="color: #1B5E20;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($relations as $relation)
                                        @if(is_array($relation) && isset($relation['table_schema']) && isset($relation['table_name']) && isset($relation['column_name']) && isset($relation['foreign_table_schema']) && isset($relation['foreign_table_name']) && isset($relation['foreign_column_name']))
                                        <tr>
                                            <td>{{ $relation['table_schema'] }}</td>
                                            <td>
                                                <a href="{{ route('database.table', [$relation['table_schema'], $relation['table_name']]) }}" style="color: #2E8B57;">
                                                    {{ $relation['table_name'] }}
                                                </a>
                                            </td>
                                            <td>{{ $relation['column_name'] }}</td>
                                            <td>
                                                <a href="{{ route('database.table', [$relation['foreign_table_schema'], $relation['foreign_table_name']]) }}" style="color: #2E8B57;">
                                                    {{ $relation['foreign_table_schema'] }}.{{ $relation['foreign_table_name'] }}.{{ $relation['foreign_column_name'] }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
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
                            <h4 style="color: #1B5E20;">Diagrama de Relaciones</h4>
                            <div id="relationDiagram" style="height: 600px; border: 1px solid #A5D6A7; border-radius: 4px;"></div>
                        </div>
                    @else
                        <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
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