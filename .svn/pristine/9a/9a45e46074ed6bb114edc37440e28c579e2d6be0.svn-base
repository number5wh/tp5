<?php

namespace app\index\validate;

use think\Validate;

class changeBank extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        //'__token__' => 'token|require',
        'name|姓名' => 'require',
        'cardaccount|银行账号' => 'require',
        'bank|开户行' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
