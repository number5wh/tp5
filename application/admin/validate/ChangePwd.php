<?php

namespace app\admin\validate;

use think\Validate;

class ChangePwd extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'password|密码'   => 'require|min:8|max:12|regex:/^[0-9a-zA-Z]{8,12}$/|confirm'
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
