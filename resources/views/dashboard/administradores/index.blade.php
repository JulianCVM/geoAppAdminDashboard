@extends('layouts.app')

@section('title', 'Administradores')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Administradores</h1>
        <a href="{{ route('dashboard.administradores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Administrador
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Administradores</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar administradores...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="adminsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($administradores as $admin)
                            @if(is_array($admin))
                            <tr>
                                <td>{{ $admin['id'] ?? 'N/A' }}</td>
                                <td>{{ $admin['email'] ?? 'Sin email' }}</td>
                                <td>{{ $admin['nombre'] ?? 'Sin nombre' }}</td>
                                <td>
                                    @if(isset($admin['rol']) && $admin['rol'] === 'superadmin')
                                        <span class="badge bg-danger">Super Admin</span>
                                    @else
                                        <span class="badge bg-primary">Admin</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($admin['created_at']) && $admin['created_at'])
                                        {{ date('d/m/Y', strtotime($admin['created_at'])) }}
                                    @else
                                        Desconocida
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay administradores disponibles</td>
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
        const table = document.getElementById('adminsTable');
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