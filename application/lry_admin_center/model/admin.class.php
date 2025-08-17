<?php
class admin {
	
	public function check_admin($adminname, $password) {
		$admin = D('admin');
		$admin_login_log = D('admin_login_log');
		$loginip = getip();

		$res = $admin->where(array('adminname' => $adminname))->find();
		if (!$res) {
			$this->logLoginAttempt($admin_login_log, $adminname, $loginip, $_POST['password'], '0', L('user_does_not_exist'));
			return array('status' => 0, 'message' => L('user_or_password_error'));
		} 

		// 检查账户锁定状态
		$lockCheck = $this->checkAccountLock($res, $admin_login_log, $adminname);
		if ($lockCheck) {
			return $lockCheck;
		}

		// 验证密码
		if ($password == $res['password']) {
			return $this->handleSuccessfulLogin($admin, $admin_login_log, $res, $adminname, $loginip);
		} else {
			return $this->handleFailedLogin($admin, $admin_login_log, $res, $adminname, $loginip);
		}
	}

	private function logLoginAttempt($admin_login_log, $adminname, $loginip, $password, $result, $cause) {
		$admin_login_log->insert(array(
			'adminname' => $adminname,
			'logintime' => SYS_TIME,
			'loginip' => $loginip,
			'password' => $password,
			'loginresult' => $result,
			'cause' => $cause
		));
	}

	private function checkAccountLock($res, $admin_login_log, $adminname) {
		if ($res['errnum'] < 5) {
			return false;
		}

		$limit_arr = array(5 => 300, 8 => 600, 12 => 1800);
		$limit_time = isset($limit_arr[$res['errnum']]) ? $limit_arr[$res['errnum']] : 0;
		
		if (!$limit_time) {
			return false;
		}

		$last_time = $admin_login_log->field('logintime')
			->where(array('adminname' => $adminname, 'loginresult' => 0))
			->order('id DESC')->one();
			
		if (SYS_TIME - $last_time < $limit_time) {
			$wait_minutes = ceil(($last_time + $limit_time - SYS_TIME) / 60);
			return array(
				'status' => 0,
				'message' => L('login_too_many') . $wait_minutes . L('minute_try_again')
			);
		}
		
		return false;
	}

	private function handleSuccessfulLogin($admin, $admin_login_log, $res, $adminname, $loginip) {
		$admin->update(
			array('loginip' => $loginip, 'logintime' => SYS_TIME, 'errnum' => 0), 
			array('adminid' => $res['adminid'])
		);
		
		$this->logLoginAttempt($admin_login_log, $adminname, $loginip, '', '1', L('login_success'));
		
		$_SESSION['adminid'] = $res['adminid'];
		$_SESSION['adminname'] = $res['adminname'];
		$_SESSION['roleid'] = $res['roleid'];
		$_SESSION['admininfo'] = $res;	
		$_SESSION['lry_sey_token'] = create_randomstr(8);	
		$_SESSION['lry_lock_screen'] = 0;			
		
		set_cookie('adminid', $res['adminid'], 0, true);						
		set_cookie('adminname', $res['adminname'], 0, true);						
		
		return array('status' => 1, 'message' => L('login_success'));
	}

	private function handleFailedLogin($admin, $admin_login_log, $res, $adminname, $loginip) {
		$this->logLoginAttempt($admin_login_log, $adminname, $loginip, $_POST['password'], '0', L('password_error'));
		
		$update = $res['errnum'] >= 12 ? '`errnum` = 0' : '`errnum` = `errnum`+1';
		$admin->update($update, array('adminid' => $res['adminid']));
		
		return array('status' => 0, 'message' => L('user_or_password_error'));
	}
}