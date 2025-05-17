<?php

namespace App\Http\Controllers;

use App\Services\SupabaseSecondaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DatabaseExplorerController extends Controller
{
    protected $supabaseSecondaryService;

    public function __construct(SupabaseSecondaryService $supabaseSecondaryService)
    {
        $this->supabaseSecondaryService = $supabaseSecondaryService;
        $this->middleware('auth.admin');
    }

    /**
     * Mostrar la página principal del explorador de base de datos
     */
    public function index()
    {
        $token = Session::get('access_token');
        $schemas = $this->supabaseSecondaryService->getSchemas($token);
        
        return view('database.index', compact('schemas'));
    }

    /**
     * Mostrar las tablas de un esquema específico
     */
    public function showSchema(Request $request, $schema)
    {
        $token = Session::get('access_token');
        $tables = $this->supabaseSecondaryService->getTablesBySchema($token, $schema);
        
        return view('database.schema', compact('schema', 'tables'));
    }

    /**
     * Mostrar la estructura y datos de una tabla específica
     */
    public function showTable(Request $request, $schema, $table)
    {
        $token = Session::get('access_token');
        $structure = $this->supabaseSecondaryService->getTableStructure($token, $schema, $table);
        $data = $this->supabaseSecondaryService->getTableData($token, $schema, $table);
        
        return view('database.table', compact('schema', 'table', 'structure', 'data'));
    }

    /**
     * Ejecutar una consulta SQL personalizada
     */
    public function executeQuery(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);
        
        $token = Session::get('access_token');
        // Extraer explícitamente la consulta SQL como una cadena
        $query = $request->input('query');
        $result = $this->supabaseSecondaryService->executeQuery($token, $query);
        
        return view('database.query_result', compact('result'));
    }

    /**
     * Mostrar formulario para consulta SQL personalizada
     */
    public function showQueryForm()
    {
        return view('database.query_form');
    }

    /**
     * Obtener la lista de relaciones entre tablas
     */
    public function getRelations()
    {
        $token = Session::get('access_token');
        
        try {
            // Intentar usar la función get_table_relations primero
            $relations = $this->supabaseSecondaryService->executeQuery($token, "SELECT get_table_relations()");
            
            // Si la respuesta contiene una sola fila con el resultado de la función
            if (is_array($relations) && count($relations) === 1 && isset($relations[0]['get_table_relations'])) {
                $relations = $relations[0]['get_table_relations'];
            }
            
            // Asegurarse de que $relations sea un array
            if (!is_array($relations)) {
                $relations = [];
            }
            
            return view('database.relations', compact('relations'));
        } catch (\Exception $e) {
            // Si la función RPC no existe, intentar con la consulta SQL tradicional
            error_log("Error al usar la función get_table_relations: " . $e->getMessage() . ". Intentando con consulta directa...");
            
            // Consulta SQL para obtener las relaciones entre tablas (foreign keys)
            $query = "
                SELECT
                    tc.table_schema, 
                    tc.constraint_name, 
                    tc.table_name, 
                    kcu.column_name, 
                    ccu.table_schema AS foreign_table_schema,
                    ccu.table_name AS foreign_table_name,
                    ccu.column_name AS foreign_column_name 
                FROM 
                    information_schema.table_constraints AS tc 
                    JOIN information_schema.key_column_usage AS kcu
                      ON tc.constraint_name = kcu.constraint_name
                      AND tc.table_schema = kcu.table_schema
                    JOIN information_schema.constraint_column_usage AS ccu
                      ON ccu.constraint_name = tc.constraint_name
                      AND ccu.table_schema = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                ORDER BY tc.table_schema, tc.table_name;
            ";
            
            $relations = $this->supabaseSecondaryService->executeQuery($token, $query);
            
            // Asegurarse de que $relations sea un array
            if (!is_array($relations)) {
                $relations = [];
            }
            
            $error = "Se requiere configurar la función get_table_relations en Supabase para visualizar las relaciones.";
            return view('database.relations', compact('relations', 'error'));
        }
    }

    /**
     * Visualizar datos de tablas relacionadas
     */
    public function showRelatedData(Request $request)
    {
        $request->validate([
            'schema' => 'required|string',
            'table' => 'required|string',
            'id_column' => 'required|string',
            'id_value' => 'required|string',
        ]);
        
        $token = Session::get('access_token');
        $schema = $request->schema;
        $table = $request->table;
        $idColumn = $request->id_column;
        $idValue = $request->id_value;
        
        // Primero obtenemos los datos de la tabla principal
        $mainData = $this->supabaseSecondaryService->executeQuery(
            $token, 
            "SELECT * FROM {$schema}.{$table} WHERE {$idColumn} = '{$idValue}' LIMIT 1"
        );
        
        // Asegurarnos de que mainData sea un array
        if (!is_array($mainData)) {
            $mainData = [];
        }
        
        // Luego obtenemos las relaciones de la tabla
        $relationsQuery = "
            SELECT
                tc.constraint_name, 
                tc.table_name, 
                kcu.column_name, 
                ccu.table_schema AS foreign_table_schema,
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
            WHERE 
                tc.constraint_type = 'FOREIGN KEY' AND
                tc.table_schema = '{$schema}' AND
                tc.table_name = '{$table}'
        ";
        
        $relations = $this->supabaseSecondaryService->executeQuery($token, $relationsQuery);
        
        // Asegurarnos de que relations sea un array
        if (!is_array($relations)) {
            $relations = [];
        }
        
        // Y también las tablas que referencian a esta tabla
        $referencingQuery = "
            SELECT
                tc.table_schema,
                tc.constraint_name, 
                tc.table_name, 
                kcu.column_name, 
                ccu.table_schema AS referenced_table_schema,
                ccu.table_name AS referenced_table_name,
                ccu.column_name AS referenced_column_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
            WHERE 
                tc.constraint_type = 'FOREIGN KEY' AND
                ccu.table_schema = '{$schema}' AND
                ccu.table_name = '{$table}' AND
                ccu.column_name = '{$idColumn}'
        ";
        
        $referencingTables = $this->supabaseSecondaryService->executeQuery($token, $referencingQuery);
        
        // Asegurarnos de que referencingTables sea un array
        if (!is_array($referencingTables)) {
            $referencingTables = [];
        }
        
        // Obtenemos los datos de las tablas relacionadas
        $relatedData = [];
        
        // Para cada tabla que hace referencia a esta tabla
        if (is_array($referencingTables)) {
            foreach ($referencingTables as $ref) {
                if (!is_array($ref) || !isset($ref['table_schema']) || !isset($ref['table_name']) || !isset($ref['column_name'])) {
                    continue;
                }
                
                $refSchema = $ref['table_schema'];
                $refTable = $ref['table_name'];
                $refColumn = $ref['column_name'];
                
                $refData = $this->supabaseSecondaryService->executeQuery(
                    $token,
                    "SELECT * FROM {$refSchema}.{$refTable} WHERE {$refColumn} = '{$idValue}' LIMIT 100"
                );
                
                // Asegurarnos de que refData sea un array
                if (!is_array($refData)) {
                    $refData = [];
                }
                
                $relatedData[$refTable] = [
                    'schema' => $refSchema,
                    'table' => $refTable,
                    'relation_type' => 'referencing',
                    'local_column' => $refColumn,
                    'foreign_column' => isset($ref['referenced_column_name']) ? $ref['referenced_column_name'] : $idColumn,
                    'data' => $refData
                ];
            }
        }
        
        return view('database.related_data', compact('schema', 'table', 'idColumn', 'idValue', 'mainData', 'relations', 'referencingTables', 'relatedData'));
    }
} 