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

    /**
     * @author lirongyaoper
     * @description 分类列表页面初始化方法
     * 该方法负责：
     * 1. 获取所有模型信息并构建模型ID到名称的映射
     * 2. 处理分类树的展开/收起状态（基于Cookie）
     * 3. 从数据库查询当前站点的所有分类数据
     * 4. 为每个分类构建详细的展示信息（链接、图标、操作按钮等）
     * 5. 使用树形结构类生成分类树HTML
     * 6. 渲染分类列表模板
     */
    public function init(){
        $modelinfo = get_site_modelinfo();
        $modelarr = array();
        foreach($modelinfo as $val){
            $modelarr[$val['modelid']] = $val['name'];
        }
        
        $category_show_status = isset($_COOKIE['category_show_status_'.self::$siteid]) ? json_decode($_COOKIE['category_show_status_'.self::$siteid], true) : array();
        $tree_toggle = 0;
        $childid_hide = '';

        /**
         * Array(3) [
         *       1 => '2'
         *       4 => '2'
         *       5 => '2'

         *   ]
         * 当$v=1时，表示分类为收起状态，子分类隐藏
         * 当$v=2时，表示分类为展开状态，子分类显示
         * 
         */
        //Palry($category_show_status);
        if($category_show_status) {

            foreach($category_show_status as $k => $v){
                if($v == '1'){
                    $childid_hide .= get_category($k, 'arrchildid', true).',';    //1,2,3,4,5,'
                    $tree_toggle = 1;
                }else{
                    $tree_toggle = 0;
                }
            }
        }
        //Palry($childid_hide);
        $arrchildid_arr = explode(',', $childid_hide);

        $tree = ryphp::load_sys_class('tree');
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $data = $this->db->field('catid AS id,catname AS name,parentid,cattype,modelid,listorder,member_publish,pclink,domain,display')
            ->where(array('siteid' => self::$siteid))
            ->order('listorder ASC,catid ASC')
            ->select();
        //Palry($data);
        $array = array();
        foreach($data as $val){
            if($val['cattype'] == "0"){
                $string = 'lry_open_full("添加内容", "'.U('content/add', array('modelid' => $val['modelid'], 'catid' => $val['id'])).'")';
                $val['catlink'] = "javascript:;' onclick='".$string;
            
            }elseif($val['cattype'] == "1"){
                $val['catlink'] = U('page_content', array('catid' => $val['id']));
            }else{
                $val['catlink'] = $val['pclink']."' target='_blank";
            }
            
            $icon = '&#xe653;';
            $action = '2';
            
            if($category_show_status && isset($category_show_status[$val['id']]) && $category_show_status[$val['id']] == '1'){
                $icon = '&#xe652;';
                $action = '1';
            }
            
            $show_status = in_array($val['id'], $arrchildid_arr) ? ' tr_hide' : '';
            //如果$val['parentid'] =0, 表示该分类为顶级分类，否则为子分类
            $val['class'] = $val['parentid'] ? 'child'.$show_status : 'top';
            
            $val['parentoff'] = $val['parentid'] ? '' : '<i class="lry-iconfont parentid" catid="'.$val['id'].'" action="'.$action.'">'.$icon.'</i> ';
            
            $val['domain'] = $val['domain'] ? '<div title="绑定域名：'.$val['domain'].'" style="color:#0194ff;font-size:12px" class="lry-iconfont">&#xe64a; 域名</div>' : '';
            
            $val['cattype'] = $val['cattype'] == "0" ? '普通栏目' : ($val['cattype'] == "1" ? '<span style="color:green">单页面</span>' : '<span style="color:red">外部链接</span>');
            
            $val['catmodel'] = $val['modelid'] && isset($modelarr[$val['modelid']]) ? $modelarr[$val['modelid']] : '无';
            
            $val['display'] = $val['display'] ? '<span class="lry-status-enable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')" ><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')" ><i class="lry-iconfont">&#xe601;</i>否</span>';
            
            $val['member_publish'] = $val['member_publish'] ? '<span class="lry-status-enable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')" ><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')" ><i class="lry-iconfont">&#xe601;</i>否</span>';
            
            $val['string'] = '<a title="增加子类" href="javascript:;" onclick="lry_open(\'增加栏目\',\''.U('add', array('modelid' => $val['modelid'], 'cattype' => $val['cattype'], 'catid' => $val['id'])).'\',800,500)" class="btn-mini btn-primary ml-5" style="text-decoration:none">增加子类</a> 
			<a title="编辑栏目" href="javascript:;" onclick="lry_open(\'编辑栏目\',\''.U('edit', array('cattype' => $val['cattype'], 'catid' => $val['id'])).'\',800,500)" class="btn-mini btn-success ml-5" style="text-decoration:none">编辑</a> 
			<a title="删除" href="javascript:;" onclick="lry_confirm(\''.U('delete', array('cattype' => $val['cattype'], 'catid' => $val['id'])).'\', \'确定要删除【'.$val['name'].'】吗？\', 1)" class="btn-mini btn-danger ml-5" style="text-decoration:none">删除</a>';   
            
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
     * 
     * @author lirongyaoper
     * description: add category
     * 
     */
    public function add(){
        $modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : get_default_model('modelid');
        //   从 $_GET['catid'] 里取父栏目 ID（如果是“增加子类”，就会有父栏目）。  没有传就默认 0，表示新增的是顶级栏目。
        $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        //  $type 是栏目类型（0 普通栏目，1 单页面，2 外部链接）。。优先从 $_GET['type'] 取（URL 上可能带着），否则从 $_POST['type']（表单提交）取。
        $type= isset($_GET['cattype']) ? intval($_GET['cattype']) : intval($_POST['cattype']);
        if(isset($_POST['dosubmit'])){
            if($_POST['domain']) $this->set_domain();
            $_POST['catname'] = trim($_POST['catname']);
            $_POST['catdir'] = trim($_POST['catdir'],' /'); 
            //如果 $type != 2，说明 不是外部链接栏目（普通栏目或单页栏目），要在本站生成访问路径。
            if($type !=2){ 
                // 检查栏目目录是否已存在
                $res = $this->db->where(array('siteid' => self::$siteid,'catdir' => $_POST['catdir']))->find();
                //如果$res不为空，说明已存在同名的栏目名称，应停止后续执行。
                if($res) return_json(array('status' => 0,'message' =>'该栏目已存在，请重新填写！'));
            }
            // 如果没有填写移动设备名称，就自动使用 PC 端的栏目名称 catname。
            if(!$_POST['mobname']) {
                $_POST['mobname'] = $_POST['catname'];
            }
            /**
             *   如果 parentid == '0'，说明这是一个顶级栏目。
             *   顶级栏目的 arrparentid 就写成 '0'，表示没有上级，只以 0 作为根。
             */            
            if($_POST['parentid']=='0'){
                $_POST['arrparentid'] = '0';
            }else{
                /**
                 *   如果 parentid != '0'，说明它是某个栏目的子栏目
                 *   先根据 parentid 从数据库取出父栏目记录，拿到父栏目的 arrparentid、arrchildid、domain 等。
                 *   将子栏目的 arrparentid 设置为：父栏目的 arrparentid 加上父栏目自己的 catid，中间用逗号拼接。
                 *   举例：
                 *      父栏目 arrparentid = 0，catid = 5
                 *      → 子栏目 arrparentid = 0,5  (父栏目 arrparentid + 父栏目 catid)
                 *      父栏目 arrparentid = 0,5，catid = 8
                 *      → 子栏目 arrparentid = 0,5,8
                 *      父栏目 arrparentid = 0,5,8，catid = 10
                 *      → 子栏目 arrparentid = 0,5,8,10
                 *     这样可以很快算出一个节点的整条父级路径。
                 */
                
                $data = $this->db->field('arrparentid, arrchildid,domain')->where(array('catid' => $_POST['parentid']))->find();
                $_POST['arrparentid'] = $data['arrparentid'].','.$_POST['parentid'];// 父级路径
            }
            //  把当前站点 ID 填入表单数据：siteid = 当前后台站点id。  支持多站点时就靠这个区分。
            $_POST['siteid'] = self::$siteid;
            /**
             *   新增的栏目暂时还没有子栏目，所以 arrchildid 先设为空字符串。
             *   后面新增完本条记录后，会通过
             * {$this->db->update(array('arrchildid' => $catid, 'pclink' => $_POST['pclink']), array('catid' => $catid));} 
             * 代码行再更新 arrchildid。
             */
            $_POST['arrchildid'] = '';
            /**
             *  把整套 $_POST 数据插入 category 表。
             *  第二个参数 true 一般表示“返回主键 ID”。
             *  完后返回的新栏目 ID 存在 $catid。
             */
            $catid = $this->db->insert($_POST,true);
            /**
             * 如果 $type != 2，说明 不是外部链接栏目：
             *     普通栏目（0）
             *     单页面栏目（1）
             *   这两类栏目会在本系统中有实际内容和真实访问地址，需要做额外处理。
             */
            if($type != 2){ 
                /**
                 *   如果 $type == 1，说明是单页面栏目。
                 *   需要向 page 表插入一条记录。
                 */
                if($type == 1){ //single page
                    $arr = array();
                    $arr['catid'] = $catid; //对应刚刚插入的栏目 ID。
                    $arr['title'] = $_POST['catname'];//    title：用栏目的 catname 作为页面标题。
                    $arr['description'] = $_POST['seo_description'];
                    $arr['content'] = '';
                    $arr['updatetime'] = SYS_TIME;
                    D('page')->insert($arr,false,false);
                }
                $domain = isset($data['domain']) ? $data['domain'] : '';
                $_POST['pclink'] = isset($_POST['domain']) && !empty($_POST['domain']) ? $_POST['domain'] : $this->get_category_url($domain,$_POST['catdir']);

            }

            $this->db->update(array('arrchildid' => $catid, 'pclink' => $_POST['pclink']), array('catid' => $catid));
            if($_POST['parentid'] != '0') $this->repairs($_POST['arrparentid']);
            if($_POST['domain']) $this-> set_domain();
            $this->delcache();
            return_json(array('status' => 1, 'message' => L('operation_success')));

        }else{
            $modelinfo = get_site_modelinfo();
            $parent_temp = $this ->db ->field('category_template,list_template,show_template,pclink')->where(array('catid' =>$catid))->find();
            //Palry($parent_temp);
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

            }else if ($type == 1){ // 单页面
                //  $type 是栏目类型（0 普通栏目，1 单页面，2 外部链接）。。优先从 $_GET['type'] 取（URL 上可能带着），否则从 $_POST['type']（表单提交）取。
                //model表中 type(0=>文章模型,产品模型,下载模型 ,2=>单页模型)
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


    public function adds(){
        $modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : get_default_model('modelid');        
        $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        if(isset($_POST['dosubmit'])) {
            //如果有提交表单，就进行数据处理
            $type = isset($_POST['cattype']) ? intval($_POST['cattype']) : 0;
            $catnames = explode('\r\n',$_POST['catnames']);
            if($_POST['parentid'] == '0'){
                // 如果 parentid == '0'，说明这是一个顶级栏目。
                $_POST['arrparentid'] = '0';
            }else{
                $data = $this->db->field('arrparentid,arrchildid,domain')->where(array('catid' =>$_POST['parentid']))->find();
                $_POST['arrparentid'] = $data["arrparentid"].','.$_POST['parentid'];
            }
            foreach($catnames as $key => $val){
                if(!$val) continue;
                if(strpos($val,'|')){
                    list($_POST['catname'],$_POST['catidr']) = explode('|',$val);
                }
                $_POST['catname'] = trim($_POST['catname']);
                $_POST['catidr'] = trim($_POST['catidr']);
                // 检查 catdir 是否已存在, 如果存在则跳过
                $res = $this -> db -> field('catid') ->where(array('siteid' => self::$siteid, 'catdir' => $_POST['catdir'])) -> one();
                if($res) continue;
                $_POST['mobname'] = $_POST['catname'];
                $_POST['siteid'] = self::$siteid;
                $_POST['arrchildid'] = '';
                $catid = $this->db->insert($_POST,true);

                if($type == 1){
                    $arr = array();
                    $arr['catid'] = $catid;
                    $arr['title'] = $_POST['catname'];
                    $arr['updatetime'] = SYS_TIME;
                    D('page')->insert($arr,false,false);
                }

                $domain = isset($data['domain']) ? $data['domain'] : '';
                $_POST['pclink'] = $this->get_category_url($domain,$_POST['catdir']);
                $this-> db ->update(array('arrchildid' => $catid, 'pclink'=> $_POST['pclink']),array('catid' => $catid));
                if($_POST['parentid'] != '0') $this->repairs($_POST['arrparentid']);
                
            }
            $this->delcache();
            return_json(array('status' => 1, 'message' =>L('operation_success')));

        }else{
            //如果没有提交表单，就进行页面显示
            $modelinfo = get_site_modelinfo();
            $default_model = get_default_model();
            $category_temp = $this->select_template('category_temp','category_',$default_model);
            $list_temp = $this->select_template('list_temp','list_',$default_model);
            $show_temp = $this->select_template('show_temp','show_',$default_model);
            $parent_temp = $this->db->field('category_template,list_template,show_template,pclink')->where(array('cateid'=>$catid))->find();
            $parent_dir = $parent_temp ? str_replace(SITE_URL, '', $parent_temp['pclink']) : '';
            $tablename = $default_model ? $default_model['alias'] : '模型别名';
            include $this->admin_tpl('category_adds');

        }

    
    }

    public function edit(){
        if(isset($_POST['dosubmit'])) {
            /**
             * 如果有提交表单，就进行数据处理
             */

            if($_POST['domain']) $this-> set_domain();
            $catid = isset($_POST['catid']) ? strval(intval($_POST['catid'])) : 0;
            $_POST['catname'] = trim($_POST['catname']);
            $_POST['catdir'] = trim($_POST['catdir'],'/');

            if($_POST['parentid']== '0'){
                $_POST['arrparentid'] = '0';

            }else{
                $data = $this->db->field('arrparentid,arrchildid,domain') -> where(array('catid' =>$_POST['parentid']))->find();
                if(strpos($data['arrparentid'],$catid) !== false || $_POST['parentid']== $catid) return_json(array('status' => 0, 'message' => '不能将类别移动到自己或自己的子类别中！'));
                $_POST['arrparentid'] =$data['arrparentid'].','.$_POST['parentid'];
            }

            if($_POST['arrparentid'] != $_POST['cpath']){
                $_POST['cpath'] = safe_replace($_POST['cpath']);
                $_POST['arrparentid'] = safe_replace($_POST['arrparentid']);
                $cpath = $_POST['cpath'].','.$catid;
                $this->db->query("UPDATE '{C('TABLE_PREFIX')}'category SET arrparentid = REPLACE(arrparentid, '{$_POST['cpath']}','{$_POST['arrparentid']}')  WHERE arrparentid LIKE '{$cpath}%' ");
            }
            //
            if($_POST['cattype'] < 2){
                $domain = isset($data['domain']) ? $data['domain'] : '';
                $_POST['pclink'] = isset($_POST['domain']) && !empty($_POST['domain']) ? $_POST['domain'] : $this->get_category_url($domain, $_POST['catdir']);

            }
            if($this->db->update($_POST,array('catid' => $catid),true)){
                if($_POST['arrparentid'] != $_POST['cpath']) $this->repairs($_POST['arrparentid'],$_POST['cpath']);
                if($_POST['domain']) $this-> set_domain();
                $this->delcache();
                return_json(array('status' => 1, 'message' => L('operation_success')));
            }else{
                return_json(array('status' => 0, 'message' => L('operation_fail')));
            }

        }else{
            /**
             * 如果没有提交表单，就进行页面显示
             */
            $type = isset($_GET['cattype']) ? intval($_GET['cattype']) :0;
            $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
            $data = $this->db->where(array('catid' => $catid))->find();
            if(!$data) showmsg('栏目不存在','stop');
            
            $modelinfo =get_site_modelinfo();
            $parent_temp = $this->db->field('category_template,list_template,show_template,pclink')->where(array('catid'=>$data['parentid']))->find();
            $parent_dir = $parent_temp ? str_replace(SITE_URL, '', $parent_temp['pclink']) : '';

            if($type ==0){
                $default_model = get_model($data['modelid'],false);
				$category_temp = $this->select_template('category_temp', 'category_', $default_model);
				$list_temp = $this->select_template('list_temp', 'list_', $default_model);
				$show_temp = $this->select_template('show_temp', 'show_', $default_model);
				$tablename = $default_model ? $default_model['alias'] : '模型别名';
				include $this->admin_tpl('category_edit');               
            }else if($type == 1){
                $page_data = D('model')->field('modelid,alias') -> where(array('cattype' =>2)) ->order('modelid ASC')-> find();
                $alias = $page_data ? $page_data['alias'] : 'page';
                $category_temp = $this->select_template('category_temp','category_', $alias);
                $tablename = $alias;
                include $this->admin_tpl('category_page_edit');
            }else{
                include $this -> admin_tpl('category_link_edit');
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
        $type = isset($_GET['cattype']) ? intval($_GET['cattype']) : 0;
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


    private function repairs($arrparentid, $cpath = null){
        $data1 = explode(',', $arrparentid);
        $data2 = $cpath ? explode(',',$cpath) : array();
        $data = array_merge($data1, $data2);
        foreach($data as $val){
            if($val) $this->repair($val);
        }
    }

    private function repair($catid){
        $this->db->update(array('arrchildid' => $this->get_arrchildid($catid)),array('catid' => $catid));
    }

    private function get_arrchildid($catid){
        $arrchildid = $catid;
        //只选出那些“以 $catid 为祖先”的所有子孙栏目
        $data = $this ->db ->field('catid')->where("FIND_IN_SET('$catid',arrparentid)")->order('catid ASC')->select();
        foreach($data as $val){
            $arrchildid .= ','.$val['catid'];
        }
        return $arrchildid;
    }






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

    private function select_template($style,$pre='',$model = null){
        if(!$model) return array();
        $site_theme   = self::$siteid ? get_site(self::$siteid,'site_theme') : C('site_theme');//rongyao 默认主题
        $tablename = is_array($model) ? $model['alias'] : $model;//article
        $pre = $model ? $pre.$tablename : $pre;//category_article
        $files = glob(RYPHP_APP.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$site_theme.DIRECTORY_SEPARATOR.$pre.'*.html');
        //去掉路径只保留文件名,如/path/to/category_article.html → category_article.html
        // array(
        //     0 => 'category_article.html',
        //     1 => 'category_article_list.html',
        //     2 => 'category_article_default.html'
        // )
        $files = @array_map('basename',$files);
        $templates = array();
        $tem_style = RYPHP_APP.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$site_theme.DIRECTORY_SEPARATOR.'config.php';
        $templates_style  = is_file($tem_style) ? require($tem_style) : array();
        $templates_style = $templates_style ? $templates_style[$style] : $templates_style;
        /**
         * $templates_style=
         * array (
         *     'category_article' => '文章频道页模板',
         *     'category_page' => '单页面模板', 
         *     'category_article_list' => '列表页模板',
         *     'category_article_default' => '默认页模板'
         *  )
         * 
         */
        if(is_array($files)){
            foreach($files as $file){
                $key = substr($file, 0, -5);
                $templates[$key] = isset($templates_style[$key]) ? $templates_style[$key] : $file;
            }
        }
        return $templates;
    }


    private function get_category_url($domain, $catdir){
        $system_str = URL_MODEL ==3 ? '' : 'index.php?s=';
        $url_mode = get_config('url_mode');
        if($url_mode ==1 || $url_mode ==3){
            return $domain ? $domain.$system_str.'/'.$catdir.'/' : get_site_url().$system_str.'/';
        }
        return SITE_PATH.$system_str.$catdir.'/';
    }

    /**
     * @author lirongyaoper
     * document: order
     *  @description: order category
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


    private function set_domain(){
        return_json(array('status' => 0, 'message' =>''));
    }


}