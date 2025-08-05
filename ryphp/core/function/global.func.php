<?php


function is_ssl(){
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])){
        return true;
    }elseif(isset($_SERVER['REQUEST_SCHEME']) && ('https' == strtolower($_SERVER['REQUEST_SCHEME']))){
        return true;
    }elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ('https' == strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']))) {
        return true;
    }elseif(isset($_SERVER['HTTP_X_FORWARDED_SCHEME']) && ('https' == strtolower($_SERVER['HTTP_X_FORWARDED_SCHEME']))) {
        return true;
    }
    return false;
}