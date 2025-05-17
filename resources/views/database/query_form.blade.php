@extends('layouts.app')

@section('title', 'Consulta SQL Personalizada')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Consulta SQL Personalizada</h5>
                    <a href="{{ route('database.index') }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <!-- Alerta para la función execute_sql -->
                    @if(session('execute_sql_error'))
                    <div class="alert alert-danger mb-4">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error: Función execute_sql no encontrada</h5>
                        <p>Se requiere la función <code>execute_sql</code> en la base de datos Supabase para usar esta funcionalidad.</p>
                        <hr>
                        <p class="mb-0">
                            <a href="{{ route('database.connection.test') }}" class="alert-link">
                                Haz clic aquí para ver instrucciones de cómo crear esta función
                            </a>
                        </p>
                    </div>
                    @endif

                    <div class="alert" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Atención:</strong> Las consultas SQL se ejecutarán directamente en la base de datos. Sé cuidadoso con las operaciones que realices.
                    </div>
                    
                    <div class="alert" style="background-color: #E1F5FE; color: #0277BD; border: 1px solid #64B5F6;">
                        <i class="fas fa-info-circle"></i> <strong>Importante:</strong> No incluyas punto y coma (;) al final de tus consultas SQL. Supabase no acepta el punto y coma final al usar la función execute_sql.
                    </div>
                    
                    <div class="alert" style="background-color: #E1F5FE; color: #0277BD; border: 1px solid #64B5F6;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Nota sobre nombres de tablas:</strong> 
                        <ul>
                            <li>En PostgreSQL, los nombres de tablas pueden ser sensibles a mayúsculas/minúsculas si se crearon con comillas dobles.</li>
                            <li>Si recibes el error "relation does not exist", usa la consulta "Listar todas las tablas" para ver los nombres exactos.</li>
                            <li>Para nombres con mayúsculas, debes usar comillas dobles: SELECT * FROM public."Reportes"</li>
                        </ul>
                    </div>
                    
                    <form action="{{ route('database.execute_query') }}" method="post" id="queryForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="query" class="form-label" style="color: #1B5E20;">Consulta SQL:</label>
                            <textarea class="form-control code-editor" id="query" name="query" rows="10" placeholder="SELECT * FROM tabla WHERE condicion = 'valor'" style="border-color: #A5D6A7;"></textarea>
                            <small class="form-text" style="color: #2E8B57;">Escribe tu consulta SQL aquí. Solo se permiten consultas SELECT para fines de seguridad. No incluyas punto y coma (;) al final.</small>
                        </div>
                        
                        <div class="alert" style="background-color: #E8F5E9; border: 1px solid #A5D6A7; color: #1B5E20;">
                            <h6><i class="fas fa-lightbulb"></i> Consultas útiles:</h6>
                            <ul class="mb-0">
                                <li><a href="#" class="query-example" data-query="SELECT table_schema, table_name FROM information_schema.tables WHERE table_schema NOT IN ('pg_catalog', 'information_schema') ORDER BY table_schema, table_name" style="color: #2E8B57;">Listar todas las tablas</a></li>
                                <li><a href="#" class="query-example" data-query="SELECT table_schema, table_name, column_name, data_type FROM information_schema.columns WHERE table_schema NOT IN ('pg_catalog', 'information_schema') ORDER BY table_schema, table_name, ordinal_position" style="color: #2E8B57;">Listar todas las columnas de todas las tablas</a></li>
                                <li><a href="#" class="query-example" data-query="SELECT table_schema, table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name ILIKE '%reporte%'" style="color: #2E8B57;">Buscar tablas que contengan 'reporte'</a></li>
                                <li><a href="#" class="query-example" data-query="SELECT * FROM public.\"Reportes\" LIMIT 100" style="color: #2E8B57;">Ver Reportes (con mayúscula y comillas dobles)</a></li>
                                <li><a href="#" class="query-example" data-query="SELECT * FROM public.reportes LIMIT 100" style="color: #2E8B57;">Ver reportes (en minúsculas)</a></li>
                                <li><a href="#" class="query-example" data-query="SELECT * FROM public.usuarios LIMIT 100" style="color: #2E8B57;">Ver usuarios</a></li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn" style="background-color: #2E8B57; color: white;">
                                <i class="fas fa-play"></i> Ejecutar Consulta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/dracula.min.css" rel="stylesheet">
<style>
    .CodeMirror {
        height: auto;
        min-height: 200px;
        border: 1px solid #A5D6A7;
        border-radius: 4px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/sql/sql.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar CodeMirror
        const editor = CodeMirror.fromTextArea(document.getElementById('query'), {
            mode: 'text/x-sql',
            theme: 'dracula',
            lineNumbers: true,
            indentWithTabs: true,
            smartIndent: true,
            lineWrapping: true,
            matchBrackets: true,
            autofocus: true
        });
        
        // Ejemplos de consultas
        document.querySelectorAll('.query-example').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const query = this.getAttribute('data-query');
                editor.setValue(query);
                editor.focus();
            });
        });
        
        // Prevenir envíos accidentales
        document.getElementById('queryForm').addEventListener('submit', function(e) {
            const query = editor.getValue().trim().toLowerCase();
            
            // Verificar si es consulta peligrosa
            if (query.includes('drop ') || query.includes('truncate ') || 
                query.includes('delete ') || query.includes('update ') || 
                query.includes('alter ') || query.includes('create ')) {
                
                if (!confirm('Has escrito una consulta potencialmente peligrosa. ¿Estás seguro de que quieres ejecutarla?')) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Eliminar punto y coma del final si existe
            if (query.endsWith(';')) {
                editor.setValue(query.substring(0, query.length - 1));
                // No prevenir el envío, pero modificar el valor
            }
        });
    });
</script>
@endsection 