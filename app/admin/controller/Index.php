<?php
// -----------------------------------------------------------------------
// |@Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Date         : 2020-07-08 16:36:17
// |----------------------------------------------------------------------
// |@LastEditTime : 2021-03-13 13:30:37
// |----------------------------------------------------------------------
// |@LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |@Description  : Index Controller Of Admin
// |----------------------------------------------------------------------
// |@FilePath     : /www.padmin.com/app/admin/controller/Index.php
// |----------------------------------------------------------------------
// |@Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\service\AdminService;
use think\admin\service\MenuService;

/**
 * 后台界面入口
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{
    /**
     * 显示后台首页
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        /*! 根据运行模式刷新权限 */
        AdminService::instance()->apply($this->app->isDebug());
        /*! 读取当前用户权限菜单树 */
        $this->menus = MenuService::instance()->getTree();
        /*! 判断当前用户的登录状态 */
        $this->login = AdminService::instance()->isLogin();
        /*! 菜单为空且未登录跳转到登录页 */
        if (empty($this->menus) && empty($this->login)) {
            $this->redirect(sysuri('admin/login/index'));
        } else {
            $this->title = '系统管理后台';
            $this->fetch();
        }
    }

    /**
     * 修改用户资料
     * @login true
     * @param integer $id 会员ID
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info($id = 0)
    {
        $this->_applyFormToken();
        if (AdminService::instance()->getUserId() === intval($id)) {
            $this->_form('SystemUser', 'admin@user/form', 'id', [], ['id' => $id]);
        } else {
            $this->error('只能修改自己的资料！');
        }
    }

    /**
     * 修改当前用户密码
     * @login true
     * @param integer $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pass($id = 0)
    {
        $this->_applyFormToken();
        if (AdminService::instance()->getUserId() !== intval($id)) {
            $this->error('只能修改当前用户的密码！');
        }
        if ($this->app->request->isGet()) {
            $this->verify = true;
            $this->_form('SystemUser', 'admin@user/pass', 'id', [], ['id' => $id]);
        } else {
            $data = $this->_vali([
                'password.require'            => '登录密码不能为空！',
                'repassword.require'          => '重复密码不能为空！',
                'oldpassword.require'         => '旧的密码不能为空！',
                'password.confirm:repassword' => '两次输入的密码不一致！',
            ]);
            $user = $this->app->db->name('SystemUser')->where(['id' => $id])->find();
            if (md5($data['oldpassword']) !== $user['password']) {
                $this->error('旧密码验证失败，请重新输入！');
            }
            if (data_save('SystemUser', ['id' => $user['id'], 'password' => md5($data['password'])])) {
                sysoplog('系统用户管理', "修改用户[{$user['id']}]密码成功");
                $this->success('密码修改成功，下次请使用新密码登录！', '');
            } else {
                $this->error('密码修改失败，请稍候再试！');
            }
        }
    }
}