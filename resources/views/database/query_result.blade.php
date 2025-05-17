@extends('layouts.app')

@section('title', 'Resultado de Consulta SQL')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultado de Consulta SQL</h5>
                    <div>
                        <a href="{{ route('database.query') }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('database.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($result['error']))
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error en la consulta</h5>
                            <p>{{ $result['error'] }}</p>
                        </div>
                    @else
                        @if(!empty($result) && is_array($result) && count($result) > 0)
                            @php
                                // Extraer todas las claves de los registros
                                $allKeys = [];
                                foreach ($result as $row) {
                                    foreach (array_keys($row) as $key) {
                                        if (!in_array($key, $allKeys)) {
                                            $allKeys[] = $key;
                                        }
                                    }
                                }
                            @endphp
                            
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> La consulta devolvió {{ count($result) }} resultados.
                            </div>
                            
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            @foreach($allKeys as $key)
                                                <th>{{ $key }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result as $index => $row)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                @foreach($allKeys as $key)
                                                    <td>
                                                        @if(array_key_exists($key, $row))
                                                            @if(is_array($row[$key]) || (is_string($row[$key]) && strlen($row[$key]) > 100))
                                                                <button type="button" class="btn btn-sm btn-outline-primary json-viewer" data-json="{{ htmlspecialchars(json_encode($row[$key])) }}">
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <form action="{{ route('database.query') }}" method="get">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Nueva Consulta
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> La consulta se ejecutó correctamente pero no devolvió resultados.
                            </div>
                            
                            <div class="mt-3">
                                <form action="{{ route('database.query') }}" method="get">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Nueva Consulta
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar JSON -->
<div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsonModalLabel">Visualizador de Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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