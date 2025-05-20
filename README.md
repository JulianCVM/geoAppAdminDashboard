# GeoApp Dashboard

![GeoApp Logo]()

## Acerca del proyecto

GeoApp Dashboard es un panel de administración para la aplicación GeoApp, diseñado para gestionar reportes georreferenciados, usuarios y logros. Esta herramienta proporciona a los administradores una interfaz completa para supervisar y gestionar todos los aspectos de la aplicación GeoApp.

Este dashboard permite:
- Visualizar y gestionar reportes creados por los usuarios
- Administrar cuentas de usuarios y permisos
- Visualizar y asignar logros
- Monitorear métricas y estadísticas de uso
- Explorar y consultar la base de datos de manera interactiva
- Visualizar datos espaciales en mapas interactivos

## Stack Tecnológico

### Backend
- **Laravel 10:** Framework PHP para desarrollo backend
- **PHP 8.1+:** Versión de PHP requerida
- **Supabase:** Plataforma de desarrollo con PostgreSQL como base de datos
- **PostgreSQL:** Sistema de gestión de base de datos con soporte para datos espaciales
- **PostGIS:** Extensión espacial para PostgreSQL utilizada para datos georreferenciados

### Frontend
- **Blade:** Sistema de plantillas de Laravel
- **Bootstrap 5:** Framework CSS para diseño responsive
- **JavaScript / jQuery:** Para interactividad en el lado del cliente
- **Leaflet.js:** Biblioteca JavaScript para mapas interactivos
- **Chart.js:** Para visualización de datos y estadísticas
- **Vis.js:** Para visualización de relaciones entre tablas de la base de datos

### Autenticación y Seguridad
- **Laravel Sanctum:** Para autenticación y manejo de tokens
- **Supabase Auth:** Integración con sistema de autenticación de Supabase
- **Row Level Security (RLS):** Para control de acceso a nivel de filas en PostgreSQL

## Estructura del proyecto

El proyecto sigue la estructura estándar de Laravel con algunas personalizaciones:

```
geoAppDashboard/
├── app/                           # Código principal
│   ├── Http/Controllers/          # Controladores
│   │   ├── AuthController.php     # Gestión de autenticación
│   │   ├── DashboardController.php # Panel principal
│   │   ├── ReportController.php   # Gestión de reportes
│   │   ├── UserController.php     # Gestión de usuarios
│   │   ├── AchievementController.php # Gestión de logros
│   │   └── DatabaseExplorerController.php # Explorador DB
│   ├── Models/                    # Modelos Eloquent
│   ├── Services/                  # Servicios
│   │   └── SupabaseSecondaryService.php # Servicio de conexión a Supabase
│   └── Middleware/                # Middleware personalizado
├── config/                        # Configuraciones
│   └── supabase.php               # Configuración de Supabase
├── database/                      # Migraciones y semillas
├── public/                        # Archivos públicos
├── resources/                     # Recursos
│   ├── views/                     # Vistas Blade
│   │   ├── auth/                  # Vistas de autenticación
│   │   ├── dashboard/             # Vistas del panel principal
│   │   ├── database/              # Vistas del explorador de base de datos
│   │   │   ├── query_form.blade.php  # Formulario de consultas SQL
│   │   │   ├── relations.blade.php   # Visualización de relaciones
│   │   │   ├── schema.blade.php      # Estructura de esquemas
│   │   │   └── table.blade.php       # Visualización de tablas
│   │   └── layouts/               # Plantillas maestras
│   ├── js/                        # JavaScript
│   └── css/                       # Estilos CSS
├── routes/                        # Definición de rutas
└── tests/                         # Pruebas automatizadas
```

## Requisitos

