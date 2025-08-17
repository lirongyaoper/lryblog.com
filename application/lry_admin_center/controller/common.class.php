<?php
defined('IN_RYPHP') or exit('Access Denied');
define('IN_ADMIN',true);
new_session_start();
class common {
    public static $siteid;
    public static $ip;
    public function __construct() {
        self::$siteid = get_siteid();
        self::$ip = getip();
        self::_check_admin();
    }

    /**
     * Check if the user is an admin
     * 1.iframe 检测: top.location !== self.location 判断当前页面是否在 iframe 中
     *  top.location: 顶层窗口的位置
     *  self.location: 当前窗口的位置
     *  如果两者不同，说明当前页面被嵌套在 iframe 中
     * 2.分情况重定向:
     *  - 在 iframe 中: 使用 top.location=url 让整个浏览器窗口跳转
     *  - 不在 iframe 中: 使用 window.location.href=url 进行常规跳转
     * 3.强制退出: exit() 确保 PHP 脚本执行完毕后立即停止
     */
    private static function _check_admin() {
        if(ROUTE_M == 'lry_admin_center' && ROUTE_C == 'index' && ROUTE_A =='login'){
            return true;
        }else{
            $adminid = intval(get_cookie('adminid'));
            if(!isset($_SESSION['adminid']) || !isset($_SESSION['roleid'])  || !$_SESSION['adminid'] || !$_SESSION['roleid'] || $adminid !=$_SESSION['adminid']){
                $loginUrl = U('lry_admin_center/index/login');
                echo "<script type='text/javascript'>
                        var url = '{$loginUrl}';
                        if(top.location !== self.location){
                            top.location = url;
                        }else{
                            window.location.href = url;
                        }
                    </script>";
				exit();
            }
        }
    }


    final public static function admin_tpl($file,$m = ''){
        if(empty($file)) return false;
        $m = empty($m)? ROUTE_M : $m;
        if(empty($m)) return false;
        return RYPHP_APP.$m.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$file.'.html';
    }
}