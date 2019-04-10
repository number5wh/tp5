<?php

namespace app\admin\validate;

use think\Validate;

class HandleWithdrawAll extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'idArr|记录信息' => 'require|array',
        'status|审批状态' => 'require|in:1,2,3'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
