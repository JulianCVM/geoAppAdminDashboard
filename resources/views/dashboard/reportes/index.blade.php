@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Reportes</h1>
    </div>
    
    <div class="card">
        <div class="card-header" style="background-color: #F5F5DC;">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0" style="color: #1B5E20;">Lista de Reportes</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar reportes...">
                        <button class="btn btn-outline-secondary" type="button" style="background-color: #8FBC8F; border-color: #2E8B57; color: white;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" style="background-color: #F9F6F0;">
            <!-- Filtros avanzados -->
            <div class="mb-4 p-3" style="background-color: #EFF8F1; border-radius: 8px; border: 1px solid #A5D6A7;">
                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <h6 style="color: #1B5E20;"><i class="fas fa-filter me-2"></i>Filtros avanzados</h6>
                    <button class="btn btn-sm" id="toggleFilters" style="background-color: #8FBC8F; color: white;">
                        <i class="fas fa-chevron-down"></i> Mostrar/Ocultar
                    </button>
                </div>
                <div id="filterContainer" class="mt-3" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" style="color: #2E8B57;">Estado:</label>
                            <select class="form-select filter-control" id="filterEstado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En proceso</option>
                                <option value="resuelto">Resuelto</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #2E8B57;">Importancia:</label>
                            <select class="form-select filter-control" id="filterImportancia">
                                <option value="">Todos</option>
                                <option value="1">1 Estrella</option>
                                <option value="2">2 Estrellas</option>
                                <option value="3">3 Estrellas</option>
                                <option value="4">4 Estrellas</option>
                                <option value="5">5 Estrellas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #2E8B57;">Fecha desde:</label>
                            <input type="date" class="form-control filter-control" id="filterFechaDesde">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #2E8B57;">Fecha hasta:</label>
                            <input type="date" class="form-control filter-control" id="filterFechaHasta">
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        <button class="btn me-2" id="applyFilters" style="background-color: #2E8B57; color: white;">
                            <i class="fas fa-check me-1"></i> Aplicar filtros
                        </button>
                        <button class="btn" id="resetFilters" style="background-color: #E57373; color: white;">
                            <i class="fas fa-times me-1"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="reportesTable">
                    <thead>
                        <tr style="background-color: #A5D6A7;">
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
                            <tr class="reporte-row" 
                                data-estado="{{ $reporte['estado'] ?? '' }}" 
                                data-importancia="{{ $reporte['importancia'] ?? '' }}" 
                                data-fecha="{{ $reporte['created_at'] ?? '' }}">
                                <td>{{ $reporte['id'] }}</td>
                                <td>
                                    <span class="badge" style="background-color: #66BB6A; color: white;">{{ $reporte['tipo'] }}</span>
                                </td>
                                <td>
                                    {{ \Illuminate\Support\Str::limit($reporte['descripcion'], 50) }}
                                </td>
                                <td>
                                    @if($reporte['estado'] == 'pendiente')
                                        <span class="badge" style="background-color: #FFB74D; color: #212121;">Pendiente</span>
                                    @elseif($reporte['estado'] == 'en_proceso')
                                        <span class="badge" style="background-color: #64B5F6; color: white;">En proceso</span>
                                    @elseif($reporte['estado'] == 'resuelto')
                                        <span class="badge" style="background-color: #4CAF50; color: white;">Resuelto</span>
                                    @else
                                        <span class="badge" style="background-color: #E57373; color: white;">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    @for($i = 0; $i < $reporte['importancia']; $i++)
                                        <i class="fas fa-star" style="color: #FFC107;"></i>
                                    @endfor
                                    @for($i = $reporte['importancia']; $i < 5; $i++)
                                        <i class="far fa-star" style="color: #BDBDBD;"></i>
                                    @endfor
                                </td>
                                <td>{{ date('d/m/Y H:i', strtotime($reporte['created_at'])) }}</td>
                                <td>
                                    <a href="{{ route('dashboard.reportes.show', $reporte['id']) }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
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
        <div class="card-footer" style="background-color: #F5F5DC;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge me-2" style="background-color: #FFB74D; color: #212121;">Pendiente</span>
                    <span class="badge me-2" style="background-color: #64B5F6; color: white;">En proceso</span>
                    <span class="badge me-2" style="background-color: #4CAF50; color: white;">Resuelto</span>
                    <span class="badge" style="background-color: #E57373; color: white;">Cancelado</span>
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
        const table = document.getElementById('reportesTable');
        const rows = table.querySelectorAll('tbody .reporte-row');
        const toggleFilters = document.getElementById('toggleFilters');
        const filterContainer = document.getElementById('filterContainer');
        const applyFilters = document.getElementById('applyFilters');
        const resetFilters = document.getElementById('resetFilters');
        
        // Mostrar/ocultar filtros
        toggleFilters.addEventListener('click', function() {
            filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none';
        });
        
        // Función para filtrar reportes
        function filterReportes() {
            const searchTerm = searchInput.value.toLowerCase();
            const estado = document.getElementById('filterEstado').value;
            const importancia = document.getElementById('filterImportancia').value;
            const fechaDesde = document.getElementById('filterFechaDesde').value;
            const fechaHasta = document.getElementById('filterFechaHasta').value;
            
            rows.forEach(row => {
                let matchesSearch = false;
                const cells = row.querySelectorAll('td');
                
                // Buscar coincidencia con el término de búsqueda
                for (let i = 0; i < cells.length; i++) {
                    if (cells[i].textContent.toLowerCase().includes(searchTerm)) {
                        matchesSearch = true;
                        break;
                    }
                }
                
                // Verificar filtros avanzados
                const rowEstado = row.getAttribute('data-estado');
                const rowImportancia = row.getAttribute('data-importancia');
                const rowFecha = row.getAttribute('data-fecha');
                const rowDate = rowFecha ? new Date(rowFecha) : null;
                
                let matchesEstado = !estado || rowEstado === estado;
                let matchesImportancia = !importancia || rowImportancia === importancia;
                let matchesFechaDesde = !fechaDesde || (rowDate && rowDate >= new Date(fechaDesde));
                let matchesFechaHasta = !fechaHasta || (rowDate && rowDate <= new Date(fechaHasta + 'T23:59:59'));
                
                // Mostrar u ocultar la fila según los filtros
                row.style.display = (matchesSearch && matchesEstado && matchesImportancia && matchesFechaDesde && matchesFechaHasta) ? '' : 'none';
            });
        }
        
        // Escuchar evento de búsqueda
        searchInput.addEventListener('keyup', filterReportes);
        
        // Aplicar filtros
        applyFilters.addEventListener('click', filterReportes);
        
        // Resetear filtros
        resetFilters.addEventListener('click', function() {
            document.getElementById('filterEstado').value = '';
            document.getElementById('filterImportancia').value = '';
            document.getElementById('filterFechaDesde').value = '';
            document.getElementById('filterFechaHasta').value = '';
            searchInput.value = '';
            filterReportes();
        });
        
        // Botón para actualizar la página
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.location.reload();
        });
    });
</script>
@endsection 