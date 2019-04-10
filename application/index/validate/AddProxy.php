<?php

namespace app\index\validate;

use think\Validate;

class AddProxy extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
//        '__token__' => 'token|require',
	    'username|账号' => 'require|mobile',
        'password|密码'   => 'require|min:8|max:12|regex:/^[0-9a-zA-Z]{8,12}$/|confirm',
        'percent|分成比例'  => 'require|number|in:10,20,30,40,50,60,70,80,90',
        'allow_addproxy|允许开通下级代理' => 'require|number|in:0,1',
        'descript|备注' => 'max:50'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'password.regex' => '密码由数字+字母组成，长度为8~12位哦'
    ];
}
