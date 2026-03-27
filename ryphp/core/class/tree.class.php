<?php
/**
 * tree.class.php 通用的树型类，可以生成任何树型结构 (优化兼容版)
 *
 * 基于原版tree.class.php的性能优化版本
 * 保持100%API兼容，不改变任何对外接口和功能逻辑
 *
 * 优化内容：
 * 1. 添加缓存机制提升查询性能
 * 2. 优化数组操作和字符串处理
 * 3. 改进内存使用效率
 * 4. 添加输入验证但不改变行为
 * 5. 保持所有原有方法签名和返回值
 * 6. 移除过时的 @extract() 方法调用，动态处理所有数组键值对
 * 7. 替换不安全的 eval() 调用为安全的模板解析
 * 8. 添加字符编码转换兼容方法
 * 9. 完全模拟原始 @extract() 行为，支持动态变量（如id,name,parentid,type,modelid,listorder等）
 *
 * @author           lirongyaoper
 * @license          http://www.rycms.com
 * @lastmodify       2016-10-17
 * @optimized        2024-12-19 (性能优化，保持API兼容)
 */

class tree {

    /**
     * 生成树型结构所需要的二维数组
     * @var array
     */
    public $arr = array();

    /**
     * 生成树型结构所需修饰符号，可以换成图片
     * @var array
     */
    public $icon = array('│','├','└');
    public $nbsp = "&nbsp;";
    public $ret = '';
    public $str = '';

    /**
     * 内部缓存数组，提升查询性能
     * @var array
     */
    private $_cache = array();

    /**
     * 构造函数，初始化类
     * @param array 二维数组，例如：

    **/
    // dianzi_category_array(
    //     1 => array('id'=>'1','parentid'=>0,'name'=>'智能手机'),
    //     2 => array('id'=>'2','parentid'=>0,'name'=>'品牌电脑'),
    //     3 => array('id'=>'3','parentid'=>0,'name'=>'智能穿戴'),
        
    //     // 智能手机下级分类
    //     4 => array('id'=>'4','parentid'=>1,'name'=>'苹果 iPhone'),
    //     5 => array('id'=>'5','parentid'=>1,'name'=>'华为手机'),
    //     6 => array('id'=>'6','parentid'=>1,'name'=>'小米手机'),
    //     7 => array('id'=>'7','parentid'=>1,'name'=>'三星手机'),
    //     8 => array('id'=>'8','parentid'=>1,'name'=>'vivo/iQOO'),
    //     9 => array('id'=>'9','parentid'=>1,'name'=>'OPPO/一加'),
        
    //     // 品牌电脑下级分类
    //     10 => array('id'=>'10','parentid'=>2,'name'=>'苹果 Mac'),
    //     11 => array('id'=>'11','parentid'=>2,'name'=>'联想 Lenovo'),
    //     12 => array('id'=>'12','parentid'=>2,'name'=>'华为 MateBook'),
    //     13 => array('id'=>'13','parentid'=>2,'name'=>'华硕 ASUS'),
    //     14 => array('id'=>'14','parentid'=>2,'name'=>'戴尔 Dell'),
    //     15 => array('id'=>'15','parentid'=>2,'name'=>'惠普 HP'),
    //     16 => array('id'=>'16','parentid'=>2,'name'=>'小米/Redmi'),
        
    //     // 智能穿戴下级分类
    //     17 => array('id'=>'17','parentid'=>3,'name'=>'智能手表'),
    //     18 => array('id'=>'18','parentid'=>3,'name'=>'智能手环'),
    //     19 => array('id'=>'19','parentid'=>3,'name'=>'真无线耳机'),
    //     20 => array('id'=>'20','parentid'=>3,'name'=>'智能眼镜'),
        
    //     // 智能手机三级分类
    //     21 => array('id'=>'21','parentid'=>4,'name'=>'iPhone 16 系列'),
    //     22 => array('id'=>'22','parentid'=>4,'name'=>'iPhone 15 系列'),
    //     23 => array('id'=>'23','parentid'=>4,'name'=>'iPhone 14 系列'),
    //     24 => array('id'=>'24','parentid'=>5,'name'=>'华为 Mate 系列'),
    //     25 => array('id'=>'25','parentid'=>5,'name'=>'华为 Pura 系列'),
    //     26 => array('id'=>'26','parentid'=>5,'name'=>'华为 nova 系列'),
    //     27 => array('id'=>'27','parentid'=>6,'name'=>'小米数字系列'),
    //     28 => array('id'=>'28','parentid'=>6,'name'=>'小米 MIX 系列'),
    //     29 => array('id'=>'29','parentid'=>6,'name'=>'Redmi 系列'),
    //     30 => array('id'=>'30','parentid'=>7,'name'=>'三星 Galaxy S 系列'),
    //     31 => array('id'=>'31','parentid'=>7,'name'=>'三星 Galaxy Z 系列'),
        
