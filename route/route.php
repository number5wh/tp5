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
Route::get('capture', 'login/verify')->name('capture');
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
    Route::post('getStatistics', 'index/getStatistics')->name('index.getStatistics');
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
//    Route::post('searchPlayer', 'account/searchPlayer')->name('account.searchPlayer');
    Route::get('proxyList', 'account/proxyList')->name('account.proxyList');
    Route::get('proxyListData', 'account/proxyListData')->name('account.proxyListData');
//    Route::post('searchProxy', 'account/searchProxy')->name('account.searchProxy');
    Route::get('addProxy', 'account/addProxy')->name('account.addProxy');
    Route::post('doAddProxy', 'account/doAddProxy')->name('account.doAddProxy');
    Route::get('getPercent', 'account/getPercent')->name('account.getPercent');
    Route::post('edit', 'account/edit')->name('account.edit');
    Route::post('doEdit', 'account/doEdit')->name('account.doEdit');
    Route::post('doPlayerEdit', 'account/doPlayerEdit')->name('account.doPlayerEdit');
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

//分享页
Route::group('template', function(){
    Route::get('index', 'template/index')->name('template.index');
    Route::get('generate', 'template/generate')->name('template.generate');
    Route::get('save/:tempid', 'template/save');
})->prefix('index/');





Route::group('test', function(){
    Route::get('index', 'test/index')->name('test.index');

});



//管理员
Route::group('admin', function() {
    Route::get('login', 'admin/login/login')->name('admin.login');
    Route::post('doLogin', 'admin/login/doLogin')->name('admin.doLogin');
    Route::get('/', 'admin/index/layout')->name('admin.layout');

    Route::get('logout', 'admin/login/logout')->name('admin.logout');


    Route::get('account/proxyList', 'admin/account/proxyList')->name('admin.account.proxyList');
    Route::get('account/proxyListData', 'admin/account/proxyListData')->name('admin.account.proxyListData');
    Route::post('account/edit', 'admin/account/edit')->name('admin.account.edit');
    Route::post('account/doEdit', 'admin/account/doEdit')->name('admin.account.doEdit');


    Route::get('withdraw/getList', 'admin/withdraw/getList')->name('admin.withdraw.list');
    Route::get('withdraw/getListData', 'admin/withdraw/getListData')->name('admin.withdraw.listData');
    Route::post('withdraw/doWithdraw', 'admin/withdraw/doWithdraw')->name('admin.withdraw.doWithdraw');
    Route::post('withdraw/doWithdrawAll', 'admin/withdraw/doWithdrawAll')->name('admin.withdraw.doWithdrawAll');

    Route::get('user/getList', 'admin/user/getList')->name('admin.user.list');
    Route::get('user/getListData', 'admin/user/getListData')->name('admin.user.listData');
    Route::get('user/add', 'admin/user/add')->name('admin.user.add');
    Route::post('user/doAdd', 'admin/user/doAdd')->name('admin.user.doAdd');

    Route::get('safeset/index', 'admin/safeset/index')->name('admin.safeset.index');
    Route::post('safeset/changePwd', 'admin/safeset/changePwd')->name('admin.safeset.changePwd');
})->prefix('admin/');



return [

];
