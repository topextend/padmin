<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 21:43:19
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-13 15:57:27
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
     * 获取商品属性
     * @return array
     */
    public function getGoodsValue(string $table, string $value): array
    {
        if ($table == 'GoodsType') {
            $map = ['status' => 1];
        } else {
            $map = ['status' => 1, 'user_id' => session('user.id')];
        }
        $query = $this->app->db->name($table);
        return $query->where($map)->order('sort desc,id desc')->column('id,'.$value);
    }
    
    /**
     * 获取商品分类
     * @return array
     */
    public function getGoodsCats(string $pid, string $value): array
    {
        $map = ['pid' => $pid];
        $query = $this->app->db->name('GoodsCat');
        return $query->where($map)->order('sort desc,id desc')->column($value);
    }
    
    /**
     * 获取JOSN分类
     * @return string
     */
    public function getJosnCats() : string
    {
        $cat = $this->getGoodsCats('0','id, cat_name');
        foreach ($cat as $key => $value)
        {
            $value['child'] = $this->getGoodsCats($cat[$key]['id'],'id, cat_name');
            foreach ($value['child'] as $kk => $vv)
            {
                $vv['child'] = $this->getGoodsCats($value['child'][$kk]['id'],'id, cat_name');
                $value['child'][$kk] = $vv;
            }
            $cats['option'][$key] = $value;
        }
        return json_encode($cats, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 最大分类级别
     * @return integer
     */
    public function getCateLevel(): int
    {
        return 3;
    }
    
    /**
     * 获取指定分类的父类ID
     * @return array
     */
    public function getParentAttr(string $cat_id) : array
    {
        $query = $this->app->db->name('GoodsCat');
        return $query->field('pid, cat_name')->where(['id' => $cat_id])->find();
    }

    /**
     * 重组选择的商品分类
     * @return string
     */
    public function selectedCats(string $cat_id) : string
    {
        $cat_3 = $this->getParentAttr($cat_id);
        $cat_2 = $this->getParentAttr($cat_3['pid']);
        $cat_1 = $this->getParentAttr($cat_2['pid']);
        return $cat_1['cat_name'] . "<font>&gt;</font>" . $cat_2['cat_name'] ."<font>&gt;</font>" . $cat_3['cat_name'];
    }
}