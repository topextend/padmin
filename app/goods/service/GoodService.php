<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 21:43:19
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-19 21:20:33
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

    /**
     * 重组属性数组
     * @return arrary
     */
    public function attrStr2Attr(string $str, string $id) : array 
    {
        $attr = explode(',', $str);
        foreach ($attr as $key =>$value)
        {
            $attrs[$key]['attr_id']    = $id;
            $attrs[$key]['attr_value'] = $value;
        }
        return $attrs;
    }    

    /**
     * 获取指定分类的属性类型
     * @return string
     */
    public function getCatsTypeID(string $cat_id) : string
    {
        $query = $this->app->db->name('GoodsCat');
        return $query->where(['id' => $cat_id])->value('type_id');
    }
    
    /**
     * 获取店铺类型属性
     * @return array
     */
    public function getGoodsAttrValue(string $cat_id): array
    {
        $type_id = $this->getCatsTypeID($cat_id);
        $query = $this->app->db->name('GoodsAttr')->where(['type_id'=>$type_id])->column('id, attr_type, attr_name, attr_values');
        foreach ($query as $key => $value) {
            $value['attr_values'] = explode(',',$value['attr_values']);
            $attrs[$key] = $value;
        }
        return $attrs;
    }

    /**
     * 商品属性转换
     * @return array
     */
    public function tree2attr(array $attr1, array $attr2, array $attr3) : array
    {
        foreach ($attr1 as $k1 =>$v1)
        {
            $attr = explode('_', $v1);
            $attr4[$k1]['attr_id'] = $attr[0];
            $attr4[$k1]['attr_value'] = $attr[1];
        }
        foreach ($attr2 as $k2 => $v2)
        {
            $attr5[] = ['attr_id'=> $v2, 'attr_value' => $attr3[$k2]];
        }
        return array_merge($attr4, $attr5);
    }

    /**
     * 商品属性组合商品ID
     * @return array
     */
    public function attrAddId(array $attr, string $goods_id)
    {
        foreach ($attr as $key =>$value)
        {
            foreach ($value as $v)
            {                
                $value['goods_id'] = $goods_id;
            }
            $attr[$key] = $value;
        }
        return $attr;
    }
    
    /**
     * 获取属性ID
     * @return array
     */
    public function attr2ID(array $attr, string $goods_id) : array
    {
        foreach ($attr as $v)
        {
            $s = explode('_', $v);
            foreach($s as $k =>$vv)
            {
                $value = $this->app->db->name('GoodsSku')->where(['goods_id' => $goods_id, 'attr_value'=>$vv])->column('goods_attr_id');  
                $newattr[$k][] = $value;
            }
        }
        return $newattr;
    }

    /**
     * 重新组合SKU
     * @return array
     */
    public function skuValue(array $attr, string $goods_id) : array
    {
        $sku_value = $this->attr2ID($attr, $goods_id);
        foreach ($sku_value[0] as $k => $v)
        {
            foreach($v as $vv)
            {
                $value[]['attr_value'] = $vv . '|' . $sku_value[1][$k][0];
            }
        }
        return $value;
    }

    /**
     * 组合SKU
     * @return array
     */
    public function att2value($attr1, $attr2, $attr3, $goods_id) : array
    {
        foreach($attr2 as $k => $v)
        {
            $attr[] = ['goods_attr'=> $attr1[$k]['attr_value'], 'goods_price' => $v, 'goods_amount' => $attr3[$k], 'goods_id' => $goods_id];
        }
        return $attr;
    }
}