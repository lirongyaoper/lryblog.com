<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
ryphp::load_common('lib/content_form'.EXT,'lry_admin_center');
ryphp::load_sys_class('page','',0);

class content extends common{

    private $content;
    public function __construct(){
        parent::__construct();
        $this->content = M('content_model');
    }

    public function init(){
        $of = input('get.of');
        $or = input('get.or');
    }

}