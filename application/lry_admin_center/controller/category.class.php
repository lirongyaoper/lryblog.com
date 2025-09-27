<?php
defined('IN_RYPHP') or exit('Access Denied');
ryphp::load_controller('common','lry_admin_center',0);
class category extends common{
    private $db;
    public function __construct(){
        parent::__construct();
        $this ->db = D('category');
    }

    /**
     * @author lirongyaoper
     * @description 分类列表
     */
    public function init(){
        $modelinfo = get_site_modelinfo();
        $modelarr = array();
        foreach($modelinfo as $val){
            $modelarr[$val['modelid']] = $val['name'];
        }
        //处理栏目展开/收起状态（第21-35行）
        $category_show_status= isset($_COOKIE['category_show_status_'.self::$siteid]) ? json_decode($_COOKIE['category_show_status_'.self::$siteid],true) : array();
        $tree_toggle = 0;
        $childid_hide = '';
        if($category_show_status) {
            foreach($category_show_status as $k =>$v){
                if($v == '1'){
                    $childid_hide .= get_category($k,'arrchildid',true).',';
                    $tree_toggle = 1;
                }else{
                    $tree_toggle = 0;
                }
            }
        }
        $arrchildid_arr = explode(',',$childid_hide);

        $tree = ryphp::load_sys_class('tree');
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $data = $this->db->field('catid AS id,catname AS name,parentid,cattype,modelid,listorder,member_publish,pclink,domain,display')->where(array('siteid'=>self::$siteid))->order('listorder ASC,catid ASC')->select();
        $array = array();
        foreach($data as $val){
            if($val['cattype'] =="0"){
                $string = 'lry_open_full("添加内容", "'.U('content/add', array('modelid'=>$val['modelid'],'catid'=>$val['id'])).'")'; 
                $val['catlink'] = "javascript:;' onclick='".$string;

            }elseif($val['cattype'] =="1"){
                $val['catlink'] = U('page_content',array('catid' => $val['id']));
            }else{
                $val['catlink'] = $val['pclink']."' target='_blank";
            }
            $icon = '&#xe653;';// 是 HTML 实体，表示一个 Unicode 字符。用于在分类树中切换展开/收起图标
            $action = '2';
            if($category_show_status && isset($category_show_status[$val['id']]) && $category_show_status[$val['id']] =='1'){
                $icon = '&#xe652;';
                $action = '1';
            }
            $show_status = in_array($val['id'],$arrchildid_arr) ? ' tr_hide' : '';
            $val['class'] = $val['parentid'] ? 'child'.$show_status : 'top';
			$val['parentoff'] = $val['parentid'] ? '' : '<i class="lry-iconfont parentid" catid="'.$val['id'].'" action="'.$action.'">'.$icon.'</i> ';
			$val['domain'] = $val['domain'] ? '<div title="绑定域名：'.$val['domain'].'" style="color:#0194ff;font-size:12px" class="lry-iconfont">&#xe64a; 域名</div>' : '';
			$val['cattype'] = $val['cattype']=="0" ? '普通栏目' : ($val['cattype']=="1" ? '<span style="color:green">单页面</span>' : '<span style="color:red">外部链接</span>');
			$val['catmodel'] = $val['modelid']&&isset($modelarr[$val['modelid']]) ? $modelarr[$val['modelid']] : '无';
			$val['display'] = $val['display'] ? '<span class="lry-status-enable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe601;</i>否</span>';
			$val['member_publish'] = $val['member_publish'] ? '<span class="lry-status-enable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe601;</i>否</span>';
			$val['string'] = '<a title="增加子类" href="javascript:;" onclick="lry_open(\'增加栏目\',\''.U('add',array('modelid'=>$val['modelid'],'type'=>$val['cattype'],'catid'=>$val['id'])).'\',800,500)" class="btn-mini btn-primary ml-5" style="text-decoration:none">增加子类</a> 
			<a title="编辑栏目" href="javascript:;" onclick="lry_open(\'编辑栏目\',\''.U('edit',array('type'=>$val['cattype'],'catid'=>$val['id'])).'\',800,500)" class="btn-mini btn-success ml-5" style="text-decoration:none">编辑</a> 
			<a title="删除" href="javascript:;" onclick="lry_confirm(\''.U('delete',array('type'=>$val['cattype'],'catid'=>$val['id'])).'\', \'确定要删除【'.$val['name'].'】吗？\', 1)" class="btn-mini btn-danger ml-5" style="text-decoration:none">删除</a>';   
            $array[] = $val;       
        }
        $str = "
                <tr class='text-c \$class'>
                    <td><input type='text' class='input-text listorder' name='listorder[]' value='\$listorder'><input type='hidden' name='catid[]' value='\$id'></td>
                    <td>\$i</td>
                    <td class='text-l'> \$parentoff\$spacer <a href='\$catlink'  class='lry_text_link'> \$name</a></td>
					<td>\$cattype</td>
					<td>\$catmodel</td>
					<td><a href='\$pclink' target='_blank'> <i class='lry-iconfont lry-iconlianjie'></i> 访问</a> \$domain</td>
					<td>\$display</td>
					<td>\$member_publish</td>
					<td class='td-manage'>\$string</td>                   
                </tr>
                ";
        $tree->init($array);
        $categorys = $tree->get_tree(0,$tree);
        include $this->admin_tpl('category_list');
    }





}