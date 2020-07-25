<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-08 18:49:12
// |----------------------------------------------------------------------
// |LastEditTime : 2020-07-08 18:55:46
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Menu Controller Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \ladmin\app\admin\controller\Menu.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\admin\service\AdminService;
use think\admin\service\MenuService;

/**
 * 系统菜单管理
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends Controller
{
    /**
     * 当前操作数据库
     * @var string
     */
    protected $table = 'SystemMenu';

    /**
     * 系统菜单管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '系统菜单管理';
        $this->_page($this->table, false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(&$data)
    {
        foreach ($data as &$vo) {
            if ($vo['url'] !== '#') {
                $vo['url'] = trim(url($vo['url']) . (empty($vo['params']) ? '' : "?{$vo['params']}"), '/\\');
            }
            $vo['ids'] = join(',', DataExtend::getArrSubIds($data, $vo['id']));
        }
        $data = DataExtend::arr2table($data);
    }

    /**
     * 添加系统菜单
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑系统菜单
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 清理权限节点
            AdminService::instance()->clearCache();
            // 选择自己的上级菜单
            $vo['pid'] = $vo['pid'] ?? input('pid', '0');
            // 读取系统功能节点
            $this->nodes = MenuService::instance()->getList();
            // 列出可选上级菜单
            $menus = $this->app->db->name($this->table)->order('sort desc,id asc')->column('id,pid,icon,url,title,params', 'id');
            $this->menus = DataExtend::arr2table(array_merge($menus, [['id' => '0', 'pid' => '-1', 'url' => '#', 'title' => '顶部菜单']]));
            if (isset($vo['id'])) foreach ($this->menus as $key => $menu) if ($menu['id'] === $vo['id']) $vo = $menu;
            foreach ($this->menus as $key => $menu) {
                if ($menu['spt'] >= 3 || $menu['url'] !== '#') unset($this->menus[$key]);
                if (isset($vo['spt']) && $vo['spt'] <= $menu['spt']) unset($this->menus[$key]);
            }
        }
    }

    /**
     * 修改菜单状态
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function state()
    {
        $this->_applyFormToken();
        $this->_save($this->table, $this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除系统菜单
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}