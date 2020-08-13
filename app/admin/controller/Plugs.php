<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 23:11:20
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-01 17:55:36
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Addons Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \padmin\app\admin\controller\Plugs.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\facade\Config;
use think\admin\Controller;
/**
 * 插件配置管理
 * Class Addons
 * @package app\admin\controller
 */
class Plugs extends Controller
{
    // 插件实例
    protected static $instance = [];

    /**
     * 插件列表管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title  = '插件列表';
        $this->assign('addons_list', $this->getAddonsList());
        return $this->fetch();
    }

    /**
     * 获取插件列表
     *
     * @return void
     */
    public function getAddonsList()
    {
        $object_list = get_addon_list();
        $list = [];
        foreach ($object_list as $object) {
            $addon_info = $object;
            $info = $this->app->db->name(Config::get('addons.database.table'))->where(['name' => $addon_info['name']]);
            $addon_info['is_install'] = empty($info) ? 0 : 1;
            $list[] = $addon_info;
        }
        return $list;
    }
}