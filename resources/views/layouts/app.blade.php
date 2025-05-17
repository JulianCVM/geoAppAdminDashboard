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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: white;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            font-weight: bold;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
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
        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }
        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }
        .nav-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 15px 0;
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
                            <i class="fas fa-database"></i> <span>Explorador de BD</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('database.connection') }}" class="nav-link {{ request()->routeIs('database.connection') ? 'active' : '' }}">
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
                            <span class="navbar-text">
                                <i class="fas fa-user me-2"></i> 
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
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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