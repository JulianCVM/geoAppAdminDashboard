@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Usuarios</h1>
    </div>
    
    <!-- Usuarios del Sistema de Gestión -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Usuarios del Sistema de Gestión</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchGestionInput" class="form-control" placeholder="Buscar administradores...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usuariosGestionTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuariosGestion as $usuario)
                            @if(is_array($usuario))
                            <tr>
                                <td>{{ $usuario['id'] ?? 'N/A' }}</td>
                                <td>
                                    <i class="fas fa-user-shield me-2"></i>
                                    {{ $usuario['nombre'] ?? 'Sin nombre' }}
                                </td>
                                <td>{{ $usuario['email'] ?? 'No disponible' }}</td>
                                <td>
                                    @if(isset($usuario['rol']) && $usuario['rol'] == 'superadmin')
                                        <span class="badge bg-danger">Super Admin</span>
                                    @else
                                        <span class="badge bg-primary">Admin</span>
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
                                <td colspan="5" class="text-center">No hay administradores disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Usuarios de la Aplicación Móvil -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Usuarios de la Aplicación Móvil</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchMovilInput" class="form-control" placeholder="Buscar usuarios...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usuariosMovilTable">
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
                        @forelse($usuariosMovil as $usuario)
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
                                <td colspan="7" class="text-center">No hay usuarios de la aplicación móvil disponibles</td>
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
        // Búsqueda para usuarios de gestión
        const searchGestionInput = document.getElementById('searchGestionInput');
        const tableGestion = document.getElementById('usuariosGestionTable');
        const rowsGestion = tableGestion.getElementsByTagName('tr');
        
        searchGestionInput.addEventListener('keyup', function() {
            const term = searchGestionInput.value.toLowerCase();
            
            for (let i = 1; i < rowsGestion.length; i++) {
                let found = false;
                const cells = rowsGestion[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    
                    if (cellText.indexOf(term) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rowsGestion[i].style.display = found ? '' : 'none';
            }
        });
        
        // Búsqueda para usuarios móviles
        const searchMovilInput = document.getElementById('searchMovilInput');
        const tableMovil = document.getElementById('usuariosMovilTable');
        const rowsMovil = tableMovil.getElementsByTagName('tr');
        
        searchMovilInput.addEventListener('keyup', function() {
            const term = searchMovilInput.value.toLowerCase();
            
            for (let i = 1; i < rowsMovil.length; i++) {
                let found = false;
                const cells = rowsMovil[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    
                    if (cellText.indexOf(term) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rowsMovil[i].style.display = found ? '' : 'none';
            }
        });
    });
</script>
@endsection 