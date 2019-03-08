<?php

namespace app\http\middleware;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if (!session('?username')) {
            return redirect(url('login'));
        }
        return  $next($request);
    }
}
