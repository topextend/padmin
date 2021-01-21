<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:11:48
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-21 17:01:35
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Index
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\controller\Index.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;
use app\goods\service\GoodService;

/**
 * 商品管理
 * Class Index
 * @package app\goods\controller
 */
class Index extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'Goods';
    
    /**
     * 商品列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '商品列表';
        $query = $this->_query($this->table);
        $query->equal('status');
        // 列表排序并显示
        $query->order('sort desc,goods_id desc')->page();
    }
    
    /**
     * 选择商品分类
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function category()
    {
        $this->title = '选择商品分类';        
        $this->cats  = GoodService::instance()->getJosnCats();
        $this->fetch('category');
    }
    
    /**
     * 添加商品
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '发布商品';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
    
    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            if (!empty(input('cat_id')))
            {
                $this->goods_brand    = GoodService::instance()->getGoodsValue('GoodsBrand', 'brand_name');
                $this->goods_whouses  = GoodService::instance()->getGoodsValue('GoodsWarehouse', 'whouse_name');
                $this->select_cats    = GoodService::instance()->selectedCats(input('cat_id'));
                $this->goods_attr     = GoodService::instance()->getGoodsAttrValue(input('cat_id'));
            }
        } elseif ($this->request->isPost()) {
            // 商品品牌
            if ($vo['brand_id']==0)  $this->error('请选择品牌!');
            // 商品仓库
            if ($vo['whouse_id']==0) $this->error('请选择仓库!');
            // 商品货号
            // if (empty($vo['goods_sn'])) $this->error('请输出商品货号!');
            // if (empty($vo['sku_names'])) $this->error('请选择商品SKU属性!');
            // // 商品图片
            // if (!empty($vo['goods_img']))
            // {
            //     $vo['goods_img'] = explode("|",$vo['goods_img']);
            //     foreach($vo['goods_img'] as $v)
            //     {
            //         $vo['goods_imgs'][]['images'] = $v;
            //     }
            //     $vo['goods_logo'] = $vo['goods_img'][0];
            //     unset($vo['goods_img']);
            // }
            // $vo['goods_sku_value'] = GoodService::instance()->tree2attr($vo['sku_value'],$vo['attr_id'],$vo['attr_value']);
            // unset($vo['attr_id']);
            // unset($vo['attr_value']);
            // unset($vo['sku_value']);
            dump($vo);die;
        }
    }

    /**
     * 创建商品成功后保存数据
     * @param bool $state
     */
    protected function _form_result(bool $state, array $data)
    {
        // if ($state) {
        //     $data['id'] = (input('id') ?: $this->app->db->name($this->table)->getLastInsID()) ?: 0;
        //     if (!empty($data['id']))
        //     {
        //         // 商品详情
        //         if (!empty($data['content']))
        //         {
        //             $content = ['goods_id' => $data['id'], 'content' => $data['content']];
        //             $this->app->db->name('GoodsContent')->save($content);
        //         }
        //         // 商品图片
        //         if (!empty($data['goods_imgs']))
        //         {
        //             foreach($data['goods_imgs'] as $vvv)
        //             {
        //                 $vvv['goods_id'] = $data['id'];
        //                 $this->app->db->name('GoodsImages')->save($vvv);
        //             }
        //         }

        //         $data['goods_sku_value'] = GoodService::instance()->attrAddId($data['goods_sku_value'], $data['id']);
        //         // 商品SKU
        //         foreach ($data['goods_sku_value'] as $v)
        //         {
        //             // 存入数据库
        //             $res = $this->app->db->name('GoodsSku')->save($v);
        //         }
        //         // 商品库存
        //         if ($res)
        //         {
        //             $value     = GoodService::instance()->skuValue($data['sku_names'], $data['id']);
        //             $sku_value = GoodService::instance()->att2value($value, $data['sku_price'], $data['sku_amount'], $data['id']);
        //             foreach($sku_value as $vv)
        //             {
        //                 // 存入数据库
        //                 $this->app->db->name('GoodsProducts')->save($vv);
        //             }
        //         }
        //     }
            // dump($sku_value);
            // dump($data);die;
        // }
    }

    /**
     * 编辑商品
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑商品';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
    
    /**
     * 修改商品状态
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function state()
    {
        $this->_save($this->table, $this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除商品
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}