<?php

defined('IN_RYPHP') or exit('Access Denied');
new_session_start();
class index{
    public function  code(){
        $yanzhengcode = ryphp::load_sys_class('code');
        if(isset($_GET['width']) && intval($_GET['width']))  $yanzhengcode->width = intval($_GET['width']);
        if(isset($_GET['height']) && intval($_GET['height']))  $yanzhengcode->height = intval($_GET['height']);
        if(isset($_GET['code_len']) && intval($_GET['code_len']))  $yanzhengcode->length = intval($_GET['code_len']);
        if(isset($_GET['font_size']) && intval($_GET['font_size']))  $yanzhengcode->font_size = intval($_GET['font_size']);
        if($yanzhengcode->width >500 || $yanzhengcode->width <10) $yanzhengcode->width = 100;
        if($yanzhengcode->height >300 || $yanzhengcode->height <10) $yanzhengcode->height =35;
        if($yanzhengcode->code_len >8 || $yanzhengcode->code_len <2) $yanzhengcode->code_len = 4;
        $yanzhengcode->show_code();
        $_SESSION['code'] = $yanzhengcode->get_code();

    }
}