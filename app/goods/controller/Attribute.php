<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:14:14
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-13 22:39:51
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Attribute
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\controller\Attribute.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;
use app\goods\service\GoodService;

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
        $map = input('type_id') ? ['a.type_id'=>$this->type_id] : 1;        
        $query = $this->_query($this->table)->alias('a')->field('a.*, b.type_name')->join('goods_type b','a.type_id = b.id');
        $query->dateBetween('a.create_at')->like('a.attr_name#attr_name')->where($map);
        // 列表排序并显示
        $query->equal('a.status')->page();
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
            $data['type_id']   = $data['type_id'] ?? input('type_id', '0');
            $this->goods_type  = GoodService::instance()->getGoodsValue('GoodsType','type_name');
            $this->attr_values = implode(',', $this->app->db->name('GoodsAttrValue')->where(['attr_id' => $data['attr_id']])->column('attr_value'));
        } elseif ($this->request->isPost()) {
            if (isset($data['attr_id']) && $data['attr_id'] > 0) {
                unset($data['attr_name']);
            } else {
                // 检查登录属性是否出现重复
                $where = ['attr_name' => $data['attr_name'], 'type_id' => $data['type_id']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("属性{$data['attr_name']}已经存在，请使用其它属性名称！");
                }
            }
        }
    }    
    
    /**
     * 创建属性成功后保存数据
     * @param bool $state
     */
    protected function _form_result(bool $state, array $data)
    {
        if ($state) {
            $data['attr_id'] = (input('attr_id') ?: $this->app->db->name($this->table)->getLastInsID()) ?: 0;
            if (!empty($data['attr_id']))
            {
                if (!empty($data['attr_values']))
                {
                    $data['attr_values'] = rtrim(str_replace('，', ',', str_replace(' ','',$data['attr_values'])),',');
                    $data['attr_values'] = GoodService::instance()->attrStr2Attr($data['attr_values'], $data['attr_id']);
                    foreach ($data['attr_values'] as $v)
                    {
                        //存入数据库
                        $this->app->db->name('GoodsAttrValue')->save($v);
                    }
                }
            }
        }
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
     * 修改商品属性状态
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