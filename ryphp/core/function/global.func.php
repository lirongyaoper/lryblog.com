<?php


function C($key = '', $default = ''){
    static $config = array();
    if(isset($config['config'])){
        if(empty($key)){
            return $config['config'];
        }elseif(isset($config['config'][$key])){
            return $config['config'][$key];
        }else{
            return $default;
        }
    }
    $pathfile = RYPHP_ROOT . 'common'. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    if(is_file($pathfile)){
        $config['config'] = include $pathfile;
    }
    if(empty($key)){
        return $config['config'];
    }elseif(isset($config['config'][$key])){
        return $config['config'][$key];
    }else{
        return $default;
    }
}

/**
 * 获取配置文件中的配置项
 * 
 * @param string $key 配置键名,格式为 "文件名.配置项" 或 "文件名"
 *                    例如: "database.host" 或 "database"
 * @param mixed $default 默认返回值,当配置项不存在时返回该值
 * @return mixed 返回配置项的值,如果配置项不存在则返回默认值
 * 
 * 使用说明:
 * 1. 配置文件必须放在 common/config 目录下
 * 2. 配置文件名必须为 .php 后缀
 * 3. 配置文件必须返回一个数组
 * 4. 静态变量 $config 用于缓存已加载的配置,避免重复加载文件
 * 
 * 示例:
 * config('database.host') - 获取 database.php 中的 host 配置项
 * config('database') - 获取 database.php 中的所有配置
 */
function config($key = '',$default = ''){
    $k = explode('.',$key);
    static $config = array();
    if(!isset($config[$k[0]])){
        $pathfile = RYPHP_ROOT . 'common'. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $k[0] . '.php';
        if(is_file($pathfile)){
            $config[$k[0]] = include $pathfile;
        }else{
            return $default;
        }
    }
    return count($k) ==1 ? $config[$k[0]] : (isset($config[$k[0]][$k[1]])? $config[$k[0]][$k[1]] : $default);
}


function D($tablename){
    static $_tables = array();
    if(isset($_tables[$tablename])) return $_tables[$tablename];
    ryphp::load_sys_class('db_factory','', 0 );
    $db_object = db_factory::get_instance() -> connect($tablename);
    $_tables[$tablename] = $db_object;
    return $db_object;

}


function getcache($name){
    ryphp::load_sys_class('cache_factory','',0);
    $cache = cache_factory:: get_instance()->get_cache_instances();
    return $cache->get($name);
}

/**
 * Get the client's IP address
 * 
 * This function attempts to get the client's IP address through various methods:
 * 1. HTTP_CLIENT_IP
 * 2. HTTP_X_FORWARDED_FOR
 * 3. REMOTE_ADDR
 * 4. $_SERVER['REMOTE_ADDR']
 *
 * @return string Returns the client's IP address if valid, otherwise returns '127.0.0.1'
 *                The returned IP will be in IPv4 format (e.g. 192.168.1.1)
 */
function getip(){
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '127.0.0.1';
}

/**
 * 获取当前完整的URL地址
 * 
 * 该函数返回包含协议、域名和请求路径的完整URL
 * 
 * @return string 返回完整的URL地址
 *                例如: https://example.com/path/to/page?param=value
 * 
 * @uses is_ssl()        判断当前是否为HTTPS协议
 * @uses safe_replace()  过滤URL中的特殊字符
 * @uses HTTP_HOST      服务器域名常量
 * 
 * @access public
 */
function get_url(){
    $sys_protocal = is_ssl()?  'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) :  $php_self.(isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']): $path_info);
    return $sys_protocal .HTTP_HOST . $relate_url;
}


