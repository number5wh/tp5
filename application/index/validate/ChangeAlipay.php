<?php

namespace app\index\validate;

use think\Validate;

class changeAlipay extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        '__token__' => 'token|require',
        'alipay_name|姓名' => 'require',
        'alipay|支付宝账号' => 'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
