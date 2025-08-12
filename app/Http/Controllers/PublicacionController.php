<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicacionController extends Controller
{
    public function manejar(Request $request)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No estás autenticado.'
            ], 401);
        }

        if ($request->isMethod('post')) {
            $accion = $request->input('accion');

            if ($accion === 'crear_publicacion') {
                return $this->crearPublicacion($request, $usuario);
            }

            if ($accion === 'crear_comentario') {
                return $this->crearComentario($request, $usuario);
            }

            return response()->json([
                'success' => false,
                'message' => 'Acción no válida. Usa "crear_publicacion" o "crear_comentario".'
            ], 400);
        }

        if ($request->isMethod('get')) {
            return $this->obtenerPublicaciones($request, $usuario);
        }

        return response()->json([
            'success' => false,
            'message' => 'Método no permitido.'
        ], 405);
    }

    private function crearPublicacion(Request $request, $usuario)
    {
        $request->validate([
            'contenido' => 'required|string',
            'tipo' => 'required|string',
            'id_archivo' => 'nullable|integer|exists:archivo,id_archivo',
        ]);

        $publicacion_id = DB::table('publicaciones')->insertGetId([
            'autor_id' => $usuario->usuario_id,
            'contenido' => $request->contenido,
            'tipo' => $request->tipo,
            'id_archivo' => $request->id_archivo,
            'fecha_publicacion' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Publicación creada exitosamente.',
            'publicacion_id' => $publicacion_id
        ]);
    }

    private function crearComentario(Request $request, $usuario)
    {
        $request->validate([
            'publicacion_id' => 'required|integer|exists:publicaciones,publicacion_id',
            'texto' => 'required|string',
        ]);

        $comentario_id = DB::table('comentarios')->insertGetId([
            'publicacion_id' => $request->publicacion_id,
            'usuario_id' => $usuario->usuario_id,
            'texto' => $request->texto,
            'fecha_comentario' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comentario agregado exitosamente.',
            'comentario_id' => $comentario_id
        ]);
    }

    private function obtenerPublicaciones(Request $request, $usuario)
    {
        $query = DB::table('publicaciones')
            ->join('usuarios', 'publicaciones.autor_id', '=', 'usuarios.usuario_id')
            ->select(
                'publicaciones.publicacion_id',
                'publicaciones.contenido',
                'publicaciones.tipo',
                'publicaciones.id_archivo',
                'publicaciones.fecha_publicacion',
                'usuarios.nombre as autor_nombre'
            )
            ->orderBy('publicaciones.fecha_publicacion', 'desc');

        if ($request->boolean('mias')) {
            $query->where('autor_id', $usuario->usuario_id);
        }

        $publicaciones = $query->get();

        return response()->json([
            'success' => true,
            'publicaciones' => $publicaciones
        ]);
    }
}
