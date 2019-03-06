<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('/', 'index/layout')->name('layout');
Route::get('hello', 'index/hello');
//

Route::group('index', function(){
    Route::get('console', 'index/console')->name('console');
    Route::get('home1', 'index/home1')->name('home1');
    Route::get('home2', 'index/home2')->name('home2');
})->prefix('index/');

Route::get('login/login', 'login/login')->name('loginForm');
Route::post('login/doLogin', 'login/doLogin')->name('doLogin');


return [

];
