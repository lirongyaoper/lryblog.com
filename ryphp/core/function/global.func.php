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
 * 检查IP是否匹配
 * @param  $ip_vague 要检查的IP或IP段，IP段(*)表示
 * @param  $ip       被检查IP
 * @return bool
 */
function check_ip_matching($ip_vague, $ip = ''){
	empty($ip) && $ip = getip();
	if(strpos($ip_vague,'*') === false){
		return $ip_vague == $ip;
	}
	if(count(explode('.', $ip_vague)) != 4) return false;
	$min_ip = str_replace('*', '0', $ip_vague);
	$max_ip = str_replace('*', '255', $ip_vague);
	$ip = ip2long($ip);
	if($ip>=ip2long($min_ip) && $ip<=ip2long($max_ip)){  
		return true; 
	}
	return false;
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

/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
	return random($lenth, '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
}


/**
* 创建订单号
* @return   string     字符串
*/
function create_tradenum(){
	return date('YmdHis').random(4);
}


function D($tablename){
    static $_tables = array();
    if(isset($_tables[$tablename])) return $_tables[$tablename];
    ryphp::load_sys_class('db_factory','', 0 );
    $db_object = db_factory::get_instance() -> connect($tablename);
    $_tables[$tablename] = $db_object;
    return $db_object;

}



/**
 * 获取请求地区
 * @param $ip	IP地址
 * @param $is_array 是否返回数组形式
 * @return string|array
 */
function get_address($ip, $is_array = false){
	if($ip == '127.0.0.1') return '本地地址';
	$content = @file_get_contents('http://api.lryper.com/api/ip/?ip='.$ip);
	$arr = json_decode($content, true);
	if(is_array($arr) && !isset($arr['error'])){
		return $is_array ? $arr : $arr['country'].'-'.$arr['province'].'-'.$arr['city'];
	}else{
		return $is_array&&is_array($arr) ? $arr : '未知';
	}
}

/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if(empty($data)) return '';
	
	if($isformdata) $data = new_stripslashes($data);
	if(version_compare(PHP_VERSION,'5.4.0','<')){
		return addslashes(json_encode($data));
	}else{
		return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_FORCE_OBJECT);
	}
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
function https_request($url, $data = '', $array = true, $timeout = 2000, $header = array()){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_NOSIGNAL, true); 
    curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout); 

    if($data){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

	if($header){
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	}
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    debug::addmsg(array('url'=>$url, 'data'=>$data), 2);
	if($output === false) {
		$curl_error = curl_error($curl);
		return $array ? array('status'=>0, 'message'=>$curl_error) : $curl_error;
	}
    curl_close($curl);
    return $array ? json_decode($output, true) : $output;
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
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}


/**
 * 返回经htmlspecialchars处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @param $filter 需要排除的字段，格式为数组
 * @return mixed
 */
function new_html_special_chars($string, $filter = array()) {
	if(!is_array($string)) return htmlspecialchars($string,ENT_QUOTES,'utf-8');
	foreach($string as $key => $val){
		$string[$key] = $filter&&in_array($key, $filter) ? $val : new_html_special_chars($val, $filter);
	}
	return $string;
}


/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
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

/**
* 产生随机字符串
* @param    int        $length  输出长度
* @param    string     $chars   可选的 ，默认为 0123456789
* @return   string     字符串
*/
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}


/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'onpointerout', 'onfullscreenchange', 'onfullscreenerror', 'onhashchange', 'onanimationend', 'onanimationiteration', 'onanimationstart', 'onmessage', 'onloadstart', 'ondurationchange', 'onloadedmetadata', 'onloadeddata', 'onprogress', 'oncanplay', 'oncanplaythrough', 'onended', 'oninput', 'oninvalid', 'onoffline', 'ononline', 'onopen', 'onpagehide', 'onpageshow', 'onpause', 'onplay', 'onplaying', 'onpopstate', 'onratechange', 'onsearch', 'onseeked', 'onseeking', 'onshow', 'onstalled', 'onstorage', 'onsuspend', 'ontimeupdate', 'ontoggle', 'ontouchcancel', 'ontouchend', 'ontouchmove', 'ontouchstart', 'ontransitionend', 'onvolumechange', 'onwaiting', 'onwheel', 'onbegin');

    $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, 'xxx', $string); 
	}
	return $string;
}	


