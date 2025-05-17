<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration (Base de datos principal)
    |--------------------------------------------------------------------------
    */
    'url' => env('SUPABASE_URL', ''),
    'key' => env('SUPABASE_KEY', ''),
    'service_key' => env('SUPABASE_SERVICE_KEY', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Supabase Secondary Configuration (Base de datos de la app móvil)
    |--------------------------------------------------------------------------
    */
    'secondary_url' => env('SUPABASE_SECONDARY_URL', ''),
    'secondary_key' => env('SUPABASE_SECONDARY_KEY', ''),
    'secondary_service_key' => env('SUPABASE_SECONDARY_SERVICE_KEY', ''),
]; 