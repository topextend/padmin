<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2021-01-12 17:12:45
// |----------------------------------------------------------------------
// |LastEditTime : 2021-01-29 13:51:40
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Class Category
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\goods\controller\Category.php
// |----------------------------------------------------------------------
// |Copyright (c) 2021 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use app\goods\service\GoodService;

/**
 * 商品分类管理
 * Class Category
 * @package app\goods\controller
 */
class Category extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsCat';
    
    /**
     * 控制器初始化
     */
    protected function initialize()
    {
        $this->cateLevel = GoodService::instance()->getCateLevel();
    }

    /**
     * 分类列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '分类列表';
        $query = $this->_query($this->table);
        $query->like('cat_name')->equal('status')->dateBetween('create_at');
        // 列表排序并显示
        $query->order('sort desc,id asc')->page();
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', DataExtend::getArrSubIds($data, $vo['id']));
        }
        $data = DataExtend::arr2table($data);
    }
    
    /**
     * 添加分类
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加分类';
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
            $data['pid'] = intval($data['pid'] ?? input('pid', '0'));
            $data['spt'] = intval($data['spt'] ?? input('spt', '0'));
            $this->goods_type = GoodService::instance()->getGoodsValue('GoodsType', 'id, type_name');
            $cates = $this->app->db->name($this->table)->order('sort desc,id desc')->select()->toArray();
            $this->goods_cats = DataExtend::arr2table(array_merge($cates, [['id' => '0', 'pid' => '-1', 'cat_name' => '顶级分类']]));
            if (isset($data['id'])) foreach ($this->goods_cats as $key => $cat) if ($cat['id'] === $data['id']) $data = $cat;
            foreach ($this->goods_cats as $key => $cat) if ($cat['spt'] >= $this->cateLevel) {
                unset($this->goods_cats[$key]);
            }
        } elseif ($this->request->isPost()) {
            // 检查分类名称是否出现重复
            if (!isset($data['id'])) {
                $where = ['cat_name' => $data['cat_name']];
                if ($this->app->db->name($this->table)->where($where)->count() > 0) {
                    $this->error("分类{$data['cat_name']}已经存在，请使用其它分类名称！");
                }
            }
        }
    }

    /**
     * 编辑分类
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑分类';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
    
    /**
     * 修改分类状态
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
     * 删除分类
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}