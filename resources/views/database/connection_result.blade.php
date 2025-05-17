@extends('layouts.app')

@section('title', 'Resultado de Diagnóstico')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Resultado del Diagnóstico</h5>
                    <a href="{{ route('database.connection') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    @if(isset($result['connection']) && $result['connection'])
                        <div class="alert" style="background-color: #E8F5E9; color: #2E7D32; border: 1px solid #4CAF50;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Conexión exitosa a la API de Supabase.</strong>
                        </div>
                        
                        <!-- Esquemas -->
                        <div class="mt-4 p-3" style="background-color: #EFF8F1; border-radius: 8px; border: 1px solid #A5D6A7;">
                            <h6 style="color: #2E8B57;">Esquemas encontrados:</h6>
                            @if(!empty($result['schemas']))
                                <ul style="color: #1B5E20;">
                                    @foreach($result['schemas'] as $schema)
                                        <li><code>{{ $schema }}</code></li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No se encontraron esquemas o no se pudo obtener la información.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Tablas -->
                        <div class="mt-4">
                            <h6 style="color: #2E8B57;">Acceso a tablas:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background-color: #A5D6A7;">
                                        <tr>
                                            <th>Tabla</th>
                                            <th>Acceso con token de usuario</th>
                                            <th>Acceso con service key</th>
                                            <th>Tiene políticas RLS</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background-color: #F9F6F0;">
                                        @foreach($result['tables'] as $tableName => $tableData)
                                            <tr>
                                                <td><code>{{ $tableName }}</code></td>
                                                <td>
                                                    @if(isset($tableData['accessible_with_user_token']) && $tableData['accessible_with_user_token'])
                                                        <span class="badge" style="background-color: #4CAF50; color: white;">Accesible</span>
                                                        @if(isset($tableData['rows_with_user_token']))
                                                            <span class="badge" style="background-color: #BDBDBD; color: #212121;">{{ $tableData['rows_with_user_token'] }} filas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge" style="background-color: #E57373; color: white;">No accesible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($tableData['accessible_with_service_key']) && $tableData['accessible_with_service_key'])
                                                        <span class="badge" style="background-color: #4CAF50; color: white;">Accesible</span>
                                                        @if(isset($tableData['rows_with_service_key']))
                                                            <span class="badge" style="background-color: #BDBDBD; color: #212121;">{{ $tableData['rows_with_service_key'] }} filas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge" style="background-color: #E57373; color: white;">No accesible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($tableData['has_rls_policies']))
                                                        @if($tableData['has_rls_policies'])
                                                            <span class="badge" style="background-color: #FFB74D; color: #212121;">Sí ({{ $tableData['policies_count'] ?? 'desconocido' }})</span>
                                                        @else
                                                            <span class="badge" style="background-color: #4CAF50; color: white;">No</span>
                                                        @endif
                                                    @else
                                                        <span class="badge" style="background-color: #BDBDBD; color: #212121;">Desconocido</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm bypass-rls-btn" 
                                                            style="background-color: #2E8B57; color: white;"
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
                        <div class="mt-4 p-3" style="background-color: #EFF8F1; border-radius: 8px; border: 1px solid #A5D6A7;">
                            <h6 style="color: #2E8B57;">Recomendaciones:</h6>
                            <ul>
                                @php $problemsFound = false; @endphp
                                
                                @foreach($result['tables'] as $tableName => $tableData)
                                    @if(isset($tableData['accessible_with_user_token']) && !$tableData['accessible_with_user_token'])
                                        <li style="color: #E57373;">
                                            La tabla <code>{{ $tableName }}</code> no es accesible con el token de usuario. 
                                            Verifica las políticas RLS en Supabase.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                    
                                    @if(isset($tableData['accessible_with_service_key']) && !$tableData['accessible_with_service_key'])
                                        <li style="color: #E57373;">
                                            La tabla <code>{{ $tableName }}</code> no es accesible ni siquiera con la service key. 
                                            Esto podría indicar que la tabla no existe o tiene otro nombre.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                    
                                    @if(isset($tableData['has_rls_policies']) && $tableData['has_rls_policies'])
                                        <li style="color: #E65100;">
                                            La tabla <code>{{ $tableName }}</code> tiene {{ $tableData['policies_count'] ?? 'algunas' }} políticas RLS. 
                                            Deberías revisar estas políticas para permitir acceso al token de servicio.
                                        </li>
                                        @php $problemsFound = true; @endphp
                                    @endif
                                @endforeach
                                
                                @if(!$problemsFound)
                                    <li style="color: #2E7D32;">
                                        No se detectaron problemas evidentes. Si aún experimentas problemas, verifica los nombres exactos de las tablas y esquemas.
                                    </li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- Soluciones -->
                        <div class="mt-4 p-3" style="background-color: #F5F5DC; border-radius: 8px;">
                            <h6 style="color: #2E8B57;">Posibles soluciones:</h6>
                            <ol style="color: #1B5E20;">
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
                        <div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #E57373;">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Error de conexión:</strong> {{ $result['error'] ?? 'No se pudo establecer conexión con Supabase.' }}
                        </div>
                        
                        <div class="mt-4 p-3" style="background-color: #FFF3E0; border-radius: 8px; border: 1px solid #FFB74D;">
                            <h6 style="color: #E65100;">Posibles causas:</h6>
                            <ul style="color: #E65100;">
                                <li>URL incorrecta de Supabase en la configuración</li>
                                <li>Claves de API inválidas (API Key o Service Key)</li>
                                <li>Servicios de Supabase no disponibles</li>
                                <li>Problemas de red o firewall</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4 p-3" style="background-color: #E1F5FE; border-radius: 8px; border: 1px solid #64B5F6;">
                            <h6 style="color: #0277BD;">Pasos para solucionar:</h6>
                            <ol style="color: #0277BD;">
                                <li>Verifica la URL y claves en <code>config/supabase.php</code></li>
                                <li>Comprueba que Supabase está operativo visitando el panel de control</li>
                                <li>Verifica permisos y configuración de red</li>
                            </ol>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 col-lg-6 mx-auto mt-4">
                        <a href="{{ route('database.connection.test') }}" class="btn" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-sync-alt me-2"></i> Repetir Diagnóstico
                        </a>
                    </div>
                </div>
                <div class="card-footer" style="background-color: #F5F5DC;">
                    <a href="{{ route('dashboard') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Resultado del bypass RLS Modal -->
        <div class="modal fade" id="bypassRLSResultModal" tabindex="-1" aria-labelledby="bypassRLSResultModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background-color: #F9F6F0; border-color: #8FBC8F;">
                    <div class="modal-header" style="background-color: #F5F5DC; border-bottom-color: #8FBC8F;">
                        <h5 class="modal-title" style="color: #1B5E20;" id="bypassRLSResultModalLabel">Resultados del acceso sin RLS</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="bypassRLSResult">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status" style="color: #2E8B57;">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background-color: #F5F5DC; border-top-color: #8FBC8F;">
                        <button type="button" class="btn" style="background-color: #8FBC8F; color: white;" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar clics en los botones de bypass RLS
        const bypassButtons = document.querySelectorAll('.bypass-rls-btn');
        const modal = new bootstrap.Modal(document.getElementById('bypassRLSResultModal'));
        const resultContainer = document.getElementById('bypassRLSResult');

        bypassButtons.forEach(button => {
            button.addEventListener('click', function() {
                const table = this.getAttribute('data-table');
                const schema = this.getAttribute('data-schema');
                
                // Mostrar modal con indicador de carga
                resultContainer.innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status" style="color: #2E8B57;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                `;
                modal.show();
                
                // Hacer la solicitud AJAX para bypassear RLS
                fetch('{{ route("database.connection.bypass-rls") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        table: table,
                        schema: schema
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.rows && data.rows.length > 0) {
                            resultContainer.innerHTML = `
                                <div class="alert" style="background-color: #E8F5E9; color: #2E7D32; border: 1px solid #4CAF50;">
                                    <strong>¡Éxito!</strong> Se accedió a la tabla sin restricciones RLS. Se encontraron ${data.rows.length} filas.
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #A5D6A7;">
                                            <tr>
                                                ${Object.keys(data.rows[0]).map(key => `<th>${key}</th>`).join('')}
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: #F9F6F0;">
                                            ${data.rows.slice(0, 5).map(row => `
                                                <tr>
                                                    ${Object.values(row).map(val => `<td>${val !== null ? val : '<em>null</em>'}</td>`).join('')}
                                                </tr>
                                            `).join('')}
                                            ${data.rows.length > 5 ? `
                                                <tr>
                                                    <td colspan="${Object.keys(data.rows[0]).length}" style="text-align: center; background-color: #EFF8F1;">
                                                        ... y ${data.rows.length - 5} filas más
                                                    </td>
                                                </tr>
                                            ` : ''}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <strong style="color: #2E8B57;">Recomendación:</strong>
                                    <p style="color: #1B5E20;">Los datos son accesibles sin RLS. Considera usar la clave de servicio (service_key) para acceder a estos datos.</p>
                                </div>
                            `;
                        } else {
                            resultContainer.innerHTML = `
                                <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                    <strong>Sin resultados</strong>: Se pudo acceder a la tabla, pero no se encontraron datos.
                                </div>
                                <div class="mt-3">
                                    <strong style="color: #2E8B57;">Posibles causas:</strong>
                                    <ul style="color: #1B5E20;">
                                        <li>La tabla existe pero está vacía</li>
                                        <li>Las políticas RLS están aplicándose correctamente y no permiten ver datos</li>
                                        <li>Se necesita un rol específico para ver los datos</li>
                                    </ul>
                                </div>
                            `;
                        }
                    } else {
                        resultContainer.innerHTML = `
                            <div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #E57373;">
                                <strong>Error:</strong> ${data.error || "No se pudo acceder a los datos"}
                            </div>
                            <div class="mt-3">
                                <strong style="color: #2E8B57;">Detalles técnicos:</strong>
                                <pre style="background-color: #F5F5DC; padding: 10px; border-radius: 5px; color: #1B5E20;">${data.details || "No hay detalles disponibles"}</pre>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    resultContainer.innerHTML = `
                        <div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #E57373;">
                            <strong>Error en la solicitud:</strong> ${error.message}
                        </div>
                    `;
                });
            });
        });
    });
</script>
@endsection 