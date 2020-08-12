<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:25:39
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-01 21:07:04
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\addons\friendlink\FriendLink.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\friendlink;

use think\Addons;

/**
 * 插件测试
 */
class FriendLink extends Addons
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
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function frendhook($param)
    {
		// 调用钩子时候的参数信息
        // print_r($param);
		// 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        // print_r($this->getConfig());
		// 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('/info');
    }
}