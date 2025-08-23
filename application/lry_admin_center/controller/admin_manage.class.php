<?php

defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
ryphp::load_sys_class('page','',0);
class admin_manage extends common{
    /**
     * @author lirongyaoper
     * 管理员列表
     */
    public function init(){
        $of = input('get.of');
        $or = input('get.or');
    }
}