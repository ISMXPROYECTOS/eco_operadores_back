<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        # Obtenemos la información que nos proporciona el cliente 
        $json = $request->json;

        # Obtenemos el usuario y contraseña
        $user = (!is_null($json['user'])) ? $json['user'] : null;
        $password = (!is_null($json['password'])) ? $json['password'] : null;

        # Validamos si el usuario y contraseña son difernete a nulos
        if ($user == null || $password == null) {
            return response()->json([
                'ok' => 'false',
                'message' => 'El usuario o contraseña no pueden ser nulas'
            ], 400);
        }

        # Preparamos la sentencia sql que nos permite buscar al usuario en la db
        $user = User::where([
            'login' => $user,
            'password' => hash('sha256', $password)
        ])->first();

        # Validamos si el usuario existe en la base de datos
        if(is_null($user)){
            return response()->json([
                'ok' => 'false',
                'message' => 'Las credenciales no corresponden a ninguna cuenta'
            ], 400);
        }

        # Inicializamos el helper JWTAuth y hacemos uso de su funcion create
        $JwtAuth = new JwtAuth();
        $token = $JwtAuth->createJwt($user['id']);

        # Retornamos el token obtenido para iniciar sesión

        return response()->json([
            'ok' => 'true',
            'message' => 'Bienvenido de nuevo!',
            'token' => $token
        ], 200);
    }
}
