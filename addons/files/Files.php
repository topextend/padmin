<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-07-30 22:25:39
// |----------------------------------------------------------------------
// |LastEditTime : 2020-08-13 23:17:40
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.padmin.com\addons\files\Files.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace addons\files;

use think\admin\Addons;

/**
 * 插件测试
 */
class Files extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'files',	// 插件标识
        'title' => '上传文件管理',	// 插件名称
        'description' => '管理所有上传到本地的文件',	// 插件简介
        'status' => 1,	// 状态
        'author' => 'Jarmin',
        'email'  => 'edshop@qq.com',
        'version' => 'V1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        // 获取插件信息
        $content = $this->info;
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的file_load_hook钩子方法
     * @return mixed
     */
    public function fileshook($param)
    {
        $postdata = [
            'upload_type'  => $param['info']['uptype'], 
            'original_name'=> $param['info']['name'], 
            'file_size'    => $param['info']['size'], 
            'mime_type'    => $param['info']['type'], 
            'file_ext'     => $param['info']['xext'], 
            'url'          => $param['info']['url'], 
            'path_url'     => $param['info']['xkey']
        ];
        $result = $this->app->db->name('SystemUploadfile')->save($postdata);
    }
}