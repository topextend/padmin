<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-08 18:58:30
// |----------------------------------------------------------------------
// |LastEditTime : 2020-07-08 18:59:06
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Auth Controller Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \ladmin\app\admin\controller\Auth.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\service\AdminService;

/**
 * 系统权限管理
 * Class Auth
 * @package app\admin\controller
 */
class Auth extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    public $table = 'SystemAuth';

    /**
     * 系统权限管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '系统权限管理';
        $query = $this->_query($this->table)->dateBetween('create_at');
        $query->like('title,desc')->equal('status')->order('sort desc,id desc')->page();
    }

    /**
     * 添加系统权限
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
     * 编辑系统权限
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
     * 修改权限状态
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
     * 权限配置节点
     * @auth true
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function apply()
    {
        $map = $this->_vali(['auth.require#id' => '权限ID不能为空！']);
        if (input('action') === 'get') {
            $checkeds = $this->app->db->name('SystemAuthNode')->where($map)->column('node');
            $this->success('获取权限节点成功！', AdminService::instance()->clearCache()->getTree($checkeds));
        } elseif (input('action') === 'save') {
            [$post, $data] = [$this->request->post(), []];
            foreach ($post['nodes'] ?? [] as $node) {
                $data[] = ['auth' => $map['auth'], 'node' => $node];
            }
            $this->app->db->name('SystemAuthNode')->where($map)->delete();
            $this->app->db->name('SystemAuthNode')->insertAll($data);
            $this->success('权限授权修改成功！', 'javascript:history.back()');
        } else {
            $this->title = '权限配置节点';
            $this->_form($this->table, 'apply');
        }
    }

    /**
     * 删除系统权限
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }

    /**
     * 删除结果处理
     * @param boolean $result
     * @throws \think\db\exception\DbException
     */
    protected function _remove_delete_result($result)
    {
        if ($result) {
            $map = $this->_vali(['auth.require#id' => '权限ID不能为空！']);
            $this->app->db->name('SystemAuthNode')->where($map)->delete();
            $this->success("权限删除成功！");
        } else {
            $this->error("权限删除失败，请稍候再试！");
        }
    }
}