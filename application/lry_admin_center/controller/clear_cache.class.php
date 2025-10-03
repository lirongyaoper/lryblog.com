<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
class clear_cache extends common{

    public function init(){

    }
    public function public_clear(){
        if(!is_writable(RYPHP_ROOT.'cache'.DIRECTORY_SEPARATOR)){
            return_json(array('status'=> 0,'message' => '系统缓存目录【cache】不可写，请检查权限.'));
        }

        $chache_file = array('index','mobile','member');
        foreach($chache_file as $files){
            $files = glob(RYPHP_ROOT.'cache'.DIRECTORY_SEPARATOR.$files.DIRECTORY_SEPARATOR.'*.tpl.php');
            foreach($files as $file){
                @unlink($file);
            }
        }

        delcache('',true);
        return_json(array('status' => 1,'message' => '系统缓存已清理完成。'));
    }
}