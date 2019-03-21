<?php

namespace app\index\controller;

use apiData\PlayerData;
use app\index\model\Paytime;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
use apiData\Sms;
use Endroid\QrCode\QrCode;
use qrCode\Code;
use think\Controller;
use think\Db;
use think\Request;

class Test extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $tempid =1;
        $templateModel = new \app\index\model\Template();
        $template = $templateModel->getRow(['template_code' => $tempid]);
 ;
        if($template){
            $proxy_id = session("code");
            $target =$filename =config('config.qrcode_dir').DIRECTORY_SEPARATOR.$proxy_id.".png";
            $filename =config('config.qrcode_dir').DIRECTORY_SEPARATOR.$proxy_id.'_'.$tempid.".png";
            $source = env('root_path').$template["template_image"];//str_replace("/public/",);


            $res = combinePic($source,$target,$template["x"],$template["y"],$filename);
//            header('Content-Disposition:attachment;filename=' . basename($filename));
//            header('Content-Length:' . filesize($filename));
////读取文件并写入到输出缓冲
//            readfile($filename);
//            exit();
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
