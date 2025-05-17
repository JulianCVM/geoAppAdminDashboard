@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Reportes</h1>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Reportes</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar reportes...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="reportesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Importancia</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportes as $reporte)
                            <tr>
                                <td>{{ $reporte['id'] }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $reporte['tipo'] }}</span>
                                </td>
                                <td>
                                    {{ \Illuminate\Support\Str::limit($reporte['descripcion'], 50) }}
                                </td>
                                <td>
                                    @if($reporte['estado'] == 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($reporte['estado'] == 'en_proceso')
                                        <span class="badge bg-info">En proceso</span>
                                    @elseif($reporte['estado'] == 'resuelto')
                                        <span class="badge bg-success">Resuelto</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    @for($i = 0; $i < $reporte['importancia']; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    @for($i = $reporte['importancia']; $i < 5; $i++)
                                        <i class="far fa-star text-warning"></i>
                                    @endfor
                                </td>
                                <td>{{ date('d/m/Y H:i', strtotime($reporte['created_at'])) }}</td>
                                <td>
                                    <a href="{{ route('dashboard.reportes.show', $reporte['id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay reportes disponibles</td>
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
        const table = document.getElementById('reportesTable');
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