function return_json($arr = array(),$show_debug = false){
    header("X-Powered-By:RYPHP/RYCMS");
    header('Content-Type:application/json;charset = utf-8');
    if(!$arr) $arr = array('status'=>0,'message'=>L('data_not_modified'));
    if(RYPHP_DEBUG || $show_debug) $arr = array_merge($arr,debug::get_debug());
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
    if(RYPHP_DEBUG){
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


/**
 * 字符截取
 * @param $string 要截取的字符串
 * @param $length 截取长度
 * @param $dot	  截取之后用什么表示
 * @param $code	  编码格式，支持UTF8/GBK
 * @return string
 */
function str_cut($string, $length, $dot = '...', $code = 'utf-8') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if($code == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}


/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return string
 */
function trim_script($str) {
	if(is_array($str)){
		foreach ($str as $key => $val){
			$str[$key] = trim_script($val);
		}
 	}else{
 		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = str_replace ( 'javascript:', 'javascript：', $str );
 	}
	return $str;
}



/**
 * 
 * I'll quickly check where `SERVER_PORT` and `URL_MODEL` are defined to ensure the explanation of their roles is accurate, then break down the ternary logic.
 * Ran tool
 *
 *	Ran tool
 *
 *	- 含义
 *	- 这是一段三元表达式，用来决定是否给 `$string` 前面加上协议与域名，生成绝对 URL。
 *	- `.` 是 PHP 的字符串拼接。
 *	- `SERVER_PORT` 是 `http://` 或 `https://`，`HTTP_HOST` 是当前域名（含端口），`URL_MODEL` 是 URL 模式常量。
 *
 *	- 逻辑拆解
 *	- 条件1：`$domain === null && URL_MODEL == 3`
 *		- 为真时：`$string = SERVER_PORT . HTTP_HOST . $string`（强制加前缀，输出绝对 URL）
 *		- 否则进入条件2
 *	- 条件2：`$domain ? SERVER_PORT . HTTP_HOST . $string : $string`
 *		- `$domain` 为真（非空/非0）：同样加前缀为绝对 URL
 *		- `$domain` 为假（空串/0/false）：保持 `$string` 原样（相对 URL）
 *
 *	- 等价判断
 *	- 只要满足以下任一条件，就会加协议+域名前缀：
 *		- `$domain === null` 且 `URL_MODEL == 3`
 *		- `$domain` 为真值
 *	- 否则保持相对路径。
 *
 *	- 示例
 *	- `$domain === null, URL_MODEL=3` → `https://example.com/path`
 *	- `$domain = true` → `https://example.com/path`
 *	- `$domain = '' (或 false), URL_MODEL!=3` → `/path`（原样）
 *
 *	- 可读性写法
 *	$shouldPrefix = ($domain === null && URL_MODEL == 3) || (bool) $domain;
 *	if ($shouldPrefix) {
 *		$string = SERVER_PORT . HTTP_HOST . $string;
 *	}
 *
 *	- 小提示
 *	- 表达式里的空格（如 `SERVER_PORT. HTTP_HOST`）在 PHP 中无影响。
 * 
 * 
 */

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
            if(!is_array($vars))  parse_str($vars, $vars);//如果是字符串则转换为数组		
            foreach($vars as $var => $val){
                if(!is_array($val)  && trim($val) !==''){
                    $val = str_replace('/','{RYPHP_ROUTE}',$val);
                    $string .= '/'.urlencode($var) .'/'. urlencode($val);
                }
            }
        }
        $string .= $suffix == true ? C('url_html_suffix') : $suffix;
    }
    $string = $domain === null  && URL_MODEL ==3 ? SERVER_PORT.HTTP_HOST.$string : ($domain ? SERVER_PORT. HTTP_HOST. $string : $string);
    // $shouldPrefix = ($domain === null && URL_MODEL == 3) || (bool) $domain;
	// if ($shouldPrefix) {
	// 	$string = SERVER_PORT . HTTP_HOST . $string;
	// }
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



/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	$data = is_string($data) ? trim($data) : '';
	if(empty($data)) return array();
	
	if(version_compare(PHP_VERSION,'5.4.0','<')) $data = stripslashes($data);
	$array = json_decode($data, true);
	return is_array($array) ? $array : array();
}




/**
 * 兼容低版本的array_column
 * @param  $array      多维数组
 * @param  $column_key 需要返回值的列
 * @param  $index_key  可选。作为返回数组的索引/键的列。
 * @return array       返回一个数组，数组的值为输入数组中某个单一列的值。
 */
