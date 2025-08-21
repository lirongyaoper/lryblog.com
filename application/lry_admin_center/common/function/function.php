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
			$s1 = $key == 0 ? ' class="selected"' : '';
			$s2 = $key == 0 ? ' style="display:block;"' : '';
			$menu_string .= '<div class="menu_dropdown">
			<dl id="'.$value['id'].'-menu">
				<dt'.$s1.'><i class="lry-iconfont '.$value['data'].' mr-5"></i>'.$value['name'].'<i class="lry-nav-icon lry-iconfont lry-iconxiangxia menu_dropdown-arrow"></i></dt>
				<dd'.$s2.'>
					<ul>';
						foreach($value['child'] as $val){
							$menu_string .= '<li><a href="javascript:void(0)" _href="'.url($val['m'].'/'.$val['c'].'/'.$val['a'],$val['data']).'"data-title="'.$val['name'].'">'.$val['name'].'</a></li>';

						}
					$menu_string .= '</ul>
				</dd>
			</dl>
			</div>';
		}
		setcache('menu_string_'.$_SESSION['roleid'],$menu_string);

	}
	return $menu_string;
}


/**
 * 设置config文件
 * @param $config 配置信息
 * 
 * 作用：重新设置配置信息，并保存到config.php文件中
 */

function set_configFile($config){
	$configFile = RYPHP_COMMON.'config/config.php';
	if(!is_writable($configFile))  return_message('Please chmod '.$configFile. ' to 0777!',0);
	$pattern = $replacement = array();
	foreach($config as $key => $value){
		$value = str_replace(array(',','$','/'),'',$value);
		$pattern[$key] = "/'".$key."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
		$replacement[$key] = "'".$key."'=>\${1}".$value."\${2}\${3},";
	}
	$str = file_get_contents($configFile);
	$str = preg_replace($pattern, $replacement, $str);
	return file_put_contents($configFile,$str,LOCK_EX);
}

/**
 * 文件下载函数
 * 
 */

function file_download($url,$md5){
	if(extension_loaded('curl')){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_TIMEOUT_MS, 5000);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_HEADER,0);
		$content= curl_exec($ch);
		curl_close($ch);
	}else{
		$content = file_get_contents($url);
	}

	if(!$content) return array('status'=>0,'message'=>'升级包不存在，请检查升级包地址是否正确！，必要时重新尝试下载');
	$filename = explode('/',$url);
	$filename = end($filename);
	$download_path = RYPHP_ROOT.'cache/down_package/'.$filename;
	$fp = fopen($download_path,'w');
	fwrite($fp,$content);
	fclose($fp);
	if(!is_file($download_path)) return array('status'=>0,'message' => '下载失败，请检查文件权限');
	if($md5 != md5_file($download_path)) return array('status'=>0,'message' => '下载的升级包MD5值不匹配,请重新尝试下载');	
	return array('status'=>1, 'message' =>'下载成功！','file_path' => $download_path);
}