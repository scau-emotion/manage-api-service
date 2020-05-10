<?php

namespace App\Http\Middleware;

use App\Exceptions\Dictionary;
use App\Exceptions\LogicException;
use App\Modals\User;
use Closure;
use Illuminate\Support\Facades\Response;

class AddTraceID
{

    public function handle($request, Closure $next, $guard = null)
    {
        header('Trace-Id: ' . $this->getTraceId($request));
        return $next($request);

    }


    public function getTraceId($request)
    {
        return $request->headers->get('x-b3-traceid');
    }
}
