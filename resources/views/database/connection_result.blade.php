@extends('layouts.app')

@section('title', 'Resultado de Diagnóstico de Conexión')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Resultado de Diagnóstico de Conexión</h5>
                    <div>
                        <a href="{{ route('database.index') }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-home"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body" style="background-color: #F9F6F0;">
                    <div class="alert" style="background-color: #E8F5E9; border: 1px solid #A5D6A7; color: #1B5E20;">
                        <i class="fas fa-info-circle me-2"></i>
                        Diagnóstico de conexión a <strong>{{ config('supabase.url') }}</strong>
                    </div>
                    
                    <!-- Estado de la Conexión -->
                    <div class="card mb-4" style="border-color: #A5D6A7;">
                        <div class="card-header" style="background-color: #A5D6A7; color: #1B5E20;">
                            <h5 class="mb-0"><i class="fas fa-plug me-2"></i> Estado de la Conexión</h5>
                        </div>
                        <div class="card-body" style="background-color: #F9F6F0;">
                            @if(isset($connection_status) && $connection_status['success'])
                                <div class="alert" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #A5D6A7;">
                                    <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i> Conexión Exitosa</h5>
                                    <p>Se ha establecido correctamente la conexión con la API de Supabase.</p>
                                </div>
                            @else
                                <div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #EF9A9A;">
                                    <h5 class="alert-heading"><i class="fas fa-times-circle me-2"></i> Error de Conexión</h5>
                                    <p>No se ha podido establecer conexión con la API de Supabase.</p>
                                    @if(isset($connection_status['error']))
                                        <hr>
                                        <p class="mb-0"><strong>Mensaje de error:</strong></p>
                                        <pre style="background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-top: 10px; white-space: pre-wrap;">{{ $connection_status['error'] }}</pre>
                                    @endif
                                </div>
                                
                                <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Posibles Soluciones</h5>
                                    <ul class="mb-0">
                                        <li>Verifica que la URL y la API Key sean correctas en el archivo <code>config/supabase.php</code></li>
                                        <li>Asegúrate de que el proyecto Supabase esté activo y funcionando</li>
                                        <li>Verifica que la función <code>execute_sql</code> esté creada en tu proyecto Supabase</li>
                                        <li>Comprueba que no hay restricciones de CORS que estén bloqueando las peticiones</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Detalles del Diagnóstico -->
                    <div class="card mb-4" style="border-color: #A5D6A7;">
                        <div class="card-header" style="background-color: #A5D6A7; color: #1B5E20;">
                            <h5 class="mb-0"><i class="fas fa-microscope me-2"></i> Detalles del Diagnóstico</h5>
                        </div>
                        <div class="card-body" style="background-color: #F9F6F0;">
                            <div class="mb-4">
                                <h6 style="color: #2E8B57;"><i class="fas fa-code me-2"></i> Función execute_sql</h6>
                                <div class="alert" style="background-color: #E1F5FE; color: #0277BD; border: 1px solid #64B5F6;">
                                    <p class="mb-2">La función <code>execute_sql</code> es necesaria para realizar consultas SQL arbitrarias.</p>
                                    <p class="mb-0">Si estás recibiendo un error 404 relacionado con esta función, deberás crearla manualmente en tu base de datos Supabase.</p>
                                </div>
                                
                                <div class="mt-3 p-3" style="background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
                                    <h6 style="color: #2E8B57;">SQL para crear la función execute_sql:</h6>
                                    <pre style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; white-space: pre-wrap;">
CREATE OR REPLACE FUNCTION public.execute_sql(sql text)
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
    result JSONB;
