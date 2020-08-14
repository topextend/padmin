<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 23:11:20
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-14 16:52:00
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
     * @return void
     */
    public function getAddonsList()
    {
        $object_list = get_addons_list();
        $list = [];
        foreach ($object_list as $object) {
            $addon_info = $object;
            $info = $this->app->db->name(Config::get('addons.database.table'))->where(['name' => $addon_info['name']])->find();
            $addon_info['is_install'] = is_null($info['is_install']) ? 0 : $info['is_install'];
            $addon_info['is_config'] = is_null($info['is_config']) ? 0 : $info['is_config'];
            $list[] = $addon_info;
        }
        return $list;
    }

    /**
     * 插件安装
     * @auth true
     */
    public function install($name = "")
    {
        // 过滤插件名称
        $plug_name = trim($name);
        if ($plug_name == '') $this->error('插件不存在！');
        // 执行插件安装
        $result = get_addons_instance($plug_name)->install();
        if ($result === false)
        {
            $this->error('插件安装失败');
        }
        $this->success('插件安装成功');
    }
    
    /**
     * 插件卸载
     * @auth true
     */
    public function uninstall($name = "")
    {
        // 过滤插件名称
        $plug_name = trim($name);
        if ($plug_name == '') $this->error('插件不存在！');
        // 执行插件卸载
        $result = get_addons_instance($plug_name)->uninstall();
        if ($result === false)
        {
            $this->error('插件卸载失败');
        }
        $this->success('插件卸载成功');
    }
}