function lry_array_column($array, $column_key, $index_key = null){
	if(function_exists('array_column')) return array_column($array, $column_key, $index_key);

    $result = array();
	foreach ($array as $key => $value) {
		if(!is_array($value)) continue;
        if($column_key){
        	if(!isset($value[$column_key])) continue;
        	$tmp = $value[$column_key];
        }else{
        	$tmp = $value;
        }
        if ($index_key) {
        	$key = isset($value[$index_key]) ? $value[$index_key] : $key;
        }
        $result[$key] = $tmp;
    }
    return $result;
}


/**
 * 判断email格式是否正确
 * @param $email
 * @return bool
 */
function is_email($email) {
	if(!is_string($email)) return false;
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}


/**
 * 判断手机格式是否正确
 * @param $mobile
 * @return bool
 */
function is_mobile($mobile) {
	return is_string($mobile) && preg_match('/1[3456789]{1}\d{9}$/',$mobile);
}


/**
 * 检测输入中是否含有错误字符
 *
 * @param string $string 要检查的字符串名称
 * @return bool
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== false) {
			return true;
		}
	}
	return false;
}
/**
 * 检查用户名是否符合规定
 *
 * @param string $username 要检查的用户名
 * @return 	boolean
 */
function is_username($username) {
	if(!is_string($username)) return false;
	$strlen = strlen($username);
	if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( $strlen > 30 || $strlen < 3 ) {
		return false;
	}
	
	//新增用户名不全是数字时，不能以数字开头
	if(preg_match('/^\d*$/', $username)){
		return true;
	}
	if(preg_match('/^\d\S/', $username)){
		return false;
	}
	
	return true;
}



/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	boolean
 */
function is_password($password) {
	$strlen = is_string($password) ? strlen($password) : 0;
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}


/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return string
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}


/**
 * 是否为图片格式
 * @return bool
 */
function is_img($ext) {
	return in_array(strtolower($ext), array('png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'ico'));
}



/**
 * IE浏览器判断
 * @return bool
 */
function is_ie() {
	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}


/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string) {
	return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}



/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 * @return null
 */
function file_down($filepath, $filename = '') {
    if (!is_file($filepath) || !is_readable($filepath)) {
        send_http_status(404);
        exit;
    }

    if(!$filename) $filename = basename($filepath);
    if(is_ie()) $filename = rawurlencode($filename);
    if(function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($finfo, $filepath);
        finfo_close($finfo);
    } else {
        if (function_exists('mime_content_type')) {
            $filetype = mime_content_type($filepath);
        } else {
            $filetype = 'application/octet-stream';
        }
    }
    $filesize = sprintf("%u", filesize($filepath));
    if(ob_get_length() !== false) @ob_end_clean();
    header('Pragma: public');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Encoding: none');
    header('Content-type: '.$filetype);
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Content-length: '.$filesize);
    $file = fopen($filepath, 'rb');
    fpassthru($file);
    fclose($file);
    exit;
}



/**
* 传入日期格式或时间戳格式时间，返回与当前时间的差距，如1分钟前，2小时前，5月前，3年前等
* @param $date 分两种日期格式"2015-09-12 14:16:12"或时间戳格式"1386743303"
* @param int $type 1为时间戳格式，$type = 2为date时间格式
* @return string
*/
function format_time($date = 0, $type = 1) {
	if($type == 2) $date = strtotime($date);
    $second = SYS_TIME - $date;
    $minute = floor($second / 60) ? floor($second / 60) : 1; 
    if ($minute >= 60 && $minute < (60 * 24)) { 
        $hour = floor($minute / 60); 
    } elseif ($minute >= (60 * 24) && $minute < (60 * 24 * 30)) { 
        $day = floor($minute / ( 60 * 24)); 
    } elseif ($minute >= (60 * 24 * 30) && $minute < (60 * 24 * 365)) { 
        $month = floor($minute / (60 * 24 * 30));
    } elseif ($minute >= (60 * 24 * 365)) { 
        $year = floor($minute / (60 * 24 * 365)); 
    }
    if (isset($year)) {
        return $year . '年前';
    } elseif (isset($month)) {
        return $month . '月前';
    } elseif (isset($day)) {
        return $day . '天前';
    } elseif (isset($hour)) {
        return $hour . '小时前';
    } elseif (isset($minute)) {
        return $minute . '分钟前';
    }
}		



/**
 * 对数据进行编码转换
 * @param array|string $data       数组或字符串
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 * @return string|array
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
	if (!is_array($data)) {
		return iconv($input, $output, $data);
	} else {
		foreach ($data as $key=>$val) {
			if(is_array($val)) {
				$data[$key] = array_iconv($val, $input, $output);
			} else {
				$data[$key] = iconv($input, $output, $val);
			}
		}
		return $data;
	}
}


/**
* 字符串加密/解密函数
* @param	string	$txt		字符串
* @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
* @param	string	$key		密钥：数字、字母、下划线
* @param	string	$expiry		过期时间
* @return	string
*/
function string_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : C('auth_key'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + SYS_TIME : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || intval(substr($result, 0, 10)) - SYS_TIME > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
	}
}


