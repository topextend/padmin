<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-08 18:53:31
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-14 16:52:08
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Queue Controller Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \padmin\app\admin\controller\Queue.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\service\AdminService;
use think\admin\service\ProcessService;
use think\admin\service\QueueService;
use think\exception\HttpResponseException;

/**
 * 系统任务管理
 * Class Queue
 * @package app\admin\controller
 */
class Queue extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'SystemQueue';

    /**
     * 系统任务管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        // 检查进程状态
        if (AdminService::instance()->isSuper()) try {
            $this->process = ProcessService::instance();
            if ($this->process->iswin() || empty($_SERVER['USER'])) {
                $this->command = $this->process->think('xadmin:queue start');
            } else {
                $this->command = "sudo -u {$_SERVER['USER']} {$this->process->think('xadmin:queue start')}";
            }
            $this->message = $this->app->console->call('xadmin:queue', ['status'])->fetch();
            $this->listen = preg_match('/process.*?\d+.*?running/', $this->message, $attr);
        } catch (\Exception $exception) {
            $this->listen = false;
            $this->message = $exception->getMessage();
        }
        // 任务状态统计
        $this->total = ['dos' => 0, 'pre' => 0, 'oks' => 0, 'ers' => 0];
        $query = $this->app->db->name($this->table)->field('status,count(1) count');
        foreach ($query->group('status')->select()->toArray() as $item) {
            if ($item['status'] === 1) $this->total['pre'] = $item['count'];
            if ($item['status'] === 2) $this->total['dos'] = $item['count'];
            if ($item['status'] === 3) $this->total['oks'] = $item['count'];
            if ($item['status'] === 4) $this->total['ers'] = $item['count'];
        }
        $this->title = '系统任务管理';
        $this->iswin = ProcessService::instance()->iswin();
        // 任务列表查询及分页
        $query = $this->_query($this->table)->dateBetween('create_at')->timeBetween('enter_time,exec_time');
        $query->like('code,title,command')->equal('status')->order('loops_time desc,id desc')->page();
    }

    /**
     * 重启系统任务
     * @auth true
     */
    public function redo()
    {
        try {
            $data = $this->_vali(['code.require' => '任务编号不能为空！']);
            $queue = QueueService::instance()->initialize($data['code'])->reset();
            $queue->progress(1, '>>> 任务重置成功 <<<', 0.00);
            $this->success('任务重置成功！', $queue->code);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 重启任务结果处理
     * @param boolean $state
     */
    protected function _redo_save_result($state)
    {
        if ($state) {
            $this->success('重启任务成功！');
        }
    }

    /**
     * WIN创建监听进程
     * @auth true
     */
    public function start()
    {
        try {
            $message = nl2br($this->app->console->call('xadmin:queue', ['start'])->fetch());
            if (stripos($message, 'daemons started successfully for pid')) {
                $this->success('任务监听主进程启动成功！');
            } elseif (stripos($message, 'daemons already exist for pid')) {
                $this->success('任务监听主进程已经存在！');
            } else {
                $this->error($message);
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * WIN停止监听进程
     * @auth true
     */
    public function stop()
    {
        try {
            $message = nl2br($this->app->console->call('xadmin:queue', ['stop'])->fetch());
            if (stripos($message, 'sent end signal to process')) {
                $this->success('停止任务监听主进程成功！');
            } elseif (stripos($message, 'processes to stop')) {
                $this->success('没有找到需要停止的进程！');
            } else {
                $this->error($message);
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 创建记录清理任务
     * @auth true
     */
    public function clear()
    {
        try {
            QueueService::instance()->addCleanQueue();
            $this->success('创建清理任务成功！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 删除系统任务
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_delete($this->table);
    }
}