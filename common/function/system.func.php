<?php

function get_siteid(){
    if(!is_file(RYPHP_APP.'site/common/function/site.php')) return 0;
    include RYPHP_APP.'site/common/function/site.php';
    return public_get_siteis();
}


function get_config($key = ''){
    if(!$configs = getcache('configs')){
        $data = D('config') -> where(array('status' => 1)) ->select();
        $configs = array();
        foreach($data as $val){
            $configs[$val['name']] = $val['value'];
        }
        setcache('configs',$configs);
    }
    if(!$key){
        return $configs;
    }else{
        return array_key_exists($key,$configs) ? $configs[$key] : '';
    }
}



function get_urlrule(){
    if(!$urlrule = getcache('urlrule')){
        $data = D('urlrule') ->order('listorder ASC, urlruleid ASC') ->limit(300)->select();
        $urlrule = array();
        foreach($data as $val){
            $val['urlrule'] = '^'.str_replace('/','\/',$val['urlrule']).'$';
            $urlrule[$val['urlrule']] = $val['route'];
        }
        setcache('urlrule',$urlrule);
    }
    return $urlrule;
}

function set_mapping($m){
    $siteid = get_siteid();
    $site_mapping = 'site_mapping_'. $m .'_'.$siteid;
    if(!$mapping = getcache($site_mapping)){
        $data = D('category')->field('catid,cattype,catdir,arrchildid') ->where(array('siteid' => $siteid,'cattype <'  =>2 )) ->order('catid ASC') -> select();
        $mapping = array();
        foreach($data as $val){
            $mapping['^'.str_replace('/','\/',$val['catdir']).'$']  = $m.'/index/lists/catid'.$val['catid'];
            if($val['cattype']) continue;
            $mapping['^'.str_replace('/','\/',$val['catdir']).'\/list_(\d+)$'] = $m.'index/lists/catid/'.$val['catid'].'/page/$1';
            if(strpos($val['arrchildid'],',')) continue;
            $mapping['^'.str_replace('/','\/',$val['catdir']).'\/(\d+)$'] = $m.'index/show/catid'.$val['catid'].'/id/$1';

        }
        $route_rules = get_urlrule();
        if(!empty($route_rules))  $mapping = array_merge($route_rules,$mapping);
        setcache($site_mapping,$mapping);
    }
    return array_merge($mapping,C('route_rules'));   
}

