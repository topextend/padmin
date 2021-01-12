<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 21:43:19
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-12 21:56:16
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\service\GoodService.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\service;

use think\admin\Service;

/**
 * 商品数据服务
 * Class GoodService
 * @package app\goods\service
 */
class GoodService extends Service
{
    /**
     * 获取店铺类型
     * @return array
     */
    public function getGoodsCatsType(string $table, string $value): array
    {
        $map = ['status' => 1];
        $query = $this->app->db->name($table);
        return $query->where($map)->order('sort desc,id desc')->column('id,'.$value);
    }
    
    /**
     * 最大分类级别
     * @return integer
     */
    public function getCateLevel(): int
    {
        return 3;
    }
}