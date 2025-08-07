<?php

function get_siteid(){
    if(!is_file(RYPHP_APP.'site/common/function/site.php')) return 0;
    include RYPHP_APP.'site/common/function/site.php';
    return public_get_siteis();
}


function set_mapping($m){
    $siteid = get_siteid();
    $site_mapping = 'site_mapping_'. $m .'_'.$siteid;
    if(!$mapping = getcache($site_mapping)){
        
    }
}