/**
 * 发送HTTPS请求
 * 
 * 该函数用于发送HTTP/HTTPS请求，支持GET和POST方法，可以自定义请求头
 * 
 * @param string $url     请求的URL地址
 * @param mixed  $data    POST请求的数据，可以是数组或字符串。如果为空则为GET请求
 * @param bool   $array   返回数据是否转换为数组，true则将json转为数组，false则返回原始数据
 * @param int    $timeout 请求超时时间，单位毫秒，默认2000ms
 * @param array  $header  自定义HTTP请求头，数组格式
 * 
 * @return mixed 如果$array为true，则返回解析后的数组：
 *               - 请求成功时返回解析后的JSON数据
 *               - 请求失败时返回 array('status' => 0, 'message' => 错误信息)
 *               如果$array为false：
 *               - 请求成功时返回原始响应数据
 *               - 请求失败时返回错误信息字符串
 * 
 * @example
 * // GET请求示例
 * https_request('https://api.example.com/data');
 * 
 * // POST请求示例
 * https_request('https://api.example.com/data', ['name' => 'test']);
 * 
 * // 自定义请求头示例
 * https_request('https://api.example.com/data', '', true, 2000, ['Authorization: Bearer token']);
 */
function https_request($url,$data = '',$array = true,$timeout = 2000,$header = array()){
    $curl = curl_init($url);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($curl,CURLOPT_NOSIGNAL, true);
    curl_setopt($curl,CURLOPT_TIMEOUT_MS,$timeout);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

    if($data){
        curl_setopt($curl,CURLOPT_POST,true); // 设置为POST请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);// 设置POST数据
    }
	if($header){
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);// 设置HTTP头部信息
	}
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);// 返回响应而不是输出
    $output = curl_exec($curl);
    debug::addmsg(array('url' => $url, 'data' => $data ),2);
    if($output === false){
        $curl_error  = curl_error($curl);
        return $array ? array('status' => 0,'message' => $curl_error) : $curl_error;
    }
    curl_close($curl);
    return $array ? json_decode($output,true) : $output;
}



/**
 * Check if the current request is using HTTPS/SSL
 *
 * Checks various server variables to determine if the connection is secure:
 * - HTTPS server variable
 * - Server port (443)
 * - Request scheme
 * - X-Forwarded-Proto header 
 * - X-Forwarded-Scheme header
 *
 * @return bool Returns true if using HTTPS/SSL, false otherwise
 */
function is_ssl(){
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])){
        return true;
    }elseif(isset($_SERVER['REQUEST_SCHEME']) && ('https' == strtolower($_SERVER['REQUEST_SCHEME']))){
        return true;
    }elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ('https' == strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']))) {
        return true;
    }elseif(isset($_SERVER['HTTP_X_FORWARDED_SCHEME']) && ('https' == strtolower($_SERVER['HTTP_X_FORWARDED_SCHEME']))) {
        return true;
    }
    return false;
}

/**
 * Check if the current request method is POST
 * 
 * This function checks whether the current HTTP request method is POST by comparing
 * the REQUEST_METHOD server variable with 'POST' (case-insensitive)
 * 
 * @return bool Returns true if request method is POST, false otherwise
 */
function is_post(){
    return 'POST' === strtoupper($_SERVER['REQUEST_METHOD']);
}


/**
 * Check if current request method is GET
 * 
 * Determines if the request method is GET by comparing with $_SERVER['REQUEST_METHOD']
 * 
 * @return boolean Returns true if request method is GET, false otherwise
 */
function is_get(){
	return 'GET' === strtoupper($_SERVER['REQUEST_METHOD']);
}
/**
 * Check if current request method is PUT
 * 
 * Determines if the request method is PUT by comparing with $_SERVER['REQUEST_METHOD']
 * 
 * @return boolean Returns true if request method is PUT, false otherwise
 */
function is_put(){
    return 'PUT' === strtoupper($_SERVER['REQUEST_METHOD']);
}
/**
 * Check if current request is AJAX
 * 
 * Determines if the request is an AJAX request by checking for HTTP_X_REQUESTED_WITH header
 * Common header sent by JavaScript libraries like jQuery when making AJAX requests
 * 
 * @return boolean Returns true if request is AJAX, false otherwise
 */
function is_ajax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}


/**
 * 获取和设置语言定义
 * @param	string		$language	语言变量
 * @param	string		$module     模块名
 * @return	string		语言字符
 */
