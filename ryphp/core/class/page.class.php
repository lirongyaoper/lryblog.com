<?php

class page{

    private $url;
    private $total_rows;
    private $list_rows;
    private $total_page;
    private $now_page;
    private $parameter;
    private $url_rule;
    private $page_prefix;


    public function __construct($total_rows, $list_rows =10, $parameter = array())
    {
        $this ->total_rows = intval($total_rows);
        $this ->list_rows = $list_rows ? intval($list_rows) : $this ->_get_page_size();
        $this ->total_page = ceil($this ->total_rows / $this->list_rows);
        $this ->now_page = isset($_GET['page']) ? intval($_GET['page']) : (isset($_POST['page']) ? intval($_POST['page']) : 1);
        $this -> now_page = $this ->now_page  > 0 ?  $this ->now_page : 1;
        $this ->parameter = empty($parameter) ? $_GET : $parameter;
        $this -> url_rule = defined('LIST_URL') && LIST_URL ? true : false;
        $this ->page_prefix = defined('PAGE_PREFIX') ? PAGE_PREFIX : 'list_' ;
        $this ->url = $this ->geturl();
     }

     protected function geturl(){
        unset($this ->parameter['m'],$this ->parameter['c'], $this ->parameter['a']);
        $this -> parameter['page'] = 'PAGE';

        if($this ->url_rule) return $this -> _list_url();
        return U(ROUTE_A,$this ->parameter);
     }









}