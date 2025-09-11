<?php


class application{
    /**
     * Constructor for the application class
     * Initializes the debug class and sets up error handling
     */
    public function __construct(){
        ryphp::load_sys_class('debug','',0);
        register_shutdown_function(array('debug','fatalerror'));
        set_error_handler(array('debug','catcher'));
        set_exception_handler(array('debug','exception'));
        $param = ryphp::load_sys_class('param');
        define('ROUTE_M',$param -> route_m());
        define('ROUTE_C',$param -> route_c());
        define('ROUTE_A',$param -> route_a());
        $this->init();
    }

    /**
     * 初始化应用程序
     */
    public function init(){
        $controller = $this->_load_controller();
        if(method_exists($controller, ROUTE_A)){
            if(substr(ROUTE_A, 0, 1) == '_'){
                self::halt('This action is inaccessible.');
            }else{
                call_user_func(array($controller,ROUTE_A));
                if(RYPHP_DEBUG){
                    debug::stop();
                    if(!defined('RYPHP_DEBUG_HIDDEN')) debug::message();
                }

            }
        }else{
            self::halt('Action does not exist: '.ROUTE_A);
        }
    }

	/**
	 * 加载控制器
	 * @param string $filename
	 * @param string $m
	 * @return obj
	 */
    private function _load_controller($filename = '',$m = ''){
        if(empty($filename)) $filename = ROUTE_C;
        if(empty($m)) $m = ROUTE_M;
        $path = RYPHP_ROOT . 'application' . DIRECTORY_SEPARATOR . $m ;
        if(!is_dir($path)) self:: halt('module does not exist:'. $m);
        $filepath = $path . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $filename . EXT;
        if(is_file($filepath)){
            include $filepath;
            if(class_exists($filename)){
                return new $filename;
            }else{
                self::halt('Controller class does not exist: ' . $filename);
            }

        }else{
            self::halt('Controller file does not exist: ' . $filepath);
        }
    }

	

	/**
	 *  提示信息页面跳转
	 *
	 * @param     string  $msg      消息提示信息
	 * @param     string  $gourl    跳转地址
	 * @param     int     $limittime  限制时间
	 * @return    void
	 */
	public static function showmsg($msg, $gourl, $limittime) {
		$gourl = empty($gourl) ? (strpos(HTTP_REFERER, SITE_URL)!==0 ? SITE_URL : htmlspecialchars(HTTP_REFERER, ENT_QUOTES, 'UTF-8')) : htmlspecialchars($gourl, ENT_QUOTES, 'UTF-8');
		$stop = $gourl!='stop' ? false : true;
		include(RYPHP_RYPHP.'core'.DIRECTORY_SEPARATOR.'message'.DIRECTORY_SEPARATOR.'message.tpl');
	}



	/**
	 * 打开调式模式情况下, 输出致命错误
	 *
	 * @param     string  $msg      提示信息
	 * @param     string  $detailed	详细信息
	 * @param     string  $type     错误类型 1:php 2:mysql
	 * @return    void
	 */
	public static function fatalerror($msg, $detailed = '', $type = 1) {
		if(ob_get_length() !== false) @ob_end_clean();
		include(RYPHP_RYPHP.'core'.DIRECTORY_SEPARATOR.'message'.DIRECTORY_SEPARATOR.'error.tpl');
		exit();
	}

	
	/**
	 *  输出错误提示
	 *
	 * @param     string  $msg      提示信息
	 * @param     int     $code     状态码
	 * @return    void
	 */

    public static function halt($msg,$code =404){
        if(ob_get_length() !== false) @ob_end_clean();
        if(!RYPHP_DEBUG) send_http_status($code);
        $tpl = is_file(RYPHP_ROOT.C('error_page')) && ! RYPHP_DEBUG ? RYPHP_ROOT.C('error_page') : RYPHP_RYPHP.'core'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'halt.tpl';
        include $tpl;
        exit();

    }


}