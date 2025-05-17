<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\SupabaseSecondaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $supabaseService;
    protected $supabaseSecondaryService;

    public function __construct(SupabaseService $supabaseService, SupabaseSecondaryService $supabaseSecondaryService)
    {
        $this->supabaseService = $supabaseService;
        $this->supabaseSecondaryService = $supabaseSecondaryService;
        $this->middleware('auth.admin');
    }

    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        $token = Session::get('access_token');
        $reportes = $this->supabaseSecondaryService->getReportes($token);
        
        $estadisticas = [
            'total_reportes' => count($reportes),
            'reportes_pendientes' => count(array_filter($reportes, function($r) {
                return isset($r['estado']) && $r['estado'] === 'pendiente';
            })),
            'reportes_en_proceso' => count(array_filter($reportes, function($r) {
                return isset($r['estado']) && $r['estado'] === 'en_proceso';
            })),
            'reportes_resueltos' => count(array_filter($reportes, function($r) {
                return isset($r['estado']) && $r['estado'] === 'resuelto';
            })),
        ];

        return view('dashboard.index', compact('estadisticas', 'reportes'));
    }

    /**
     * Mostrar lista de reportes
     */
    public function reportes()
    {
        $token = Session::get('access_token');
        $reportes = $this->supabaseSecondaryService->getReportes($token);
        
        return view('dashboard.reportes.index', compact('reportes'));
    }

    /**
     * Mostrar detalle de un reporte
     */
    public function showReporte($id)
    {
        $token = Session::get('access_token');
        $reportes = $this->supabaseSecondaryService->getReportes($token);
        
        $reporte = null;
        foreach ($reportes as $r) {
            if ($r['id'] == $id) {
                $reporte = $r;
                break;
            }
        }
        
        if (!$reporte) {
            return redirect()->route('dashboard.reportes')->with('error', 'Reporte no encontrado');
        }
        
        return view('dashboard.reportes.show', compact('reporte'));
    }

    /**
     * Actualizar el estado de un reporte
     */
    public function updateReporteEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto,cancelado',
        ]);
        
        $token = Session::get('access_token');
        $resultado = $this->supabaseSecondaryService->updateReporteEstado($token, $id, $request->estado);
        
        if (isset($resultado['error'])) {
            return redirect()->back()->with('error', $resultado['error']);
        }
        
        return redirect()->route('dashboard.reportes.show', $id)->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Mostrar lista de usuarios
     */
    public function usuarios()
    {
        $token = Session::get('access_token');
        
        // Obtener usuarios de la aplicación móvil (base secundaria)
        $usuariosMovil = $this->supabaseSecondaryService->getUsuarios($token);
        
        // Verificar si $usuariosMovil es un array válido
        if (!is_array($usuariosMovil)) {
            $usuariosMovil = [];
        }
        
        // Obtener usuarios del sistema de gestión (administradores)
        $usuariosGestion = $this->supabaseService->getAdministradores($token);
        
        // Verificar si $usuariosGestion es un array válido
        if (!is_array($usuariosGestion)) {
            $usuariosGestion = [];
        }
        
        return view('dashboard.usuarios.index', compact('usuariosMovil', 'usuariosGestion'));
    }

    /**
     * Mostrar lista de logros
     */
    public function logros()
    {
        $token = Session::get('access_token');
        $logros = $this->supabaseSecondaryService->getLogros($token);
        
        // Verificar si $logros es un array válido
        if (!is_array($logros)) {
            $logros = [];
        }
        
        return view('dashboard.logros.index', compact('logros'));
    }

    /**
     * Mostrar formulario para crear logro
     */
    public function createLogro()
    {
        return view('dashboard.logros.create');
    }

    /**
     * Guardar un nuevo logro
     */
    public function storeLogro(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'icono' => 'required|string',
            'puntos' => 'required|integer|min:0',
            'condicion' => 'required|string',
            'categoria' => 'required|string',
            'nivel_requerido' => 'required|integer|min:0',
        ]);
        
        $token = Session::get('access_token');
        $resultado = $this->supabaseSecondaryService->createLogro($token, [
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'icono' => $request->icono,
            'puntos' => $request->puntos,
            'condicion' => $request->condicion,
            'categoria' => $request->categoria,
            'nivel_requerido' => $request->nivel_requerido,
            'created_at' => date('c')
        ]);
        
        if (isset($resultado['error'])) {
            return redirect()->back()->withInput()->with('error', $resultado['error']);
        }
        
        return redirect()->route('dashboard.logros')->with('success', 'Logro creado correctamente');
    }

    /**
     * Mostrar lista de administradores
     */
    public function administradores()
    {
        $token = Session::get('access_token');
        $administradores = $this->supabaseService->getAdministradores($token);
        
        // Verificar si $administradores es un array válido
        if (!is_array($administradores)) {
            $administradores = [];
        }
        
        return view('dashboard.administradores.index', compact('administradores'));
    }

    /**
     * Mostrar formulario para crear administrador
     */
    public function createAdmin()
    {
        return view('dashboard.administradores.create');
    }

    /**
     * Guardar un nuevo administrador
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'nombre' => 'required|string|max:255',
            'rol' => 'required|in:admin,superadmin',
        ]);
        
        $token = Session::get('access_token');
        $resultado = $this->supabaseService->createAdmin(
            $token,
            $request->email,
            $request->password,
            $request->nombre,
            $request->rol
        );
        
        if (isset($resultado['error'])) {
            return redirect()->back()->withInput($request->except('password'))->with('error', $resultado['error']);
        }
        
        return redirect()->route('dashboard.administradores')->with('success', 'Administrador creado correctamente');
    }
} 