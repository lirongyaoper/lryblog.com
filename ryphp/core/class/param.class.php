<?php

class param {

    private $route_config = '';

    public function __construct(){
        $route_config = array_merge(C('route_config'), config('route', array()));
        $this -> route_config = isset($route_config[HTTP_HOST]) ? $route_config[HTTP_HOST] :  $route_config['default'];
        if(URL_MODEL){
            if(C('set_pathinfo')) $this -> set_pathinfo();
            $this ->pathinfo_url();
        }
        return true;
    }


    	
	/**
	 * 获取模型
	 */
	public function route_m() {
		$m = isset($_GET['m']) && !empty($_GET['m']) ? $_GET['m'] : (isset($_POST['m']) && !empty($_POST['m']) ? $_POST['m'] : '');
		$m = $this->safe_deal($m);
		return !empty($m) ? $m : $this->route_config['m'];
	}

	
	/**
	 * 获取控制器
	 */
	public function route_c() {
		$c = isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c'] : (isset($_POST['c']) && !empty($_POST['c']) ? $_POST['c'] : '');
		$c = $this->safe_deal($c);
		return !empty($c) ? $c : $this->route_config['c'];
	}

	
	/**
	 * 获取事件
	 */
	public function route_a() {
		$a = isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a'] : (isset($_POST['a']) && !empty($_POST['a']) ? $_POST['a'] : '');
		$a = $this->safe_deal($a);
		return !empty($a) ? $a : $this->route_config['a'];
	}



	/**
	 * 安全处理函数
	 * 处理m,a,c
	 */
	private function safe_deal($str) {
		if(!is_string($str)) return '';
		$str = trim($str);
		if(!MAGIC_QUOTES_GPC) $str = addslashes($str);
		if (strlen($str) > 128) application::halt('parameter length cannot exceed 128 character.');
		return str_replace(array('/', '.'), '', $str);
	}


	private function pathinfo_url(){
		if(!isset($_GET['s'])) return false;
		if(is_string($_GET['s'])) $_SERVER['PATH_INFO'] = $_GET['s'];
		unset($_GET['s']);
		if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])){
			$_SERVER['PATH_INFO'] = str_ireplace(array(C('url_html_suffix'),'index.php'),'',$_SERVER['PATH_INFO']);
			if(C('route_mapping')) $this ->mapping(set_mapping($this->route_config['m']));
			
		}
			 

	}















































































































































































































































}