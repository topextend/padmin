<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-09-25 18:02:13
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-15 16:43:53
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : 
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/farm/service/FarmService.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\farm\service;

use think\admin\extend\DataExtend;
use think\admin\Service;

/**
 * 补单数据服务
 * Class FarmService
 * @package app\farm\service
 */
class FarmService extends Service
{
    /**
     * 获取店铺数据
     * @return array
     */
    public function getList(string $table, string $value = 'shop_name'): array
    {
        $map = ['status' => 1];
        $query = $this->app->db->name($table);
        return $query->where($map)->order('sort desc,id desc')->column($value);
    }
    
    /**
     * 拼多多订单号前六位转日期
     * @return string
     */
    static function PddOrder2Date(string $value) : string
    {
        return '20'. substr($value , 0 , 2) . '-' . substr($value , 2 , 2) . '-' . substr($value , 4 , 2);
    }
}