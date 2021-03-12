<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-03-02 16:26:31
// |----------------------------------------------------------------------
// |LastEditTime : 2021-03-12 14:09:26
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : /www.padmin.com/app/farm/controller/Shelves.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\farm\controller;

use think\admin\Controller;
use app\farm\service\FarmService;

/**
 * 店铺数据管理
 * Class Store
 * @package app\market\controller
 */
class Shelves extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'FarmShelves';

    /**
     * 上架记录
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title     = "上架记录";
        $this->store_id  = input('id',0);
        $query = $this->_query($this->table)->alias('a')->field('a.id as id, a.goods_sn, a.distribution_price, a.create_at, b.whouse_name')->join('goods_warehouse b','a.whouse_id = b.id');
        $query->where(['a.store_id' => $this->store_id])->like('a.goods_sn#goods_sn, a.create_at#create_at');
        // 列表排序并显示
        $query->order('a.create_at desc')->page();
    }
    
    /**
     * 添加店铺
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加记录';
        $this->store_id  = input('store_id',0);
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑店铺
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑记录';
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
            $this->shops   = FarmService::instance()->getList('FarmStore', 'id, shop_type, shop_name');
            $this->whouses = FarmService::instance()->getList('GoodsWarehouse', 'id, whouse_name');
        }
    }

    /**
     * 修改店铺状态
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
     * 删除店铺
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}