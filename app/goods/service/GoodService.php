<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 21:43:19
// |----------------------------------------------------------------------
// |LastEditTime : 2021-02-27 23:38:47
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
        if ($table == 'GoodsType' || $table == 'GoodsSpecs') {
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
        $query = $this->app->db->name('GoodsAttr')->where(['type_id'=>$type_id])->column('id, attr_type, attr_name, attr_notice, attr_values');
        if ($query) {
            foreach ($query as $key => $value) {
                $value['attr_values'] = explode(',', $value['attr_values']);
                $attrs[$key] = $value;
            }
            return $attrs;
        } else {
            return [];
        }
    }

    /**
     * 图片数组转换
     * @return array
     */
    public function attrToImageValue(string $str) : array
    {
        $images = explode('|', $str);
        foreach ($images as $img)
        {
            $imgs[] = ['images' => $img];
        }
        return $imgs;
    }

    /**
     * 多数组转换成SKU属性数组
     * @return array
     */
    public function arrayToSkuValue(array $attr1, array $attr2, array $attr3, array $attr4) : array
    {
        $attr5 = array_merge($attr3, $attr1);
        $attr6 = array_merge($attr4, $attr2);
        foreach ($attr5 as $k => $v)
        {
            $attr7[] = ['attr_id'=> $v, 'attr_value' => $attr6[$k]];
        }
        return $attr7;
    }

    /**
     * 多数组转换成库存数组
     * @return array
     */
    public function arrayToProcudtValue(array $attr1, array $attr2, array $attr3, array $attr4, array $attr5) : array
    {
        foreach($attr1 as $k => $v)
        {
            $attr[] = ['goods_attr'=> $v, 'market_price' => $attr2[$k], 'shop_price' => $attr3[$k], 'stock_price' => $attr4[$k], 'goods_amount' => $attr5[$k]];
        }
        return $attr;
    }

    /**
     * 获取商品SKU属性名称
     */
    public function getGoodsAttrName(string $goods_id) : array
    {
        $attr = $this->app->db->name('GoodsSku')->where(['goods_id' => $goods_id])->column('attr_value');
        return $attr;
    }
    
    /**
     * 获取商品图片
     */
    public function getGoodsImages(string $goods_id) : string
    {
        $attr = $this->app->db->name('GoodsImages')->where(['goods_id' => $goods_id])->column('images');
        return implode('|', $attr);
    }

    /**
     * 获取商品详情
     */
    public function getGoodsContent(string $goods_id) : string
    {
        $str = $this->app->db->name('GoodsContent')->where(['goods_id' => $goods_id])->value('content');
        return $str ?: '';
    }
    
    /**
     * 获取商品库存信息
     */
    public function getGoodsAttrPrice(string $goods_id) : string 
    {
        $attr = $this->app->db->name('GoodsProducts')->where(['goods_id' => $goods_id])->column('market_price,shop_price,stock_price,goods_amount');
        return json_encode($attr);
    }
}