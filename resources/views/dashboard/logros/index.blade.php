@extends('layouts.app')

@section('title', 'Logros')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3" style="color: #1B5E20;">Gestión de Logros</h1>
        <a href="{{ route('dashboard.logros.create') }}" class="btn" style="background-color: #2E8B57; color: white;">
            <i class="fas fa-plus me-1"></i> Nuevo Logro
        </a>
    </div>
    
    <div class="card">
        <div class="card-header" style="background-color: #F5F5DC;">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0" style="color: #1B5E20;">Lista de Logros</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar logros...">
                        <button class="btn btn-outline-secondary" type="button" style="background-color: #8FBC8F; border-color: #2E8B57; color: white;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" style="background-color: #F9F6F0;">
            <div class="table-responsive">
                <table class="table table-hover" id="logrosTable">
                    <thead>
                        <tr style="background-color: #A5D6A7;">
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
                                        <i class="{{ $logro['icono'] }} fa-lg" style="color: #FFC107;"></i>
                                    @else
                                        <i class="fas fa-trophy fa-lg" style="color: #FFC107;"></i>
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
                                    <span class="badge" style="background-color: #66BB6A; color: white;">{{ $logro['categoria'] ?? 'Sin categoría' }}</span>
                                </td>
                                <td style="color: #2E8B57; font-weight: bold;">{{ $logro['puntos'] ?? '0' }}</td>
                                <td>
                                    <span class="badge" style="background-color: #64B5F6; color: white;">Nivel {{ $logro['nivel_requerido'] ?? '0' }}</span>
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
        <div class="card-footer" style="background-color: #F5F5DC;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small style="color: #1B5E20;">
                        <i class="fas fa-info-circle me-1"></i> Los logros se sincronizan automáticamente con la aplicación móvil
                    </small>
                </div>
                <div>
                    <button id="refreshBtn" class="btn" style="background-color: #2E8B57; color: white;">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar
                    </button>
                </div>
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
        
        // Botón para actualizar la página
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.location.reload();
        });
    });
</script>
@endsection 