/**
 * 获取内容中的图片
 * @param string $content 内容
 * @return string
 */
function match_img($content){
    preg_match("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png|webp))\\2/i", $content, $match);
    return isset($match[3]) ? $match[3] : ''; 
}


/**
 * 获取远程图片并把它保存到本地, 确定您有把文件写入本地服务器的权限
 * @param string $content 文章内容
 * @param string $targeturl 可选参数，对方网站的网址，防止对方网站的图片使用"/upload/1.jpg"这样的情况
 * @return string $content 处理后的内容
 */
function grab_image($content, $targeturl = ''){
	preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png|webp))\\2/i", $content, $img_array);
	$img_array = isset($img_array[3]) ? array_unique($img_array[3]) : array();
	
	if($img_array) {
		$path =  C('upload_file').'/'.date('Ym/d');
		$urlpath = SITE_PATH.$path;
		$imgpath =  RYPHP_ROOT.$path;
		if(!is_dir($imgpath)) @mkdir($imgpath, 0777, true);
	}
	
	foreach($img_array as $value){
		$val = $value;		
		if(strpos($value, 'http') === false){
			if(!$targeturl) continue;
			$value = $targeturl.$value;
		}	
		if(strpos($value, '?')){ 
			$value = explode('?', $value);
			$value = $value[0];
		}
		if(substr($value, 0, 4) != 'http'){
			continue;
		}
		$ext = fileext($value);
		if(!is_img($ext)) continue;
		$imgname = date('YmdHis').rand(100,999).'.'.$ext;
		$filename = $imgpath.'/'.$imgname;
		$urlname = $urlpath.'/'.$imgname;
		
		ob_start();
		@readfile($value);
		$data = ob_get_contents();
		ob_end_clean();
		$data && file_put_contents($filename, $data);
	 
		if(is_file($filename)){                         
			$content = str_replace($val, $urlname, $content);
		}
	}
	return $content;        
}


/**
 * 生成缩略图函数
 * @param  $imgurl 图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $autocut 是否自动裁剪 默认不裁剪，当高度或宽度有一个数值为0时，自动关闭
 * @param  $smallpic 无图片是默认图片路径
 * @return string
 */
function thumb($imgurl, $width = 300, $height = 200 ,$autocut = 0, $smallpic = 'nopic.jpg') {
	global $image;
	$upload_url = SITE_PATH.C('upload_file').'/';
	$upload_path = RYPHP_ROOT.C('upload_file').'/';
	if(empty($imgurl)) return STATIC_URL.'images/'.$smallpic;
	if(!strpos($imgurl, '://')) $imgurl = SERVER_PORT.HTTP_HOST.$imgurl;
	$imgurl_replace= str_replace(SITE_URL.C('upload_file').'/', '', $imgurl); 
	if(!extension_loaded('gd') || strpos($imgurl_replace, '://')) return $imgurl;
	if(!is_file($upload_path.$imgurl_replace)) return STATIC_URL.'images/'.$smallpic;

	list($width_t, $height_t, $type, $attr) = getimagesize($upload_path.$imgurl_replace);
	if($width>=$width_t || $height>=$height_t) return $imgurl;

	$newimgurl = dirname($imgurl_replace).'/thumb_'.$width.'_'.$height.'_'.basename($imgurl_replace);

	if(is_file($upload_path.$newimgurl)) return $upload_url.$newimgurl;

	if(!is_object($image)) {
		ryphp::load_sys_class('image','','0');
		$image = new image(1);
	}
	return $image->thumb($upload_path.$imgurl_replace, $upload_path.$newimgurl, $width, $height, '', $autocut) ? $upload_url.$newimgurl : $imgurl;
}

