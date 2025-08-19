<?php

function url($url='', $vars='') {	
	$url = trim($url, '/');
	$arr = explode('/', $url);
	$string = SITE_PATH;
	if(URL_MODEL == 0){
		$string .= 'index.php?';
		$string .= 'm='.$arr[0].'&c='.$arr[1].'&a='.$arr[2];
		if($vars){
			if(is_array($vars)) $vars = http_build_query($vars);
			$string .= '&'.$vars;
		}
	}else{
		if(URL_MODEL == 1) $string .= 'index.php?s=';
		if(URL_MODEL == 4) $string .= 'index.php/';
		$string .= $url;
		if($vars){
			if(!is_array($vars)) parse_str($vars, $vars);			
            foreach ($vars as $var => $val){
                if(trim($val) !== '') $string .= '/'.$var.'/'.$val;
            } 
		}
        $string .= C('url_html_suffix');		
	}

	return $string;
}

/**
 * 从数据库获取菜单列表
 */
function get_menu_list(){
	$menu_list = D('menu')->field('`id`,`name`,`m`,`c`,`a`,`data`')->where(array('parentid'=>'0','display' => '1')) ->order('listorder ASC ,id ASC')->limit('20')->select();
	foreach ($menu_list as $key => $value){
		$child = D('menu')->field('`id`,`name`,`m`,`c`,`a`,`data`')->where(array('parentid' =>$value['id'],'display' => '1')) -> order('listorder ASC,id ASC')->select();
		foreach($child as $k => $v){
			if($_SESSION['roleid'] != 1){
				$data = D('admin_role_priv') -> field('roleid')->where(array('roleid' =>$_SESSION['roleid'],'m' =>$v['m'],'c'=> $v['c'],'a'=>$v['a'])) ->find();
				if(!$data) unset($child[$k]);
			}
		}
		if($child){
			$menu_list[$key]['child'] = $child;
		}else{
			unset($menu_list[$key]);
		}
	}
	return array_values($menu_list);// array_values() 函数在 PHP 中不是递归的，它只会处理当前数组的第一层（顶层）元素，而不会递归处理嵌套的数组。
}


function show_menu(){
	if(!$menu_string = getcache('menu_string_'.$_SESSION['roleid'])){
		$menu_list = get_menu_list();
		$menu_string = '';
		foreach($menu_list as $key => $value){
			
		}

	}
}
