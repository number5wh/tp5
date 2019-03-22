<?php

namespace app\index\controller;

use app\index\model\UserTemplate;
use qrCode\Code;
use shortUrl\ShortUrl;
use think\Controller;
use think\Request;

class Template extends Controller
{
    protected $middleware = ['Auth'];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $tempid = 1;
        $userTemplateModel = new UserTemplate();
        $tempInfo          = $userTemplateModel->getRow(['proxy_id' => session('code'), 'template_code' => $tempid]);
        $pic  = $shorturl  = '';
        $proxy_id = session("code");
        if ($tempInfo) {
            $pic = $tempInfo['image_url'];
            if ($tempInfo['down_url']) {
                $shorturl = $tempInfo['down_url'];
            } else {
                $pic = "upload/qrcode/". $proxy_id . '_' . $tempid . ".png";
                $shorturl = ShortUrl::geturl('http://'.$_SERVER['SERVER_NAME'].'/'.$pic);
                if ($shorturl) {
                    $userTemplateModel->updateByWhere(['proxy_id' => $proxy_id, 'template_code' => $tempid], ['down_url' => $shorturl]);
                }
            }

        } else {
            Code::qrcode($proxy_id);
            $templateModel = new \app\index\model\Template();
            $template      = $templateModel->getRow(['template_code' => $tempid]);
            if ($template) {

                $target   = $filename = config('config.qrcode_dir') . DIRECTORY_SEPARATOR . $proxy_id . ".png";
                $filename = config('config.qrcode_dir') . DIRECTORY_SEPARATOR . $proxy_id . '_' . $tempid . ".png";
                $source   = env('root_path') . $template["template_image"];//str_replace("/public/",);
                $res      = combinePic($source, $target, $template["x"], $template["y"], $filename);
                if ($res) {
                    $pic = "upload/qrcode/". $proxy_id . '_' . $tempid . ".png";
                    $insertData =     [
                        'proxy_id' => session('code'),
                        'template_code' => $tempid,
                        'qrcode' => "upload/qrcode/". $proxy_id . ".png",
                        'image_url' => "upload/qrcode/". $proxy_id . '_' . $tempid . ".png",
                    ];
                    //获取短链接地址
                    $shorturl = ShortUrl::geturl('http://'.$_SERVER['SERVER_NAME'].'/'.$pic);
                    if ($shorturl) {
                        $insertData['down_url'] = $shorturl;
                    }
                    $userTemplateModel->add($insertData);
                }
//            header('Content-Disposition:attachment;filename=' . basename($filename));
//            header('Content-Length:' . filesize($filename));
////读取文件并写入到输出缓冲
//            readfile($filename);
//            exit();
            }
        }


        $this->assign('pic', $pic);
        $this->assign('short_url', $shorturl);
        return view('index');
    }

    public function generate()
    {
        $data = ['code' => 0, 'msg' => '', 'pic' => '', 'short_url' => '', 'tempid' => ''];
        $tempid = $this->request->tempid ? intval($this->request->tempid) : 1;
        $pic  = $shorturl  = '';
        if (!in_array($tempid, [1, 2, 3, 4, 5, 6])) {
            $tempid = 1;
        } else {
            if ($tempid == 6) {
                $tempid = 1;
            } else {
                $tempid++;
            }
        }
        $data['tempid'] = $tempid;
        $userTemplateModel = new UserTemplate();
        $tempInfo          = $userTemplateModel->getRow(['proxy_id' => session('code'), 'template_code' => $tempid]);
        $proxy_id = session("code");
        if ($tempInfo) {
            $data['pic'] = $tempInfo['image_url'];
            if ($tempInfo['down_url']) {
                $shorturl = $tempInfo['down_url'];
            } else {
                $pic = "upload/qrcode/". $proxy_id . '_' . $tempid . ".png";
                $shorturl = ShortUrl::geturl('http://'.$_SERVER['SERVER_NAME'].'/'.$pic);
                if ($shorturl) {
                    $userTemplateModel->updateByWhere(['proxy_id' => $proxy_id, 'template_code' => $tempid], ['down_url' => $shorturl]);
                }
            }
            $data['short_url'] = $shorturl;
            return json($data);
        }
        $templateModel = new \app\index\model\Template();
        $template      = $templateModel->getRow(['template_code' => $tempid]);

        if ($template) {
            $target   = $filename = config('config.qrcode_dir') . DIRECTORY_SEPARATOR . $proxy_id . ".png";
            $filename = config('config.qrcode_dir') . DIRECTORY_SEPARATOR . $proxy_id . '_' . $tempid . ".png";
            $source   = env('root_path') . $template["template_image"];//str_replace("/public/",);
            $res      = combinePic($source, $target, $template["x"], $template["y"], $filename);
            if ($res) {
                $pic = "upload/qrcode/". $proxy_id . '_' . $tempid . ".png";
                $insertData =     [
                    'proxy_id' => session('code'),
                    'template_code' => $tempid,
                    'qrcode' => "upload/qrcode/". $proxy_id . ".png",
                    'image_url' => "upload/qrcode/". $proxy_id . '_' . $tempid . ".png",
                ];
                //获取短链接地址
                $shorturl = ShortUrl::geturl('http://'.$_SERVER['SERVER_NAME'].'/'.$pic);
                if ($shorturl) {
                    $insertData['down_url'] = $shorturl;
                }
                $userTemplateModel->add($insertData);
            }
//            header('Content-Disposition:attachment;filename=' . basename($filename));
//            header('Content-Length:' . filesize($filename));
////读取文件并写入到输出缓冲
//            readfile($filename);
//            exit();
        }
        $data['pic'] = $pic;
        $data['short_url'] = $shorturl;
        return json($data);
    }

    /**
     * 下载二维码
     */
    public function save()
    {
        $tempid = $this->request->tempid ? intval($this->request->tempid) : 1;
        $pic    = '';
        if (!in_array($tempid, [1, 2, 3, 4, 5, 6])) {
            $tempid = 1;
        }
        $userTemplateModel = new UserTemplate();
        $tempInfo          = $userTemplateModel->getRow(['proxy_id' => session('code'), 'template_code' => $tempid]);
        if ($tempInfo) {
            header("Cache-control: private");
            header('Content-Type: image/png');
            header('Content-Disposition:attachment;filename=' . basename($tempInfo['image_url']));
            header('Content-Length:' . filesize(env('root_path').'public/'.$tempInfo['image_url']));
//读取文件并写入到输出缓冲
            readfile(env('root_path').'public/'.$tempInfo['image_url']);
            exit();
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
