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
Route::get('login', 'login/login')->name('loginForm');
Route::post('login/doLogin', 'login/doLogin')->name('doLogin');
Route::get('logout', 'login/logout')->name('logout');
//

Route::group('index', function(){
    Route::get('console', 'index/console')->name('console');
    Route::get('home1', 'index/home1')->name('home1');
    Route::get('home2', 'index/home2')->name('home2');
})->prefix('index/');

//提现管理
Route::group('withdraw', function(){
    Route::get('getList', 'withdraw/getList')->name('withdraw.list');
    Route::get('getListData', 'withdraw/getListData')->name('withdraw.listData');
    Route::get('apply', 'withdraw/apply')->name('withdraw.apply');
    Route::get('doApply', 'withdraw/doApply')->name('withdraw.doApply');
    Route::get('settle', 'withdraw/settle')->name('withdraw.settle');
    Route::get('doSettle', 'withdraw/doSettle')->name('withdraw.doSettle');
})->prefix('index/');

//用户信息
Route::group('user', function(){
    Route::get('getList', 'user/getList')->name('user.list');
    Route::get('getListData', 'user/getListData')->name('user.listData');
})->prefix('index/');

//账号信息
Route::group('account', function(){
    Route::get('playerList', 'account/playerList')->name('account.playerList');
    Route::get('playerListData', 'account/playerListData')->name('account.playerListData');
    Route::get('searchPlayer', 'account/searchPlayer')->name('account.searchPlayer');
    Route::get('agentList', 'account/agentList')->name('account.agentList');
    Route::get('agentListData', 'account/agentListData')->name('account.agentListData');
    Route::get('searchAgent', 'account/searchAgent')->name('account.searchAgent');

})->prefix('index/');




return [

];
