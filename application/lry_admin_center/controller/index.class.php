<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common',ROUTE_M,0);
ryphp::load_common('function/function.php', 'lry_admin_center');
class index extends common{

    public function init(){
        
        debug();
		$total = D('guestbook')->field('id')->where(array('replyid'=>0,'siteid'=> self::$siteid,'isread'=>'0'))->total();
		get_menu_list();
		Palry(get_menu_list());
		//include $this->admin_tpl('index');
		
    }

 
	/**
	 * 管理员登录
	 */	
	public function login() {
		if(is_post()) {
			if(empty($_SESSION['code']) || strtolower($_POST['code'])!=$_SESSION['code']){
				$_SESSION['code'] = '';
				return_json(array('status'=>0,'message'=>L('code_error')));
			}
			$_SESSION['code'] = '';
			$_POST['username'] = trim($_POST['username']);
			if(!is_username($_POST['username'])) return_json(array('status'=>0,'message'=>L('user_name_format_error')));
			if(!is_password($_POST['password'])) return_json(array('status'=>0,'message'=>L('password_format_error')));
			$res = M('admin')->check_admin($_POST['username'], password($_POST['password']));
			if($res['status']){
				return_json(array('status'=>1,'message'=>L('login_success'),'url'=>U('init')));
			}else{
				return_json($res);
			}
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