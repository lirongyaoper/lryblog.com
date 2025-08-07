<?php
class db_factory{
    public static $instances = null;
    public static $class = null;

    private function __construct()
    {
        
    }

    public static function get_instance(){
        if(self::$instances == null){
            self::$instances = new self();
            switch(C('db_type')){
                case 'mysql':
                    ryphp::load_sys_class('db_mysql','',0);
                    self::$class = 'db_mysql';
                    break;
				case 'mysqli' :
					ryphp::load_sys_class('db_mysqli','',0);
					self::$class = 'db_mysqli';
					break;
				case 'pdo' :
					// 加载优化版本的PDO数据库类
					ryphp::load_sys_class('db_pdo_optimized','',0);
					self::$class = 'db_pdo';
					break;
				default :
					ryphp::load_sys_class('db_mysql','',0);
					self::$class = 'db_mysql';
            }
        }
        return self::$instances;
    }



    public function connect($tabname){
        return new self::$class(array(
            'db_host' =>  C('db_host'),
            'db_user' => C('db_user'),
            'db_pwd' => C('db_pwd'),
            'db_name' => C('db_name'),
            'db_port' => C('db_port'),
            'db_charset'=>C('db_charset', 'utf8'),
            'db_prefix'=>C('db_prefix')

        ),$tabname);
    }
}