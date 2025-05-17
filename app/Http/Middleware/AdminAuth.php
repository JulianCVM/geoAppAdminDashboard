<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\SupabaseService;

class AdminAuth
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si hay un token de acceso y datos de usuario en sesión
        if (!Session::has('access_token') || !Session::has('user')) {
            Session::flash('error', 'Debes iniciar sesión para acceder al panel de administración.');
            return redirect()->route('login');
        }

        // Obtener datos del usuario de sesión
        $user = Session::get('user');

        // Verificar si el usuario es administrador
        try {
            $isAdmin = $this->supabaseService->isAdmin($user['id']);
            if (!$isAdmin) {
                Session::flash('error', 'No tienes permisos de administrador para acceder al panel.');
                Session::flush(); // Cerrar sesión
                return redirect()->route('login');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error al verificar permisos de administrador: ' . $e->getMessage());
            Session::flush(); // Cerrar sesión en caso de error
            return redirect()->route('login');
        }

        return $next($request);
    }
} 