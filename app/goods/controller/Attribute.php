<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:14:14
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-13 08:31:00
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Attribute
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\controller\Attribute.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;

/**
 * 商品属性管理
 * Class Attribute
 * @package app\goods\controller
 */
class Attribute extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsAttr';
}