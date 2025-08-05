<?php


header("Content-Type: text/html; charset = utf-8");
//set timezone
date_default_timezone_set('Asia/Shanghai');

defined('RYPHP_ROOT') or exit('Access Denied!');

define('IN_RYPHP',true);

define('RYPHP_PATH', RYPHP_ROOT . 'ryphp' . DIRECTORY_SEPARATOR);
define('RYPHP_VERSION', '1.0.0');
define('RYPHP_RELEASE', '20250707');
//define time of system start
define('SYS_START_TIME',microtime(true));

//defime time
define('SYS_TIME', time());

//define dirctory of application
define('RYPHP_APP',RYPHP_ROOT.'application'.DIRECTORY_SEPARATOR);

ryphp::load_sys_func('global');


class ryphp {
    public static function load_sys_func($func){
        if(is_file(RYPHP_PATH.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'function'.DIRECTORY_SEPARATOR.$func.'.func.php')){
            require_once(RYPHP_PATH.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'function'.DIRECTORY_SEPARATOR.$func.'.func.php');}
    }


    
}

