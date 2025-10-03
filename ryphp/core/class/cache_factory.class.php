<?php
class cache_factory{
    public static  $instances = null;
    public static $class = null;
    public static $config = null;
    public static $cache_instances = null;

    private function __construct()
    {
        
    }
    /**
     * Retrieves a singleton instance of the cache factory
     * 
     * This method implements the singleton pattern to ensure only one instance
     * of the cache factory exists. It determines which caching implementation
     * to use based on the configured cache_type setting.
     *
     * Supported cache types:
     * - file: File-based caching
     * - redis: Redis caching
     * - memcache: Memcache caching
     * 
     * If no cache type is specified or an invalid type is given, 
     * it defaults to file-based caching.
     *
     * @return self Returns the singleton instance of the cache factory
     *
     * @uses C() Function to get configuration values
     * @uses ryphp::load_sys_class() Loads the appropriate cache implementation class
     *
     * @static
     * @access public
     */

    public static function get_instance(){
        if(self::$instances == null){
            self::$instances = new self();
            switch(C('cache_type')){
                case 'file':
                    ryphp::load_sys_class('cache_file','',0);
                    self::$class = 'cache_file';
                    self::$config = C('file_config');
                    break;
                case 'redis':
					ryphp::load_sys_class('cache_redis','',0);
					self::$class = 'cache_redis';
					self::$config = C('redis_config');
					break;
				case 'memcache' : 
					ryphp::load_sys_class('cache_memcache','',0);
					self::$class = 'cache_memcache';
					self::$config = C('memcache_config');
					break;
				default :
					ryphp::load_sys_class('cache_file','',0);
					self::$class = 'cache_file';
					self::$config = C('file_config');                    
            }
        }
        return self :: $instances;

    }

    /**
     * Gets or initializes the cache instance using lazy loading pattern
     * 
     * This method implements a singleton pattern to ensure only one cache instance exists.
     * If the cache instance hasn't been created yet (is null), it creates a new instance
     * using the configured cache class and config parameters.
     *
     * @access public
     * @return object Returns the singleton cache instance
     * @throws Exception If cache class or config is not properly set
     * @static The cache instance is stored in a static property
     */
    public function get_cache_instances(){
        if(self::$cache_instances ==null){
            self::$cache_instances = new self::$class(self::$config);
        }
        return self::$cache_instances;
    }

}