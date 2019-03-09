<?php

namespace app\index\validate;

use think\Validate;

class changePwd extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'password|密码'   => 'require|min:6|confirm',
        'code|验证码' => 'require|regex:\d{4}'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'code.regex' => '请输入4位验证码数字'
    ];
}
