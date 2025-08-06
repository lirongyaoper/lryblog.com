<?php

class debug{



    public static $info = array();
    public static $sqls = array();
    public static $request = array();
    public static $stoptime ;
    public static $msg = array(
        2 => '错误警告',
        8 => '错误通知',
        256 => '自定义错误',
        512 => '自定义警告',
        1024 => '自定义通知',
        2048 => '编码标准化警告',
        8192 => '已弃用警告',
        'Unknown' => '未知错误'
    );
    public function __construct(){
        
    }
    public static function spent(){
        if(!self::$stoptime) self::stop();
        return round((self::$stoptime- SYS_START_TIME ),4);
    }

    public static function fatalerror(){
        if($e = error_get_last()){
            switch ($e['type']){
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                ob_end_clean();
                if(RY_DEBUG && !defined('DEBUG_HIDDEN')){
                    application :: fatalerror($e['message'],$e['file']. 'on line' .$e['line'],1);
                }else{
                    write_error_log(array(
                        'FatalError',
                        $e['message'],
                        $e['file'],
                        $e['line']
                    ));
                    application :: halt('error message has been saved.',500);
                }
                break;
            }
        }
    }


    public static function addmsg($msg, $type = 0, $start_time =0){
        switch($type){
            case 0:
                self::$info[] = $msg;
                break;
            case 1:
                self::$sqls[] = htmlspecialchars($msg, ENT_QUOTES,  'UTF-8').';[ RunTime:'.number_format(microtime(true)-$start_time , 6).'s ]';
        }
    }
    public static function catcher($errno, $errstr, $errfile, $errline){
        if (!(error_reporting() & $errno)) {
            return;
        }
        self::halt("Error: [$errno] $errstr in $errfile on line $errline");
    }

    public static function exception($exception){
        self::halt("Exception: " . $exception->getMessage());
    }

    public static function stop(){
        // Placeholder for stopping debug
        self::$stoptime = microtime(true);
    }

    public static function message(){
        // Placeholder for displaying debug messages
    }
}