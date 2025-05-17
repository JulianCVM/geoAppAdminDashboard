<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\SupabaseSecondaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DiagnosticController extends Controller
{
    protected $supabaseService;
    protected $supabaseSecondaryService;

    public function __construct(SupabaseService $supabaseService, SupabaseSecondaryService $supabaseSecondaryService)
    {
        $this->supabaseService = $supabaseService;
        $this->supabaseSecondaryService = $supabaseSecondaryService;
    }

    /**
     * Verificar las tablas requeridas
     */
    public function checkTables()
    {
        $results = [];
        
        // Verificar la tabla administradores
        $results['administradores'] = $this->supabaseService->checkTable('administradores');
        
        // Verificar la tabla public.administradores
        $results['public_administradores'] = $this->supabaseService->checkTable('public.administradores');
        
        // Verificar otras tablas (agregar más según sea necesario)
        $results['usuarios'] = $this->supabaseService->checkTable('usuarios');
        $results['logros'] = $this->supabaseService->checkTable('logros');
        $results['reportes'] = $this->supabaseService->checkTable('Reportes');
        
        return response()->json([
            'tables' => $results,
            'supabase_url' => config('supabase.url'),
            'key_length' => strlen(config('supabase.key')),
            'service_key_length' => strlen(config('supabase.service_key')),
        ]);
    }
    
    /**
     * Verificar la conexión a la base de datos secundaria
     */
    public function checkSecondaryConnection()
    {
        $token = Session::get('access_token');
        
        // Verificar la conexión
        $connectionResult = $this->supabaseSecondaryService->testConnection($token);
        
        // Detectar nombres de tablas
        $tableNames = $this->supabaseSecondaryService->detectTableNames($token);
        
        return view('diagnostic.secondary_connection', [
            'connectionResult' => $connectionResult,
            'tableNames' => $tableNames,
            'secondary_url' => config('supabase.secondary_url'),
            'key_length' => strlen(config('supabase.secondary_key')),
            'service_key_length' => strlen(config('supabase.secondary_service_key')),
        ]);
    }
    
    /**
     * Información para crear la tabla administradores
     */
    public function createAdminTable()
    {
        return response()->json([
            'message' => 'Para crear la tabla administradores, debes ejecutar el siguiente SQL en Supabase SQL Editor:',
            'sql' => "
CREATE TABLE IF NOT EXISTS administradores (
  id UUID REFERENCES auth.users ON DELETE CASCADE NOT NULL PRIMARY KEY,
  email TEXT NOT NULL,
  nombre TEXT,
  rol TEXT NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Crear políticas de acceso si no existen
ALTER TABLE administradores ENABLE ROW LEVEL SECURITY;

-- Eliminar políticas existentes
DROP POLICY IF EXISTS \"Los administradores pueden ver todos los administradores\" ON administradores;
DROP POLICY IF EXISTS \"Solo superadmin puede modificar administradores\" ON administradores;

-- Crear nuevas políticas
CREATE POLICY \"Los administradores pueden ver todos los administradores\"
  ON administradores FOR SELECT
  USING (true);

CREATE POLICY \"Solo superadmin puede modificar administradores\"
  ON administradores FOR ALL
  USING (auth.uid() IN (SELECT id FROM administradores WHERE rol = 'superadmin'));
            "
        ]);
    }
    
    /**
     * Probar la creación de un usuario administrador
     */
    public function testCreateAdmin()
    {
        try {
            // Crear un usuario de prueba
            $email = 'test_' . time() . '@example.com';
            $password = 'password123';
            $nombre = 'Usuario de Prueba';
            
            // Intentar registrar en Supabase Auth
            $authResult = $this->supabaseService->registerAdmin($email, $password, $nombre);
            
            return response()->json([
                'success' => !isset($authResult['error']),
                'result' => $authResult,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ], 500);
        }
    }
} 