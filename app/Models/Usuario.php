<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    protected $table = 'usuarios';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'email',
        'contrasena',
        'fecha_registro',
        'biografia',
    ];

    // Indica a Laravel que tu campo de contraseña se llama 'contrasena'
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Métodos requeridos por JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey(); // retorna el valor de la llave primaria
    }

    public function getJWTCustomClaims()
    {
        return []; // puedes agregar campos personalizados si lo deseas
    }
}
