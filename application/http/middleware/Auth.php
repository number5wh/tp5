<?php

namespace app\http\middleware;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if (!session('?role') || session('role') != 'proxy') {
            session(null);
            cookie('adminname', null);
            cookie('username', null);
            cookie('auth', null);
            return redirect(url('login'));
        }
        return  $next($request);
    }
}
