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

                    <!-- Alerta para la función get_table_relations -->
                    @if(isset($error) && strpos($error, 'get_table_relations') !== false)
                    <div class="alert mb-4" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                        <h5><i class="fas fa-exclamation-triangle"></i> Función get_table_relations requerida</h5>
                        <p>Para visualizar correctamente el diagrama de relaciones, se recomienda crear la función <code>get_table_relations</code> en Supabase.</p>
                        <hr>
                        <div style="background-color: #f5f5f5; border-radius: 4px; padding: 10px; margin-top: 10px;">
                            <pre style="white-space: pre-wrap; margin-bottom: 0;">
CREATE OR REPLACE FUNCTION public.get_table_relations()
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
    result JSONB;
BEGIN
    EXECUTE 'SELECT to_jsonb(array_agg(row_to_json(t))) FROM (
        SELECT
            tc.table_schema, 
            tc.constraint_name, 
            tc.table_name, 
            kcu.column_name, 
            ccu.table_schema AS foreign_table_schema,
            ccu.table_name AS foreign_table_name,
            ccu.column_name AS foreign_column_name 
        FROM 
            information_schema.table_constraints AS tc 
            JOIN information_schema.key_column_usage AS kcu
              ON tc.constraint_name = kcu.constraint_name
              AND tc.table_schema = kcu.table_schema
            JOIN information_schema.constraint_column_usage AS ccu
              ON ccu.constraint_name = tc.constraint_name
              AND ccu.table_schema = tc.table_schema
        WHERE tc.constraint_type = ''FOREIGN KEY''
        ORDER BY tc.table_schema, tc.table_name
    ) t' INTO result;
    
    RETURN result;
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('error', SQLERRM, 'detail', SQLSTATE);
END;
$$;</pre>
                        </div>
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
                                                <a href="{{ route('database.related_data', [
                                                    'schema' => $relation['table_schema'], 
                                                    'table' => $relation['table_name'],
                                                    'id_column' => $relation['column_name'],
                                                    'id_value' => '_placeholder_'
                                                ]) }}" 
                                                class="btn btn-sm view-relation-btn" 
                                                data-schema="{{ $relation['table_schema'] }}" 
                                                data-table="{{ $relation['table_name'] }}"
                                                data-column="{{ $relation['column_name'] }}"
                                                style="background-color: #2E8B57; color: white;">
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

<!-- Modal para seleccionar un valor de ID -->
<div class="modal fade" id="idValueModal" tabindex="-1" aria-labelledby="idValueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #F9F6F0; border-color: #A5D6A7;">
            <div class="modal-header" style="background-color: #F5F5DC; border-bottom-color: #A5D6A7;">
                <h5 class="modal-title" id="idValueModalLabel" style="color: #1B5E20;">Explorar Relación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="relationExploreForm">
                    <input type="hidden" id="modalSchema" name="schema">
                    <input type="hidden" id="modalTable" name="table">
                    <input type="hidden" id="modalColumn" name="id_column">
                    
                    <div class="mb-3">
                        <label for="idValue" class="form-label" style="color: #1B5E20;">Valor del ID para explorar</label>
                        <input type="text" class="form-control" id="idValue" name="id_value" placeholder="Ingresa el valor del ID" required style="border-color: #A5D6A7;">
                        <div class="form-text" style="color: #2E8B57;">
                            Ingresa el valor específico para la columna <span id="columnNameDisplay"></span> de la tabla <span id="tableNameDisplay"></span>.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top-color: #A5D6A7;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #A5D6A7; color: white;">Cancelar</button>
                <button type="button" class="btn" id="exploreRelationBtn" style="background-color: #2E8B57; color: white;">Explorar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/dist/vis-network.min.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/vis-network.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar botones de explorar relación
        const viewRelationBtns = document.querySelectorAll('.view-relation-btn');
        const relationExploreForm = document.getElementById('relationExploreForm');
        const idValueModal = new bootstrap.Modal(document.getElementById('idValueModal'));
        
        viewRelationBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const schema = this.getAttribute('data-schema');
                const table = this.getAttribute('data-table');
                const column = this.getAttribute('data-column');
                
                document.getElementById('modalSchema').value = schema;
                document.getElementById('modalTable').value = table;
                document.getElementById('modalColumn').value = column;
                document.getElementById('columnNameDisplay').textContent = column;
                document.getElementById('tableNameDisplay').textContent = table;
                
                idValueModal.show();
            });
        });
        
        document.getElementById('exploreRelationBtn').addEventListener('click', function() {
            const schema = document.getElementById('modalSchema').value;
            const table = document.getElementById('modalTable').value;
            const idColumn = document.getElementById('modalColumn').value;
            const idValue = document.getElementById('idValue').value;
            
            if (idValue) {
                window.location.href = `{{ url('dashboard/database/related-data') }}?schema=${schema}&table=${table}&id_column=${idColumn}&id_value=${idValue}`;
            }
        });
        
        // Crear diagrama de relaciones si hay datos
        @if(!empty($relations) && is_array($relations) && count($relations) > 0)
            try {
                console.log('Preparando datos para diagrama de relaciones');
                
                // Preparar datos para el diagrama
                const nodes = new vis.DataSet();
                const edges = new vis.DataSet();
                
                // Mapeo para evitar duplicados
                const addedNodes = {};
                
                @foreach($relations as $relation)
                    @if(is_array($relation) && isset($relation['table_schema']) && isset($relation['table_name']) && isset($relation['column_name']) && isset($relation['foreign_table_schema']) && isset($relation['foreign_table_name']) && isset($relation['foreign_column_name']))
                    try {
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
                            console.log('Añadido nodo origen:', sourceNodeId);
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
                            console.log('Añadido nodo destino:', targetNodeId);
                        }
                        
                        // Relación
                        edges.add({
                            from: sourceNodeId,
                            to: targetNodeId,
                            label: '{{ $relation['column_name'] }} → {{ $relation['foreign_column_name'] }}',
                            arrows: 'to',
                            title: '{{ isset($relation['constraint_name']) ? $relation['constraint_name'] : '' }}'
                        });
                        console.log('Añadida relación:', sourceNodeId, '->', targetNodeId);
                    } catch (err) {
                        console.error('Error al procesar relación:', err);
                    }
                    @endif
                @endforeach
                
                console.log('Nodos creados:', nodes.length);
                console.log('Relaciones creadas:', edges.length);
                
                // Verificar si tenemos nodos para mostrar
                if (nodes.length > 0) {
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
                            width: 2,
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
                            improvedLayout: true
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
                        },
                        interaction: {
                            hover: true,
                            tooltipDelay: 200
                        }
                    };
                    
                    console.log('Creando red de visualización');
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
                    
                    network.on('stabilizationIterationsDone', function() {
                        console.log('Diagrama estabilizado');
                    });
                } else {
                    console.warn('No hay nodos para mostrar en el diagrama');
                    document.getElementById('relationDiagram').innerHTML = '<div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;"><i class="fas fa-exclamation-triangle"></i> No hay suficientes datos para mostrar el diagrama.</div>';
                }
            } catch (error) {
                console.error('Error al crear diagrama:', error);
                document.getElementById('relationDiagram').innerHTML = '<div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #EF9A9A;"><i class="fas fa-exclamation-circle"></i> Error al renderizar el diagrama: ' + error.message + '</div>';
            }
        @endif
    });
</script>
@endsection 