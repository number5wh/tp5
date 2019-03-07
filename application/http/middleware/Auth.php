<?php

namespace app\http\middleware;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if (!session('?user')) {
            return redirect('/login');
        }
        return  $next($request);
    }
}
