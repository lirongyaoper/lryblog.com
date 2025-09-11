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
        $modelarr = array();
        foreach($modelinfo as $val){
            $modelarr[$val['modelid']] = $val['name'];
        }
        //处理栏目展开/收起状态（第21-35行）
        $category_show_status= isset($_COOKIE['category_show_status_'.self::$siteid]) ? json_decode($_COOKIE['category_show_status_'.self::$siteid],true) : array();
        $tree_toggle = 0;
        $childid_hide = '';
        if($category_show_status) {
            foreach($category_show_status as $k =>$v){
                if($v == '1'){
                    $childid_hide .= get_category($k,'arrchildid',true).',';
                    $tree_toggle = 1;
                }else{
                    $tree_toggle = 0;
                }
            }
        }
        $arrchildid_arr = explode(',',$childid_hide);

    }





}