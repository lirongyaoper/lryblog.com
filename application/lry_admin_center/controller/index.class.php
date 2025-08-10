<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common',ROUTE_M,0);
class index extends common{

    public function init(){
        
        echo 'Welcome to the Admin Center!';
    }

    public function login(){
        if(is_post()){

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