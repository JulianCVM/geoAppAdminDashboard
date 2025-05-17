@extends('layouts.app')

@section('title', 'Esquema: ' . $schema)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F5F5DC;">
                    <h5 class="mb-0" style="color: #1B5E20;">Esquema: {{ $schema }}</h5>
                    <a href="{{ route('database.index') }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body" style="background-color: #F9F6F0;">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3" style="color: #2E8B57;">Tablas en el esquema {{ $schema }}</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead style="background-color: #A5D6A7;">
                                        <tr>
                                            <th width="5%" style="color: #1B5E20;">#</th>
                                            <th style="color: #1B5E20;">Nombre de Tabla</th>
                                            <th width="15%" style="color: #1B5E20;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tables as $index => $table)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong style="color: #1B5E20;">{{ $table['table_name'] }}</strong>
                                                </td>
                                                <td>
                                                    <a href="{{ route('database.table', [$schema, $table['table_name']]) }}" class="btn btn-sm" style="background-color: #2E8B57; color: white;">
                                                        <i class="fas fa-table"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No se encontraron tablas en este esquema.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 