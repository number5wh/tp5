<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function doLogin()
    {
        
    }
}
