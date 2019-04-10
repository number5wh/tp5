<?php

namespace app\admin\validate;

use think\Validate;

class AddUser extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
//        '__token__' => 'token|require',
	    'username|账号' => 'require|min:6|max:12|regex:/^[0-9a-zA-Z]{8,12}$/',
        'password|密码'   => 'require|min:8|max:12|regex:/^[0-9a-zA-Z]{8,12}$/|confirm',
        'role|角色'  => 'require|number|in:2',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'password.regex' => '密码由数字+字母组成，长度为8~12位哦',
        'username.regex' => '账号由数字+字母组成，长度为6~12位哦',
        'role.in' => '角色信息有误'
    ];
}