BEGIN
    EXECUTE 'SELECT to_jsonb(array_agg(row_to_json(t))) FROM (' || sql || ') t' INTO result;
    RETURN result;
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('error', SQLERRM, 'detail', SQLSTATE);
END;
$$;</pre>
                                </div>
                                
                                <p class="mt-3" style="color: #1B5E20;">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    Después de crear la función, asegúrate de configurar los permisos RLS apropiados para permitir su ejecución.
                                </p>
                            </div>
                            
                            <!-- Función get_table_relations -->
                            <div class="mb-4">
                                <h6 style="color: #2E8B57;"><i class="fas fa-project-diagram me-2"></i> Función get_table_relations</h6>
                                <div class="alert" style="background-color: #E1F5FE; color: #0277BD; border: 1px solid #64B5F6;">
                                    <p class="mb-2">La función <code>get_table_relations</code> es necesaria para visualizar correctamente el diagrama de relaciones entre tablas.</p>
                                    <p class="mb-0">Si no puedes ver el diagrama de relaciones o hay errores al cargar las relaciones, deberás crear esta función en tu base de datos Supabase.</p>
                                </div>
                                
                                <div class="mt-3 p-3" style="background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
                                    <h6 style="color: #2E8B57;">SQL para crear la función get_table_relations:</h6>
                                    <pre style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; white-space: pre-wrap;">
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
                                
                                <p class="mt-3" style="color: #1B5E20;">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    Esta función optimiza la visualización de relaciones al evitar múltiples consultas a la base de datos.
                                </p>
                            </div>
                            
                            @if(isset($schemas_status))
                                <div class="mb-4">
                                    <h6 style="color: #2E8B57;"><i class="fas fa-database me-2"></i> Esquemas</h6>
                                    @if($schemas_status['success'])
                                        <div class="alert" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #A5D6A7;">
                                            <p class="mb-0"><i class="fas fa-check-circle me-2"></i> Se obtuvieron los esquemas correctamente.</p>
                                        </div>
                                    @else
                                        <div class="alert" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #EF9A9A;">
                                            <p class="mb-0"><i class="fas fa-times-circle me-2"></i> Error al obtener esquemas.</p>
                                            @if(isset($schemas_status['error']))
                                                <hr>
                                                <pre style="background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-top: 10px; white-space: pre-wrap;">{{ $schemas_status['error'] }}</pre>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Verificación de Políticas RLS -->
                            @if(isset($rls_verification))
                                <div class="mb-4">
                                    <h6 style="color: #2E8B57;"><i class="fas fa-shield-alt me-2"></i> Políticas RLS (Row Level Security)</h6>
                                    
                                    @foreach($rls_verification as $table => $status)
                                        <div class="card mb-3" style="border: 1px solid #A5D6A7;">
                                            <div class="card-header" style="background-color: #E8F5E9; color: #1B5E20;">
                                                <h6 class="mb-0">Tabla: {{ $table }}</h6>
                                            </div>
                                            <div class="card-body" style="background-color: #F9F6F0;">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: #A5D6A7;">
                                                        <span>Accesible con token de usuario</span>
                                                        @if($status['accessible_with_user_token'])
                                                            <span class="badge" style="background-color: #4CAF50; color: white;">Sí</span>
                                                        @else
                                                            <span class="badge" style="background-color: #E57373; color: white;">No</span>
                                                        @endif
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: #A5D6A7;">
                                                        <span>Accesible con service key</span>
                                                        @if($status['accessible_with_service_key'])
                                                            <span class="badge" style="background-color: #4CAF50; color: white;">Sí</span>
                                                        @else
                                                            <span class="badge" style="background-color: #E57373; color: white;">No</span>
                                                        @endif
                                                    </li>
                                                    @if(isset($status['rows_with_service_key']))
                                                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: #A5D6A7;">
                                                            <span>Filas con service key</span>
                                                            <span class="badge" style="background-color: #2E8B57; color: white;">{{ $status['rows_with_service_key'] }}</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Logs de Diagnóstico -->
                            @if(isset($logs) && !empty($logs))
                                <div class="mb-4">
                                    <h6 style="color: #2E8B57;"><i class="fas fa-terminal me-2"></i> Logs del Diagnóstico</h6>
                                    <div style="max-height: 500px; overflow-y: auto; border: 1px solid #A5D6A7; border-radius: 4px; padding: 10px; background-color: #f8f9fa;">
                                        <pre style="white-space: pre-wrap; margin-bottom: 0; color: #333;">{{ implode("\n", $logs) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('database.connection.test') }}" class="btn me-2" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-redo me-1"></i> Volver a Ejecutar Diagnóstico
                        </a>
                        <a href="{{ route('database.index') }}" class="btn" style="background-color: #8FBC8F; color: white;">
                            <i class="fas fa-arrow-left me-1"></i> Volver al Explorador
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 