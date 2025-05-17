<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GeoApp Dashboard') }} - Login</title>
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
        .login-container {
            max-width: 400px;
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
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="brand">
                <h1>GeoApp Dashboard</h1>
                <p class="text-muted">Panel de administración</p>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0" style="color: #1B5E20;">Iniciar Sesión</h4>
                </div>
                <div class="card-body p-4" style="background-color: #F9F6F0;">
                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ session('reset_email') ?? old('email') }}" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Ingresar</button>
                        </div>
                        <div class="text-center mt-3">
                            <p class="mb-0" style="color: #1B5E20;">¿No tienes cuenta? <a href="{{ route('register') }}" style="color: #2E8B57;">Regístrate aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para logs de depuración -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de login cargada');
            
            // Depuración para ver si hay mensaje de éxito
            @if(session('success'))
                console.log('Mensaje de éxito: {{ session('success') }}');
            @endif
            
            // Depuración para ver si hay email de restablecimiento
            @if(session('reset_email'))
                console.log('Email de restablecimiento: {{ session('reset_email') }}');
            @endif
            
            // Escuchar al formulario de login
            const loginForm = document.querySelector('form');
            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                console.log('Intentando login con email:', email);
            });
        });
    </script>
</body>
</html> 