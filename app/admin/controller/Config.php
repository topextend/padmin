<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-07-08 18:55:13
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:25:33
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : Config Controller Of Admin
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/admin/controller/Config.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\service\AdminService;
use think\admin\service\ModuleService;
use think\admin\service\SystemService;
use think\admin\storage\AliossStorage;
use think\admin\storage\QiniuStorage;
use think\admin\storage\TxcosStorage;

/**
 * 系统参数配置
 * Class Config
 * @package app\admin\controller
 */
class Config extends Controller
{
    /**
     * 系统参数配置
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '系统参数配置';
        $this->isSuper = AdminService::instance()->isSuper();
        $this->version = ModuleService::instance()->getVersion();
        $this->fetch();
    }

    /**
     * 修改系统参数
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function system()
    {
        $this->_applyFormToken();
        if ($this->request->isGet()) {
            $this->title = '修改系统参数';
            $this->fetch();
        } else {
            if ($xpath = $this->request->post('xpath')) {
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $xpath)) {
                    $this->error('后台入口名称需要是由英文字母开头！');
                }
                if ($xpath !== 'admin' && file_exists($this->app->getBasePath() . $xpath)) {
                    $this->error("后台入口名称{$xpath}已经存在应用！");
                }
                SystemService::instance()->setRuntime(null, [$xpath => 'admin']);
            }
            foreach ($this->request->post() as $name => $value) sysconf($name, $value);
            sysoplog('系统配置管理', "修改系统参数成功");
            $this->success('修改系统参数成功！', sysuri("{$xpath}/index/index") . '#' . url("{$xpath}/config/index"));
        }
    }

    /**
     * 修改文件存储
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function storage()
    {
        $this->_applyFormToken();
        if ($this->request->isGet()) {
            $this->type = input('type', 'local');
            if ($this->type === 'alioss') $this->points = AliossStorage::region();
            elseif ($this->type === 'qiniu') $this->points = QiniuStorage::region();
            elseif ($this->type === 'txcos') $this->points = TxcosStorage::region();
            $this->fetch("storage-{$this->type}");
        } else {
            $post = $this->request->post();
            if (!empty($post['storage']['allow_exts'])) {
                $exts = array_unique(explode(',', strtolower($post['storage']['allow_exts'])));
                if (sort($exts) && in_array('php', $exts)) $this->error('禁止上传可执行的文件！');
                $post['storage']['allow_exts'] = join(',', $exts);
            }
            foreach ($post as $name => $value) sysconf($name, $value);
            sysoplog('系统配置管理', "修改系统存储参数");
            $this->success('修改文件存储成功！');
        }
    }
}