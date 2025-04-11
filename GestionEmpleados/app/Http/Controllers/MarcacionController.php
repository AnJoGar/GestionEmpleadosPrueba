<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Marcacion;
class MarcacionController extends Controller
{
    public function historial($empleado_id)
    {
        // Validar que el empleado exista
        if (!\App\Models\User::where('id', $empleado_id)->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Empleado no encontrado'
            ], 404);
        }

        $marcaciones = Marcacion::where('empleado_id', $empleado_id)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $marcaciones
        ]);
    }

    /**
     * Registrar una nueva marcación
     * POST /api/marcaciones
     */
    public function almacenar(Request $request)
    {
        \Log::debug('Datos recibidos:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|integer|exists:users,id',
            'tipo_marcacion' => 'required|in:ingreso,salida,almuerzo_inicio,almuerzo_fin'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
    
        // Obtener la última marcación del empleado
        $ultimaMarcacion = Marcacion::where('empleado_id', $request->empleado_id)
            ->latest('timestamp')
            ->first();

        // Validación 1: No puede repetir el mismo tipo de marcación consecutiva
        if ($ultimaMarcacion && $ultimaMarcacion->tipo_marcacion === $request->tipo_marcacion) {
            return response()->json([
                'success' => false,
                'error' => 'No puede registrar el mismo tipo de marcación consecutivamente'
            ], 400);
        }

        // Validación 2: Secuencia lógica de marcaciones
        if ($ultimaMarcacion) {
            $tipoAnterior = $ultimaMarcacion->tipo_marcacion;
            $tipoActual = $request->tipo_marcacion;

            // Validar secuencia incorrecta
            $secuenciaInvalida = false;
            $mensajeError = '';

            switch ($tipoAnterior) {
                case 'ingreso':
                    if (!in_array($tipoActual, ['almuerzo_inicio', 'salida'])) {
                        $secuenciaInvalida = true;
                        $mensajeError = 'Después de un ingreso solo puede registrar almuerzo_inicio o salida';
                    }
                    break;
                
                case 'almuerzo_inicio':
                    if ($tipoActual !== 'almuerzo_fin') {
                        $secuenciaInvalida = true;
                        $mensajeError = 'Después de almuerzo_inicio debe registrar almuerzo_fin';
                    }
                    break;
                
                case 'almuerzo_fin':
                    if ($tipoActual !== 'salida') {
                        $secuenciaInvalida = true;
                        $mensajeError = 'Después de almuerzo_fin debe registrar salida';
                    }
                    break;
                
                case 'salida':
                    if ($tipoActual !== 'ingreso') {
                        $secuenciaInvalida = true;
                        $mensajeError = 'Después de una salida solo puede registrar un ingreso';
                    }
                    break;
            }

            if ($secuenciaInvalida) {
                return response()->json([
                    'success' => false,
                    'error' => $mensajeError,
                    'ultima_marcacion' => $ultimaMarcacion->tipo_marcacion
                ], 400);
            }
        } elseif ($request->tipo_marcacion !== 'ingreso') {
            // Validación 3: La primera marcación debe ser de ingreso
            return response()->json([
                'success' => false,
                'error' => 'La primera marcación debe ser de ingreso'
            ], 400);
        }
    
        // Crear la marcación
        $marcacion = Marcacion::create([
            'empleado_id' => $request->empleado_id,
            'tipo_marcacion' => $request->tipo_marcacion,
            'timestamp' => now() // Usamos la fecha/hora actual
        ]);
    
        return response()->json([
            'success' => true,
            'data' => $marcacion,
            'message' => 'Marcación registrada correctamente'
        ], 201);
    }

    public function index($empleado_id)
    {
        $marcaciones = Marcacion::where('empleado_id', $empleado_id)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json($marcaciones);
    }
}
