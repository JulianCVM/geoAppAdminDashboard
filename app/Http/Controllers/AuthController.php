<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        if (Session::has('access_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $this->supabaseService->login($request->email, $request->password);

        if (isset($response['error'])) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['auth' => $response['error']]);
        }

        // Guardar el token de acceso en la sesión
        Session::put('access_token', $response['access_token']);
        Session::put('refresh_token', $response['refresh_token']);

        // Obtener información del usuario
        $user = $this->supabaseService->getUser($response['access_token']);
        
        // Verificar si es administrador
        $isAdmin = $this->supabaseService->isAdmin($user['id']);
        
        if (!$isAdmin) {
            Session::flush();
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['auth' => 'No tienes permisos de administrador.']);
        }
        
        Session::put('user', $user);

        return redirect()->route('dashboard');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegisterForm()
    {
        if (Session::has('access_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nombre' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
            'codigo_superadmin' => 'required',
        ]);

        // Verificar el código de superadmin (puedes almacenar este código en .env o en la base de datos)
        $superadminCode = config('app.superadmin_code', 'admin123');
        
        if ($request->codigo_superadmin !== $superadminCode) {
            return redirect()->back()
                ->withInput($request->only('email', 'nombre'))
                ->withErrors(['codigo_superadmin' => 'El código de superadmin es incorrecto.']);
        }

        // Registrar el usuario en Supabase
        $adminData = $this->supabaseService->registerAdmin(
            $request->email,
            $request->password,
            $request->nombre
        );

        if (isset($adminData['error'])) {
            return redirect()->back()
                ->withInput($request->only('email', 'nombre'))
                ->withErrors(['registro' => $adminData['error']]);
        }

        // Iniciar sesión con el nuevo usuario
        $response = $this->supabaseService->login($request->email, $request->password);
        
        if (isset($response['error'])) {
            return redirect()->route('login')
                ->with('success', 'Te has registrado correctamente. Ahora puedes iniciar sesión.');
        }
        
        // Guardar el token de acceso en la sesión
        Session::put('access_token', $response['access_token']);
        Session::put('refresh_token', $response['refresh_token']);
        
        // Obtener información del usuario
        $user = $this->supabaseService->getUser($response['access_token']);
        Session::put('user', $user);

        return redirect()->route('dashboard');
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        if (Session::has('access_token')) {
            $this->supabaseService->logout(Session::get('access_token'));
        }

        Session::flush();
        return redirect()->route('login');
    }

    /**
     * Manejar callback de autenticación de Supabase
     */
    public function handleAuthCallback(Request $request)
    {
        // Este método maneja las redirecciones después de verificar el correo
        if ($request->has('error')) {
            return redirect()->route('login')
                ->withErrors(['auth' => $request->input('error_description', 'Error de autenticación')]);
        }
        
        return redirect()->route('login')
            ->with('success', 'Tu correo ha sido verificado. Ahora puedes iniciar sesión.');
    }

    /**
     * Mostrar formulario de restablecimiento de contraseña
     */
    public function showResetPasswordForm(Request $request)
    {
        // Si no hay token en los parámetros, podría estar en el fragmento de URL
        // Mostraremos el formulario y manejaremos la extracción del token con JavaScript
        return view('auth.reset-password', [
            'token' => $request->input('token', ''),
            'email' => $request->input('email', '')
        ]);
    }

    /**
     * Procesar restablecimiento de contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);
        
        // Verificar que hay un token
        if (empty($request->input('token'))) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['reset' => 'No se proporcionó un token de restablecimiento de contraseña.']);
        }
        
        // Registrar para depuración (parte del token)
        $token = $request->input('token');
        $tokenPrefix = substr($token, 0, 30);
        error_log("Token recibido (inicio): {$tokenPrefix}...");
        error_log("Email utilizado: " . $request->input('email'));
        
        try {
            // Intentar decodificar el JWT para obtener información adicional
            $tokenParts = explode('.', $token);
            if (count($tokenParts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                if ($payload) {
                    error_log("Token payload: " . json_encode($payload));
                    // Verificar si el email en el token coincide con el enviado
                    if (isset($payload['email']) && $payload['email'] !== $request->input('email')) {
                        error_log("ADVERTENCIA: El email en el token ({$payload['email']}) no coincide con el proporcionado ({$request->input('email')})");
                        
                        // Si hay un email en el token y es diferente, vamos a usar ese
                        if (isset($payload['email'])) {
                            error_log("Usando email del token: " . $payload['email']);
                            $email = $payload['email'];
                        } else {
                            $email = $request->input('email');
                        }
                    } else {
                        $email = $request->input('email');
                    }
                } else {
                    $email = $request->input('email');
                }
            } else {
                $email = $request->input('email');
            }
        } catch (\Exception $e) {
            error_log("Error al decodificar token: " . $e->getMessage());
            $email = $request->input('email');
        }
        
        $response = $this->supabaseService->resetPassword(
            $email,
            $request->input('password'),
            $token
        );
        
        if (isset($response['error'])) {
            // Registrar para depuración
            error_log("Error al restablecer contraseña: " . json_encode($response));
            
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['reset' => 'Error al restablecer la contraseña: ' . $response['error']]);
        }
        
        // Guardar el email en sesión flash para autocompletar el login
        Session::flash('reset_email', $email);
        
        // Registrar éxito
        error_log("¡Contraseña restablecida exitosamente para el email: {$email}!");
        
        return redirect()->route('login')
            ->with('success', 'Tu contraseña ha sido actualizada correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.');
    }
} 