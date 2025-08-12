<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    public function manejar(Request $request)
    {
        $accion = $request->input('accion');

        if ($accion === 'crear_evento') {
            return $this->crearEvento($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Acción no válida. Usa "crear_evento".'
        ], 400);
    }

    private function crearEvento(Request $request)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No estás autenticado.'
            ], 401);
        }

        $request->validate([
            'nombre_evento' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'es_publico' => 'required|boolean'
        ]);

        $evento_id = DB::table('eventos')->insertGetId([
            'creador_id' => $usuario->usuario_id,
            'nombre_evento' => $request->nombre_evento,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'es_publico' => $request->es_publico,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evento creado exitosamente.',
            'evento_id' => $evento_id
        ]);
    }
}
