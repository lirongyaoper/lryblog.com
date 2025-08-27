<?php
defined('IN_RYPHP') or exit('Access Denied');

class index{
    
    
    public $page = 0;

    public function __construct()
    {
         isset($_GET['page']) && $this ->page = intval($_GET['page']);
    }

    public function init(){
        $data = D('category')->field('catid AS id,catname AS name,parentid,`cattype`,modelid,listorder,member_publish,pclink,domain,display')->where(array('siteid'=>0))->order('listorder ASC,catid ASC')->select();
        var_dump($data);
    }
}