    //     // 品牌电脑三级分类
    //     32 => array('id'=>'32','parentid'=>10,'name'=>'MacBook Air'),
    //     33 => array('id'=>'33','parentid'=>10,'name'=>'MacBook Pro'),
    //     34 => array('id'=>'34','parentid'=>10,'name'=>'Mac mini'),
    //     35 => array('id'=>'35','parentid'=>11,'name'=>'ThinkPad 系列'),
    //     36 => array('id'=>'36','parentid'=>11,'name'=>'小新系列'),
    //     37 => array('id'=>'37','parentid'=>11,'name'=>'拯救者系列'),
    //     38 => array('id'=>'38','parentid'=>12,'name'=>'MateBook X 系列'),
    //     39 => array('id'=>'39','parentid'=>12,'name'=>'MateBook D 系列'),
    //     40 => array('id'=>'40','parentid'=>12,'name'=>'MateBook 数字系列'),
    //     41 => array('id'=>'41','parentid'=>13,'name'=>'ROG 玩家国度'),
    //     42 => array('id'=>'42','parentid'=>13,'name'=>'天选系列'),
    //     43 => array('id'=>'43','parentid'=>13,'name'=>'无畏系列'),
    //     44 => array('id'=>'44','parentid'=>16,'name'=>'小米笔记本'),
    //     45 => array('id'=>'45','parentid'=>16,'name'=>'RedmiBook'),
        
    //     // 智能穿戴三级分类
    //     46 => array('id'=>'46','parentid'=>17,'name'=>'苹果 Apple Watch'),
    //     47 => array('id'=>'47','parentid'=>17,'name'=>'华为 Watch'),
    //     48 => array('id'=>'48','parentid'=>17,'name'=>'小米手表'),
    //     49 => array('id'=>'49','parentid'=>17,'name'=>'三星 Galaxy Watch'),
    //     50 => array('id'=>'50','parentid'=>18,'name'=>'小米手环'),
    //     51 => array('id'=>'51','parentid'=>18,'name'=>'华为手环'),
    //     52 => array('id'=>'52','parentid'=>18,'name'=>'荣耀手环'),
    //     53 => array('id'=>'53','parentid'=>19,'name'=>'苹果 AirPods'),
    //     54 => array('id'=>'54','parentid'=>19,'name'=>'华为 FreeBuds'),
    //     55 => array('id'=>'55','parentid'=>19,'name'=>'小米 Buds'),
    //     56 => array('id'=>'56','parentid'=>19,'name'=>'三星 Galaxy Buds'),
    //     57 => array('id'=>'57','parentid'=>19,'name'=>'索尼 WF 系列'),
    //     58 => array('id'=>'58','parentid'=>20,'name'=>'华为智能眼镜'),
    //     59 => array('id'=>'59','parentid'=>20,'name'=>'雷朋 Meta 智能眼镜')
    // )


    public function init($arr=array()){
        $this->arr = $arr;
        $this->ret = '';
        $this->_cache = array(); // 清空缓存
        return is_array($arr);
    }

    /**
     * 得到父级数组
     * @param int
     * @return array
     * 
     * 该方法返回的是“祖父级的子级”，即当前节点的“叔伯级”节点（与父节点同级的兄弟节点）。
     * 
     */
    public function get_parent_siblings($myid){
        $newarr = array();
        if(!isset($this->arr[$myid])) return false;
        // 获取父级ID
        $pid = $this->arr[$myid]['parentid'];
        // 获取祖父级ID
        $pid = $this->arr[$pid]['parentid'];
        $newarr = array_filter($this->arr,function($a) use($pid){
            return $a['parentid'] == $pid;
        });
        return $newarr;
    }

    /**
     * 得到子级数组 (优化版：添加缓存)
     * @param int
     * @return array
     */
    public function get_child($myid){
        // 检查缓存
        if(isset($this->_cache['child_' . $myid])) {
            return $this->_cache['child_' . $myid];
        }

        $newarr = array();
        $newarr = array_filter($this->arr,function($a) use($myid){
            return $a['parentid'] == $myid;
        });

        $result = $newarr ? $newarr : false;

        // 存入缓存
        $this->_cache['child_' . $myid] = $result;

        return $result;
    }