function L($language = '', $module = ''){
	static $_lang = array();
	if(empty($language)) return $_lang;
        
	$lang = C('language');
	$module = empty($module) ? ROUTE_M : $module;
	if(!$_lang) { 
		$sys_lang = require(RYPHP_PATH.'language'.DIRECTORY_SEPARATOR.$lang.'.lang.php');
		$module_lang = array();
		if(is_file(RYPHP_APP.$module.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$lang.'.lang.php')){
			$module_lang = require(RYPHP_APP.$module.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$lang.'.lang.php');
		}
		$_lang = array_merge($sys_lang, $module_lang);
	}
	if(array_key_exists($language,$_lang)) {
		return $_lang[$language];
	}
	
	return $language;
}



/**
 * 兼容PHP低版本的json_encode
 * @param  array   $array
 * @param  integer $options
 * @param  integer $depth 
 * @return string|false  //是 PHP 中用于将 PHP 变量（如数组、对象）
 * 转换为 JSON 格式字符串的函数。它是 PHP 与前端（JavaScript）或其他系统进行数据交换的重要工具。
 */
function new_json_encode($array, $options = 0, $depth = 0){
	if(version_compare(PHP_VERSION,'5.4.0','<')) {
	    $jsonstr = json_encode($array);
	}else{
	    $jsonstr = $depth ? json_encode($array, $options, $depth) : json_encode($array, $options);
	}   
	return $jsonstr;
}

/**
 * 打印各种类型的数据，调试程序时使用。
 * @param mixed $var 变量，支持传入多个
 * @return null
 */
function P($var){
	foreach(func_get_args() as $value){
		echo '<pre style="background:#18171B;color:#EBEBEB;border-radius:3px;padding:5px 8px;margin:8px 0;font:12px Menlo, Monaco, Consolas, monospace;word-wrap:break-word;white-space:pre-wrap">';
		var_dump($value);
		echo '</pre>';
	}
	return null;
}

function return_json($arr = array(),$show_debug = false){
    header("X-Powered-By:RYPHP/RYCMS");
    header('Content-Type:application/json;charset = utf-8');
    if(!$arr) $arr = array('status'=>0,'message'=>L('data_not_modified'));
    if(RY_DEBUG || $show_debug) $arr = array_merge($arr,debug::get_debug());
    exit(new_json_encode($arr,JSON_UNESCAPED_UNICODE));
}



/**
 * 对字符串进行安全过滤处理
 * 
 * 此函数用于清理字符串中的潜在危险字符，防止XSS攻击和SQL注入等安全问题。
 * 主要进行以下处理：
 * - 移除URL编码字符（%20, %27, %2527）
 * - 移除特殊字符（*, ", ', ;, \）
 * - HTML转义尖括号（< 转换为 &lt;, > 转换为 &gt;）
 * - 移除花括号（{, }）
 * 
 * @param mixed $string 需要处理的字符串
 * @return mixed 如果输入是字符串则返回处理后的安全字符串，否则原样返回
 */
function safe_replace($string) {
	if(!is_string($string)) return $string;
	$string = trim($string);
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}	




/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code){
    static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded',
            550 => 'Can not connect to MySQL server'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}	

function setcache($name,$data,$timeout = 0){
    ryphp::load_sys_class('cache_factory','',0);
    $cache = cache_factory :: get_instance()->get_cache_instances();
    return $cache ->set($name,$data,$timeout);
}



/**
 *  提示信息页面跳转
 *
 * @param     string  $msg      消息提示信息
 * @param     string  $gourl    跳转地址,stop为停止
 * @param     int     $limittime  限制时间
 * @return    void
 */

function showmsg($msg, $gourl = '', $limittime  =3){
    application::showmsg($msg, $gourl, $limittime);
    if(RY_DEBUG){
        debug::stop();
        debug::message();
    }
    exit;
}



function sizecount($size, $prec = 2) {
    // Use static array to avoid recreating it on each function call
    static $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
    
    // Force numeric value and handle negative sizes
    $size = abs(floatval($size));
    
    // Early return for 0 size
    if ($size === 0) {
        return "0 B";
    }
    
    // Calculate power of 1024 using log to avoid loop
    $pow = floor(log($size, 1024));
    // Constrain to available units
    $pow = min($pow, count($units) - 1);
    
    // Calculate final size
    $size /= pow(1024, $pow);
    
    return round($size, $prec) . ' ' . $units[$pow];
}


