<?php
/**
 * db_pdo.class.php	 PDO数据库类
 * 
 * @author           李荣耀  
 * @license          http://www.lryper.com
 * @lastmodify       2024-03-10
 */

class db_pdo{
	
	private static $link = null;       		 //数据库连接资源句柄
	private static $db_link = array();  	 //数据库连接资源池
	private $config = array();  	  		 //数据库配置信息
	private $tablename;                      //数据库表名,不包含表前缀
	private $key = array();           		 //存放条件语句
	private $lastsql = '';            		 //存放sql语句
	private static $params = array(
		PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
	);
		
	public function __construct($config,$tablename)
    {
        $this ->config = $config;
        $this->tablename= $tablename;
        if(is_null(self::$link)) $this ->db(0,$config);
    }	
	public function connect(){
		try{
			$dns = 'mysql:host='.$this->config['db_host'].';dbname='.$this->config['db_name'].';port='.intval($this->config['db_port']).';charset='.$this->config['db_charset']; 
			self::$link = new PDO($dns,$this->config['db_user'],$this->config['db_pwd'],self::$params);
			return self::$link;
		}catch(PDOException $e){
			self::$link = null;
			$mysql_error = RY_DEBUG ? $e -> getMessage() : 'Can not connect to MySQL server!';
			application::halt($mysql_error,550); 
		}
	}


	public function db($linknum = 0, $config = array()){
		if(isset(self::$db_link[$linknum])){
			self::$link = self::$db_link[$linknum]['db'];
			$this ->config = self::$db_link[$linknum]['config'];
		}else{
			if(empty($config)) $this->geterr('Database number to'.$linknum.' Not existent!');
			$this->config = $config;
			self::$db_link[$linknum]['db'] = self::$link = self::connect();
			self::$db_link[$linknum]['config'] = $config;
		}
		return $this;
	}


	private function get_tablename(){
		$alias = isset($this->key['alias']) ? $this ->key['alias'].' ' :'';
		return '`'.$this->config['db_name'].'` . `'.$this->config['db_prefix'].$this->tablename.'`'.$alias;
	}

		
	/**
	 * 内部方法：过滤函数
	 * @param $value
	 * @param $chars
	 * @return string
	 */	
	private function safe_data($value, $chars = false){
		
		if(is_string($value)){
			if(!MAGIC_QUOTES_GPC) $value = addslashes($value);
			if($chars) $value = htmlspecialchars($value);
		}

		return $value;
	}

	private function filter_field($arr,$primary = true,$field = true){
		if($field){
			$fields = $this ->get_fields();
			foreach($arr as $k => $v){
				if(!in_array($k,$fields,true)) unset($arr[$k]);
			}
		}
		if($primary){
			$p = $this ->get_primary();
			if(isset($arr[$p]))   unset($arr[$p]);
		}
		return $arr;
	}
	

	private function execute($sql,$is_private = false){
		try{
			if($is_private) return self::$link->query($sql);
			$statement = self::$link->prepare($sql);
			if(isset($this->key['where']['bind'])) { 
				foreach($this->key['where']['bind'] as $key => $val){
					$statement->bindValue($key+1, $val);
					//组装预处理SQL，便于调试
					$sql = substr_replace($sql, '\''.$val.'\'', strpos($sql, '?'), 1);
				}
			}
			$sql_start_time = microtime(true);
			$statement ->execute();
			$this->lastsql = $sql;
			RY_DEBUG && debug::addmsg($sql, 1, $sql_start_time);
			$this->key = array();
			return $statement;
		}catch (PDOException $e){
			if (strpos($e->getMessage(), 'server has gone away') !== false) {
		        self::$db_link[0]['db'] = self::$link = self::connect();
		        return $this->execute($sql, $is_private);
		    }
			$this->geterr('Execute SQL error, message : '.$e->getMessage(), $sql);
		}
	}

	







	
	/**
	 * 获取错误提示
	 */		
	private function geterr($msg, $sql=''){
	    if(PHP_SAPI == 'cli'){
	    	throw new Exception('MySQL Error: '.$msg.' | '.$sql);
	    }
		
		if(RY_DEBUG){
			if(is_ajax()) return_json(array('status'=>0, 'message'=>'MySQL Error: '.$msg.' | '.$sql));
			application::fatalerror($msg, $sql, 2);	
		}else{
			write_error_log(array('MySQL Error', $msg, $sql));
			if(is_ajax()) return_json(array('status'=>0, 'message'=>'MySQL Error!'));
			application::halt('MySQL Error!', 500);
		}
	}





			

	public function get_primary($table = '') {
		$table = empty($table) ? $this->get_tablename() : $table;
		$sql = "SHOW COLUMNS FROM $table";
		$listqeury = $this->execute($sql, true);
	    $data = $listqeury->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $value) {
			if($value['Key'] == 'PRI') { 
				return $value['Field'];
			}
		}
		return $data[0]['Field'];
	}
	

	/**
	 * 获取数据库 所有表
	 * @return array 
	 */		
	public function list_tables() {
		$tables = array();
		$listqeury = $this->execute('SHOW TABLES', true);
		$data = $listqeury->fetchAll(PDO::FETCH_NUM);	
		foreach ($data as $value) {
			$tables[] = $value[0];
		}
		return $tables;
	}	


	/**
	 * 检查表是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	public function table_exists($table) {
		$table = C('db_prefix').str_replace(C('db_prefix'), '', $table);
		$tables = $this->list_tables();
		return in_array($table, $tables);
	}

	/**
	 * 获取表字段
	 * @param $table 		数据表 可选
	 * @return array
	 */
	public function get_fields($table = '') {
		$table = empty($table) ? $this->get_tablename() : $table;
		$fields = array();
		$sql = "SHOW COLUMNS FROM $table";
		$listqeury = $this->execute($sql, true);
	    $data = $listqeury->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $value) {
			$fields[] = $value['Field'];
		}
		return $fields;
	}

	public function field_exists($table,$field){
		$fields = $this-> get_fields($table);
		return in_array($field,$fields);
	}


	public function version(){
		return self::$link ->getAttribute(PDO::ATTR_SERVER_VERSION);
	}


	public function close(){
		self::$link = null;
		return true;
	}

	
}