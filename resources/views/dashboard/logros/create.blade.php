@extends('layouts.app')

@section('title', 'Crear Logro')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Crear Nuevo Logro</h1>
        <a href="{{ route('dashboard.logros') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Formulario de Logro</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.logros.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('categoria') is-invalid @enderror" id="categoria" name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="reportes" {{ old('categoria') == 'reportes' ? 'selected' : '' }}>Reportes</option>
                            <option value="comunidad" {{ old('categoria') == 'comunidad' ? 'selected' : '' }}>Comunidad</option>
                            <option value="participacion" {{ old('categoria') == 'participacion' ? 'selected' : '' }}>Participación</option>
                            <option value="especial" {{ old('categoria') == 'especial' ? 'selected' : '' }}>Especial</option>
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="icono" class="form-label">Icono <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="iconoPreview" class="fas fa-trophy"></i></span>
                            <input type="text" class="form-control @error('icono') is-invalid @enderror" id="icono" name="icono" value="{{ old('icono', 'fas fa-trophy') }}" required>
                        </div>
                        <small class="form-text text-muted">Utiliza clases de Font Awesome (ej: fas fa-trophy, fas fa-star)</small>
                        @error('icono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="puntos" class="form-label">Puntos <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('puntos') is-invalid @enderror" id="puntos" name="puntos" value="{{ old('puntos', 10) }}" min="0" required>
                        @error('puntos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="condicion" class="form-label">Condición <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('condicion') is-invalid @enderror" id="condicion" name="condicion" value="{{ old('condicion') }}" required>
                        <small class="form-text text-muted">Condición para desbloquear el logro (ej: reportes >= 5)</small>
                        @error('condicion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="nivel_requerido" class="form-label">Nivel Requerido <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('nivel_requerido') is-invalid @enderror" id="nivel_requerido" name="nivel_requerido" value="{{ old('nivel_requerido', 1) }}" min="0" required>
                        @error('nivel_requerido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Logro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconoInput = document.getElementById('icono');
        const iconoPreview = document.getElementById('iconoPreview');
        
        iconoInput.addEventListener('input', function() {
            // Eliminar todas las clases existentes
            iconoPreview.className = '';
            
            // Añadir las nuevas clases
            const clases = iconoInput.value.trim().split(' ');
            clases.forEach(clase => {
                if (clase) {
                    iconoPreview.classList.add(clase);
                }
            });
        });
    });
</script>
@endsection
 