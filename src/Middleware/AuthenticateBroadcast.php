<?php

namespace Elyerr\ApiExtend\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth as Authenticable;

class AuthenticateBroadcast
{
    public function handle($request, Closure $next)
    {
        $token = $this->getAuthorization($request);

        if ($this->user_can_join($token)) {
            return $next($request);
        }

        return $next($request);
    }

    /**
     * Obtiene el token que se envia en la cabecera de la Authorization
     * @param $request
     */
    public function getAuthorization($request)
    {
        return $request->header('Authorization') ?
        explode(' ', $request->header('Authorization'))[1] : null;
    }

    /**
     * obtiene el identificador del usuario a travez del token
     * @param String $token
     */
    public function userID($token)
    {
        $personalToken = Authenticable::PersonalAccessToken($token);

        return $personalToken;

    }

    /**
     * verifica que el usuario se encuentre en el sistema y se autentique
     * @param String $token
     * @return Boolean
     */
    public function user_can_join($token)
    {
        $sanctum = $this->userID($token);

        if ($sanctum) {
            $className = $sanctum->tokenable_type;
            $authenticableId = $sanctum->tokenable_id;

            $user = (new $className())->find($authenticableId);

            if ($user) {
                Auth::login($user);
                return true;
            }
        }

        return false;
    }
}
