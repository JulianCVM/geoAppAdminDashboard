@extends('layouts.app')

@section('title', 'Logros')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Logros</h1>
        <a href="{{ route('dashboard.logros.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Logro
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Logros</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar logros...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="logrosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Puntos</th>
                            <th>Nivel Requerido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logros as $logro)
                            @if(is_array($logro))
                            <tr>
                                <td>{{ $logro['id'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($logro['icono']) && $logro['icono'])
                                        <i class="{{ $logro['icono'] }} fa-lg"></i>
                                    @else
                                        <i class="fas fa-trophy fa-lg"></i>
                                    @endif
                                </td>
                                <td>{{ $logro['nombre'] ?? 'Sin nombre' }}</td>
                                <td>
                                    @if(isset($logro['descripcion']))
                                        {{ \Illuminate\Support\Str::limit($logro['descripcion'], 50) }}
                                    @else
                                        Sin descripción
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $logro['categoria'] ?? 'Sin categoría' }}</span>
                                </td>
                                <td>{{ $logro['puntos'] ?? '0' }}</td>
                                <td>
                                    <span class="badge bg-primary">Nivel {{ $logro['nivel_requerido'] ?? '0' }}</span>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay logros disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('logrosTable');
        const rows = table.getElementsByTagName('tr');
        
        searchInput.addEventListener('keyup', function() {
            const term = searchInput.value.toLowerCase();
            
            for (let i = 1; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    
                    if (cellText.indexOf(term) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    });
</script>
@endsection 