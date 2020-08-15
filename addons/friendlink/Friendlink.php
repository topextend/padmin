<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:25:39
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-15 18:13:46
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Friendlink Class
// |----------------------------------------------------------------------
// |FilePath     : \padmin\addons\friendlink\Friendlink.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\friendlink;

use think\admin\Addons;

/**
 * 插件测试
 */
class Friendlink extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'friendlink',	// 插件标识
        'title' => '友情链接',	// 插件名称
        'description' => '友情链接插件',	// 插件简介
        'status' => 1,	// 状态
        'author' => 'Jarmin',
        'email'  => 'edshop@qq.com',
        'version' => 'V1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $sql = importsql($this->info['name']);
        if ($sql === false)
        {
            return false;
        }
        $content['name']        = $this->info['name'];
        $content['description'] = $this->info['description'];
        $content['is_install']  = 1;
        $content['is_config']   = 0;
        $content['status']      = 1;
        $content['mark']        = 'fileshook';
        $content['list']        = $this->info['name'];
        $result = $this->app->db->name('hooks')->save($content);
        if (!$result)
        {
            return false;
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $sql = uninstallsql($this->info['name']);
        if ($sql === false)
        {
            return false;
        }
        $result = $this->app->db->name('hooks')->where(['name' => $this->info['name']])->delete();
        if (!$result)
        {
            return false;
        }
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function friendlinkhook($param)
    {
        return $this->fetch('/info');
    }
}