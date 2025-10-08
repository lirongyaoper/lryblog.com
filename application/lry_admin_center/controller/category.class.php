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
                    <td>\$id</td>
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
        $categorys = $tree->get_tree(0, $str);
        include $this->admin_tpl('category_list');
    }

    /**
     * @author lirongyaoper
     * document: order
     * 
     */
    public function order(){
        if(isset($_POST['catid']) && is_array($_POST['catid'])){
            foreach($_POST['catid'] as $key => $val){
                $this->db->update(array('listorder' => $_POST['listorder'][$key]),array('catid' => intval($val)));
            }
            $this->delcache();

        }
        showmsg(L('operation_success'),'',1);
    }

    /**
     * 
     * @author lirongyaoper
     * description: add category
     * 
     */
    public function add(){
        $modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : get_default_model('modelid');
        $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        $type= isset($_GET['type']) ? intval($_GET['type']) : intval($_POST['type']);
        if(isset($_POST['dosubmit'])){
            if($_POST['domain']) $this->set_domain();
            $_POST['catname'] = trim($_POST['catname']);
            $_POST['catdir'] = trim($_POST['catdir'],' /'); 
            if($type !=2){ // no external link
                $res = $this->db->where(array('siteid' => self::$siteid,'catdir' => $_POST['catdir']))->find();
                if($res) return_json(array('status' => 0,'message' =>'该栏目已存在，请重新填写！'));
            }
            if(!$_POST['mobname']) $_POST['mobname'] = $_POST['catname'];
            if($_POST['parentid']=='0'){
                $_POST['arrparentid'] = '0';
            }else{
                $data = $this->db->field('arrparentid, arrchildid,domain')->where(array('catid' => $_POST['parentid']))->find();
                $_POST['arrparentid'] = $data['arrparentid'].','.$_POST['parentid'];// 父级路径
            }
            $_POST['siteid'] = self::$siteid;
            $_POST['arrchildid'] = '';
            $catid = $this->db->insert($_POST,true);
            Palry($catid);
            if($type != 2){ // no external link
                if($type == 1){ //single page
                    $arr = array();
                    $arr['catid'] = $catid;
                    $arr['title'] = $_POST['catname'];
                    $arr['description'] = $_POST['seo_description'];
                    $arr['content'] = '';
                    $arr['updatetime'] = SYS_TIME;
                    D('page')->insert($arr,false,false);
                }
                $domain = isset($data['domain']) ? $data['domain'] : '';
                $_POST['pclink'] = isset($_POST['domain']) && !empty($_POST['domain']) ? $_POST['domain'] : $this->get_category_url($domain,$_POST['catdir']);

            }



        }else{
            $modelinfo = get_site_modelinfo();
            $parent_temp = $this ->db ->field('category_template,list_template,show_template,pclink')->where(array('catid' =>$catid))->find();
            $parent_dir = $parent_temp ? str_replace(SITE_URL, '',$parent_temp['pclink']) : '';

            if($type == 0){
                $default_model = $modelid ? get_model($modelid,false) : get_default_model();
                    // Palry($default_model);
                    // Array(14) [
                    //     modelid => 1
                    //     siteid => 0
                    //     name => '文章模型'
                    //     tablename => 'article'
                    //     alias => 'article'
                    //     description => '文章模型'
                    //     setting => ''
                    //     inputtime => 1466393786
                    //     items => 0
                    //     disabled => 0
                    //     type => 0
                    //     sort => 0
                    //     issystem => 1
                    //     isdefault => 1                   
                    // ]
                $category_temp = $this->select_template('category_temp','category_',$default_model);
                $list_temp = $this->select_template('list_temp','list_',$default_model);
                $show_temp = $this->select_template('show_temp','show_',$default_model);
                $tablename = $default_model ? $default_model['alias'] : '模型别名';
                include $this->admin_tpl('category_add');

            }else if ($type == 1){
                $page_data = D('model') ->field('modelid,alias')->where(array('type' => 2)) ->order('modelid ASC')->find();
                $alias = $page_data ? $page_data['alias'] : 'page';
                $category_temp = $this->select_template('category_temp','category_',$alias);
                $tablename = $alias;
                include $this->admin_tpl('category_page');
            }else{
                include $this->admin_tpl('category_link');
            }
        }
    }

    /**
     * @author lirongyaoper
     * description: delete
     * 
     */
    public function delete(){
        $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $data = $this->db->field('arrparentid, arrchildid')->where(array('catid' => $catid))->find();
        if(strpos($data['arrchildid'],',')){
            return_json(array('status' => 0, 'message' => '该分类下有子栏目，请先删除子栏目后再进行此操作！'));
        }
        $allcontent = D('all_content')->field('allid') -> where(array('catid'=>$catid))->one();
        if($allcontent) return_json(array('status' => 0, 'message' => '该分类下有内容，请先删除内容或转移内容后再进行此操作！'));
        if($this->db->delete(array('catid' =>$catid))){
            if($type ==1) D('page')->delete(array('catid' => $catid));
            $this->repairs($data['arrparentid']);
            $this->delcache();
            return_json(array('status' =>1, 'message' => L('operation_success')));
        }else{
            return_json(array('status' =>0, 'message' => L('operation_fail')));
        }
        
    }





    /**
     * @author lirongyaoper
     *  clear cache of category
     */
    private function delcache(){
        $site_mapping = self::$siteid ? 'site_mapping_site_'.self::$siteid : 'site_mapping_index_'.self::$siteid;
        delcache('categoryinfo');
        delcache('categoryinfo_siteid_'.self::$siteid);
        delcache($site_mapping);
    }

    /**
     * @author lirongyaoper
     * description: select template
     * 
     */
    private function select_template($style,$pre='',$model = null){
        if(!$model) return array();
        $site_theme   = self::$siteid ? get_site(self::$siteid,'site_theme') : C('site_theme');
        $tablename = is_array($model) ? $model['alias'] : $model;//article
        $pre = $model ? $pre.$tablename : $pre;//category_article
        $files = glob(RYPHP_APP.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$site_theme.DIRECTORY_SEPARATOR.$pre.'*.html');
            // array(
            //     0 => '/home/.../application/index/view/rongyao/category_article.html',
            //     1 => '/home/.../application/index/view/rongyao/category_article_list.html',
            //     2 => '/home/.../application/index/view/rongyao/category_article_default.html'
            // )
        $files = @array_map('basename',$files);
            // array(
            //     0 => 'category_article.html',
            //     1 => 'category_article_list.html',
            //     2 => 'category_article_default.html'
            // )
        $templates = array();
        $tem_style = RYPHP_APP.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$site_theme.DIRECTORY_SEPARATOR.'config.php';
        $templates_style  = is_file($tem_style) ? require($tem_style) : array();
        $templates_style = $templates_style ? $templates_style[$style] : $templates_style;
        if(is_array($files)){
            foreach($files as $file){
                $key = substr($file, 0, -5);
                $templates[$key] = isset($templates_style[$key]) ? $templates_style[$key] : $file;

            }
        }
        return $templates;
    }

    private function repairs(){}











    public function public_category_template(){
        $modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 1;
        $default_model = $modelid ? get_model($modelid,'alias') : 'page';
        $data = array(
            'category_template' => $this->select_template('category_temp','category_',$default_model),
            'list_template' => $this->select_template('list_temp','list_',$default_model),
            'show_template' => $this->select_template('show_temp','show_',$default_model),
            'tablename' => $default_model
        );
        return_json($data);
    }

    private function get_category_url(){
        return 1;
    }
    private function set_domain(){
        return_json(array('status' => 0, 'message' =>''));
    }


}