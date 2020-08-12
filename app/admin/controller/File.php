<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-08 18:55:13
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-12 14:59:34
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Config Controller Of Admin
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\app\admin\controller\File.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\admin\controller;

use think\admin\Controller;

/**
 * 上传文件管理
 * Class File
 * @package app\admin\controller
 */
class File extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'SystemUploadfile';

    /**
     * 上传文件管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '上传文件管理';
        $query = $this->_query($this->table)->like('upload_type,original_name,url,mime_type,file_ext');
        $query->dateBetween('create_at')->order('upload_at desc')->page();
    }
    
    /**
     * 删除文件
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_delete_file(input('post.id'));
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
    
    /**
     * 获取上传文件物理地址
     */
    private function _get_file_path($id)
    {
        $path = $this->app->db->name($this->table)->where('id','in',$id)->field('upload_type, path_url')->select()->toArray();
        return $path;
    }

    /**
     * 删除相应文件
     */
    private function _delete_file($id)
    {
        $result = $this->_get_file_path($id);
        if (!empty($result))
        {
            $path = $this->app->getRootPath() . 'public/upload/';
            foreach ($result as $key => $value)
            {
                $dir = explode("/" , $value['path_url']);
                // 删除文件
                @unlink($path .  $value['path_url']);
                // 删除目录
                @rmdir($path . $dir[0]);
                // 清除用户头像缓存
                $this->app->session->set('user.headimg',NULL);
            }
        }
    }
}
