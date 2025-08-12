<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegistroController extends Controller
{
    public function manejar(Request $request)
    {
        $accion = $request->input('accion');

        if ($accion === 'registro') {
            return $this->registrar($request);
        }

        if ($accion === 'login') {
            return $this->iniciarSesion($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Acción no válida. Usa "accion": "login" o "accion": "registro".'
        ], 400);
    }

    private function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:255',
            'email'       => 'required|email|unique:usuarios,email',
            'contrasena'  => 'required|string|min:6',
            'biografia'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errores' => $validator->errors()
            ], 422);
        }

        $usuario = Usuario::create([
            'nombre'         => $request->nombre,
            'email'          => $request->email,
            'contrasena'     => Hash::make($request->contrasena),
            'fecha_registro' => Carbon::now(),
            'biografia'      => $request->biografia,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'usuario_id' => $usuario->usuario_id
        ]);
    }

    private function iniciarSesion(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->contrasena, 
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $usuario = Usuario::where('email', $credentials['email'])->first();

        return response()->json([
            'success' => true,
            'token' => $token,
            'usuario' => [
                'usuario_id' => $usuario->usuario_id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'biografia' => $usuario->biografia,
                'fecha_registro' => $usuario->fecha_registro
            ]
        ]);
    }

    public function refreshToken()
    {
        try {
            $nuevoToken = JWTAuth::parseToken()->refresh();
            return response()->json([
                'success' => true,
                'token' => $nuevoToken
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado.'
            ], 401);
        }
    }
}
