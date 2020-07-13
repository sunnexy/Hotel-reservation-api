<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
//use App\Traits\HandlesResponse;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    //use HandlesResponse;
    protected $auth;


    public function handle($request, Closure $next)
    {
        if ($request->user())
        {
            return $next($request);
        }
        return response()->json('Unauthorized',401);
    }

//    public function handle($request, Closure $next)
//    {
//        if ($this->auth->check()) {
//            return response('Unauthorized.', 401);
//        }
//        return $next($request);
//    }

//    protected function redirectTo($request)
//    {
//        if (! $request->expectsJson()) {
//            return route('login');
//        }
//    }
}
