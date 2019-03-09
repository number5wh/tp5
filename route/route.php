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
Route::get('login', 'login/login')->name('login');
Route::post('login/doLogin', 'login/doLogin')->name('doLogin');
Route::get('logout', 'login/logout')->name('logout');

//首页
Route::group('index', function(){
    Route::get('home', 'index/home')->name('home');
    //刷新token
    Route::get('refreshToken', function() {
        return Request::token();
    })->cache(false)->name('getToken');
    Route::post('profitStatistics', 'index/profitStatistics')->name('index.profitStatistics');

})->prefix('index/');

//提现管理
Route::group('withdraw', function(){
    Route::get('getList', 'withdraw/getList')->name('withdraw.list');
    Route::get('getListData', 'withdraw/getListData')->name('withdraw.listData');
    Route::get('getAmount', 'withdraw/getAmount')->name('withdraw.getAmount');
    Route::get('apply', 'withdraw/apply')->name('withdraw.apply');
    Route::post('doApply', 'withdraw/doApply')->name('withdraw.doApply');
    Route::get('set', 'withdraw/set')->name('withdraw.set');
    Route::post('doSetAlipay', 'withdraw/doSetAlipay')->name('withdraw.doSetAlipay');
    Route::post('doSetBank', 'withdraw/doSetBank')->name('withdraw.doSetBank');
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
    Route::get('addAgent', 'account/addAgent')->name('account.addAgent');
    Route::get('doAddAgent', 'account/doAddAgent')->name('account.doAddAgent');
})->prefix('index/');

//安全设置
Route::group('safeset', function(){
    Route::get('index', 'safeset/index')->name('safeset.index');
    Route::post('changePwd', 'safeset/changePwd')->name('safeset.changePwd');
    Route::post('changeMobile', 'safeset/changeMobile')->name('safeset.changeMobile');
})->prefix('index/');

//明细查询
Route::group('detail', function(){
    Route::get('index', 'detail/index')->name('detail.index');
    Route::get('getData', 'detail/getData')->name('detail.getData');
})->prefix('index/');
//发送短信
Route::group('sendmsg', function(){
    Route::post('index', 'sendmsg/index')->name('sendmsg.index');
    Route::post('index2', 'sendmsg/index2')->name('sendmsg.index2');
})->prefix('index/');

Route::group('test', function(){
    Route::get('index', 'test/index')->name('test.index');
});


return [

];
