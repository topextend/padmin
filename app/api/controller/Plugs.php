<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-09 15:22:12
// |----------------------------------------------------------------------
// |LastEditTime : 2020-07-09 16:51:57
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Plugs Controller Of Api
// |----------------------------------------------------------------------
// |FilePath     : \www.ladmin.com\app\api\controller\Plugs.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\api\controller;

use think\admin\Controller;
use think\admin\service\AdminService;
use think\admin\service\SystemService;
use think\exception\HttpResponseException;

/**
 * 通用插件管理
 * Class Plugs
 * @package app\admin\controller\api
 */
class Plugs extends Controller
{
    /**
     * 系统图标选择器
     */
    public function icon()
    {
        $this->title = '图标选择器';
        $this->field = input('field', 'icon');
        $this->fetch(realpath(__DIR__ . '../view/plugs/icon.html'));
    }

    /**
     * 网站压缩发布
     * @login true
     */
    public function push()
    {
        try {
            if (AdminService::instance()->isSuper()) {
                SystemService::instance()->pushRuntime();
                $this->success('网站缓存加速成功！');
            } else {
                $this->error('只有超级管理员才能操作！');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 清理运行缓存
     * @login true
     */
    public function clear()
    {
        try {
            if (AdminService::instance()->isSuper()) {
                SystemService::instance()->clearRuntime();
                $this->success('清理网站缓存成功！');
            } else {
                $this->error('只有超级管理员才能操作！');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 当前运行模式
     * @login true
     */
    public function debug()
    {
        if (AdminService::instance()->isSuper()) {
            if (input('state')) {
                SystemService::instance()->productMode(true);
                $this->success('已切换为生产模式！');
            } else {
                SystemService::instance()->productMode(false);
                $this->success('已切换为开发模式！');
            }
        } else {
            $this->error('只有超级管理员才能操作！');
        }
    }

    /**
     * 优化数据库
     * @login true
     */
    public function optimize()
    {
        if (AdminService::instance()->isSuper()) {
            $this->_queue('优化数据库所有数据表', 'xadmin:database optimize', 0, [], 0, 0);
        } else {
            $this->error('只有超级管理员才能操作！');
        }
    }
}