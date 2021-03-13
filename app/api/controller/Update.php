<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-07-09 15:26:09
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:24:58
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : Update Controller Of Api
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/api/controller/Update.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\api\controller;

use think\admin\Controller;
use think\admin\service\ModuleService;
use think\admin\service\SystemService;

/**
 * 安装服务端支持
 * Class Update
 * @package app\admin\controller\api
 */
class Update extends Controller
{

    /**
     * 访问环境拦截
     */
    protected function initialize()
    {
        if (!SystemService::instance()->checkRunMode('dev')) {
            $this->error('只允许访问本地或官方代码！');
        }
    }

    /**
     * 读取文件内容
     */
    public function get()
    {
        $filename = decode(input('encode', '0'));
        if (!ModuleService::instance()->checkAllowDownload($filename)) {
            $this->error('下载的文件不在认证规则中！');
        }
        if (file_exists($realname = $this->app->getRootPath() . $filename)) {
            $this->success('读取文件内容成功！', [
                'content' => base64_encode(file_get_contents($realname)),
            ]);
        } else {
            $this->error('读取文件内容失败！');
        }
    }

    /**
     * 读取文件列表
     */
    public function node()
    {
        $this->success('获取文件列表成功！', ModuleService::instance()->getChanges(
            json_decode($this->request->post('rules', '[]', ''), true),
            json_decode($this->request->post('ignore', '[]', ''), true)
        ));
    }

    /**
     * 获取模块信息
     */
    public function version()
    {
        $this->success('获取模块信息成功！', ModuleService::instance()->getModules());
    }

}