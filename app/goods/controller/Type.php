<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2021-03-13 13:54:35
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-15 16:44:43
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : 
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/goods/controller/Type.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;

/**
 * 商品类型管理
 * Class Type
 * @package app\goods\controller
 */
class Type extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsType';

    /**
     * 商品类型列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '商品类型列表';
        $query = $this->_query($this->table);
        $query->like('type_name, create_at')->equal('status');
        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
    }

    /**
     * 添加商品类型
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加商品类型';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品类型
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑商品类型';
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
            // 检查类型名称是否出现重复
            if (!isset($data['id'])) {
                $where = ['type_name' => $data['type_name']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("类型{$data['type_name']}已经存在，请使用其它类型名称！");
                }
            }
        }
    }
    
    /**
     * 修改商品类型状态
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
     * 删除商品类型
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}