<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common',ROUTE_M,0);
class index extends common{

    public function init(){
        
        echo 'Welcome to the Admin Center!';
    }
}