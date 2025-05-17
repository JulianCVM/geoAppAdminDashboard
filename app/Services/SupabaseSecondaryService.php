<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use Exception;

class SupabaseSecondaryService
{
    private $url;
    private $key;
    private $serviceKey;
    private $client;

    public function __construct()
    {
        $this->url = config('supabase.secondary_url');
        $this->key = config('supabase.secondary_key');
        $this->serviceKey = config('supabase.secondary_service_key');
        
        // Verificar que tenemos las credenciales necesarias
        if (empty($this->url) || empty($this->key) || empty($this->serviceKey)) {
            error_log("ADVERTENCIA: Credenciales de Supabase secundario incompletas");
        } else {
            error_log("INFO: Conectando a Supabase secundario: " . $this->url);
        }
        
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers' => [
                'apikey' => $this->key,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
            // Aumentar timeout para consultas lentas
            'timeout' => 30,
        ]);
    }

    /**
     * Obtener todos los reportes de la base secundaria
     */
    public function getReportes($accessToken)
    {
        try {
            error_log("Intentando obtener reportes de la base secundaria");
            
            // Probar primero con el token de acceso del usuario
            try {
                $response = $this->client->get('/rest/v1/Reportes', [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Reportes obtenidos de secundaria con token de usuario: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            } catch (Exception $userTokenError) {
                error_log("Error al usar token de usuario, intentando con service key: " . $userTokenError->getMessage());
                
                // Si falla, probar con la clave de servicio
                $response = $this->client->get('/rest/v1/Reportes', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Reportes obtenidos de secundaria con service key: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            }
        } catch (Exception $e) {
            error_log("Error al obtener reportes de la base secundaria: " . $e->getMessage());
            
            // Intentar determinar la causa del error
            if (strpos($e->getMessage(), "Unauthorized") !== false || strpos($e->getMessage(), "401") !== false) {
                error_log("Error de autorización - Verifica las claves y políticas RLS en Supabase");
            } else if (strpos($e->getMessage(), "does not exist") !== false) {
                error_log("La tabla 'Reportes' no existe en la base secundaria o tiene otro nombre");
            } else if (strpos($e->getMessage(), "Connection") !== false) {
                error_log("Error de conexión - Verifica la URL y si el servicio está disponible");
            }
            
            return [];
        }
    }

    /**
     * Obtener todos los usuarios de la base secundaria
     */
    public function getUsuarios($accessToken)
    {
        try {
            error_log("Intentando obtener usuarios de la base secundaria");
            
            // Probar primero con el token de acceso del usuario
            try {
                $response = $this->client->get('/rest/v1/usuarios', [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Usuarios obtenidos de secundaria con token de usuario: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            } catch (Exception $userTokenError) {
                error_log("Error al usar token de usuario, intentando con service key: " . $userTokenError->getMessage());
                
                // Si falla, probar con la clave de servicio
                $response = $this->client->get('/rest/v1/usuarios', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Usuarios obtenidos de secundaria con service key: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            }
        } catch (Exception $e) {
            error_log("Error al obtener usuarios de la base secundaria: " . $e->getMessage());
            
            // Intentar determinar la causa del error
            if (strpos($e->getMessage(), "Unauthorized") !== false || strpos($e->getMessage(), "401") !== false) {
                error_log("Error de autorización - Verifica las claves y políticas RLS en Supabase");
            } else if (strpos($e->getMessage(), "does not exist") !== false) {
                error_log("La tabla 'usuarios' no existe en la base secundaria o tiene otro nombre");
            } else if (strpos($e->getMessage(), "Connection") !== false) {
                error_log("Error de conexión - Verifica la URL y si el servicio está disponible");
            }
            
            return [];
        }
    }

    /**
     * Obtener todos los logros de la base secundaria
     */
    public function getLogros($accessToken)
    {
        try {
            error_log("Intentando obtener logros de la base secundaria");
            
            // Probar primero con el token de acceso del usuario
            try {
                $response = $this->client->get('/rest/v1/logros', [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Logros obtenidos de secundaria con token de usuario: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            } catch (Exception $userTokenError) {
                error_log("Error al usar token de usuario, intentando con service key: " . $userTokenError->getMessage());
                
                // Si falla, probar con la clave de servicio
                $response = $this->client->get('/rest/v1/logros', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Logros obtenidos de secundaria con service key: " . count($data) . " registros");
                return is_array($data) ? $data : [];
            }
        } catch (Exception $e) {
            error_log("Error al obtener logros de la base secundaria: " . $e->getMessage());
            
            // Intentar determinar la causa del error
            if (strpos($e->getMessage(), "Unauthorized") !== false || strpos($e->getMessage(), "401") !== false) {
                error_log("Error de autorización - Verifica las claves y políticas RLS en Supabase");
            } else if (strpos($e->getMessage(), "does not exist") !== false) {
                error_log("La tabla 'logros' no existe en la base secundaria o tiene otro nombre");
            } else if (strpos($e->getMessage(), "Connection") !== false) {
                error_log("Error de conexión - Verifica la URL y si el servicio está disponible");
            }
            
            return [];
        }
    }
    
    /**
     * Obtener todos los datos de una tabla específica
     */
    public function getTableData($accessToken, $schema, $table)
    {
        try {
            $endpoint = '/rest/v1/';
            if ($schema !== 'public') {
                $endpoint .= "{$schema}.";
            }
            $endpoint .= $table;
            
            error_log("Consultando tabla: {$endpoint}");
            
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'apikey' => $this->key,
                ],
                'query' => [
                    'limit' => 1000
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al consultar tabla {$schema}.{$table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener lista de tablas de un esquema
     */
    public function getSchemas($accessToken)
    {
        try {
            // Consultar la información de los esquemas usando una consulta SQL
            $response = $this->client->post('/rest/v1/rpc/execute_sql', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sql' => "SELECT DISTINCT table_schema FROM information_schema.tables WHERE table_schema NOT IN ('pg_catalog', 'information_schema')"
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener esquemas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener lista de tablas de un esquema
     */
    public function getTablesBySchema($accessToken, $schema)
    {
        try {
            // Consultar la información de las tablas usando una consulta SQL
            $response = $this->client->post('/rest/v1/rpc/execute_sql', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sql' => "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$schema}' ORDER BY table_name"
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener tablas del esquema {$schema}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estructura de una tabla específica
     */
    public function getTableStructure($accessToken, $schema, $table)
    {
        try {
            // Consultar la estructura de la tabla usando una consulta SQL
            $response = $this->client->post('/rest/v1/rpc/execute_sql', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sql' => "
                        SELECT 
                            column_name, 
                            data_type, 
                            is_nullable, 
                            column_default
                        FROM 
                            information_schema.columns 
                        WHERE 
                            table_schema = '{$schema}' 
                            AND table_name = '{$table}'
                        ORDER BY 
                            ordinal_position
                    "
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener estructura de la tabla {$schema}.{$table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ejecutar una consulta SQL personalizada
     */
    public function executeQuery($accessToken, $query)
    {
        try {
            // Verificar si $query es un objeto Request y obtener la consulta
            if (is_object($query) && method_exists($query, 'input')) {
                $query = $query->input('query');
            }
            
            error_log("Ejecutando consulta SQL: " . substr($query, 0, 100) . "...");
            
            $response = $this->client->post('/rest/v1/rpc/execute_sql', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sql' => $query
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al ejecutar consulta SQL: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Verificar políticas RLS y probar conexión con la base de datos secundaria
     */
    public function testConnection($accessToken)
    {
        try {
            error_log("Verificando conexión a Supabase secundario y políticas RLS");
            
            // Paso 1: Verificar conexión básica
            $response = $this->client->get('/rest/v1/', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                ],
            ]);
            
            error_log("Conexión básica a la API establecida correctamente");
            
            // Paso 2: Obtener lista de esquemas disponibles
            $schemas = [];
            try {
                $schemasResponse = $this->client->post('/rest/v1/rpc/execute_sql', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'query' => "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('information_schema', 'pg_catalog', 'pg_toast')"
                    ]
                ]);
                
                $schemasData = json_decode($schemasResponse->getBody()->getContents(), true);
                if (is_array($schemasData)) {
                    foreach ($schemasData as $row) {
                        if (isset($row['schema_name'])) {
                            $schemas[] = $row['schema_name'];
                        }
                    }
                }
                
                error_log("Esquemas encontrados: " . implode(", ", $schemas));
            } catch (Exception $e) {
                error_log("Error al obtener esquemas: " . $e->getMessage());
            }
            
            // Paso 3: Verificar políticas RLS en tablas principales
            $tablesToCheck = ['usuarios', 'logros', 'Reportes'];
            $result = [];
            
            foreach ($tablesToCheck as $table) {
                try {
                    // Intentar consultar con token de usuario
                    try {
                        $response = $this->client->get("/rest/v1/{$table}?limit=1", [
                            'headers' => [
                                'Authorization' => "Bearer {$accessToken}",
                                'apikey' => $this->key,
                            ],
                        ]);
                        
                        $data = json_decode($response->getBody()->getContents(), true);
                        $result[$table] = [
                            'accessible_with_user_token' => true,
                            'rows_with_user_token' => is_array($data) ? count($data) : 0
                        ];
                    } catch (Exception $e) {
                        error_log("Error al acceder a {$table} con token de usuario: " . $e->getMessage());
                        $result[$table]['accessible_with_user_token'] = false;
                    }
                    
                    // Intentar consultar con service key
                    try {
                        $response = $this->client->get("/rest/v1/{$table}?limit=1", [
                            'headers' => [
                                'Authorization' => "Bearer {$this->serviceKey}",
                                'apikey' => $this->key,
                            ],
                        ]);
                        
                        $data = json_decode($response->getBody()->getContents(), true);
                        $result[$table]['accessible_with_service_key'] = true;
                        $result[$table]['rows_with_service_key'] = is_array($data) ? count($data) : 0;
                    } catch (Exception $e) {
                        error_log("Error al acceder a {$table} con service key: " . $e->getMessage());
                        $result[$table]['accessible_with_service_key'] = false;
                    }
                    
                    // Verificar si existen políticas RLS para esta tabla
                    try {
                        $policiesResponse = $this->client->post('/rest/v1/rpc/execute_sql', [
                            'headers' => [
                                'Authorization' => "Bearer {$this->serviceKey}",
                                'apikey' => $this->key,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'query' => "SELECT tablename, policyname, permissive, WITH CHECK 
                                           FROM pg_policies 
                                           WHERE tablename = '{$table}'"
                            ]
                        ]);
                        
                        $policies = json_decode($policiesResponse->getBody()->getContents(), true);
                        $result[$table]['has_rls_policies'] = is_array($policies) && count($policies) > 0;
                        $result[$table]['policies_count'] = is_array($policies) ? count($policies) : 0;
                    } catch (Exception $e) {
                        error_log("Error al verificar políticas RLS para {$table}: " . $e->getMessage());
                    }
                    
                } catch (Exception $tableError) {
                    $result[$table] = [
                        'error' => $tableError->getMessage(),
                        'exists' => false
                    ];
                }
            }
            
            error_log("Resultados de la verificación: " . json_encode($result));
            return [
                'connection' => true,
                'schemas' => $schemas,
                'tables' => $result
            ];
            
        } catch (Exception $e) {
            error_log("Error crítico al verificar conexión: " . $e->getMessage());
            return [
                'connection' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Intenta desactivar temporalmente RLS para una consulta específica (solo para administradores)
     */
    public function bypassRLS($accessToken, $table, $schema = 'public')
    {
        try {
            error_log("Intentando obtener acceso a {$schema}.{$table} sin restricciones RLS");
            
            // Verificar si el usuario es administrador antes de permitir este tipo de acceso
            $verifyAdminResponse = $this->client->post('/rest/v1/rpc/execute_sql', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => "SELECT is_admin()"
                ]
            ]);
            
            $adminData = json_decode($verifyAdminResponse->getBody()->getContents(), true);
            $isAdmin = false;
            
            if (is_array($adminData) && count($adminData) > 0) {
                $isAdmin = $adminData[0]['is_admin'] ?? false;
            }
            
            if (!$isAdmin) {
                error_log("Usuario no es administrador, no se permite bypass RLS");
                return [
                    'success' => false,
                    'error' => 'Solo los administradores pueden ejecutar esta operación'
                ];
            }
            
            // Ejecutar la consulta como superusuario (requiere que la función esté creada en la BD)
            $response = $this->client->post('/rest/v1/rpc/admin_fetch_all', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'p_table' => $schema . '.' . $table
                ]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            error_log("Datos obtenidos sin restricciones RLS: " . count($data) . " registros");
            
            return [
                'success' => true,
                'data' => $data
            ];
            
        } catch (Exception $e) {
            error_log("Error al intentar bypass RLS: " . $e->getMessage());
            
            // Si falla, es posible que la función admin_fetch_all no exista
            // Intentamos crear la función (solo si el usuario es superusuario)
            try {
                $createFunctionResponse = $this->client->post('/rest/v1/rpc/execute_sql', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'query' => "
                        CREATE OR REPLACE FUNCTION admin_fetch_all(p_table text)
                        RETURNS SETOF json AS
                        $$
                        BEGIN
                          RETURN QUERY EXECUTE 'SELECT json_agg(t) FROM ' || p_table || ' t';
                        END;
                        $$
                        LANGUAGE plpgsql
                        SECURITY DEFINER;"
                    ]
                ]);
                
                error_log("Función admin_fetch_all creada correctamente");
                
                // Intentar nuevamente la consulta
                $response = $this->client->post('/rest/v1/rpc/admin_fetch_all', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'p_table' => $schema . '.' . $table
                    ]
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                return [
                    'success' => true,
                    'data' => $data
                ];
                
            } catch (Exception $createFunctionError) {
                error_log("Error al crear función admin_fetch_all: " . $createFunctionError->getMessage());
                return [
                    'success' => false,
                    'error' => 'No se pudo acceder a los datos: ' . $e->getMessage()
                ];
            }
        }
    }

    /**
     * Actualizar el estado de un reporte
     */
    public function updateReporteEstado($accessToken, $reporteId, $nuevoEstado)
    {
        try {
            $response = $this->client->patch("/rest/v1/Reportes?id=eq.{$reporteId}", [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Prefer' => 'return=minimal',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'estado' => $nuevoEstado,
                    'updated_at' => date('c')
                ]
            ]);

            return ['success' => true];
        } catch (Exception $e) {
            error_log("Error al actualizar estado de reporte: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }
    
    /**
     * Crear un nuevo logro
     */
    public function createLogro($accessToken, $logro)
    {
        try {
            $response = $this->client->post('/rest/v1/logros', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                    'Prefer' => 'return=representation',
                    'Content-Type' => 'application/json',
                ],
                'json' => $logro
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            error_log("Error al crear logro: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Detectar automáticamente los nombres correctos de las tablas
     */
    public function detectTableNames($accessToken)
    {
        error_log("Iniciando detección automática de nombres de tablas");
        $result = [];
        
        try {
            // Obtener lista de esquemas
            $schemas = [];
            try {
                $response = $this->client->post('/rest/v1/rpc/execute_sql', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'sql' => "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('information_schema', 'pg_catalog', 'pg_toast')"
                    ]
                ]);
                
                $schemasData = json_decode($response->getBody()->getContents(), true);
                if (is_array($schemasData)) {
                    foreach ($schemasData as $row) {
                        if (isset($row['schema_name'])) {
                            $schemas[] = $row['schema_name'];
                        }
                    }
                }
                
                error_log("Esquemas encontrados: " . implode(", ", $schemas));
            } catch (Exception $e) {
                error_log("Error al obtener esquemas: " . $e->getMessage());
                
                // Si falla el método RPC, intentar obtener esquemas directamente
                try {
                    $response = $this->client->get('/rest/v1/', [
                        'headers' => [
                            'Authorization' => "Bearer {$this->serviceKey}",
                            'apikey' => $this->key,
                        ],
                    ]);
                    
                    $data = json_decode($response->getBody()->getContents(), true);
                    if (isset($data['paths']) && is_array($data['paths'])) {
                        $schemas = ['public']; // Al menos asumimos que existe el esquema público
                    }
                } catch (Exception $e2) {
                    error_log("Error al obtener esquemas alternativos: " . $e2->getMessage());
                }
            }
            
            // Si no se encontraron esquemas, usar al menos 'public'
            if (empty($schemas)) {
                $schemas = ['public'];
            }
            
            // Posibles nombres para las tablas que buscamos
            $possibleTableNames = [
                'reportes' => ['reportes', 'Reportes', 'reporte', 'Reporte', 'reports', 'Reports'],
                'usuarios' => ['usuarios', 'Usuarios', 'usuario', 'Usuario', 'users', 'Users'],
                'logros' => ['logros', 'Logros', 'logro', 'Logro', 'achievements', 'Achievements'],
            ];
            
            // Para cada esquema, intentar encontrar las tablas
            foreach ($schemas as $schema) {
                error_log("Verificando tablas en esquema: {$schema}");
                
                try {
                    // Obtener lista de tablas en este esquema
                    $tablesResponse = $this->client->post('/rest/v1/rpc/execute_sql', [
                        'headers' => [
                            'Authorization' => "Bearer {$this->serviceKey}",
                            'apikey' => $this->key,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'sql' => "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$schema}'"
                        ]
                    ]);
                    
                    $tablesData = json_decode($tablesResponse->getBody()->getContents(), true);
                    $tables = [];
                    
                    if (is_array($tablesData)) {
                        foreach ($tablesData as $row) {
                            if (isset($row['table_name'])) {
                                $tables[] = $row['table_name'];
                            }
                        }
                    }
                    
                    error_log("Tablas encontradas en esquema {$schema}: " . implode(", ", $tables));
                    
                    // Para cada tipo de tabla que buscamos, verificar si existe alguna variante
                    foreach ($possibleTableNames as $tableType => $variants) {
                        foreach ($variants as $variant) {
                            if (in_array($variant, $tables)) {
                                $result[$tableType] = [
                                    'schema' => $schema,
                                    'table' => $variant,
                                    'found' => true
                                ];
                                error_log("Tabla '{$tableType}' encontrada como '{$schema}.{$variant}'");
                                break; // Salir del bucle de variantes una vez encontrada
                            }
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error al obtener tablas del esquema {$schema}: " . $e->getMessage());
                    
                    // Si falla el método RPC, intentar verificar tablas directamente
                    foreach ($possibleTableNames as $tableType => $variants) {
                        foreach ($variants as $variant) {
                            try {
                                $endpoint = '/rest/v1/';
                                if ($schema !== 'public') {
                                    $endpoint .= "{$schema}.";
                                }
                                $endpoint .= $variant;
                                
                                $response = $this->client->get($endpoint . '?limit=1', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$this->serviceKey}",
                                        'apikey' => $this->key,
                                    ],
                                ]);
                                
                                // Si no hay error, la tabla existe
                                $result[$tableType] = [
                                    'schema' => $schema,
                                    'table' => $variant,
                                    'found' => true
                                ];
                                error_log("Tabla '{$tableType}' encontrada como '{$schema}.{$variant}' (método directo)");
                                break; // Salir del bucle de variantes una vez encontrada
                            } catch (Exception $e2) {
                                // La tabla no existe, continuar con la siguiente variante
                            }
                        }
                    }
                }
            }
            
            // Establecer valores predeterminados para las tablas no encontradas
            foreach ($possibleTableNames as $tableType => $variants) {
                if (!isset($result[$tableType])) {
                    $result[$tableType] = [
                        'schema' => 'public',
                        'table' => $variants[0], // Usar el primer nombre como predeterminado
                        'found' => false
                    ];
                    error_log("No se encontró la tabla '{$tableType}', usando valor predeterminado '{$variants[0]}'");
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error crítico en detección de tablas: " . $e->getMessage());
            
            // En caso de error crítico, devolver valores predeterminados
            return [
                'reportes' => ['schema' => 'public', 'table' => 'reportes', 'found' => false],
                'usuarios' => ['schema' => 'public', 'table' => 'usuarios', 'found' => false],
                'logros' => ['schema' => 'public', 'table' => 'logros', 'found' => false],
            ];
        }
    }
} 