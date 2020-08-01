<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 23:11:20
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-01 12:33:39
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Addons Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\admin\controller\Plugs.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\facade\Config;
use think\Container;

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

    public function getAddonsList()
    {
        $object_list = $this->getUninstalledList();
        $list = [];
        foreach ($object_list as $object) {
            $addon_info = $object->info;
            $info = $this->app->db->name('hooks')->where(['name' => $addon_info['name']]);
            $addon_info['is_install'] = empty($info) ? 0 : 1;
            $list[] = $addon_info;
        }
        
        return $list;
    }
    /**
     * 获取插件
     */
    public function getUninstalledList()
    {
        $dir_list = get_dir(Config::get('addons.path'));
        foreach ($dir_list as $key => $v) {
            $class = "\\" . Config::get('addons.dir') . "\\" . $v . "\\" .ucfirst($v);
            if (!isset(self::$instance[$class])) {
                self::$instance[$class] = new $class(app());
            }
        }
        return self::$instance;
    }
}