/**
 * 水印添加
 * @param $source 原图片路径
 * @param $target 生成水印图片途径，默认为空，覆盖原图
 * @return string
 */
function watermark($source, $target = '') {
	global $image_w;
	if(empty($source)) return $source;
	if(strpos($source, '://')) $source = str_replace(SERVER_PORT.HTTP_HOST, '', $source);
	if(!extension_loaded('gd') || strpos($source, '://')) return $source;
	
	if(!is_object($image_w)){
		ryphp::load_sys_class('image','','0');
		$image_w = new image(1,1);
	}

	if(SITE_PATH == '/'){
		$source = RYPHP_ROOT.$source;
		$target = $target ? RYPHP_ROOT.$target : $source;
		$image_w->watermark($source, $target);
		return str_replace(RYPHP_ROOT, '', $target);
	}else{
		$source = RYPHP_ROOT.str_replace(SITE_PATH, '', $source);
		$target = $target ? RYPHP_ROOT.str_replace(SITE_PATH, '', $target) : $source;
		$image_w->watermark($source, $target);
		return SITE_PATH.str_replace(RYPHP_ROOT, '', $target);
	}
}


/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 *
 *说明：本函数在最新版本中已被弃用 荣耀说明
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
	if($in_column && is_array($data)) {
		$ids = '\''.implode('\',\'', $data).'\'';
		$sql = "$in_column IN ($ids)";
		return $sql;
	} else {
		if ($front == '') {
			$front = ' AND ';
		}
		if(is_array($data)) {
			$sql = '';
			foreach ($data as $key => $val) {
				$sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
			}
			return $sql;
		} else {
			return $data;
		}
	}
}

/**
 * 设置 cookie
 * @param string $name     变量名
 * @param string $value    变量值
 * @param int $time    过期时间
 * @param boolean $httponly  
 */
function set_cookie($name, $value = '', $time = 0, $httponly = false) {
	$time = $time > 0 ? SYS_TIME + $time : $time;
	$name = C('cookie_pre').$name;
	$value = is_array($value) ? 'in_ryphp'.string_auth(json_encode($value),'ENCODE',md5(RYPHP_ROOT.C('db_pwd'))) : string_auth($value,'ENCODE',md5(RYPHP_ROOT.C('db_pwd')));
	$httponly = $httponly ? $httponly : C('cookie_httponly');
	setcookie($name, $value, $time, C('cookie_path'), C('cookie_domain'), C('cookie_secure'), $httponly);
	$_COOKIE[$name] = $value;
}


/**
 * 获取 cookie
 * @param string $name     	  变量名，如果没有传参，则获取所有cookie
 * @param string $default     默认值，当值不存在时，获取该值
 * @return string
 */
function get_cookie($name = '', $default = '') {
	if(!$name) return $_COOKIE;
	$name = C('cookie_pre').$name;
	if(isset($_COOKIE[$name])){
		if(strpos($_COOKIE[$name],'in_ryphp')===0){
			$temp = substr($_COOKIE[$name],9);
			return json_decode(MAGIC_QUOTES_GPC?stripslashes(string_auth($temp,'DECODE',md5(RYPHP_ROOT.C('db_pwd')))):string_auth($temp,'DECODE',md5(RYPHP_ROOT.C('db_pwd'))), true);
        }
		return string_auth(safe_replace($_COOKIE[$name]),'DECODE',md5(RYPHP_ROOT.C('db_pwd')));
	}else{
		return $default;
	}	
}


/**
 * 删除 cookie
 * @param string $name     变量名，如果没有传参，则删除所有cookie
 * @return bool
 */
function del_cookie($name = '') {	
	if(!$name){
		foreach($_COOKIE as $key => $val) { 
			setcookie($key, '', SYS_TIME - 3600, C('cookie_path'), C('cookie_domain'), C('cookie_secure'), C('cookie_httponly'));
			unset($_COOKIE[$key]);
		}		
	}else{
		$name = C('cookie_pre').$name;
		if(!isset($_COOKIE[$name])) return true;
		setcookie($name, '', SYS_TIME - 3600, C('cookie_path'), C('cookie_domain'), C('cookie_secure'), C('cookie_httponly'));
		unset($_COOKIE[$name]);
	}
	return true;
}


