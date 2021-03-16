<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2021-03-13 14:13:02
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-15 16:42:16
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : 
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/goods/controller/Attribute.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;

/**
 * 商品属性管理
 * Class Attribute
 * @package app\goods\controller
 */
class Attribute extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsAttr';
    
    /**
     * 商品属性列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '商品属性列表';
        $this->type_id = input('type_id', 0);
        $map = input('type_id') ? ['type_id'=>$this->type_id] : 1;
        $query = $this->_query($this->table)->where($map);
        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
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
        } elseif ($this->request->isPost()) {
            if (!isset($data['id'])) {
                // 检查属性名称是否出现重复
                $where = ['attr_name' => $data['attr_name']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("属性{$data['attr_name']}已经存在，请使用其它属性名称！");
                }
            }
            // 正则过滤全角逗号及空格
            $data['attr_values'] = preg_replace("/(，)/" ,',' ,preg_replace('# #','',$data['attr_values']));
        }
    }
    
    /**
     * 添加商品属性
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加商品属性';
        $this->type_id = input('type_id', 0);
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品属性
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑商品属性';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 删除商品属性
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}