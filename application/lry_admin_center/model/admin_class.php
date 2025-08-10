<?php
class admin{
    public function check_admin($adminname,$password){
        $admin = D('admin');
        $admin_login_log = D('admin_login_log');
        $loginip = getip();
        $res = $admin ->where(array('adminname'=>$adminname))->find();
        if(!$res){
            $admin_login_log -> insert(array(
                'adminname' => $adminname,
                'logintime' => SYS_TIME,
                'loginip' => $loginip,
                'password' => $_POST['password'],
                'loginresult' => 0,
                'cause' => L('user_does_not_exist')
            ));
            return array('status' => 0,'message'=>L('user_or_password_error'));
        }
        

    }
}