/**
 * 获取env 配置
 * @param string $env_key 参数key
 * @param string $default 默认值
 * @return string|bool
 */
function env($env_key, $default = '') {
    static $env_data = array();

    if (empty($env_data)) {
        $env_file = RYPHP_ROOT . '.env';

        if (!is_file($env_file)) return $default;
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (preg_match('/^([^=]+)=(.*)$/', $line, $matches)) {
                $key = trim($matches[1]);
                $value = trim($matches[2]);

                $pattern = '/^[\s]*([\'"])(.*?)(?<!\\\\)\1(?=\s*#|$)/';
                if (preg_match($pattern, $value, $matches)) {
                    $value = $matches[2]; 
                } else {
                    $value = strtok($value, '#');
                }

                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = $value + 0;
                }

                $env_data[$key] = $value;
            }
        }
    }

    return array_key_exists($env_key, $env_data) ? $env_data[$env_key] : $default;
}

/**
 * 用于实例化一个model对象
 * @param string $classname 模型名
 * @param string $m 模块
 * @return object
 */	
function M($classname, $m = ''){
	return ryphp::load_model($classname, $m);
}


/**
 * 用于临时屏蔽debug信息
 * @return null
 */	
function debug(){
	defined('RYPHP_DEBUG_HIDDEN') or define('RYPHP_DEBUG_HIDDEN', true);
}



/**
 * 用于设置模块的主题
 * @return null
 */	
function set_module_theme($theme = 'default'){
	defined('MODULE_THEME') or define('MODULE_THEME', $theme);
}


/**
 * 删除缓存
 * @param string $name 缓存名称
 * @param $flush 是否清空所有缓存
 * @return    bool
 */
function delcache($name, $flush = false) {
	ryphp::load_sys_class('cache_factory','',0);
	$cache = cache_factory::get_instance()->get_cache_instances();
	return !$flush ? $cache->delete($name) : $cache->flush();
}



/**
 * 模板调用
 * @param  string $module   模块名
 * @param  string $template 模板名称
 * @param  string $theme    强制模板风格
 * @return void           
 */
function template($module = '', $template = 'index', $theme = ''){
	if(!$module) $module = 'index';
	$template_c = RYPHP_ROOT.'cache'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
	$theme = !$theme ? (!defined('MODULE_THEME') ? C('site_theme') : MODULE_THEME) : $theme;
	$template_path = RYPHP_APP.$module.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR;
    $filename = $template.'.html';
	$tplfile = $template_path.$filename;   
	if(!is_file($tplfile)) {
		$template = RYPHP_DEBUG ? str_replace(RYPHP_ROOT, '', $tplfile) : basename($tplfile);
		showmsg($template.L('template_does_not_exist'), 'stop');			                      
	}	
	if(!is_dir(RYPHP_ROOT.'cache'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR)){
		@mkdir(RYPHP_ROOT.'cache'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR, 0777, true);
	}
	$template = basename($template).'_'.md5($template_path.$template);	
	$template_c = $template_c.$template.'.tpl.php'; 		
	if(!is_file($template_c) || filemtime($template_c) < filemtime($tplfile)) {
		$lry_tpl = ryphp::load_sys_class('lry_tpl');
		$compile = $lry_tpl->tpl_replace(@file_get_contents($tplfile));
		file_put_contents($template_c, $compile);
	}
	return $template_c;
}

/**
 * 下发队列任务
 * @param  string $job    队列任务类名称
 * @param  array  $params 传入的参数
 * @param  string $queue  队列名称
 * @return string|false   任务id
 */
function dispatch($job, $params = array(), $queue = ''){
    $res = ryphp::load_job($job, 0);
    if(!$res) return $res;

    $object = new $job($params);
    ryphp::load_sys_class('queue_factory','',0);

    $data = array(
        'uuid' => md5(create_randomstr()),
        'job' => $job,
        'object' => serialize($object),
        'attempts' => 0,
        'create_time' => SYS_TIME
    );
    queue_factory::get_instance()->lpush($queue ? $queue : trim(C('queue_name')), $data);
    return $data['uuid'];
}


/**
 * 根据请求方式自动返回信息
 * @param   $message 
 * @param   $status  
 * @param   $url  
 * @return  void           
 */
function return_message($message, $status = 1, $url = ''){
	$data = array('status'=>$status, 'message'=>$message);
	if($url) $data['url'] = $url;
	is_ajax() && return_json($data);
	showmsg($message, $url ? $url : ($status ? '' : 'stop'));
}



