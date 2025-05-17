<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GeoApp Dashboard') }} - @yield('title', 'Admin')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --eco-green-primary: #2E8B57;
            --eco-green-light: #8FBC8F;
            --eco-green-dark: #1B5E20;
            --eco-accent: #66BB6A;
            --eco-accent-light: #A5D6A7;
            --eco-amber: #FFC107;
            --eco-blue: #64B5F6;
            --eco-beige: #F5F5DC;
            --eco-sand: #F9F6F0;
            --eco-brown: #795548;
            --eco-white: #FFFFFF;
            --eco-black: #212121;
            --eco-gray: #BDBDBD;
            --eco-gray-light: #EEEEEE;
            --eco-success: #4CAF50;
            --eco-warning: #FFB74D;
            --eco-error: #E57373;
            --eco-info: #64B5F6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--eco-sand);
        }
        .sidebar {
            min-height: 100vh;
            background-color: var(--eco-green-dark);
            color: var(--eco-white);
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: var(--eco-white);
            background-color: var(--eco-green-primary);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            color: var(--eco-accent-light);
        }
        .main-content {
            padding: 20px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .card-header {
            background-color: var(--eco-beige);
            font-weight: bold;
            border-bottom: 1px solid var(--eco-green-light);
            color: var(--eco-green-dark);
            border-radius: 8px 8px 0 0 !important;
        }
        .card-body {
            background-color: var(--eco-sand);
        }
        .card-footer {
            background-color: var(--eco-beige);
            border-top: 1px solid var(--eco-green-light);
            border-radius: 0 0 8px 8px !important;
        }
        .navbar {
            background-color: var(--eco-beige);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: var(--eco-green-dark) !important;
        }
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-card h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .btn {
            border-radius: 5px;
            font-weight: 500;
        }
        .btn-primary {
            background-color: var(--eco-green-primary);
            border-color: var(--eco-green-primary);
        }
        .btn-primary:hover {
            background-color: var(--eco-green-dark);
            border-color: var(--eco-green-dark);
        }
        .btn-success {
            background-color: var(--eco-success);
            border-color: var(--eco-success);
        }
        .btn-warning {
            background-color: var(--eco-warning);
            border-color: var(--eco-warning);
            color: var(--eco-black);
        }
        .btn-info {
            background-color: var(--eco-info);
            border-color: var(--eco-info);
        }
        .btn-danger {
            background-color: var(--eco-error);
            border-color: var(--eco-error);
        }
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            border-color: var(--eco-success);
            color: var(--eco-green-dark);
        }
        .alert-danger {
            background-color: rgba(229, 115, 115, 0.1);
            border-color: var(--eco-error);
            color: #d32f2f;
        }
        .alert-warning {
            background-color: rgba(255, 183, 77, 0.1);
            border-color: var(--eco-warning);
            color: #e65100;
        }
        .alert-info {
            background-color: rgba(100, 181, 246, 0.1);
            border-color: var(--eco-info);
            color: #0277bd;
        }
        .nav-divider {
            height: 1px;
            background-color: var(--eco-green-light);
            opacity: 0.3;
            margin: 15px 0;
        }
        .table thead {
            background-color: var(--eco-accent-light);
            color: var(--eco-green-dark);
        }
        .table tbody {
            background-color: var(--eco-sand);
        }
        .form-control:focus {
            border-color: var(--eco-green-light);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="text-center mb-4">
                    <h4>GeoApp Dashboard</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.reportes') }}" class="nav-link {{ request()->routeIs('dashboard.reportes*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('estadisticas') }}" class="nav-link {{ request()->routeIs('estadisticas*') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Estadísticas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.usuarios') }}" class="nav-link {{ request()->routeIs('dashboard.usuarios*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.logros') }}" class="nav-link {{ request()->routeIs('dashboard.logros*') ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i> Logros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.administradores') }}" class="nav-link {{ request()->routeIs('dashboard.administradores*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i> Administradores
                        </a>
                    </li>
                    
                    <div class="nav-divider"></div>
                    
                    <li class="nav-item">
                        <a href="{{ route('database.index') }}" class="nav-link {{ request()->routeIs('database*') ? 'active' : '' }}">
                            <i class="fas fa-database"></i> Base de Datos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('database.query') }}" class="nav-link {{ request()->routeIs('database.query') ? 'active' : '' }}">
                            <i class="fas fa-terminal"></i> Consulta SQL
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('database.relations') }}" class="nav-link {{ request()->routeIs('database.relations') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i> Relaciones
                        </a>
                    </li>
                    
                    <div class="nav-divider"></div>
                    
                    <li class="nav-item">
                        <a href="{{ route('database.explorer') }}" class="nav-link {{ request()->routeIs('database.explorer') ? 'active' : '' }}">
                            <i class="fas fa-search"></i> <span>Explorador de BD</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('diagnostic.check-secondary-connection') }}" class="nav-link {{ request()->routeIs('diagnostic.check-secondary-connection') ? 'active' : '' }}">
                            <i class="fas fa-stethoscope"></i> <span>Diagnóstico de Conexión</span>
                        </a>
                    </li>
                    
                    <div class="nav-divider"></div>
                    
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4 rounded">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="d-flex align-items-center">
                            <span class="navbar-text" style="color: var(--eco-green-dark);">
                                <i class="fas fa-user-circle me-2" style="color: var(--eco-green-primary);"></i> 
                                @if(Session::has('user'))
                                    {{ Session::get('user')['email'] }}
                                @else
                                    Usuario
                                @endif
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html> 