<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:13:44
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-12 22:05:14
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Warehouse
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\controller\Warehouse.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;

/**
 * 商品仓库管理
 * Class Warehouse
 * @package app\goods\controller
 */
class Warehouse extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsWarehouse';

    /**
     * 仓库列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '仓库列表';
        $query = $this->_query($this->table);
        $query->like('whouse_name, whouse_user_name, create_at')->equal('status');
        // 加载对应数据
        if (session('user.id') <> '10000')
        {
            $query->where(['user_id' => session('user.id')]);
        }
        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
    }

    /**
     * 添加仓库
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加仓库';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑仓库
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑仓库';
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
        if ($this->request->isPost()) {
            if (isset($data['id']) && $data['id'] > 0) {
                unset($data['whouse_name']);
            } else {
                // 检查登录属性是否出现重复
                $where = ['whouse_name' => $data['whouse_name']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("仓库{$data['whouse_name']}已经存在，请使用其它仓库名称！");
                }
            }
        }
    }
    
    /**
     * 修改仓库状态
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
     * 删除仓库
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}