<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
class category extends common{
    private $db;
    public function __construct(){
        parent::__construct();
        $this ->db = D('category');
    }

    /**
     * @author lirongyaoper
     * @description 分类列表
     */
    public function init(){
        $modelinfo = get_site_modelinfo();
    }





}