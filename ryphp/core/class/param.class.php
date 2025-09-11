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


	/**
	 * Process URL path information and handle route parameters
	 * 处理URL路径信息并处理路由参数
	 *
	 * This method handles PATH_INFO based URL routing by:
	 * 该方法通过以下步骤处理基于PATH_INFO的URL路由：
	 * 
	 * 1. Checks if 's' parameter exists in $_GET
	 *    检查$_GET中是否存在's'参数
	 * 
	 * 2. Converts $_GET['s'] to PATH_INFO if it exists
	 *    如果存在$_GET['s']则转换为PATH_INFO
	 * 
	 * 3. Removes HTML suffix and 'index.php' from PATH_INFO
	 *    从PATH_INFO中移除HTML后缀和'index.php'
	 * 
	 * 4. Applies route mapping if enabled
	 *    如果启用了路由映射则应用映射规则
	 * 
	 * 5. Parses PATH_INFO into components:
	 *    将PATH_INFO解析为以下组件：
	 *    - $_GET['m'] = module name (模块名)
	 *    - $_GET['c'] = controller name (控制器名)
	 *    - $_GET['a'] = action name (动作名)
	 * 
	 * 6. Processes additional key-value pairs in URL path
	 *    处理URL路径中的其他键值对
	 *
	 * @return boolean Returns false if 's' parameter is not set, true otherwise
	 *                 如果's'参数未设置返回false，否则返回true
	 * @access private
	 */
	private function pathinfo_url(){
		if(!isset($_GET['s'])) return false;
		if(is_string($_GET['s'])) $_SERVER['PATH_INFO'] = $_GET['s'];
		unset($_GET['s']);
		if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])){
			$_SERVER['PATH_INFO'] = str_ireplace(array(C('url_html_suffix'),'index.php'),'',$_SERVER['PATH_INFO']);
			if(C('route_mapping')) $this ->mapping(set_mapping($this->route_config['m']));
			$pathinfo = explode('/',trim($_SERVER['PATH_INFO'],'/'));
			$_GET['m'] = isset($pathinfo[0]) ? $pathinfo[0] : '';
			$_GET['c'] = isset($pathinfo[1]) ? $pathinfo[1] : '';
			$_GET['a'] = isset($pathinfo[2]) ? $pathinfo[2] : '';

			$total = count($pathinfo);
			for($i = 3; $i < $total;$i+=2){
				if(isset($pathinfo[$i+1])) $_GET[$pathinfo[$i]] = str_replace('{RYPHP_ROUTE}', '/', $pathinfo[$i+1]);
			}

			
		}
		return true;	 

	}

	
	/**
	 * URL路由映射处理方法
	 * 
	 * 该方法根据预定义的路由规则将URL路径重写为新的格式
	 * 
	 * @param array $rules 路由映射规则数组,键为匹配模式,值为替换模式
	 *                     例如: ['old-path' => 'new-path']
	 * 
	 * @return void
	 * 
	 * 处理流程:
	 * 1. 检查参数是否为数组,非数组则直接返回
	 * 2. 获取当前请求的PATH_INFO,确保以/结尾
	 * 3. PATH_INFO为空则直接返回
	 * 4. 遍历路由规则数组
	 * 5. 将规则键构造为正则表达式
	 * 6. 匹配成功则用规则值替换匹配内容
	 * 7. 更新$_SERVER['PATH_INFO']为新路径
	 */
	private function mapping($rules){
		if(!is_array($rules)) return;
		$pathinfo = is_string($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'].'/') : '';
		if(!$pathinfo) return;
		foreach($rules as $k => $v){
			$reg = '/'.$k.'/i';
			if (preg_match($reg,$pathinfo)){
				$reg = preg_replace($reg,$v,$pathinfo);
				$_SERVER['PATH_INFO'] = '/'.$reg;
				break;
			}

		}
	}


	/**
	 * Sets the path information based on URL request
	 * 
	 * This private method handles URL path processing by:
	 * 1. Checking if URL parameter 's' already exists and is not empty
	 * 2. Extracting path info by removing script name from request URI
	 * 3. Processing the path info:
	 *    - URL decoding the path
	 *    - Removing query string if exists
	 *    - Setting processed path to $_GET['s']
	 *
	 * @access private
	 * @return void|null Returns early if $_GET['s'] is already set
	 *
	 * Example URL:
	 * http://example.com/index.php/path/to/page?param=value
	 * Will set $_GET['s'] to: /path/to/page
	 */

	private function set_pathinfo(){
		if(isset($_GET['s']) && !empty($_GET['s'])) return;
		$pathinfo = str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['REQUEST_URI']);
		if($pathinfo){
			$pathinfo = urldecode($pathinfo);
			$pos = strpos($pathinfo, '?');
			if($pos !==false)  $pathinfo = substr($pathinfo,0,$pos);
			if($pathinfo)  $_GET['s'] = $pathinfo;
		}

	}











}