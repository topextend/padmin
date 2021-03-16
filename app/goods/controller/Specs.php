<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2021-03-13 15:52:08
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-16 15:20:24
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : 
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/goods/controller/Specs.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;
use app\goods\service\GoodService;

/**
 * 商品属性管理
 * Class Specs
 * @package app\goods\controller
 */
class Specs extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsSpecs';
    
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
        $map = input('type_id') ? ['a.type_id'=>$this->type_id] : 1;
        $query = $this->_query($this->table);
        $query->alias('a')->field('a.id as id, a.type_id, a.spec_name, b.id as spid, b.value_name, b.spec_values, b.sort as sort');
        $query->join('goods_specs_value b','a.id = b.spec_id')->where($map);
        // 列表排序并显示
        $query->order('b.sort desc,a.id desc')->page();
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
            // $string = "白色|rgb(255,255,255),米白色|rgb(238,222,176),乳白色|rgb(255,251,240),象牙白|rgb(255,255,240)";
            // echo preg_replace('/\|rgb\([\s\S]*?\)/i','',$string);
            if (!empty(input('spec_id'))) {
                $map = ['id' => input('spec_id')];
                $data['value_name']  = GoodService::instance()->getValue('GoodsSpecsValue', $map, 'value_name');
                $data['color_code']  = GoodService::instance()->getValue('GoodsSpecsValue', $map, 'color_code');
                $data['spec_values'] = GoodService::instance()->getValue('GoodsSpecsValue', $map, 'spec_values');
            }
        } elseif ($this->request->isPost()) {
            // 对POST过来的数据进来过滤处理
            if (!empty($data['value_name']))  $postDate['value_name']  = preg_replace("/\s+/",'',$data['value_name']);
            if (!empty($data['spec_values'])) $postDate['spec_values'] = preg_replace('/(，)/' ,',' ,preg_replace('# #','',$data['spec_values']));
            // 如果不存在ID是新增规格
            if (!isset($data['id'])) {
                // 检查属性名称是否出现重复
                $where = ['spec_name' => $data['spec_name']];
                $postDate['color_code']  = $data['color_code'] ? preg_replace("/\s+/",'',$data['color_code']) : "";
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    unset($data['spec_name']);
                    unset($data['type_id']);
                } else {
                    $result = $this->app->db->name($this->table)->save(['spec_name' => $data['spec_name'], 'type_id' => $data['type_id']]);
                    if ($result) {
                        // 销毁整个数组
                        array_splice($data, 0);
                    }
                }
                // 获取spec_id
                $postDate['spec_id'] = $this->app->db->name($this->table)->where($where)->value('id');
                // 保存数据
                $this->app->db->name('GoodsSpecsValue')->save($postDate);
            } else {
                // 获取spec_id
                if (!empty($data['id']))         $postDate['spec_id']    = $data['id'];
                if (!empty($data['color_code'])) $postDate['color_code'] = preg_replace("/\s+/",'',$data['color_code']);
                // 更新数据
                $this->app->db->name('GoodsSpecsValue')->where(['id' => input('spec_id')])->update($postDate);
            }
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
     * 编辑商品属性排序
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sort()
    {
        $this->app->db->name('GoodsSpecsValue')->where(['id' => input('id')])->update([input('action')=>input('sort')]);
    }

    /**
     * 删除商品属性
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $count = $this->app->db->name('GoodsSpecsValue')->where(['spec_id' => input('id')])->count();
        if ($count > 1) {
            $this->_delete('GoodsSpecsValue','',['id' => input('spec_id')]);
        } else {
            $this->app->db->name('GoodsSpecsValue')->where(['id' => input('spec_id')])->delete();
            $this->_delete($this->table);
        }
    }
}