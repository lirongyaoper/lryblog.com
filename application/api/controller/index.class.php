<?php

defined('IN_RYPHP') or exit('Access Denied');
new_session_start();
class index{
	public function code(){	
		$code = ryphp::load_sys_class('code');
		if(isset($_GET['width']) && intval($_GET['width'])) $code->width = intval($_GET['width']);
		if(isset($_GET['height']) && intval($_GET['height'])) $code->height = intval($_GET['height']);
		if(isset($_GET['code_len']) && intval($_GET['code_len'])) $code->code_len = intval($_GET['code_len']);
		if(isset($_GET['font_size']) && intval($_GET['font_size'])) $code->font_size = intval($_GET['font_size']);
		if($code->width > 500 || $code->width < 10)  $code->width = 100;
		if($code->height > 300 || $code->height < 10)  $code->height = 35;
		if($code->code_len > 8 || $code->code_len < 2)  $code->code_len = 4;
		$code->show_code();
		$_SESSION['code'] = $code->get_code();
	}

}