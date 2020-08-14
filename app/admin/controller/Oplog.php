<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-08 18:51:39
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-14 16:51:52
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Oplog Controller Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \padmin\app\admin\controller\Oplog.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;

/**
 * 系统日志管理
 * Class Oplog
 * @package app\admin\controller
 */
class Oplog extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    public $table = 'SystemOplog';

    /**
     * 系统日志管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '系统日志管理';
        $query = $this->_query($this->table)->like('action,node,content,username,geoip');
        $query->dateBetween('create_at')->order('id desc')->page();
    }

    /**
     * 列表数据处理
     * @auth true
     * @param array $data
     * @throws \Exception
     */
    protected function _index_page_filter(&$data)
    {
        $ip = new \Ip2Region();
        foreach ($data as &$vo) {
            $isp = $ip->btreeSearch($vo['geoip']);
            $vo['isp'] = str_replace(['内网IP', '0', '|'], '', $isp['region'] ?? '');
        }
    }

    /**
     * 清理系统日志
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function clear()
    {
        if ($this->app->db->name($this->table)->whereRaw('1=1')->delete() !== false) {
            $this->success('日志清理成功！');
        } else {
            $this->error('日志清理失败，请稍候再试！');
        }
    }

    /**
     * 删除系统日志
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}