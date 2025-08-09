<?php


/**
 * 
 * page.class.php
 * 改变如下：
 *	   1. public function total() -> public function get_totalpage()
 *    2.public function getpage() ->public function get_nowpage()
 *
 * 
 */

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

     private function make_url($page){
         if($page == 1 && $this ->url_rule && !strpos($this->url, '?')) return strstr($this->url,$this ->page_prefix . 'PAGE',true);
         return str_replace('PAGE',$page, $this->url);
     }

   public function get_totalpage() {
      return $this->total_page;
   }

   public function get_nowpage(){
      return $this->now_page;
   }

   public function gethome(){
      return '<a href=" '.$this->make_url(1) .' " class ="homepage">' .L('home_page').'</a>';
   }

   public function getend(){
      return '<a href ="' .$this->make_url($this->total_page). '" clas="endpage"> '. L('end_page').'</a>';
   }

	/**
	 * 获得上页
	 */
	public function getpre(){
		if($this->now_page<=1){
			return '<a href="'.$this->make_url(1).'" class="nopage">'.L('pre_page').'</a>';
		}
		return '<a href="'.$this->make_url($this->now_page-1).'" class="prepage">'.L('pre_page').'</a>';
	}

	
	/**
	 * 获得下页
	 */
	public function getnext(){
		if($this->now_page>=$this->total_page){
			return '<a href="'.$this->make_url($this->now_page).'" class="nopage">'.L('next_page').'</a>';	
		}
		return '<a href="'.$this->make_url($this->now_page+1).'" class="nextpage">'.L('next_page').'</a>';
	}
	
	

   public function start_rows(){
      if($this ->total_page && $this->now_page > $this->total_page)  $this->now_page = $this->total_page;
      return ($this ->now_page -1) * ($this->list_rows);
   }

   public function list_rows(){
      return $this ->list_rows;
   }


   public function limit(){
      return $this ->start_rows() .  ',' . $this->list_rows();
   }


   public function page_size($sizes = array(10,20,30,40,50,100)){
      if(!is_array($sizes)) return '';
      $string = '<select name ="page_size" class="select" data-url="' .$this->url .  '" onchange ="lry_page_size(this)">';
      foreach($sizes as $val){
         $select = $this ->list_rows == $val ? 'selected' : '';
         $string .= '<option value ="' . $val. '" '. $select. '>'. $val. L('article_page').'</option>';
      }
      $string .= '</select>';
      return $string;
   }

   public function getlist(){
      $str = '';
      if($this->total_page <=5){
         
         for($i = 1;$i<= $this->total_page; $i++){
            $class = $this->now_page ==$i ? ' curpage' : '';
            $str.= '<a href="'. $this->make_url($i).'" class="listpage'. $class.'">' . $i . '</a>';
         
         }
      }else{
         if($this->now_page <=3){
            $p =5;
         }else{
            $p = ($this->now_page+2) >= $this->total_page ? $this->total_page : $this->now_page + 2;
         }
      }
   }






}