@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Usuarios</h1>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Usuarios</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuarios...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usuariosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Nivel</th>
                            <th>Puntos</th>
                            <th>Anónimo</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            @if(is_array($usuario))
                            <tr>
                                <td>{{ $usuario['id'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($usuario['foto']) && $usuario['foto'])
                                        <img src="{{ $usuario['foto'] }}" alt="Foto de {{ $usuario['nombre'] ?? 'Usuario' }}" class="rounded-circle me-2" width="30" height="30">
                                    @else
                                        <i class="fas fa-user-circle me-2"></i>
                                    @endif
                                    {{ $usuario['nombre'] ?? 'Sin nombre' }}
                                </td>
                                <td>{{ $usuario['ciudad'] ?? 'No especificada' }}</td>
                                <td>
                                    <span class="badge bg-primary">Nivel {{ $usuario['nivel'] ?? '0' }}</span>
                                </td>
                                <td>{{ $usuario['puntos'] ?? '0' }}</td>
                                <td>
                                    @if(isset($usuario['es_anonimo']) && $usuario['es_anonimo'])
                                        <span class="badge bg-warning text-dark">Anónimo</span>
                                    @else
                                        <span class="badge bg-success">Registrado</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($usuario['created_at']) && $usuario['created_at'])
                                        {{ date('d/m/Y', strtotime($usuario['created_at'])) }}
                                    @else
                                        Desconocida
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay usuarios disponibles</td>
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
        const table = document.getElementById('usuariosTable');
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