@extends('layouts.app')

@section('title', 'Diagnóstico de Conexión')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Diagnóstico de Conexión a Base de Datos Secundaria</h5>
                </div>
                <div class="card-body">
                    <p class="mb-4">
                        Esta herramienta diagnosticará problemas de conexión con la base de datos Supabase secundaria 
                        y verificará las políticas RLS (Row Level Security) que pueden estar bloqueando el acceso a los datos.
                    </p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Verifica que la configuración en <code>config/supabase.php</code> sea correcta antes de continuar.
                    </div>
                    
                    <div class="mt-4">
                        <h6>El diagnóstico verificará:</h6>
                        <ul>
                            <li>Conexión básica a la API de Supabase</li>
                            <li>Credenciales (API key y Service Key)</li>
                            <li>Esquemas disponibles en la base de datos</li>
                            <li>Políticas RLS en tablas principales (usuarios, logros, reportes)</li>
                            <li>Acceso con token de usuario vs. service key</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2 col-lg-6 mx-auto mt-4">
                        <a href="{{ route('database.connection.test') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-stethoscope me-2"></i> Iniciar Diagnóstico
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 