- PHP 8.1 o superior
- Composer 2.0+
- Node.js 16+ y NPM
- Base de datos PostgreSQL con extensión PostGIS (o una cuenta en [Supabase](https://supabase.com))
- Servidor web (Apache, Nginx)
- Extensiones PHP: pdo_pgsql, gd, xml, curl, mbstring, zip

## Instalación

### 1. Clonar el repositorio

```bash
git clone [url-del-repositorio]
cd geoAppDashboard
```

### 2. Instalar dependencias

```bash
composer install
npm install
npm run dev
```

### 3. Configuración del entorno

Copia el archivo `.env.example` a `.env` y configura las variables de entorno:

```bash
cp .env.example .env
php artisan key:generate
```

El archivo `.env.example` contiene todas las configuraciones necesarias, incluyendo:
- Configuración de la aplicación
- Configuración de base de datos
- Credenciales de Supabase (principal y secundaria)
- Código de superadmin para registro

### 4. Configuración de Supabase

La aplicación utiliza Supabase como base de datos principal. Necesitarás:

1. Crear una cuenta en [Supabase](https://supabase.com)
2. Crear un nuevo proyecto
3. Activar la extensión PostGIS para soporte de datos espaciales
4. Configurar las siguientes variables en tu archivo `.env`:

```
# Supabase Principal
SUPABASE_URL=https://tu-proyecto.supabase.co
SUPABASE_KEY=tu-clave-publica-anon
SUPABASE_SERVICE_KEY=tu-clave-secreta-de-servicio

# Supabase Secundario (puede ser el mismo que el principal)
SUPABASE_SECONDARY_URL=https://tu-proyecto.supabase.co
SUPABASE_SECONDARY_KEY=tu-clave-publica-anon
SUPABASE_SECONDARY_SERVICE_KEY=tu-clave-secreta-de-servicio

# Código para registro de superadministrador
SUPERADMIN_CODE=codigo-secreto-para-registrar-administradores
```

Para encontrar estas claves en Supabase:
- Ve a la sección "Project Settings" > "API" en tu proyecto de Supabase
- La URL del proyecto aparece en la parte superior
- La clave anon/public (key) se encuentra en "Project API keys"
- La clave de servicio (service_key) aparece como "service_role secret"

### 5. Configuración de funciones SQL requeridas en Supabase

El explorador de base de datos requiere dos funciones SQL personalizadas en Supabase. Debes crear estas funciones ejecutando las siguientes consultas en el Editor SQL de Supabase:

#### Función execute_sql

Esta función permite ejecutar consultas SQL dinámicas desde la aplicación:

```sql
CREATE OR REPLACE FUNCTION public.execute_sql(sql text)
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
    result JSONB;
BEGIN
    EXECUTE 'SELECT to_jsonb(array_agg(row_to_json(t))) FROM (' || sql || ') t' INTO result;
    RETURN result;
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('error', SQLERRM, 'detail', SQLSTATE);
END;
$$;
```

#### Función get_table_relations

Esta función mapea las relaciones entre tablas para la visualización de diagramas:

```sql
CREATE OR REPLACE FUNCTION public.get_table_relations()
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
    result JSONB;
BEGIN
    EXECUTE 'SELECT to_jsonb(array_agg(row_to_json(t))) FROM (
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
        WHERE tc.constraint_type = ''FOREIGN KEY''
        ORDER BY tc.table_schema, tc.table_name
    ) t' INTO result;
    
    RETURN result;
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('error', SQLERRM, 'detail', SQLSTATE);
END;
$$;
```

### 6. Estructura de la base de datos

La aplicación espera las siguientes tablas principales en Supabase (los nombres son sensibles a mayúsculas/minúsculas):

#### Tabla de usuarios (auth.users)
Esta tabla es manejada automáticamente por Supabase Auth.

#### Tabla de Reportes
```sql
CREATE TABLE public."Reportes" (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES auth.users(id),
    titulo TEXT NOT NULL,
    descripcion TEXT,
    ubicacion GEOMETRY(Point, 4326),
    tipo VARCHAR(50),
    estado VARCHAR(20) DEFAULT 'pendiente',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índice espacial para búsquedas geográficas eficientes
CREATE INDEX reportes_ubicacion_idx ON public."Reportes" USING GIST(ubicacion);
```

#### Tabla de Logros
```sql
CREATE TABLE public."Logros" (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    condicion TEXT,
    puntos INTEGER DEFAULT 0,
    icono VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

#### Tabla de asignación de logros
```sql
CREATE TABLE public."UsuarioLogros" (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES auth.users(id),
    logro_id UUID REFERENCES public."Logros"(id),
    fecha_obtencion TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(user_id, logro_id)
);
```

> **Nota importante:** Los nombres de las tablas en PostgreSQL pueden ser sensibles a mayúsculas/minúsculas si se crearon con comillas dobles. Para acceder a tablas como "Reportes" o "Logros" en consultas SQL, debes usar comillas dobles: `SELECT * FROM public."Reportes"`.

## Ejecución

Una vez configurado, puedes iniciar el servidor con:

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## Características principales

### Panel de control
El panel principal muestra un resumen de la actividad de la aplicación, incluyendo:
- Número total de usuarios
- Reportes creados (por estado y categoría)
- Gráficos de actividad reciente
- Mapa de calor de reportes georreferenciados

### Gestión de reportes
- Visualización de reportes en lista y en mapa
- Filtrado por estado, tipo, fecha y ubicación
- Asignación de estados (pendiente, en proceso, resuelto, etc.)
- Exportación de datos en formatos CSV y Excel

### Gestión de usuarios
- Visualización de todos los usuarios registrados
- Asignación de roles y permisos
- Suspensión/activación de cuentas
- Visualización de actividad y logros

### Sistema de logros
- Creación y gestión de logros
- Asignación manual o automática de logros a usuarios
- Visualización de estadísticas de logros

### Explorador de base de datos
El panel de administración incluye un explorador de base de datos que permite:

1. Ver la estructura de la base de datos
2. Visualizar relaciones entre tablas mediante diagramas interactivos
3. Ejecutar consultas SQL personalizadas
4. Explorar datos relacionados de manera visual

### Consejos para consultas SQL

- No incluyas punto y coma (;) al final de tus consultas SQL. Supabase no acepta el punto y coma final al usar la función execute_sql.
- Para tablas con nombres sensibles a mayúsculas, usa comillas dobles: `SELECT * FROM public."Reportes"`.
- Si recibes errores de "relation does not exist", usa la consulta "Listar todas las tablas" para ver los nombres exactos.

## Solución de problemas comunes

### Error: "Function execute_sql does not exist"
Este error ocurre cuando la función `execute_sql` no ha sido creada en Supabase. Asegúrate de ejecutar el código SQL proporcionado en la sección de configuración.

### Error: "Relation does not exist"
Este error suele ocurrir por problemas con la sensibilidad a mayúsculas en los nombres de tablas. Verifica el nombre exacto de la tabla en Supabase y asegúrate de usar comillas dobles si contiene mayúsculas.

### Error de conexión con Supabase
Verifica que las credenciales en el archivo `.env` sean correctas y que el proyecto de Supabase esté activo.

### Problemas con PostGIS
Si encuentras errores relacionados con datos espaciales, asegúrate de que la extensión PostGIS esté habilitada en Supabase:

```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

## Seguridad y rendimiento

### Políticas de seguridad (RLS) en Supabase

Si utilizas políticas RLS (Row Level Security) en Supabase, asegúrate de que las claves de servicio tengan los permisos adecuados para acceder a los datos necesarios para el dashboard.

Ejemplo de política RLS para Reportes:

```sql
-- Permitir a los administradores acceso completo
CREATE POLICY "Los administradores pueden acceder a todos los reportes"
ON public."Reportes"
FOR ALL
TO authenticated
USING (
  auth.uid() IN (
    SELECT user_id FROM public.administrators
  )
);

-- Permitir a los usuarios ver solo sus propios reportes
CREATE POLICY "Los usuarios pueden ver sus propios reportes"
ON public."Reportes"
FOR SELECT
TO authenticated
USING (
  auth.uid() = user_id
);
```

### Optimización de consultas espaciales

Para consultas que involucran datos espaciales, usa los índices espaciales:

```sql
-- Consulta optimizada para buscar reportes en un área
SELECT * FROM public."Reportes"
WHERE ST_DWithin(
  ubicacion,
  ST_SetSRID(ST_MakePoint(-99.1332, 19.4326), 4326),
  5000  -- metros
);
```

## Desarrollo y contribución

### Entorno de desarrollo

Configurar el entorno de desarrollo local:

```bash
# Ejecutar en modo desarrollo
npm run dev

# Compilar para producción
npm run build
```

### Ejecutar pruebas

```bash
php artisan test
```

### Mantenimiento

Para mantener la aplicación actualizada:

```bash
composer update
npm update
```

## Despliegue

### Requisitos para producción
- Servidor web con soporte para PHP 8.1+
- Configuración de HTTPS
- Base de datos PostgreSQL con extensión PostGIS
- Variables de entorno correctamente configuradas

## Soporte

Si tienes preguntas o problemas, contacta al equipo de desarrollo de GeoApp en support@geoapp.com.

## Roadmap

Próximas características:
- Integración con sistemas de notificaciones push
- Panel de análisis avanzado con machine learning
- Exportación a formatos SIG (Sistemas de Información Geográfica)
- Integración con APIs externas de servicios públicos

## Licencia

[Especificar licencia]
