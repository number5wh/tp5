<?php

namespace app\http\middleware;

class AdminAuth
{
    public function handle($request, \Closure $next)
    {
        if (!session('?role') || session('role') != 'admin') {
            session(null);
//            cookie('adminname', null);
            cookie('username', null);
            cookie('auth', null);
            return redirect(url('admin.login'));
        }
        return  $next($request);
    }
}
