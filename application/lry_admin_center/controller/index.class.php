<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common',ROUTE_M,0);
class index extends common{

    public function init(){
        
        //debug();
		$total = D('guestbook')->field('id') ->where(array('replyid'=>0,'siteid'=>self::$siteid, 'isread'=>'0'))->total();

		include $this->admin_tpl('index');
		
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


	/**
	 * @author: lirongyaoper
	 * 推出后台登录
	 * 
	 */
	public function public_logout(){
		unset($_SESSION['adminid'],$_SESSION['adminname'],$_SESSION['roleid'],$_SESSION['admininfo'],$_SESSION['lry_sey_token']);
		del_cookie('adminid');
		del_cookie('adminname');
		showmsg(L('you_have_safe_exit'),U('login'),1);
	}



	/**
	 * @author: lirongyaoepr
	 */
	public function public_lock_screen(){
		$_SESSION['lry_lock_screen'] = 1;
		return_json(array('status' => 1,'message'=> L('operation_success')));
	}

	/**
	 * @author: lirongyaoepr
	 * 解锁屏幕
	 * 
	 * @return json
	 */
	public function public_unlock_screen(){
		$res = M('admin')->check_admin($_SESSION['adminname'],password($_POST['password']));
		if(!$res['status']) return_json($res);
		$_SESSION['lry_lock_screen'] = 0;
		return_json(array('status'=>1,'message' =>L('login_success')));
	}

	/**
	 * @author: lirongyaoepr
	 */
	public function public_home(){
		debug();
		//系统自动更新功能暂时省略
		//...
		//...
		//...
		$tpl = RYPHP_APP.'lry_admin_center'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'public_home.html';
		if(!is_file($tpl)) $this->_force_logout();
		$html = file_get_contents($tpl);
		if(!strpos($html,'RYCMS') || !strpos($html,'lryblog.com')){
			$this->_force_logout();
		}


		//统计信息
		ob_start();
		$count = array();
		$count[] = D('all_content')-> where(array('siteid' => self::$siteid))->total();
		$count[] = D('admin')->total();
		$count[] = D('member')->total();
		$count[] = D('module')->total();

		
		include $this->admin_tpl('public_home');
		$data = ob_get_contents();
		ob_end_clean();
		echo $data;

	}

	

}