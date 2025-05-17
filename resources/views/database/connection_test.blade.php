@extends('layouts.app')

@section('title', 'Diagnóstico de Conexión')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Diagnóstico de Conexión a Base de Datos Secundaria</h5>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <p class="mb-4">
                        Esta herramienta diagnosticará problemas de conexión con la base de datos Supabase secundaria 
                        y verificará las políticas RLS (Row Level Security) que pueden estar bloqueando el acceso a los datos.
                    </p>
                    
                    <div class="alert" style="background-color: #E1F5FE; color: #0277BD; border: 1px solid #64B5F6;">
                        <i class="fas fa-info-circle me-2"></i>
                        Verifica que la configuración en <code>config/supabase.php</code> sea correcta antes de continuar.
                    </div>
                    
                    <div class="mt-4 p-3" style="background-color: #EFF8F1; border-radius: 8px; border: 1px solid #A5D6A7;">
                        <h6 style="color: #2E8B57;">El diagnóstico verificará:</h6>
                        <ul style="color: #1B5E20;">
                            <li>Conexión básica a la API de Supabase</li>
                            <li>Credenciales (API key y Service Key)</li>
                            <li>Esquemas disponibles en la base de datos</li>
                            <li>Políticas RLS en tablas principales (usuarios, logros, reportes)</li>
                            <li>Acceso con token de usuario vs. service key</li>
                        </ul>
                    </div>
                    
                    <!-- Información sobre función execute_sql -->
                    <div class="card mt-4" style="border-color: #A5D6A7;">
                        <div class="card-header" style="background-color: #A5D6A7; color: #1B5E20;">
                            <h6 class="mb-0"><i class="fas fa-code"></i> Funciones SQL requeridas</h6>
                        </div>
                        <div class="card-body" style="background-color: #F9F6F0;">
                            <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Este explorador de base de datos requiere que existan dos funciones SQL personalizadas en tu base de datos Supabase.
                            </div>
                            
                            <p style="color: #1B5E20;">Si estás recibiendo errores 404 relacionados con <code>execute_sql</code> o problemas al visualizar las relaciones, debes crear estas funciones manualmente en el SQL Editor de Supabase.</p>
                            
                            <!-- Función execute_sql -->
                            <div class="mb-4">
                                <h6 style="color: #2E8B57; border-bottom: 1px solid #A5D6A7; padding-bottom: 8px;">1. Función <code>execute_sql</code></h6>
                                <p style="color: #1B5E20;">Esta función es necesaria para ejecutar consultas SQL desde la aplicación:</p>
                                
                                <div class="p-3 mt-3" style="background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
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
                            </div>
                            
                            <!-- Función get_table_relations -->
                            <div>
                                <h6 style="color: #2E8B57; border-bottom: 1px solid #A5D6A7; padding-bottom: 8px;">2. Función <code>get_table_relations</code></h6>
                                <p style="color: #1B5E20;">Esta función es necesaria para visualizar correctamente el diagrama de relaciones entre tablas:</p>
                                
                                <div class="p-3 mt-3" style="background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
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
                            </div>
                            
                            <div class="alert mt-3" style="background-color: #E8F5E9; border: 1px solid #A5D6A7; color: #1B5E20;">
                                <i class="fas fa-shield-alt me-2"></i>
                                <strong>Seguridad:</strong> Estas funciones usan <code>SECURITY DEFINER</code>, lo que significa que se ejecutarán con los privilegios del usuario que las creó. 
                                Asegúrate de configurar las políticas RLS adecuadas para proteger tus datos.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 col-lg-6 mx-auto mt-4">
                        <a href="{{ route('database.connection.test') }}" class="btn btn-lg" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-stethoscope me-2"></i> Iniciar Diagnóstico
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
    </div>
</div>
@endsection 