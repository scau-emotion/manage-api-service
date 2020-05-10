<?php

namespace App\Http\Middleware;

use App\Exceptions\Dictionary;
use App\Exceptions\LogicException;
use App\Modals\User;
use Closure;

class Authenticate
{

    private $userId;
    private $expiredAt;
    private $verifiedToken;

    public function handle($request, Closure $next, $guard = null)
    {
        $span = app('Tracing')->startActiveSpan('auth check');


        if (env('APP_ENV') == strtolower('production')) {

            if (
                empty($request->input('token'))
                or
                ($this->getTokenInfo($request->input('token')) and !$this->checkToken())
            ) {
                $span->close();
                throw new LogicException(...Dictionary::LoginInfoError);
            }

            $user_id = $this->userId;

            app('Tracing')->startSpan('Auth Info', [
                'tags' => [
                    'UserId' => $user_id
                ]
            ]);

            app()->singleton('user', function () use ($user_id) {
                return User::getUserById($user_id);
            });

        }


        $span->close();

        return $next($request);
    }

    public function checkToken()
    {
        $token_verify = md5($this->userId . ',' . $this->expiredAt . ',emotion_token');

        return (
            time() <= $this->expiredAt AND $token_verify == $this->verifiedToken
        );


    }

    public function getTokenInfo($token)
    {
        list($this->userId, $this->expiredAt, $this->verifiedToken) = explode(',', base64_decode($token));

        return true;
    }
}
