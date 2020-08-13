<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:33:03
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-02 02:25:18
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\addons\friendlink\controller\Index.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\friendlink\controller;

use think\admin\Controller;
use think\admin\service\AdminService;
use think\admin\service\MenuService;

/**
 * 友情链接
 * @auth true
 * @menu true
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    public $table = 'FriendLink';

    /**
     * 绑定模板路径
     */
    public $template_index = '../addons/friendlink/view/index/index.html';
    public $template_form  = '../addons/friendlink/view/index/form.html';
    
    /**
     * 友情链接列表页
     * @auth true
     * @menu true
     * @login true
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->login = AdminService::instance()->apply(true)->isLogin();
        $this->menus = MenuService::instance()->getTree();
        if (empty($this->menus) && empty($this->login)) {
            $this->redirect(sysuri('admin/login/index'));
        } else {
            $this->title = '友情链接';
            $query = $this->_query($this->table)->dateBetween('create_at');
            $query->equal('status')->order('sort desc,id desc')->page(true, true, false, 0, $this->template_index);
        }
    }

    /**
     * 增加友情链接
     * @auth true
     * @return void
     */
    public function add()
    {
        $this->_applyFormToken();
        $this->_form($this->table, $this->template_form);
    }

    /**
     * 删除友情链接
     * @auth true
     * @return void
     */
    public function remove()
    {
        //
    }
}