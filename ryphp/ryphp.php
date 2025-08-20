<?php
/**
 * RYPHP框架入口文件   
 * 
 * @author           李荣耀  
 * @license          http://www.lryper.com
 * @lastmodify       2016-09-19
 */

//设置系统的输出字符为utf-8
header("Content-Type: text/html; charset = utf-8");
//set timezone
date_default_timezone_set('Asia/Shanghai');

defined('RYPHP_ROOT') or exit('Access Denied!');

define('IN_RYPHP',true);

define('RYPHP_RYPHP', RYPHP_ROOT . 'ryphp' . DIRECTORY_SEPARATOR);
define('RYPHP_COMMON', RYPHP_ROOT . 'common' . DIRECTORY_SEPARATOR);

define('RYPHP_VERSION', '1.0.0');
define('RYPHP_RELEASE', '20250707');
//define time of system start
define('SYS_START_TIME',microtime(true));

//defime time
define('SYS_TIME', time());

//define dirctory of application
define('RYPHP_APP',RYPHP_ROOT.'application'.DIRECTORY_SEPARATOR);


ryphp::load_sys_func('global');

//主机协议
define('SERVER_PORT', is_ssl() ? 'https://' : 'http://');

//当前访问的主机名
define('HTTP_HOST',(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
//来源
define('HTTP_REFERER',(isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : ''));

define('EXT','.class.php');

//IS_CGI
define('IS_CGI', (0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
//这段代码的作用是：
//    检测PHP是否运行在CGI或FastCGI模式下
//    如果是，定义常量IS_CGI为1
//    如果不是，定义为0

if(IS_CGI){
	//CGI/FASTCGI模式下
    $_temp = explode('.php',$_SERVER['SCRIPT_NAME']);
    define('PHP_FILE',rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
}else{
    define('PHP_FILE', rtrim($_SERVER['SCRIPT_NAME'], '/'));
}


define('SITE_PATH',str_replace('index.php', '' , PHP_FILE));
define('SITE_URL',SERVER_PORT.HTTP_HOST.SITE_PATH);
define('STATIC_URL',SITE_URL.'common/static/');

if(version_compare(PHP_VERSION,'5.4.0','<') && function_exists('get_magic_quotes_gpc')) {
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
} elseif(version_compare(PHP_VERSION,'7.0.0','<')) {
    // PHP 5.4.0 - 6.x 版本
    define('MAGIC_QUOTES_GPC', false);
} else {
    // PHP 7.0+ 版本
    define('MAGIC_QUOTES_GPC', false);
}


//加载公用文件
ryphp::load_common('function/system.func.php');
ryphp::load_common('function/extention.func.php');
ryphp::load_common('data/version.php'); 
defined('RYCMS_SOFTNAME') or define('RYCMS_SOFTNAME', 'RYCMS内容管理系统');

class ryphp {
		
	/**
	 * 初始化应用程序
	 */
    public static function app_init(){
        return self::load_sys_class('application');
    }
	
	/**
	 * 加载系统的函数库
	 * @param string $func 函数库名
	 */
    public static function load_sys_func($func){
        if(is_file(RYPHP_RYPHP.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'function'.DIRECTORY_SEPARATOR.$func.'.func.php')){
            require_once(RYPHP_RYPHP.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'function'.DIRECTORY_SEPARATOR.$func.'.func.php');}
    }
		
	/**
	 * 加载系统类方法
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 * @return object or true
	 */
    public static function load_sys_class($classname,$path = '', $initialize =1){
       return self::_load_class($classname,$path,$initialize); 
    }
	
	/**
	 * 加载类文件函数
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
    private static function _load_class($classname,$path = '', $initialize = 1){
        static $classes = array();
        if(empty($path)) {
            $path = RYPHP_RYPHP . 'core' . DIRECTORY_SEPARATOR . 'class';
        }
        $key = md5($path.$classname);
        if(isset($classes[$key])){
            return $initialize && !is_object($classes[$key])  ? new $classname : $classes[$key];
        }
        $classfile = $path. DIRECTORY_SEPARATOR.$classname.EXT;
        if(!is_file($classfile)){
            debug::addmsg($path.DIRECTORY_SEPARATOR.$classname.EXT.L('does_not_exist'));
            return false;
        }
        include $classfile;
        if($initialize){
            $classes[$key] = new $classname;
            return $classes[$key];
        }else{
            $classes[$key] = true;
        }
        return $classes[$key];
    }
	
	/**
	 * 加载common目录下的文件
	 * @param string $path 文件地址（包括文件全称）
	 * @param string $m 模块(如果模块名为空，则加载根目录下的common)
	 */
    public static function load_common($path,$m = ''){
        if(empty($m)){
            if(is_file(RYPHP_ROOT.'common'.DIRECTORY_SEPARATOR.$path)){
                return include RYPHP_ROOT.'common'.DIRECTORY_SEPARATOR.$path;
            }else{
                debug::addmsg(RYPHP_RYPHP.'common'.DIRECTORY_SEPARATOR.$path.L('does_not_exist'));

            }
        }else{
			if (is_file(RYPHP_APP.$m.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.$path)) {
				return include RYPHP_APP.$m.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.$path;
			}else{
				debug::addmsg(RYPHP_APP.$m.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.$path.L('does_not_exist'));
			}          
        }
    }
	
	/**
	 * 加载控制器
	 * @param string $c 控制器名
	 * @param string $m 模块
	 * @param intger $initialize 是否初始化
	 * @return object or true
	 */
    public static function load_controller($c,$m='',$initialize = 1){
        $m  = empty($m) ? ROUTE_M : $m;
        if (empty($m)) return false;
        return self::_load_class($c,RYPHP_APP.$m.DIRECTORY_SEPARATOR.'controller',$initialize);
    }

	
	/**
	 * 加载模型
	 * @param string $classname 模型名
	 * @param string $m 模块
	 * @param intger $initialize 是否初始化
	 * @return object or true
	 */
	public static function load_model($classname, $m = '', $initialize = 1) {
		$m = empty($m) ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_class($classname, RYPHP_APP.$m.DIRECTORY_SEPARATOR.'model', $initialize);
	}
	
	/**
	 * 加载队列处理器
	 * @param string $classname 控制器名
	 * @param intger $initialize 是否初始化
	 * @return object or true
	 */
	public static function load_job($classname, $initialize = 1) {

		return self::_load_class($classname, RYPHP_RYPHP.'jobs', $initialize);
	}	

}

