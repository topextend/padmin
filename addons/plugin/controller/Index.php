<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:33:03
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-14 16:49:09
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \padmin\addons\plugin\controller\Index.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\plugin\controller;

/**
 * 测试
 * Class Index
 * @package app\admin\controller
 */
class Index
{
    /**
     * 测试列表页
     * @auth true
     * @menu true
     * @login true
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function link()
    {
        echo 'hello link';
    }
}