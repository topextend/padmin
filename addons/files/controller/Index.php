<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:33:03
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-15 18:13:58
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : Files Controller
// |----------------------------------------------------------------------
// |FilePath     : \padmin\addons\files\controller\Index.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\files\controller;

use think\admin\Controller;

/**
 * 上传文件管理
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
    public $table = 'SystemUploadfile';
    
    /**
     * 绑定模板路径
     */
    public $template_index = '../addons/files/view/index.html';
    
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
        $query->dateBetween('create_at')->order('upload_at desc')->page(true, true, false, 0, $this->template_index);
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
            }
        }
    }
}