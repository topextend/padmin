<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-08-14 02:45:16
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:37:37
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : Modelu Class
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/admin/controller/Module.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------

namespace app\admin\controller;

use think\admin\Controller;
use think\admin\service\ModuleService;

/**
 * 系统模块管理
 * Class Module
 * @package app\admin\controller
 */
class Module extends Controller
{
    /**
     * 系统模块管理
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '系统模块管理';
        $this->modules = ModuleService::instance()->change();
        $this->fetch();
    }

    /**
     * 安装更新模块
     * @auth true
     */
    public function install()
    {
        $data = $this->_vali(['name.require' => '模块名称不能为空！']);
        [$state, $message] = ModuleService::instance()->install($data['name']);
        $state ? $this->success($message) : $this->error($message);
    }

    /**
     * 查看模块更新
     * @auth true
     */
    public function change()
    {
        $data = $this->_vali(['name.require' => '模块名称不能为空！']);
        $modules = ModuleService::instance()->online();
        $locals = ModuleService::instance()->getModules();
        if (isset($modules[$data['name']])) {
            $this->module = $modules[$data['name']];
            $this->current = $locals[$data['name']] ?? [];
            $pattern = "|^(\d{4})\.(\d{2})\.(\d{2})\.(\d+)$|";
            $this->module['change'] = array_reverse($this->module['change']);
            foreach ($this->module['change'] as $version => &$change) {
                $change = ['content' => $change, 'version' => preg_replace($pattern, '$1年$2月$3日 第 $4 次更新', $version)];
            }
            $this->fetch();
        } else {
            $this->error('未查询到模块更新记录！');
        }
    }
}