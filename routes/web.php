<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\DatabaseExplorerController;
use App\Http\Controllers\DatabaseConnectionController;
use App\Http\Controllers\EstadisticasController;

// Rutas de autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de registro
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Rutas de diagnóstico
Route::prefix('diagnostic')->group(function () {
    Route::get('/check-tables', [DiagnosticController::class, 'checkTables'])->name('diagnostic.check-tables');
    Route::get('/create-admin-table', [DiagnosticController::class, 'createAdminTable'])->name('diagnostic.create-admin-table');
    Route::get('/test-create-admin', [DiagnosticController::class, 'testCreateAdmin'])->name('diagnostic.test-create-admin');
    Route::get('/check-secondary-connection', [DiagnosticController::class, 'checkSecondaryConnection'])->name('diagnostic.check-secondary-connection');
});

// Rutas para manejar redirecciones de Supabase
Route::get('/auth/callback', [AuthController::class, 'handleAuthCallback'])->name('auth.callback');
Route::get('/auth/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('auth.reset-password');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password.submit');

// Rutas del dashboard (protegidas por middleware auth.admin)
Route::prefix('dashboard')->middleware('auth.admin')->group(function () {
    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reportes
    Route::get('/reportes', [DashboardController::class, 'reportes'])->name('dashboard.reportes');
    Route::get('/reportes/{id}', [DashboardController::class, 'showReporte'])->name('dashboard.reportes.show');
    Route::patch('/reportes/{id}/estado', [DashboardController::class, 'updateReporteEstado'])->name('dashboard.reportes.update-estado');
    
    // Usuarios
    Route::get('/usuarios', [DashboardController::class, 'usuarios'])->name('dashboard.usuarios');
    
    // Logros
    Route::get('/logros', [DashboardController::class, 'logros'])->name('dashboard.logros');
    Route::get('/logros/crear', [DashboardController::class, 'createLogro'])->name('dashboard.logros.create');
    Route::post('/logros', [DashboardController::class, 'storeLogro'])->name('dashboard.logros.store');
    
    // Administradores
    Route::get('/administradores', [DashboardController::class, 'administradores'])->name('dashboard.administradores');
    Route::get('/administradores/crear', [DashboardController::class, 'createAdmin'])->name('dashboard.administradores.create');
    Route::post('/administradores', [DashboardController::class, 'storeAdmin'])->name('dashboard.administradores.store');
    
    // Estadísticas
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');
    Route::get('/estadisticas/informe/json', [EstadisticasController::class, 'generarInformeJSON'])->name('estadisticas.informe.json');
    
    // Explorador de Base de Datos
    Route::prefix('database')->group(function () {
        Route::get('/', [DatabaseExplorerController::class, 'index'])->name('database.index');
        Route::get('/explorer', [DatabaseExplorerController::class, 'index'])->name('database.explorer');
        Route::get('/schema/{schema}', [DatabaseExplorerController::class, 'showSchema'])->name('database.schema');
        Route::get('/{schema}/{table}', [DatabaseExplorerController::class, 'showTable'])->name('database.table');
        Route::get('/query', [DatabaseExplorerController::class, 'showQueryForm'])->name('database.query');
        Route::post('/query', [DatabaseExplorerController::class, 'executeQuery'])->name('database.execute_query');
        Route::get('/relations', [DatabaseExplorerController::class, 'getRelations'])->name('database.relations');
        Route::get('/related-data', [DatabaseExplorerController::class, 'showRelatedData'])->name('database.related_data');
    });
});

// Diagnóstico de conexión a base de datos secundaria
Route::get('/database/connection', [DatabaseConnectionController::class, 'index'])->name('database.connection');
Route::get('/database/connection/test', [DatabaseConnectionController::class, 'testConnection'])->name('database.connection.test');
Route::post('/database/connection/bypass-rls', [DatabaseConnectionController::class, 'bypassRls'])->name('database.connection.bypass-rls');