function U($url = '',$vars = '',$domain = null, $suffix = true){
    $url = trim($url, '/');
    $arr = explode('/',$url);
    $num = count($arr);
    $string = SITE_PATH;
    if(URL_MODEL==0){
        $string .='index.php?';
        if($num ==3){
            $string .= 'm=' . $arr[0] . '&c=' . $arr[1] .'&a=' . $arr[2];
        }elseif($num ==2){
            $string .= 'm=' . ROUTE_M . '&c=' . $arr[1] .'&a=' . $arr[1];
        }else{
            $string .= 'm=' . ROUTE_M . '&c=' . ROUTE_C . '&a=' .$arr[0];
        }
        if($vars){
            if(is_array($vars))  $vars = http_build_query($vars);
            $string .= '&'.$vars;
        }
    }else{
        if(URL_MODEL == 1) $string .= 'index.php?s=';
        if(URL_MODEL == 4) $string .= 'index.php/';
        if($num ==3){
            $string .= $url;
        }elseif($num ==2){
            $string .= ROUTE_M . '/' .$url;
        }else{
            $string .= ROUTE_M . '/' . ROUTE_C . '/' . $url;
        }
        if($vars){
            if(!is_array($vars))  parse_str($vars, $vars);
            foreach($vars as $var => $val){
                if(!is_array($val)  && trim($val) !==''){
                    $val = str_replace('/','{LRYPHP_PATH}',$val);
                    $string .= '/'.urlencode($var) .'/'. urlencode($val);
                }
            }
        }
        $string .= $suffix == true ? C('url_html_suffix') : $suffix;
    }
    $string = $domain === null  && URL_MODEL ==3 ? SERVER_PORT.HTTP_HOST.$string : ($domain ? SERVER_PORT. HTTP_HOST. $string : $string);
    return $string;
}



/**
 * 写入错误日志到文件
 * 
 * @param mixed $err_array 错误信息数组或字符串
 * @param string $path 日志文件保存路径,默认为空(使用系统cache目录)
 * @return bool 写入成功返回true,失败返回false
 * 
 * 功能说明:
 * 1. 检查是否开启错误日志保存功能
 * 2. 记录错误发生的时间、URL、IP地址
 * 3. 如果有POST数据,将POST数据也记录到日志
 * 4. 将所有信息以"|"分隔符连接
 * 5. 自动创建日志目录
 * 6. 当日志文件超过20M时自动备份
 * 7. 日志文件首行包含PHP退出语句防止直接访问
 * 
 * 使用示例:
 * write_error_log('发生错误');
 * write_error_log(['错误1','错误2']);
 * write_error_log($error, '/custom/path');
 */

function write_error_log($err_array, $path = ''){
    if(!C('error_log_save') || defined('CLOSE_WRITE_LOG')) return false;
    $err_array = is_array($err_array) ? $err_array : array($err_array);
    $message[] = date('Y-m-d H:i:s');
    $message[] = get_url();
    $message[] = getip();
    if(isset($_POST) && !empty($_POST)) {
        $message[] = new_json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }
    $message = array_merge($message, $err_array);
    $message = join(' | ',$message).'\r\n';
    if(!$path) $path = RYPHP_ROOT . 'cache';
    if(!is_dir($path)) @mkdir($path, 0777, true);
    $file = $path . DIRECTORY_SEPARATOR . 'error_log.php';
    if(is_file($file) && filesize($file) > 20971520){
        @rename($file, $path . DIRECTORY_SEPARATOR . 'error_log' . date('YmdHis') .rand(100,999). '.php');

    }
    if(!is_writeable($file)) return false;
    if(!is_file($file)){
        error_log("<?php exit('Access Denied'); ?>\r\n", 3, $file);
    }
    return error_log($message , 3, $file);
}


