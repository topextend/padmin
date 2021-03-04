<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:11:48
// |----------------------------------------------------------------------
// |LastEditTime : 2021-03-03 21:14:31
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
    
    public function test()
    {
        $arr1 = ['白色','黑色'];
        $arr2 = ['36','37','38'];
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                $result[]  = $item1 . "_" . $item2;
            }
        }
        // $res = array_merge_recursive($attr1, $attr2);
        dump($result);
        // dump(array_merge($attr_value,$attrs));
    }
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
     * @param array $data
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(&$data)
    {
        if ($this->request->isGet()) {
            $cat_id = input('cat_id') ?: $this->app->db->name($this->table)->where(['goods_id'=> input('goods_id')])->value('cat_id');
            $this->goods_brand    = GoodService::instance()->getGoodsValue('GoodsBrand', 'brand_name');
            $this->goods_whouses  = GoodService::instance()->getGoodsValue('GoodsWarehouse', 'whouse_name');
            $this->select_cats    = GoodService::instance()->selectedCats($cat_id);
            $this->goods_attr     = GoodService::instance()->getGoodsAttrValue($cat_id) ?: $this->error('请先配置类型属性');
            // dump($this->goods_attr);
            // $this->attr_price     = input('goods_id') ? GoodService::instance()->getGoodsAttrPrice(input('goods_id')) : 0;
            if (!empty(input('goods_id'))) {
                $this->attr_value = GoodService::instance()->getGoodsAttrName(input('goods_id'));
                $this->goods_imgs = GoodService::instance()->getGoodsImages(input('goods_id'));
                $this->goods_content = GoodService::instance()->getGoodsContent(input('goods_id'));
            }
        } elseif ($this->request->isPost()) {
            // 检查商品货号是否出现重复
            if (!isset($data['goods_id'])) {
                $where = ['goods_sn' => $data['goods_sn'], 'user_id' => session('user.id')];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("货号 {$data['goods_sn']} 已经存在，请使用其它货号名称！");
                }
            }
            // 商品品牌
            if ($data['brand_id']==0)  $this->error('请选择品牌!');
            // 商品仓库
            if ($data['whouse_id']==0) $this->error('请选择仓库!');
            // 商品图片
            if (!empty($data['goods_img']))
            {
                $data['goods_img']  = GoodService::instance()->attrToImageValue($data['goods_img']);
                $data['goods_logo'] = $data['goods_img'][0]['images'];
            } else {
                $this->error('请上传轮播图!');
            }
            // 商品规格
            if (empty($data['spec_name']) || empty($data['attr_id'])) $this->error('未选择规格属性!');
            // 商品详情
            if (empty($data['content'])) $this->error('请填写商品详情!');
            // 规格属性重组
            $data['sku_value'] = GoodService::instance()->arrayToSkuValue($data['spec_name'], $data['spec_size'], $data['spec_name_note'], $data['spec_size_note'], $data['spec_img'], $data['attr_id'], $data['attr_value'], $data['spec_id']);
            $data['product_value'] = GoodService::instance()->arrayToProcudtValue($data['spec_name'], $data['spec_size'], $data['shop_price'], $data['stock_price'], $data['goods_amount']);
            unset($data['image']);
            unset($data['attr_id']);
            unset($data['attr_value']);
            unset($data['spec_id']);
            unset($data['spec_name']);
            unset($data['spec_size']);
            unset($data['spec_name_note']);
            unset($data['spec_size_note']);
            unset($data['spec_img']);
            unset($data['market_price']);
            unset($data['shop_price']);
            unset($data['stock_price']);
            unset($data['goods_amount']);
        }
    }

    /**
     * 创建商品成功后保存数据
     * @param bool $state
     */
    protected function _form_result(bool $state, array $data)
    {
        if ($state) {
            $data['goods_id'] = $this->app->db->name($this->table)->getLastInsID() ?: $data['goods_id'];
            $map = ['goods_id' => $data['goods_id']];
            // 商品详情
            if (!empty($data['content']))
            {
                $content = ['goods_id' => $data['goods_id'], 'content' => $data['content']];
                if ($this->app->db->name('GoodsContent')->where($map)->count() > 0) {
                    $this->app->db->name('GoodsContent')->where($map)->update($content);
                } else {
                    $this->app->db->name('GoodsContent')->save($content);
                }
            }
            // 商品图片
            if (!empty($data['goods_img']))
            {
                $this->app->db->name('GoodsImages')->where($map)->delete();
                foreach($data['goods_img'] as $imgs)
                {
                    $imgs['goods_id'] = $data['goods_id'];
                    $this->app->db->name('GoodsImages')->save($imgs);
                }
            }
            // 商品SKU
            if (!empty($data['sku_value']))
            {
                $this->app->db->name('GoodsSku')->where($map)->delete();
                foreach ($data['sku_value'] as $v)
                {
                    $v['goods_id'] = $data['goods_id'];
                    // 存入数据库
                    $this->app->db->name('GoodsSku')->save($v);
                }
            }
            // 商品库存
            $this->app->db->name('GoodsProducts')->where($map)->delete();
            foreach($data['product_value'] as $vv)
            {
                $vv['goods_id'] = $data['goods_id'];
                // 存入数据库
                $this->app->db->name('GoodsProducts')->save($vv);
            }
        }
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