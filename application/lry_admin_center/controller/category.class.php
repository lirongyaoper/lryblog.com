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
        // 第1-6行：获取站点所有模型信息，构建 modelid => modelname 的映射数组
        // 用于后续显示每个分类所属的模型名称
        $modelinfo = get_site_modelinfo(); // 获取当前站点的所有模型信息
        $modelarr = array(); // 初始化模型映射数组
        foreach($modelinfo as $val){
            // 将模型ID作为键，模型名称作为值存入数组
            $modelarr[$val['modelid']] = $val['name'];
        }
        
        // 第8-22行：处理栏目展开/收起状态
        // 从Cookie中读取用户设置的分类展开/收起状态，用于记忆用户的操作习惯
        $category_show_status = isset($_COOKIE['category_show_status_'.self::$siteid]) ? json_decode($_COOKIE['category_show_status_'.self::$siteid], true) : array(); // 获取Cookie中存储的分类展开状态，格式为 {catid: '1'或'0'}      
        $tree_toggle = 0; // 树节点切换标志（当前未使用）
        $childid_hide = ''; // 用于存储需要隐藏的子分类ID字符串
        
        if($category_show_status) {
            // 遍历所有已收起的分类（值为'1'表示收起状态）
            foreach($category_show_status as $k => $v){
                if($v == '1'){
                    // 获取该分类的所有子分类ID，并拼接到隐藏列表中
                    $childid_hide .= get_category($k, 'arrchildid', true).',';
                    $tree_toggle = 1; // 标记有分类被收起
                }else{
                    $tree_toggle = 0; // 标记分类展开（实际会被多次覆盖，逻辑可优化）
                }
            }
        }
        // 将需要隐藏的子分类ID字符串转换为数组，用于后续判断
        $arrchildid_arr = explode(',', $childid_hide);

        // 第24-27行：初始化树形结构类
        $tree = ryphp::load_sys_class('tree'); // 加载系统树形结构类
        // 设置树形结构的图标：垂直线、├─、└─
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;'; // 设置缩进空格
        
        // 第28行：从数据库查询当前站点的所有分类数据
        // 字段说明：catid(分类ID), catname(分类名), parentid(父分类ID), cattype(分类类型), 
        //          modelid(模型ID), listorder(排序), member_publish(会员投稿), pclink(PC链接), 
        //          domain(绑定域名), display(是否显示)
        $data = $this->db->field('catid AS id,catname AS name,parentid,cattype,modelid,listorder,member_publish,pclink,domain,display')
            ->where(array('siteid' => self::$siteid)) // 只查询当前站点的分类
            ->order('listorder ASC,catid ASC') // 按排序字段和ID升序排列
            ->select(); // 执行查询
        
        // 第29-60行：遍历所有分类数据，为每个分类构建详细的展示信息
        $array = array(); // 初始化用于树形结构的数组
        foreach($data as $val){
            // 第31-39行：根据分类类型(cattype)设置分类链接
            if($val['cattype'] == "0"){
                // 类型0：普通栏目，点击后打开添加内容的全屏弹窗
                $string = 'lry_open_full("添加内容", "'.U('content/add', array('modelid' => $val['modelid'], 'catid' => $val['id'])).'")'; 
                $val['catlink'] = "javascript:;' onclick='".$string; // 构建JavaScript点击事件

            }elseif($val['cattype'] == "1"){
                // 类型1：单页面，链接到单页内容编辑页
                $val['catlink'] = U('page_content', array('catid' => $val['id']));
            }else{
                // 类型2：外部链接，直接使用pclink并在新窗口打开
                $val['catlink'] = $val['pclink']."' target='_blank";
            }
            
            // 第41-46行：设置分类展开/收起图标和操作状态
            $icon = '&#xe653;'; // 默认展开图标（HTML实体，表示Unicode字符）
            $action = '2'; // 默认操作状态为展开（'2'表示点击后执行收起）
            
            if($category_show_status && isset($category_show_status[$val['id']]) && $category_show_status[$val['id']] == '1'){
                // 如果该分类在Cookie中标记为收起状态
                $icon = '&#xe652;'; // 切换为收起图标
                $action = '1'; // 操作状态改为收起（'1'表示点击后执行展开）
            }
            
            // 第48-49行：设置分类行的显示状态和CSS类
            // 判断当前分类是否在需要隐藏的子分类列表中
            $show_status = in_array($val['id'], $arrchildid_arr) ? ' tr_hide' : '';
            // 如果有父分类则为子分类（class='child'），否则为顶级分类（class='top'）
            $val['class'] = $val['parentid'] ? 'child'.$show_status : 'top';
            
            // 第50行：为顶级分类添加展开/收起图标，子分类不显示
            $val['parentoff'] = $val['parentid'] ? '' : '<i class="lry-iconfont parentid" catid="'.$val['id'].'" action="'.$action.'">'.$icon.'</i> ';
            
            // 第51行：如果分类绑定了域名，显示域名标识
            $val['domain'] = $val['domain'] ? '<div title="绑定域名：'.$val['domain'].'" style="color:#0194ff;font-size:12px" class="lry-iconfont">&#xe64a; 域名</div>' : '';
            
            // 第52行：根据分类类型显示对应的文本标签
            $val['cattype'] = $val['cattype'] == "0" ? '普通栏目' : ($val['cattype'] == "1" ? '<span style="color:green">单页面</span>' : '<span style="color:red">外部链接</span>');
            
            // 第53行：显示分类关联的模型名称，如果没有关联模型则显示"无"
            $val['catmodel'] = $val['modelid'] && isset($modelarr[$val['modelid']]) ? $modelarr[$val['modelid']] : '无';
            
            // 第54行：构建"是否显示"字段的可点击切换按钮
            // 根据display值显示绿色"是"或红色"否"，点击可切换状态
            $val['display'] = $val['display'] ? '<span class="lry-status-enable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="display" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe601;</i>否</span>';
            
            // 第55行：构建"会员投稿"字段的可点击切换按钮
            // 根据member_publish值显示绿色"是"或红色"否"，点击可切换状态
            $val['member_publish'] = $val['member_publish'] ? '<span class="lry-status-enable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe81f;</i>是</span>' : '<span class="lry-status-disable" data-field="member_publish" data-id="'.$val['id'].'" onclick="lry_change_status(this,\''.U('public_change_status').'\')"><i class="lry-iconfont">&#xe601;</i>否</span>';
            
            // 第56-58行：构建操作按钮组（增加子类、编辑、删除）
            $val['string'] = '<a title="增加子类" href="javascript:;" onclick="lry_open(\'增加栏目\',\''.U('add', array('modelid' => $val['modelid'], 'type' => $val['cattype'], 'catid' => $val['id'])).'\',800,500)" class="btn-mini btn-primary ml-5" style="text-decoration:none">增加子类</a> 
			<a title="编辑栏目" href="javascript:;" onclick="lry_open(\'编辑栏目\',\''.U('edit', array('type' => $val['cattype'], 'catid' => $val['id'])).'\',800,500)" class="btn-mini btn-success ml-5" style="text-decoration:none">编辑</a> 
			<a title="删除" href="javascript:;" onclick="lry_confirm(\''.U('delete', array('type' => $val['cattype'], 'catid' => $val['id'])).'\', \'确定要删除【'.$val['name'].'】吗？\', 1)" class="btn-mini btn-danger ml-5" style="text-decoration:none">删除</a>';   
            
            // 第59行：将处理后的分类数据添加到数组中
            $array[] = $val;       
        }
        
        // 第61-73行：定义树形结构的HTML模板
        // 使用变量占位符（$id, $name等），将在树形结构类中被替换为实际值
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
        
        // 第74-75行：初始化树形结构并生成HTML
        $tree->init($array); // 将分类数据传入树形结构类
        $categorys = $tree->get_tree(0, $str); // 从根节点(0)开始生成树形HTML，使用上面定义的模板
        
        // 第76行：加载分类列表模板并渲染页面
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
        $site_theme   = self::$siteid ? get_site(self::$siteid,'site_theme') : C('site_theme');//rongyao 默认主题
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