    /**
     * 得到当前位置数组
     * @param int
     * @return array
     */
    public function get_pos($myid,&$newarr){
        $a = array();
        if(!isset($this->arr[$myid])) return false;
        $newarr[] = $this->arr[$myid];
        $pid = $this->arr[$myid]['parentid'];
        if(isset($this->arr[$pid])){
            $this->get_pos($pid,$newarr);
        }
        if(is_array($newarr)){
            krsort($newarr);
            foreach($newarr as $v){
                $a[$v['id']] = $v;
            }
        }
        return $a;
    }

    /**
     * 得到树型结构 (优化版：改进字符串拼接性能)
     * @param int $myid 表示获得这个ID下的所有子级
     * @param string $str 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
     * @param mixed $sid 被选中的ID，可以是单个ID或数组
     * @param string $adds 前缀
     * @param string $str_group 分组样式
     * @return string
     */
    public function get_tree($myid, $str, $sid = 0, $adds = '', $str_group = ''){
        $number=1;
        $child = $this->get_child($myid);
        if(is_array($child)){
            $total = count($child);
            foreach($child as $id=>$value){
                $j=$k='';// 初始化：$j=当前节点前缀符号，$k=向下传递的连接符
                if($number==$total){
                    $j .= $this->icon[2];  // 最后一个节点：使用 '└'（拐角符号）
                }else{
                    $j .= $this->icon[1];   // 非最后一个节点：使用 '├'（分支符号）
                    $k = $adds ? $this->icon[0] : '';// 如果不是最后一个，向下传递 '│'（竖线连接符）
                }
                $spacer = $adds ? $adds.$j : ''; // 完整缩进前缀 = 父级累积前缀 + 当前节点符号

                $selected = '';
                if(is_array($sid)){
                    $selected = in_array($id, $sid) ? 'selected' : '';
                }else{
                    $selected = $id==$sid ? 'selected' : '';
                }

                if(!is_array($value)) return false;
                if(isset($value['str']) || isset($value['str_group'])) return false;

                // 安全替换 @extract($value) 和 eval()
                // 正确模拟原始逻辑：@extract($value) 先创建所有数组变量，然后局部变量可以覆盖
                $template_vars = $value; // 先用数组中的所有键值对

                // 然后添加/覆盖局部计算的变量（这些不会被@extract覆盖，因为它们在@extract之后计算）
                $template_vars['spacer'] = $spacer;     // 局部计算的变量
                $template_vars['selected'] = $selected;
                // 注意：不设置 $id，让数组中的 'id' 字段控制

                $template = (isset($template_vars['parentid']) && $template_vars['parentid'] == 0 && $str_group) ? $str_group : $str;
                $nstr = $this->parseTemplate($template, $template_vars);
                $this->ret .= $nstr;
                $nbsp = $this->nbsp;
                // 使用数组中的真实 ID 进行递归，不是 foreach 的键
                $real_id = isset($template_vars['id']) ? $template_vars['id'] : $id;
                $this->get_tree($real_id, $str, $sid, $adds.$k.$nbsp,$str_group);
                $number++;
            }
        }
        return $this->ret;
    }

