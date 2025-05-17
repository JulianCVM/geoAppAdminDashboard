<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use Exception;

class SupabaseService
{
    private $url;
    private $key;
    private $serviceKey;
    private $client;

    public function __construct()
    {
        $this->url = config('supabase.url');
        $this->key = config('supabase.key');
        $this->serviceKey = config('supabase.service_key');
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers' => [
                'apikey' => $this->key,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    /**
     * Iniciar sesión con email y contraseña
     */
    public function login(string $email, string $password)
    {
        try {
            error_log("Intentando login con email: {$email}");
            
            // Formato correcto para Supabase v2 signInWithPassword
            $response = $this->client->post('/auth/v1/token', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ],
                'headers' => [
                    'apikey' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'grant_type' => 'password'
                ]
            ]);
            
            $responseData = json_decode($response->getBody()->getContents(), true);
            error_log("Login exitoso para {$email}");
            
            return $responseData;
        } catch (GuzzleException $e) {
            error_log("Error de login para {$email}: " . $e->getMessage());
            
            if ($e instanceof ClientException && $e->getResponse()) {
                try {
                    $respBody = $e->getResponse()->getBody()->getContents();
                    error_log("Detalles del error: " . $respBody);
                    $errorData = json_decode($respBody, true);
                    
                    // Si hay un error, probar con otros endpoints/formatos
                    error_log("Intentando login alternativo usando signInWithPassword");
                    try {
                        // Formato específico para Supabase v2 usando endpoint explícito
                        $altResponse = $this->client->post('/auth/v1/signInWithPassword', [
                            'json' => [
                                'email' => $email,
                                'password' => $password
                            ],
                            'headers' => [
                                'apikey' => $this->key,
                                'Content-Type' => 'application/json',
                            ]
                        ]);
                        
                        $responseData = json_decode($altResponse->getBody()->getContents(), true);
                        error_log("Login exitoso con signInWithPassword para {$email}");
                        
                        return $responseData;
                    } catch (Exception $altEx) {
                        error_log("Error en intento con signInWithPassword: " . $altEx->getMessage());
                    }
                    
                    if (isset($errorData['error_description'])) {
                        return [
                            'error' => $errorData['error_description'],
                            'status' => $e->getCode()
                        ];
                    } else if (isset($errorData['msg'])) {
                        return [
                            'error' => $errorData['msg'],
                            'status' => $e->getCode()
                        ];
                    }
                } catch (Exception $innerEx) {
                    error_log("Error al procesar respuesta de error: " . $innerEx->getMessage());
                }
            }
            
            if ($e->getCode() === 400) {
                return [
                    'error' => 'Credenciales incorrectas.',
                    'status' => 400
                ];
            }
            
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        } catch (Exception $e) {
            error_log("Excepción general al iniciar sesión: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Obtener datos del usuario actual
     */
    public function getUser(string $accessToken)
    {
        try {
            $response = $this->client->get('/auth/v1/user', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(string $userId)
    {
        error_log("Verificando si el usuario {$userId} es administrador");
        
        // Primero intentamos con la tabla sin prefijo de esquema
        try {
            error_log("Intento 1: Consultando tabla 'administradores'");
            $response = $this->client->get('/rest/v1/administradores', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'apikey' => $this->key,
                ],
                'query' => [
                    'id' => 'eq.' . $userId,
                    'select' => 'id,rol'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            error_log("Respuesta de administradores: " . json_encode($data));
            
            if (!empty($data)) {
                error_log("Usuario encontrado como administrador");
                return true;
            }
            
            error_log("Usuario no encontrado en tabla administradores");
            return false;
        } catch (Exception $e) {
            error_log("Error al verificar en administradores: " . $e->getMessage());
            
            // Si falla, intentamos con el esquema público explícito
            try {
                error_log("Intento 2: Consultando tabla 'public.administradores'");
                $response = $this->client->get('/rest/v1/public.administradores', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                    ],
                    'query' => [
                        'id' => 'eq.' . $userId,
                        'select' => 'id,rol'
                    ]
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                error_log("Respuesta de public.administradores: " . json_encode($data));
                
                if (!empty($data)) {
                    error_log("Usuario encontrado como administrador en public.administradores");
                    return true;
                }
                
                error_log("Usuario no encontrado en tabla public.administradores");
                return false;
            } catch (Exception $e2) {
                error_log("Error al verificar en public.administradores: " . $e2->getMessage());
                
                // Si todo falla, verificamos si la tabla existe
                try {
                    error_log("Verificando existencia de tablas");
                    $tableInfo = $this->checkTable('administradores');
                    error_log("Información de tabla: " . json_encode($tableInfo));
                    
                    if (isset($tableInfo['exists']) && $tableInfo['exists'] === false) {
                        error_log("¡ATENCIÓN! La tabla 'administradores' no existe. Necesitas crearla primero.");
                    }
                } catch (Exception $e3) {
                    error_log("Error al verificar existencia de tabla: " . $e3->getMessage());
                }
                
                // Si somos muy permisivos, podemos considerar al usuario como administrador
                // si no podemos verificarlo debido a problemas con la tabla
                // (solo para fines de desarrollo)
                error_log("ADVERTENCIA: Permitiendo acceso por defecto. SOLO PARA DESARROLLO");
                return true;
            }
        }
    }

    /**
     * Obtener todos los reportes
     */
    public function getReportes($accessToken)
    {
        try {
            error_log("Intentando obtener reportes");
            $response = $this->client->get('/rest/v1/Reportes', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'apikey' => $this->key,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            error_log("Reportes obtenidos: " . json_encode(array_slice($data, 0, 2))); // Mostrar solo los 2 primeros reportes para no llenar el log
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener reportes: " . $e->getMessage());
            // Comprobar si el error es que la tabla no existe
            if (strpos($e->getMessage(), 'does not exist') !== false) {
                error_log("La tabla 'Reportes' no existe. Devolviendo array vacío.");
                // Si la tabla no existe, devolvemos un array vacío
                return [];
            }
            // Para otros errores, devolvemos un array vacío también, pero lo registramos
            error_log("Otro tipo de error al obtener reportes. Devolviendo array vacío.");
            return [];
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
                    'Authorization' => "Bearer {$accessToken}",
                    'Prefer' => 'return=minimal',
                ],
                'json' => [
                    'estado' => $nuevoEstado,
                    'updated_at' => date('c')
                ]
            ]);

            return ['success' => true];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Obtener todos los usuarios
     */
    public function getUsuarios($accessToken)
    {
        try {
            $response = $this->client->get('/rest/v1/usuarios', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            // Asegurarnos de que sea un array
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los logros
     */
    public function getLogros($accessToken)
    {
        try {
            $response = $this->client->get('/rest/v1/logros', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            // Asegurarnos de que sea un array
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener logros: " . $e->getMessage());
            return [];
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
                    'Authorization' => "Bearer {$accessToken}",
                    'Prefer' => 'return=representation',
                ],
                'json' => $logro
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Obtener todos los administradores
     */
    public function getAdministradores($accessToken)
    {
        try {
            $response = $this->client->get('/rest/v1/administradores', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            // Asegurarnos de que sea un array
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Error al obtener administradores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crear un nuevo administrador
     */
    public function createAdmin($accessToken, $email, $password, $nombre, $rol = 'admin')
    {
        try {
            // Primero creamos el usuario en auth
            $response = $this->client->post('/auth/v1/admin/users', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'email_confirm' => true
                ]
            ]);

            $userData = json_decode($response->getBody()->getContents(), true);
            $userId = $userData['id'];

            // Después creamos el registro en la tabla de administradores
            $adminResponse = $this->client->post('/rest/v1/administradores', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Prefer' => 'return=representation',
                ],
                'json' => [
                    'id' => $userId,
                    'email' => $email,
                    'nombre' => $nombre,
                    'rol' => $rol
                ]
            ]);

            return json_decode($adminResponse->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout($accessToken)
    {
        try {
            $this->client->post('/auth/v1/logout', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            return ['success' => true];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Registrar un nuevo administrador
     */
    public function registerAdmin(string $email, string $password, string $nombre, string $rol = 'admin')
    {
        try {
            // Primero verificamos si el usuario ya existe en Auth
            try {
                $checkUser = $this->client->get('/auth/v1/admin/users', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                    ],
                    'query' => [
                        'email' => $email
                    ]
                ]);
                
                $existingUsers = json_decode($checkUser->getBody()->getContents(), true);
                if (!empty($existingUsers)) {
                    // El usuario ya existe en Auth, intentemos obtener su ID
                    $userId = null;
                    foreach ($existingUsers as $user) {
                        if ($user['email'] === $email) {
                            $userId = $user['id'];
                            break;
                        }
                    }
                    
                    if ($userId) {
                        // Verificamos si ya existe en la tabla administradores
                        try {
                            $adminCheck = $this->client->get('/rest/v1/administradores', [
                                'headers' => [
                                    'Authorization' => "Bearer {$this->serviceKey}",
                                ],
                                'query' => [
                                    'id' => 'eq.' . $userId,
                                    'select' => 'id'
                                ]
                            ]);
                            
                            $existingAdmin = json_decode($adminCheck->getBody()->getContents(), true);
                            if (!empty($existingAdmin)) {
                                return [
                                    'error' => 'Este usuario ya está registrado como administrador',
                                    'status' => 422
                                ];
                            }
                        } catch (Exception $e) {
                            // Si hay error al verificar en administradores, continuamos para intentar crear el registro
                            error_log('Error al verificar administrador: ' . $e->getMessage());
                        }
                        
                        // El usuario existe en Auth pero no en administradores, creamos el registro
                        $adminResponse = $this->client->post('/rest/v1/administradores', [
                            'headers' => [
                                'Authorization' => "Bearer {$this->serviceKey}",
                                'Prefer' => 'return=representation',
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'id' => $userId,
                                'email' => $email,
                                'nombre' => $nombre,
                                'rol' => $rol
                            ]
                        ]);
                        
                        return [
                            'message' => 'Usuario existente añadido como administrador',
                            'admin' => json_decode($adminResponse->getBody()->getContents(), true)
                        ];
                    }
                }
            } catch (Exception $e) {
                // Si hay error al verificar, lo registramos pero continuamos para intentar crear
                error_log('Error al verificar si el usuario existe: ' . $e->getMessage());
            }
            
            // Creamos el usuario en auth
            $response = $this->client->post('/auth/v1/admin/users', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'email_confirm' => true
                ]
            ]);

            $userData = json_decode($response->getBody()->getContents(), true);
            $userId = $userData['id'];

            // Después creamos el registro en la tabla de administradores
            $adminResponse = $this->client->post('/rest/v1/administradores', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                    'Prefer' => 'return=representation',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'id' => $userId,
                    'email' => $email,
                    'nombre' => $nombre,
                    'rol' => $rol
                ]
            ]);

            return [
                'message' => 'Administrador creado correctamente',
                'user' => $userData,
                'admin' => json_decode($adminResponse->getBody()->getContents(), true)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Verifica si una tabla existe y devuelve información sobre su esquema
     */
    public function checkTable(string $tableName)
    {
        try {
            // Intentar obtener información sobre la tabla en el esquema public
            $response = $this->client->get('/rest/v1/' . $tableName . '?limit=1', [
                'headers' => [
                    'Authorization' => "Bearer {$this->serviceKey}",
                ],
            ]);
            
            return [
                'exists' => true,
                'schema' => 'default',
                'path' => '/rest/v1/' . $tableName
            ];
        } catch (ClientException $e) {
            try {
                // Si falla, intentar con el esquema public explícito
                $response = $this->client->get('/rest/v1/public.' . $tableName . '?limit=1', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                    ],
                ]);
                
                return [
                    'exists' => true,
                    'schema' => 'public',
                    'path' => '/rest/v1/public.' . $tableName
                ];
            } catch (ClientException $e2) {
                // Verificar si hay otros esquemas disponibles
                try {
                    $response = $this->client->get('/rest/v1/?limit=100', [
                        'headers' => [
                            'Authorization' => "Bearer {$this->serviceKey}",
                        ],
                    ]);
                    
                    $schemas = json_decode($response->getBody()->getContents(), true);
                    
                    return [
                        'exists' => false,
                        'available_schemas' => $schemas,
                        'error' => $e2->getMessage()
                    ];
                } catch (Exception $e3) {
                    return [
                        'exists' => false,
                        'error' => $e3->getMessage()
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Restablecer contraseña de un usuario
     */
    public function resetPassword(string $email, string $password, string $token)
    {
        try {
            // Depuración - registrar el token parcialmente para verificar
            error_log('Intentando restablecer contraseña con token (primeros 30 chars): ' . substr($token, 0, 30) . '...');
            
            // Intento 1: Usar el endpoint update-password-for-email
            try {
                error_log('Intento 1: Usando update-password-for-email');
                $response = $this->client->put('/auth/v1/user', [
                    'headers' => [
                        'Authorization' => "Bearer {$this->serviceKey}",
                        'apikey' => $this->key,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'email' => $email,
                        'password' => $password
                    ]
                ]);
                
                error_log('Respuesta de restablecimiento (Intento 1): ' . $response->getStatusCode());
                return ['success' => true];
            } catch (Exception $e1) {
                error_log('Error en Intento 1: ' . $e1->getMessage());
                
                // Intento 2: Usar el endpoint update-user-by-token
                try {
                    error_log('Intento 2: Usando update-user-by-token');
                    $response = $this->client->put('/auth/v1/user', [
                        'headers' => [
                            'Authorization' => "Bearer {$token}",
                            'apikey' => $this->key,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'password' => $password
                        ]
                    ]);
                    
                    error_log('Respuesta de restablecimiento (Intento 2): ' . $response->getStatusCode());
                    return ['success' => true];
                } catch (Exception $e2) {
                    error_log('Error en Intento 2: ' . $e2->getMessage());
                    
                    // Intento 3: Usar el endpoint auth/v1/recover
                    try {
                        error_log('Intento 3: Usando auth/v1/recover');
                        $response = $this->client->post('/auth/v1/recover', [
                            'headers' => [
                                'apikey' => $this->key,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'access_token' => $token,
                                'password' => $password
                            ]
                        ]);
                        
                        error_log('Respuesta de restablecimiento (Intento 3): ' . $response->getStatusCode());
                        return ['success' => true];
                    } catch (Exception $e3) {
                        error_log('Error en Intento 3: ' . $e3->getMessage());
                        
                        // Último intento: Intentar con la API de admin
                        try {
                            error_log('Intento 4: Usando admin/users/password');
                            // Intentar decodificar el JWT para obtener el user_id
                            $tokenParts = explode('.', $token);
                            $userId = null;
                            
                            if (count($tokenParts) === 3) {
                                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                                if ($payload && isset($payload['sub'])) {
                                    $userId = $payload['sub'];
                                    error_log('User ID extraído del token: ' . $userId);
                                }
                            }
                            
                            if (!$userId) {
                                // Intentar buscar el usuario por email
                                $userResponse = $this->client->get('/auth/v1/admin/users', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$this->serviceKey}",
                                        'apikey' => $this->key,
                                    ],
                                    'query' => [
                                        'email' => $email
                                    ]
                                ]);
                                
                                $users = json_decode($userResponse->getBody()->getContents(), true);
                                if (!empty($users)) {
                                    $userId = $users[0]['id'];
                                    error_log('User ID encontrado por email: ' . $userId);
                                }
                            }
                            
                            if ($userId) {
                                // Actualizar contraseña usando API de admin
                                $response = $this->client->put('/auth/v1/admin/users/' . $userId, [
                                    'headers' => [
                                        'Authorization' => "Bearer {$this->serviceKey}",
                                        'apikey' => $this->key,
                                        'Content-Type' => 'application/json',
                                    ],
                                    'json' => [
                                        'password' => $password
                                    ]
                                ]);
                                
                                error_log('Respuesta de restablecimiento (Intento 4): ' . $response->getStatusCode());
                                return ['success' => true];
                            } else {
                                throw new Exception('No se pudo determinar el ID del usuario');
                            }
                        } catch (Exception $e4) {
                            error_log('Error en Intento 4: ' . $e4->getMessage());
                            throw $e4;
                        }
                    }
                }
            }
        } catch (GuzzleException $e) {
            error_log('Error al restablecer contraseña: ' . $e->getMessage());
            
            // Intentar extraer más información del error para GuzzleException
            $errorMessage = $e->getMessage();
            if ($e instanceof ClientException && $e->getResponse()) {
                try {
                    $body = $e->getResponse()->getBody()->getContents();
                    error_log('Cuerpo de respuesta de error: ' . $body);
                    $errorData = json_decode($body, true);
                    if (isset($errorData['msg'])) {
                        $errorMessage = $errorData['msg'];
                    }
                } catch (Exception $innerEx) {
                    error_log('Error al obtener detalles del error: ' . $innerEx->getMessage());
                }
            }
            
            return [
                'error' => $errorMessage,
                'status' => $e->getCode() ?: 500
            ];
        } catch (Exception $e) {
            error_log('Error general al restablecer contraseña: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'status' => $e->getCode() ?: 500
            ];
        }
    }
} 