<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GeoApp Dashboard') }} - Restablecer Contraseña</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reset-container {
            max-width: 450px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            text-align: center;
            padding: 20px 0;
        }
        .btn-primary {
            background-color: #3b71ca;
            border-color: #3b71ca;
            padding: 12px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #2e5ca8;
            border-color: #2e5ca8;
        }
        .form-label {
            font-weight: 500;
        }
        .brand {
            text-align: center;
            margin-bottom: 40px;
        }
        .brand h1 {
            font-weight: bold;
            color: #3b71ca;
        }
        .form-control {
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="brand">
                <h1>GeoApp Dashboard</h1>
                <p class="text-muted">Restablece tu Contraseña</p>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Nueva Contraseña</h4>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auth.reset-password.submit') }}">
                        @csrf
                        <input type="hidden" name="token" id="tokenInput" value="{{ $token }}">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $email }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Volver al login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para extraer el token del fragmento de URL -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Extraer fragmento de URL
            const fragment = window.location.hash.substring(1);
            const tokenInput = document.getElementById('tokenInput');
            const emailInput = document.getElementById('email');
            
            if (fragment) {
                try {
                    console.log('Fragmento URL completo:', fragment);
                    
                    // Parsear los parámetros del fragmento
                    const params = new URLSearchParams(fragment);
                    
                    // Obtener todos los parámetros para depuración
                    const allParams = {};
                    for (const [key, value] of params.entries()) {
                        allParams[key] = value;
                    }
                    console.log('Parámetros encontrados:', allParams);
                    
                    // Obtener el token de acceso y tipo
                    const accessToken = params.get('access_token');
                    const tokenType = params.get('token_type') || '';
                    const type = params.get('type') || '';
                    
                    console.log('Fragmento de URL detectado', { tokenType, type });
                    
                    if (accessToken) {
                        console.log('Token encontrado en fragmento URL');
                        
                        // Establecer el token en el campo oculto
                        tokenInput.value = accessToken;
                        
                        // Si el email está vacío pero está en los parámetros del fragmento o en la URL
                        const email = params.get('email');
                        if (!emailInput.value) {
                            if (email) {
                                emailInput.value = email;
                                console.log('Email extraído de los parámetros:', email);
                            }
                            // También intentamos decodificar el email del token JWT
                            else if (accessToken && accessToken.split('.').length > 1) {
                                try {
                                    const base64Url = accessToken.split('.')[1];
                                    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                                    const tokenPayload = JSON.parse(
                                        decodeURIComponent(
                                            window.atob(base64)
                                            .split('')
                                            .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
                                            .join('')
                                        )
                                    );
                                    
                                    console.log('Token payload decodificado:', tokenPayload);
                                    
                                    if (tokenPayload.email) {
                                        emailInput.value = tokenPayload.email;
                                        console.log('Email extraído del token JWT:', tokenPayload.email);
                                    }
                                } catch (e) {
                                    console.error('Error al decodificar token JWT:', e);
                                }
                            }
                        }
                    } else {
                        console.error('No se encontró access_token en el fragmento de URL');
                    }
                } catch (error) {
                    console.error('Error al procesar fragmento de URL:', error);
                }
            } else {
                // Verificar si el token ya está en el campo (desde la variable de servidor)
                if (tokenInput.value) {
                    console.log('Usando token proporcionado por el servidor');
                } else {
                    console.warn('No se encontró token en la URL ni en las variables de servidor');
                }
            }
        });
    </script>
</body>
</html> 