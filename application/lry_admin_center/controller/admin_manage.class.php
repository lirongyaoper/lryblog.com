<?php

defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
ryphp::load_sys_class('page','',0);
class admin_manage extends common{
    /**
     * @author lirongyaoper
     * 管理员列表
     */
    public function init(){
        $of = input('get.of');
        $or = input('get.or');
        $of = in_array($of,array('adminid','adminname','realname','email','roleid','addtime','logintime','loginip','adpeople')) ? $of : 'adminid';
        $or = in_array($or,array('ASC','DESC')) ? $or : 'DESC';
        $roleid = isset($_GET['roleid']) ? intval($_GET['roleid']) :0;
        $where = $roleid ? array('roleid' => $roleid) : array();
        if(isset($_GET['dosubmit'])){
            $type = isset($_GET['type']) ? intval($_GET['type']) : 1;
            $searinfo = isset($_GET['searinfo']) ? safe_replace(trim($_GET['searinfo'])):'';
            if(isset($_GET['start']) && isset($_GET['end']) && $_GET['start']){
                $where['addtime>='] = strtotime($_GET['start']);
                $where['addtime<='] = strtotime($_GET['end']);
            }
            if($searinfo){
                if($type =='1'){
                    $where['adminname'] = '%'.$searinfo.'%';
                }else if($type == '2'){
                    $where['email'] = '%'.$searinfo.'%';
                }else if($type == '3'){
                    $where['realname'] = '%'.$searinfo.'%';
                }else{
                    $where['addpeople'] = '%'.$searinfo.'%';
                }
            }
        }
        $admin = D('admin');
        $total = $admin->where($where)->total();
        $page = new page($total, 15);
        $data = $admin->where($where)->order("$of $or")->limit($page->limit())->select();
        $role_data = D('admin_role')->field('roleid,rolename') ->where(array('disabled'=>0))->order('roleid ASC')->limit(100)->select();

        include $this->admin_tpl('admin_list');
    }
    /**
     * @author lirongyaoper
     */

     public function public_edit_info(){
        $adminid = $_SESSION['adminid'];
        if (isset($_POST['dosubmit'])){
            if($_POST['email'] && !is_email($_POST['email'])) return_json(array('status'=>0,'message'=>L('mail_format_error')));
            if(D('admin')->update(array('realname'=>$_POST['realname'],'nickname'=>$_POST['nickname'],'email' => $_POST['email']),array('adminid'=>$adminid),true)){
                $res= D('admin')->where(array('adminid'=>$adminid))->find();
                $_SESSION['admininfo'] = $res;
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json();
            }
        }else{
            $data = D('admin')->where(array('adminid' => $adminid))->find();
            include $this->admin_tpl('public_edit_info');
        }
     }
}