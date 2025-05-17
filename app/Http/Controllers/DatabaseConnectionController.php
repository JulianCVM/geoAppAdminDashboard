<?php

namespace App\Http\Controllers;

use App\Services\SupabaseSecondaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DatabaseConnectionController extends Controller
{
    protected $supabaseSecondaryService;

    public function __construct(SupabaseSecondaryService $supabaseSecondaryService)
    {
        $this->supabaseSecondaryService = $supabaseSecondaryService;
        $this->middleware('auth.admin');
    }

    /**
     * Mostrar página de diagnóstico de conexión
     */
    public function index()
    {
        return view('database.connection_test');
    }

    /**
     * Ejecutar prueba de conexión y verificar políticas
     */
    public function testConnection(Request $request)
    {
        $token = Session::get('access_token');
        $result = $this->supabaseSecondaryService->testConnection($token);
        
        return view('database.connection_result', compact('result'));
    }

    /**
     * Intentar acceder a datos sin restricciones RLS (solo admins)
     */
    public function bypassRls(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'schema' => 'required|string',
        ]);
        
        $token = Session::get('access_token');
        $result = $this->supabaseSecondaryService->bypassRLS(
            $token, 
            $request->table, 
            $request->schema
        );
        
        if ($result['success'] && isset($result['data'])) {
            return response()->json($result);
        }
        
        return response()->json($result, 400);
    }
} 