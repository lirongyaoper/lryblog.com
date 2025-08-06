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

