@extends('layouts.app')

@section('title', 'Resultado de Diagnóstico')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultado del Diagnóstico</h5>
                    <a href="{{ route('database.connection') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($result['connection']) && $result['connection'])
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Conexión exitosa a la API de Supabase.</strong>
                        </div>
                        
                        <!-- Esquemas -->
                        <div class="mt-4">
                            <h6>Esquemas encontrados:</h6>
                            @if(!empty($result['schemas']))
                                <ul>
                                    @foreach($result['schemas'] as $schema)
                                        <li><code>{{ $schema }}</code></li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No se encontraron esquemas o no se pudo obtener la información.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Tablas -->
                        <div class="mt-4">
                            <h6>Acceso a tablas:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tabla</th>
                                            <th>Acceso con token de usuario</th>
                                            <th>Acceso con service key</th>
                                            <th>Tiene políticas RLS</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['tables'] as $tableName => $tableData)
                                            <tr>
                                                <td><code>{{ $tableName }}</code></td>
                                                <td>
                                                    @if(isset($tableData['accessible_with_user_token']) && $tableData['accessible_with_user_token'])
                                                        <span class="badge bg-success">Accesible</span>
                                                        @if(isset($tableData['rows_with_user_token']))
                                                            <span class="badge bg-secondary">{{ $tableData['rows_with_user_token'] }} filas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-danger">No accesible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($tableData['accessible_with_service_key']) && $tableData['accessible_with_service_key'])
                                                        <span class="badge bg-success">Accesible</span>
                                                        @if(isset($tableData['rows_with_service_key']))
                                                            <span class="badge bg-secondary">{{ $tableData['rows_with_service_key'] }} filas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-danger">No accesible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($tableData['has_rls_policies']))
                                                        @if($tableData['has_rls_policies'])
                                                            <span class="badge bg-warning text-dark">Sí ({{ $tableData['policies_count'] ?? 'desconocido' }})</span>
                                                        @else
                                                            <span class="badge bg-success">No</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Desconocido</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm bypass-rls-btn" 
                                                            data-table="{{ $tableName }}"
                                                            data-schema="public">
                                                        <i class="fas fa-unlock me-1"></i> Intentar sin RLS
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Recomendaciones -->
                        <div class="mt-4">
                            <h6>Recomendaciones:</h6>
                            <ul>
                                @php $problemsFound = false; @endphp
                                
                                @foreach($result['tables'] as $tableName => $tableData)
                                    @if(isset($tableData['accessible_with_user_token']) && !$tableData['accessible_with_user_token'])
                                        <li class="text-danger">
                                            La tabla <code>{{ $tableName }}</code> no es accesible con el token de usuario. 
                                            Verifica las políticas RLS en Supabase.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                    
                                    @if(isset($tableData['accessible_with_service_key']) && !$tableData['accessible_with_service_key'])
                                        <li class="text-danger">
                                            La tabla <code>{{ $tableName }}</code> no es accesible ni siquiera con la service key. 
                                            Esto podría indicar que la tabla no existe o tiene otro nombre.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                    
                                    @if(isset($tableData['has_rls_policies']) && $tableData['has_rls_policies'])
                                        <li class="text-warning">
                                            La tabla <code>{{ $tableName }}</code> tiene {{ $tableData['policies_count'] ?? 'algunas' }} políticas RLS. 
                                            Deberías revisar estas políticas para permitir acceso al token de servicio.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                @endforeach
                                
                                @if(!$problemsFound)
                                    <li class="text-success">
                                        No se detectaron problemas evidentes. Si aún experimentas problemas, verifica los nombres exactos de las tablas y esquemas.
                                    </li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- Soluciones -->
                        <div class="mt-4">
                            <h6>Posibles soluciones:</h6>
                            <ol>
                                <li>
                                    <strong>Configurar políticas RLS:</strong> En Supabase, ve a la sección "Authentication" > "Policies" 
                                    y configura políticas que permitan acceso a las tablas para el rol "service_role".
                                </li>
                                <li>
                                    <strong>Verificar claves de servicio:</strong> Asegúrate de que en <code>config/supabase.php</code> 
                                    estás usando la clave de servicio correcta (service_role key, no la anon key).
                                </li>
                                <li>
                                    <strong>Nombres de tablas:</strong> Verifica que los nombres de las tablas en tu código coincidan 
                                    exactamente con los nombres en Supabase, incluyendo mayúsculas/minúsculas.
                                </li>
                                <li>
                                    <strong>Prefijo de esquema:</strong> Para algunas tablas puedes necesitar especificar el esquema, 
                                    por ejemplo <code>public.usuarios</code> en lugar de solo <code>usuarios</code>.
                                </li>
                            </ol>
                        </div>
                        
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Error de conexión:</strong> {{ $result['error'] ?? 'No se pudo establecer conexión con Supabase.' }}
                        </div>
                        
                        <div class="mt-4">
                            <h6>Posibles causas:</h6>
                            <ul>
                                <li>URL incorrecta de Supabase en la configuración</li>
                                <li>Claves de API inválidas (API Key o Service Key)</li>
                                <li>Servicios de Supabase no disponibles</li>
                                <li>Problemas de red o firewall</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Pasos para solucionar:</h6>
                            <ol>
                                <li>Verifica la URL y claves en <code>config/supabase.php</code></li>
                                <li>Comprueba que Supabase está operativo visitando el panel de control</li>
                                <li>Verifica permisos y configuración de red</li>
                            </ol>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 col-lg-6 mx-auto mt-4">
                        <a href="{{ route('database.connection.test') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-2"></i> Repetir Diagnóstico
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para mostrar datos sin RLS -->
    <div class="modal fade" id="bypassRlsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos sin restricciones RLS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-5" id="rlsLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Obteniendo datos sin restricciones RLS...</p>
                    </div>
                    <div id="rlsData" style="display:none;">
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <span id="rlsSuccess"></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="rlsDataTable">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="rlsError" style="display:none;">
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <span id="rlsErrorMessage"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar clic en botones de bypass RLS
        const bypassButtons = document.querySelectorAll('.bypass-rls-btn');
        const modal = new bootstrap.Modal(document.getElementById('bypassRlsModal'));
        
        bypassButtons.forEach(button => {
            button.addEventListener('click', function() {
                const table = this.getAttribute('data-table');
                const schema = this.getAttribute('data-schema');
                
                // Restablecer modal
                document.getElementById('rlsLoading').style.display = 'block';
                document.getElementById('rlsData').style.display = 'none';
                document.getElementById('rlsError').style.display = 'none';
                
                // Mostrar modal
                modal.show();
                
                // Hacer solicitud para obtener datos sin RLS
                fetch('{{ route("database.connection.bypass-rls") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ table, schema })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('rlsLoading').style.display = 'none';
                    
                    if (data.success && data.data) {
                        // Mostrar datos
                        document.getElementById('rlsData').style.display = 'block';
                        document.getElementById('rlsSuccess').textContent = 
                            `Se obtuvieron ${Array.isArray(data.data) ? data.data.length : 'algunos'} registros de ${schema}.${table}`;
                        
                        // Crear tabla con datos
                        const tableHead = document.querySelector('#rlsDataTable thead');
                        const tableBody = document.querySelector('#rlsDataTable tbody');
                        
                        tableHead.innerHTML = '';
                        tableBody.innerHTML = '';
                        
                        if (Array.isArray(data.data) && data.data.length > 0) {
                            // Crear encabezados de tabla
                            const headerRow = document.createElement('tr');
                            const sampleRow = data.data[0];
                            
                            for (const key in sampleRow) {
                                const th = document.createElement('th');
                                th.textContent = key;
                                headerRow.appendChild(th);
                            }
                            
                            tableHead.appendChild(headerRow);
                            
                            // Crear filas de datos (máximo 10 para no sobrecargar)
                            const maxRows = Math.min(10, data.data.length);
                            for (let i = 0; i < maxRows; i++) {
                                const row = document.createElement('tr');
                                
                                for (const key in data.data[i]) {
                                    const td = document.createElement('td');
                                    
                                    // Formatear valor
                                    let value = data.data[i][key];
                                    if (value === null) {
                                        td.innerHTML = '<em class="text-muted">NULL</em>';
                                    } else if (typeof value === 'object') {
                                        td.innerHTML = '<code>' + JSON.stringify(value).slice(0, 50) + '</code>';
                                    } else if (value.toString().length > 50) {
                                        td.textContent = value.toString().slice(0, 50) + '...';
                                    } else {
                                        td.textContent = value;
                                    }
                                    
                                    row.appendChild(td);
                                }
                                
                                tableBody.appendChild(row);
                            }
                            
                            // Indicar si hay más filas
                            if (data.data.length > maxRows) {
                                const infoRow = document.createElement('tr');
                                const infoCell = document.createElement('td');
                                infoCell.setAttribute('colspan', Object.keys(sampleRow).length);
                                infoCell.className = 'text-center text-muted';
                                infoCell.textContent = `... y ${data.data.length - maxRows} filas más`;
                                infoRow.appendChild(infoCell);
                                tableBody.appendChild(infoRow);
                            }
                        } else {
                            tableBody.innerHTML = '<tr><td class="text-center">No hay datos disponibles</td></tr>';
                        }
                    } else {
                        // Mostrar error
                        document.getElementById('rlsError').style.display = 'block';
                        document.getElementById('rlsErrorMessage').textContent = 
                            data.error || 'Error desconocido al intentar obtener datos sin restricciones RLS';
                    }
                })
                .catch(error => {
                    document.getElementById('rlsLoading').style.display = 'none';
                    document.getElementById('rlsError').style.display = 'block';
                    document.getElementById('rlsErrorMessage').textContent = 
                        'Error de conexión: ' + error.message;
                });
            });
        });
    });
</script>
@endsection 