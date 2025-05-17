<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GeoApp Dashboard') }} - Registro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F9F6F0;
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: #A5D6A7; 
            border-left: 4px solid #2E8B57;
        }
        .card-header {
            background-color: #F5F5DC;
            border-bottom: 1px solid #A5D6A7;
            font-weight: bold;
            text-align: center;
            padding: 20px 0;
        }
        .btn-primary {
            background-color: #2E8B57;
            border-color: #2E8B57;
            padding: 12px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #1B5E20;
            border-color: #1B5E20;
        }
        .btn-outline-secondary {
            border-color: #8FBC8F;
            color: #2E8B57;
        }
        .btn-outline-secondary:hover {
            background-color: #8FBC8F;
            border-color: #8FBC8F;
            color: white;
        }
        .form-label {
            font-weight: 500;
            color: #1B5E20;
        }
        .brand {
            text-align: center;
            margin-bottom: 40px;
        }
        .brand h1 {
            font-weight: bold;
            color: #2E8B57;
        }
        .form-control {
            padding: 12px;
            border-color: #A5D6A7;
        }
        .form-text {
            color: #2E8B57;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="brand">
                <h1>GeoApp Dashboard</h1>
                <p class="text-muted">Registro de Administradores</p>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0" style="color: #1B5E20;">Crear Cuenta de Administrador</h4>
                </div>
                <div class="card-body p-4" style="background-color: #F9F6F0;">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="codigo_superadmin" class="form-label">Código de Superadmin</label>
                            <input type="password" class="form-control" id="codigo_superadmin" name="codigo_superadmin" required>
                            <small class="form-text">Este código es proporcionado por el superadministrador del sistema.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Ya tengo una cuenta</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 