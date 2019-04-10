<?php

namespace app\index\validate;

use think\Validate;

class DoWithdraw extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'checktype|账号类型' => 'require|in:1,2',
        'amount|提现金额'   => 'require|float|gt:0',
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
