<?php

namespace App\Http\Controllers;

use App\Services\SupabaseSecondaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EstadisticasController extends Controller
{
    protected $supabaseSecondaryService;

    public function __construct(SupabaseSecondaryService $supabaseSecondaryService)
    {
        $this->supabaseSecondaryService = $supabaseSecondaryService;
        $this->middleware('auth.admin');
    }

    /**
     * Mostrar la página de estadísticas
     */
    public function index()
    {
        $token = Session::get('access_token');
        $reportes = $this->supabaseSecondaryService->getReportes($token);
        
        // Estadísticas generales
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
            'reportes_cancelados' => count(array_filter($reportes, function($r) {
                return isset($r['estado']) && $r['estado'] === 'cancelado';
            })),
        ];
        
        // Datos para gráfico de estados
        $datosEstados = [
            'labels' => ['Pendiente', 'En proceso', 'Resuelto', 'Cancelado'],
            'data' => [
                $estadisticas['reportes_pendientes'],
                $estadisticas['reportes_en_proceso'],
                $estadisticas['reportes_resueltos'],
                $estadisticas['reportes_cancelados']
            ],
            'colors' => ['#FFB74D', '#64B5F6', '#4CAF50', '#E57373']
        ];
        
        // Datos para gráfico de importancia
        $importancias = array_count_values(array_map(function($reporte) {
            return $reporte['importancia'] ?? 0;
        }, $reportes));
        
        $datosImportancia = [
            'labels' => ['1 Estrella', '2 Estrellas', '3 Estrellas', '4 Estrellas', '5 Estrellas'],
            'data' => [
                $importancias[1] ?? 0,
                $importancias[2] ?? 0,
                $importancias[3] ?? 0,
                $importancias[4] ?? 0,
                $importancias[5] ?? 0
            ],
            'colors' => ['#A5D6A7', '#8FBC8F', '#66BB6A', '#4CAF50', '#2E8B57']
        ];
        
        // Tendencia temporal (reportes por mes)
        $reportesPorMes = [];
        foreach ($reportes as $reporte) {
            if (isset($reporte['created_at'])) {
                $fecha = date('Y-m', strtotime($reporte['created_at']));
                if (!isset($reportesPorMes[$fecha])) {
                    $reportesPorMes[$fecha] = 0;
                }
                $reportesPorMes[$fecha]++;
            }
        }
        
        // Ordenar por fecha
        ksort($reportesPorMes);
        
        $datosTendencia = [
            'labels' => array_map(function($fecha) {
                return date('M Y', strtotime($fecha . '-01'));
            }, array_keys($reportesPorMes)),
            'data' => array_values($reportesPorMes),
            'color' => '#64B5F6'
        ];
        
        // Tipo de reporte
        $tiposReporte = array_count_values(array_map(function($reporte) {
            return $reporte['tipo'] ?? 'Sin tipo';
        }, $reportes));
        
        arsort($tiposReporte); // Ordenar por cantidad descendente
        
        $datosTipos = [
            'labels' => array_keys($tiposReporte),
            'data' => array_values($tiposReporte),
            'colors' => ['#8FBC8F', '#A5D6A7', '#66BB6A', '#4CAF50', '#2E8B57', '#1B5E20']
        ];
        
        return view('dashboard.estadisticas.index', compact(
            'estadisticas', 
            'datosEstados', 
            'datosImportancia', 
            'datosTendencia',
            'datosTipos'
        ));
    }
    
    /**
     * Generar informe en formato JSON
     */
    public function generarInformeJSON()
    {
        $token = Session::get('access_token');
        $reportes = $this->supabaseSecondaryService->getReportes($token);
        
        $informe = [
            'fecha_generacion' => date('Y-m-d H:i:s'),
            'total_reportes' => count($reportes),
            'distribucion_estados' => [
                'pendiente' => count(array_filter($reportes, function($r) {
                    return isset($r['estado']) && $r['estado'] === 'pendiente';
                })),
                'en_proceso' => count(array_filter($reportes, function($r) {
                    return isset($r['estado']) && $r['estado'] === 'en_proceso';
                })),
                'resuelto' => count(array_filter($reportes, function($r) {
                    return isset($r['estado']) && $r['estado'] === 'resuelto';
                })),
                'cancelado' => count(array_filter($reportes, function($r) {
                    return isset($r['estado']) && $r['estado'] === 'cancelado';
                })),
            ],
            'reportes_recientes' => array_slice(
                array_filter($reportes, function($r) {
                    return isset($r['created_at']) && strtotime($r['created_at']) > strtotime('-30 days');
                }),
                0, 10
            )
        ];
        
        return response()->json($informe);
    }
} 