/**
 * 生成验证key
 * @param $prefix   前缀
 * @return string
 */
function make_auth_key($prefix) {
	return md5($prefix.RYPHP_ROOT.C('auth_key'));
}



/**
 * 记录日志
 * @param $message 日志信息
 * @param $filename 文件名称
 * @param $ext 文件后缀
 * @param $path 日志路径
 * @return bool
 */
function write_log($message, $filename = '', $ext = '.log', $path = '') {
	$message = is_array($message) ? new_json_encode($message, JSON_UNESCAPED_UNICODE) : $message;
	$message = date('Y-m-d H:i:s').' '.$message."\r\n";
	if(!$path) $path = RYPHP_ROOT.'cache/syslog';
	if(!is_dir($path)) @mkdir($path, 0777, true);
	
	if(!$filename) $filename = date('Ymd').$ext;
	
	return error_log($message, 3, $path.DIRECTORY_SEPARATOR.$filename);
}



/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    if (empty($msg))
        $msg    = '系统将在'.$time.'秒之后自动跳转到'.$url.'！';
    if (!headers_sent()) {
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header('refresh:'.$time.';url='.$url);
            echo($msg);
        }
        exit();
    } else {
        $str    = '<meta http-equiv="Refresh" content="'.$time.';URL='.$url.'">';
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 获取输入数据
 * @param string $key 获取的变量名
 * @param mixed $default 默认值
 * @param string $function 处理函数
 * @return mixed
 */
function input($key = '', $default = '', $function = ''){
	if ($pos = strpos($key, '.')) {
		list($method, $key) = explode('.', $key, 2);
		if (!in_array($method, array('get', 'post', 'request'))) {
			$key    = $method . '.' . $key;
			$method = 'param';
		}
	} else {
		$method = 'param';
	}

	$method = strtolower($method);

	if ($method == 'get') {
		return empty($key) ? $_GET : (isset($_GET[$key]) ? ($function ? $function($_GET[$key]) : $_GET[$key]) : $default);
	} elseif ($method == 'post') {
		$_POST = $_POST ? $_POST : (file_get_contents('php://input') ? json_decode(file_get_contents('php://input'), true) : array());
		return empty($key) ? $_POST : (isset($_POST[$key]) ? ($function ? $function($_POST[$key]) : $_POST[$key]) : $default);
	} elseif ($method == 'request') {
		return empty($key) ? $_REQUEST : (isset($_REQUEST[$key]) ? ($function ? $function($_REQUEST[$key]) : $_REQUEST[$key]) : $default);
	} elseif ($method == 'param') {
		$param = array_merge($_GET, is_array($_POST)?$_POST:array(), $_REQUEST);
		return empty($key) ? $param : (isset($param[$key]) ? ($function ? $function($param[$key]) : $param[$key]) : $default);
	} else {
		return false;
	}
}

/**
 * 这个函数 new_session_start() 是一个安全性增强的会话启动函数，用于替代 PHP 原生的 session_start()。
 * 以httponly方式开启SESSION
 * @return bool
 */
function new_session_start(){
	if(ini_get('session.auto_start')) return true;
	// session_save_path(RYPHP_PATH.'cache/sessions');
	ini_set('session.cookie_httponly', true);
	$session_name = session_name();
	if (isset($_COOKIE[$session_name]) && !preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $_COOKIE[$session_name])) {
        unset($_COOKIE[$session_name]);
    } 
	return session_start();
}


/**
 * 创建TOKEN，确保已经开启SESSION
 * @param bool $isinput 是否返回input
 * @return string
 */
function create_token($isinput = true){
	if(!isset($_SESSION['lry_sey_token'])) $_SESSION['lry_sey_token'] = create_randomstr(8);
	return $isinput ? '<input type="hidden" name="token" value="'.$_SESSION['lry_sey_token'].'">' : $_SESSION['lry_sey_token'];
}


/**
 * 验证TOKEN，确保已经开启SESSION
 * @param string $token 
 * @param bool $delete
 * @return bool
 */
function check_token($token, $delete=false){
	if(!$token || !isset($_SESSION['lry_sey_token']) || $token!=$_SESSION['lry_sey_token']) return false;
	if($delete) unset($_SESSION['lry_sey_token']);
	return true;
}