<?php

namespace App\Http\Middleware;

use App\Exceptions\Dictionary;
use App\Exceptions\LogicException;
use Closure;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (env('APP_ENV') == strtolower('production')) {
            if (empty($request->input('token')) || !$this->checkToken($request->input('token'))) {
                throw new LogicException(...Dictionary::LoginInfoError);
            }
        }

        return $next($request);
    }

    public function checkToken($token)
    {
        $token = explode(',', base64_decode($token));
        $token_verify = md5($token[0] . ',' . $token[1] . ',emotion_token');
        if (time() < $token[1] AND $token_verify = $token_verify[3]) {
            return true;
        }
        return false;
    }
}
