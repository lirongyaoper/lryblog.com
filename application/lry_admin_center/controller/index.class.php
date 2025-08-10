<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common',ROUTE_M,0);
class index extends common{

    public function init(){
        
        echo 'Welcome to the Admin Center!';
    }

    public function login(){
        if(is_post()){
            if(empty($_SESSION['code']) || strtolower($_POST['code']) != $_SESSION['code']){
                $_SESSION['code'] = '';
                return_json(array('status'=>0,'message' =>L('code_error')));
            }
            $_SESSION['code'] = ''; //已验证结束，应该清除session验证码
            $_POST['username'] =trim($_POST['username']);
            if(!is_username($_POST['username'])){
                return_json(array('status'=>0,'message' =>L('user_name_format_error')));
            }
            if(!is_password($_POST['password'])){
                return_json(array('status'=>0,'message' =>L('password_format_error')));
            }
            $res = M('admin')->check_admin($_POST['username'],$_POST['password']);
        }else{
            $this->_login();
        }
    }











    private function _login(){
        ob_start();
        include $this ->admin_tpl('login');
        $data = ob_get_contents();
        ob_end_clean();
        echo $data.base64_decode('PCEtLSBQb3dlcmVkIEJ5ICBSWVBIUOWboumYnyAgLS0+');
        
    }





}