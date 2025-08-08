<?php
defined('IN_RYPHP') or exit('Access Denied');

class index{
    
    
    public $page = 0;

    public function __construct()
    {
         isset($_GET['page']) && $this ->page = intval($_GET['page']);
    }

    public function init(){

        echo "nihao,woaini ";
    }
}