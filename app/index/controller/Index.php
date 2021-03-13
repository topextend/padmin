<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-07-08 16:50:35
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:22:33
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : Index of Index
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/index/controller/Index.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\index\controller;

use think\admin\Controller;
/**
 * index模块首页
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{
    public function index()
    {
        $this->redirect(sysuri('admin/login/index'));
    }
}
