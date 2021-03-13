<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2021-02-27 16:41:02
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:22:45
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : 
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/goods/controller/Svalue.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;
use app\goods\service\GoodService;

/**
 * 商品规格属性管理
 * Class Type
 * @package app\goods\controller
 */
class Svalue extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsSpecsValue';

    /**
     * 商品规格属性列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '规格属性列表';
        $this->spec_id = input('spec_id', 0);
        $query = $this->_query($this->table);
        $query->where(['spec_id' => $this->spec_id])->equal('status');
        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
    }

    /**
     * 添加规格属性
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加规格属性';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑规格属性
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑规格属性';
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
            $data['spec_id'] = $data['spec_id'] ?? input('spec_id', '0');
            $this->goods_spec = GoodService::instance()->getGoodsValue('GoodsSpecs','spec_name');
        } elseif ($this->request->isPost()) {
            // 检查类型名称是否出现重复
            if (!isset($data['id'])) {
                $where = ['name' => $data['name']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("类型{$data['name']}已经存在，请使用其它属性名称！");
                }
            }
            // 正则过滤全角逗号及空格
            $data['values'] = preg_replace("/(，)/" ,',' ,preg_replace('# #','',$data['values']));
        }
    }
    
    /**
     * 修改规格属性状态
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
     * 删除规格属性
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}