<?php
defined('IN_RYPHP') or exit('Access Denied');
define('IN_ADMIN',true);
class common {
    public static $siteid;
    public static $ip;
    public function __construct() {
        self::$siteid = get_siteid();
        self::$ip = getip();
        self::check_admin();
    }

    public static function check_admin() {
        if(ROUTE_M == 'lry_admin_center' && ROUTE_C == 'index' && ROUTE_A =='login'){
            return true;
        }else{
            exit('Access Denied,buyaoyisi-ryphp');
        }
    }


    final public static function admin_tpl($file,$m = ''){
        if(empty($file)) return false;
        $m = empty($m)? ROUTE_M : $m;
        if(empty($m)) return false;
        return RYPHP_APP.$m.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$file.'.html';
    }
}