<?php

namespace app\index\validate;

use think\Validate;

class changeMobile extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'mobile|手机号' => 'require|mobile',
        'mobile_confirm|手机号' => 'require|mobile|confirm:mobile',
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
