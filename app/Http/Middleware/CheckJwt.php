<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JwtAuth;

class CheckJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        # Verificamos si la cabecera de autenticxación existe
        if (!$request->header('Authorization')) {
            return response()->json([
                'ok' => 'false',
                'message' => 'La petición no cuenta con la cabecera de autenticación'
            ], 400);
        }

        # Proporcionamos el token al helper "JWTAuth", para procesarlo
        $JwtAuth = new JwtAuth();
        $response = $JwtAuth->checkToken($request->header('Authorization'));
        
        # Verificamos si la respuesta es erronea y enviamos el error
        if($response['ok'] == 'false'){
            return response()->json($response, $response['code']);
        }

        $request['user_id'] = $response['user_id'];
        return $next($request);
    }
}