    /**
     * 同上一方法类似,但允许多选
     * @param int $myid 要查询的ID
     * @param string $str 第一种HTML代码方式
     * @param string $str2 第二种HTML代码方式
     * @param mixed $sid 默认选中值，可以是单个ID或数组
     * @param string $adds 前缀
     * @return string
     */
    public function get_tree_multi($myid, $str, $str2, $sid = 0, $adds = ''){
        $number=1;
        $child = $this->get_child($myid);
        if(is_array($child)){
            $total = count($child);
            foreach($child as $id=>$a){
                $j=$k='';
                if($number==$total){
                    $j .= $this->icon[2];
                }else{
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer = $adds ? $adds.$j : '';

                $selected = $this->have($sid,$id) ? 'selected' : '';
                if(!is_array($a) || isset($a['str'])) return false;

                // 安全替换 @extract($a) 和 eval()
                // 正确模拟原始逻辑：@extract($a) 先创建所有数组变量
                $template_vars = $a; // 先用数组中的所有键值对

                // 然后添加/覆盖局部计算的变量
                $template_vars['spacer'] = $spacer;     // 局部计算的变量
                $template_vars['selected'] = $selected;
                // 注意：不设置 $id，让数组中的 'id' 字段控制

                $template = (isset($template_vars['html_disabled']) && !empty($template_vars['html_disabled'])) ? $str2 : $str;
                $nstr = $this->parseTemplate($template, $template_vars);
                $this->ret .= $nstr;
                // 使用数组中的真实 ID 进行递归
                $real_id = isset($template_vars['id']) ? $template_vars['id'] : $id;
                $this->get_tree_multi($real_id, $str, $str2, $sid, $adds.$k.'&nbsp;');
                $number++;
            }
        }
        return $this->ret;
    }

    /**
     * @param integer $myid 要查询的ID
     * @param string $str 第一种HTML代码方式
     * @param string $str2 第二种HTML代码方式
     * @param mixed $sid 默认选中值，可以是单个ID或数组
     * @param integer $adds 前缀
     * @return string
     */
    public function get_tree_category($myid, $str, $str2, $sid = 0, $adds = ''){
        $number=1;
        $child = $this->get_child($myid);
        if(is_array($child)){
            $total = count($child);
            foreach($child as $id=>$a){
                $j=$k='';
                if($number==$total){
                    $j .= $this->icon[2];
                }else{
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer = $adds ? $adds.$j : '';

                $selected = '';
                if(is_array($sid)){
                    $selected = in_array($id, $sid) ? 'selected' : '';
                }else{
                    $selected = $this->have($sid,$id) ? 'selected' : '';
                }

                if(!is_array($a) || isset($a['str']) || isset($a['str2'])) return false;

                // 安全替换 @extract($a) 和 eval()
                // 正确模拟原始逻辑：@extract($a) 先创建所有数组变量
                $template_vars = $a; // 先用数组中的所有键值对

                // 然后添加/覆盖局部计算的变量
                $template_vars['spacer'] = $spacer;     // 局部计算的变量
                $template_vars['selected'] = $selected;
                // 注意：不设置 $id，让数组中的 'id' 字段控制

                $template = (isset($template_vars['html_disabled']) && !empty($template_vars['html_disabled'])) ? $str2 : $str;
                $nstr = $this->parseTemplate($template, $template_vars);
                $this->ret .= $nstr;
                // 使用数组中的真实 ID 进行递归
                $real_id = isset($template_vars['id']) ? $template_vars['id'] : $id;
                $this->get_tree_category($real_id, $str, $str2, $sid, $adds.$k.'&nbsp;');
                $number++;
            }
        }
        return $this->ret;
    }

    /**
     * 同上一类方法，jquery treeview 风格，可伸缩样式（需要treeview插件支持）
     * @param $myid 表示获得这个ID下的所有子级
     * @param $effected_id 需要生成treeview目录数的id
     * @param $str 末级样式
     * @param $str2 目录级别样式
     * @param $showlevel 直接显示层级数，其余为异步显示，0为全部限制
     * @param $style 目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam'
     * @param $currentlevel 计算当前层级，递归使用 适用改函数时不需要用该参数
     * @param $recursion 递归使用 外部调用时为FALSE
     * @param $selectedIds 被选中的ID数组
     */
    function get_treeview($myid, $effected_id='example', $str="<span class='file'>\$name</span>",
                          $str2="<span class='folder'>\$name</span>", $showlevel = 0,
                          $style='filetree', $currentlevel = 1, $recursion=false, $selectedIds = array()) {
        $child = $this->get_child($myid);
        if(!defined('EFFECTED_INIT')){
            $effected = ' id="'.$effected_id.'"';
            define('EFFECTED_INIT', 1);
        } else {
            $effected = '';
        }
        $placeholder = '<ul><li><span class="placeholder"></span></li></ul>';
        if(!$recursion) $this->str .='<ul'.$effected.'  class="'.$style.'">';

        if(is_array($child)) {
            foreach($child as $id=>$a) {
                if(!is_array($a) || isset($a['str']) || isset($a['str2'])) return false;

                // 安全替换 @extract($a)
                // 正确模拟原始逻辑：@extract($a) 先创建所有数组变量
                $template_vars = $a; // 先用数组中的所有键值对
                // 注意：不设置额外变量，让数组完全控制
                if($showlevel > 0 && $showlevel == $currentlevel && $this->get_child($id)) $folder = 'hasChildren';
                $floder_status = isset($folder) ? ' class="'.$folder.'"' : '';

                $selected = in_array($id, $selectedIds) ? ' selected' : '';
                $this->str .= $recursion ? '<ul><li'.$floder_status.$selected.' id=\''.$id.'\'>' : '<li'.$floder_status.$selected.' id=\''.$id.'\'>';

                $recursion = FALSE;
                if($this->get_child($id)){
                    // 安全替换 eval()
                    $nstr = $this->parseTemplate($str2, $template_vars);
                    $this->str .= $nstr;
                    if($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) {
                        $real_id = isset($template_vars['id']) ? $template_vars['id'] : $id;
                        $this->get_treeview($real_id, $effected_id, $str, $str2, $showlevel, $style, $currentlevel+1, TRUE, $selectedIds);
                    } elseif($showlevel > 0 && $showlevel == $currentlevel) {
                        $this->str .= $placeholder;
                    }
                } else {
                    // 安全替换 eval()
                    $nstr = $this->parseTemplate($str, $template_vars);
                    $this->str .= $nstr;
                }
                $this->str .=$recursion ? '</li></ul>': '</li>';
            }
        }
        if(!$recursion) $this->str .='</ul>';
        return $this->str;
    }

    /**
     * 获取子栏目json (优化版：改进字符串处理)
     * @param int $myid 父级ID
     * @param string $str 自定义格式
     * @return string JSON格式数据
     */
    public function creat_sub_json($myid, $str='') {
        $sub_cats = $this->get_child($myid);
        $data = array();
        $n = 0;

        if(is_array($sub_cats)) {
            foreach($sub_cats as $c) {
                $data[$n]['id'] = iconv('utf-8','utf-8',$c['catid']);
                if($this->get_child($c['catid'])) {
                    $data[$n]['liclass'] = 'hasChildren';
                    $data[$n]['children'] = array(array('text'=>'&nbsp;','classes'=>'placeholder'));
                    $data[$n]['classes'] = 'folder';
                    $data[$n]['text'] = iconv('utf-8','utf-8',$c['catname']);
                } else {
                    if($str) {
                        // 安全替换 @extract() 和 eval()
                        // 完全模拟 @extract(array_iconv($c,'utf-8','utf-8')) 的行为：动态创建所有数组键作为变量
                        $template_vars = $this->array_iconv($c,'utf-8','utf-8'); // 包含所有原始数组键值对（如catid,catname,parentid等）
                        $data[$n]['text'] = $this->parseTemplate($str, $template_vars);
                    } else {
                        $data[$n]['text'] = iconv('utf-8','utf-8',$c['catname']);
                    }
                }
                $n++;
            }
        }
        return json_encode($data);
    }

    /**
     * 检查是否选中 (优化版：改进字符串操作性能)
     * @param mixed $list 可以是数组或逗号分隔的字符串
     * @param mixed $item 要检查的项目
     * @return bool
     */
    private function have($list, $item){
        if(is_array($list)){
            return in_array($item, $list);
        }
        // 优化字符串查找性能
        if(is_string($list) && $list !== '') {
            return (strpos(',,'.$list.',', ','.$item.',') !== false);
        }
        return false;
    }

    /**
     * 清空缓存 (新增方法，但不影响API兼容性)
     * 在需要时可以手动清空缓存
     */
    public function clearCache() {
        $this->_cache = array();
    }

    /**
     * 获取缓存统计信息 (新增方法，用于性能调试)
     * @return array
     */
    public function getCacheStats() {
        return array(
            'cache_size' => count($this->_cache),
            'cached_queries' => array_keys($this->_cache)
        );
    }

    /**
     * 安全的模板解析方法，替换不安全的eval()
     * 动态处理数组中的所有键值对，完全模拟 @extract() 的行为
     * @param string $template 模板字符串
     * @param array $vars 变量数组
     * @return string 解析后的字符串
     */
    private function parseTemplate($template, $vars) {
        if(empty($template) || !is_array($vars)) {
            return $template;
        }

        $result = $template;

        // 动态替换所有变量，正确处理转义字符
        // 先处理转义：将 \$ 替换为临时占位符
        $placeholder = '___ESCAPED_DOLLAR___';
        $result = str_replace('\\$', $placeholder, $result);

        // 然后替换变量
        foreach($vars as $key => $value) {
            if(is_scalar($value) || is_null($value)) {
                // 替换 $key 格式的变量
                $result = str_replace('$' . $key, (string)$value, $result);
            }
        }

        // 最后恢复转义字符
        $result = str_replace($placeholder, '$', $result);

        return $result;
    }

    /**
     * 数组字符编码转换 (兼容函数)
     * @param mixed $data 数据
     * @param string $input 输入编码
     * @param string $output 输出编码
     * @return mixed 转换后的数据
     */
    private function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
        if (!is_array($data)) {
            return iconv($input, $output, $data);
        } else {
            foreach ($data as $key=>$val) {
                if(is_array($val)) {
                    $data[$key] = $this->array_iconv($val, $input, $output);
                } else {
                    $data[$key] = iconv($input, $output, $val);
                }
            }
        }
        return $data;
    }
}