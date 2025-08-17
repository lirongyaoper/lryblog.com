<?php
/**
 * db_pdo.class.php	 PDO数据库类
 * 
 * @author           李荣耀, RY
 * @license          http://www.lryper.com
 * @lastmodify       2025-08-07
 */

// 引入自定义异常类
ryphp::load_sys_class('DbException', '', 0);

class db_pdo {
    
    /**
     * 数据库连接资源句柄
     * @var PDO|null
     */
    private static $link = null;
    
    /**
     * 数据库连接资源池
     * @var array
     */
    private static $db_link = array();
    
    /**
     * 数据库配置信息
     * @var array
     */
    private $config = array();
    
    /**
     * 数据库表名,不包含表前缀
     * @var string
     */
    private $tablename;
    
    /**
     * 存放条件语句
     * @var array
     */
    private $key = array();
    
    /**
     * 存放sql语句
     * @var string
     */
    private $lastsql = '';
    
    /**
     * PDO连接参数
     * @var array
     */
    private static $params = array(
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    );
    
    /**
     * 事务状态
     * @var bool
     */
    private $transaction = false;
    
    /**
     * 构造函数
     * @param array $config 数据库配置
     * @param string $tablename 表名
     */
    public function __construct($config, $tablename) {
        $this->config = $config;
        $this->tablename = $tablename;
        if (is_null(self::$link)) {
            $this->db(0, $config);
        }
    }
    
    /**
     * 连接数据库
     * @return PDO|null
     * @throws DbException
     */
    public function connect() {
        try {
            $dns = 'mysql:host=' . $this->config['db_host'] . ';dbname=' . $this->config['db_name'] . ';port=' . intval($this->config['db_port']) . ';charset=' . $this->config['db_charset'];
            self::$link = new PDO($dns, $this->config['db_user'], $this->config['db_pwd'], self::$params);
            return self::$link;
        } catch (PDOException $e) {
            self::$link = null;
            $mysql_error = RYPHP_DEBUG ? $e->getMessage() : 'Can not connect to MySQL server!';
            throw new DbException($mysql_error, 550, 'connection_error');
        }
    }
    
    /**
     * 获取数据库连接
     * @param int $linknum 连接号
     * @param array $config 配置信息
     * @return $this
     * @throws DbException
     */
    public function db($linknum = 0, $config = array()) {
        if (isset(self::$db_link[$linknum])) {
            self::$link = self::$db_link[$linknum]['db'];
            $this->config = self::$db_link[$linknum]['config'];
        } else {
            if (empty($config)) {
                throw new DbException('Database number to ' . $linknum . ' Not existent!', 0, 'config_error');
            }
            $this->config = $config;
            self::$db_link[$linknum]['db'] = self::$link = self::connect();
            self::$db_link[$linknum]['config'] = $config;
        }
        return $this;
    }
    
    /**
     * 获取表名
     * @return string
     */
    private function get_tablename() {
        $alias = isset($this->key['alias']) ? $this->key['alias'] . ' ' : '';
        return '`' . $this->config['db_name'] . '` . `' . $this->config['db_prefix'] . $this->tablename . '`' . $alias;
    }
    
    /**
     * 内部方法：过滤函数
     * @param mixed $value 值
     * @param bool $chars 是否转换特殊字符
     * @return string
     */
    private function safe_data($value, $chars = false) {
        if (is_string($value)) {
            if (!MAGIC_QUOTES_GPC) {
                $value = addslashes($value);
            }
            if ($chars) {
                $value = htmlspecialchars($value);
            }
        }
        return $value;
    }
    
    /**
     * 过滤字段
     * @param array $arr 数据数组
     * @param bool $primary 是否过滤主键
     * @param bool $field 是否过滤字段
     * @return array
     */
    private function filter_field($arr, $primary = true, $field = true) {
        if ($field) {
            $fields = $this->get_fields();
            foreach ($arr as $k => $v) {
                if (!in_array($k, $fields, true)) {
                    unset($arr[$k]);
                }
            }
        }
        if ($primary) {
            $p = $this->get_primary();
            if (isset($arr[$p])) {
                unset($arr[$p]);
            }
        }
        return $arr;
    }
    
    /**
     * 执行SQL语句
     * @param string $sql SQL语句
     * @param bool $is_private 是否为私有查询
     * @return PDOStatement
     * @throws DbException
     */
    private function execute($sql, $is_private = false) {
        try {
            if ($is_private) {
                return self::$link->query($sql);
            }
            
            $statement = self::$link->prepare($sql);
            if (isset($this->key['where']['bind'])) {
                foreach ($this->key['where']['bind'] as $key => $val) {
                    $statement->bindValue($key + 1, $val);
                    // 组装预处理SQL，便于调试
                    $sql = substr_replace($sql, '\'' . $val . '\'', strpos($sql, '?'), 1);
                }
            }
            
            $sql_start_time = microtime(true);
            $statement->execute();
            $this->lastsql = $sql;
            RYPHP_DEBUG && debug::addmsg($sql, 1, $sql_start_time);
            $this->key = array();
            return $statement;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'server has gone away') !== false) {
                self::$db_link[0]['db'] = self::$link = self::connect();
                return $this->execute($sql, $is_private);
            }
            throw new DbException('Execute SQL error, message : ' . $e->getMessage(), 0, 'execute_error', $sql);
        }
    }
    
    /**
     * 获取错误提示
     * @param string $msg 错误信息
     * @param string $sql SQL语句
     * @throws DbException
     */
    private function geterr($msg, $sql = '') {
        if (PHP_SAPI == 'cli') {
            throw new DbException('MySQL Error: ' . $msg . ' | ' . $sql, 0, 'cli_error', $sql);
        }
        
        if (RYPHP_DEBUG) {
            if (is_ajax()) {
                return_json(array('status' => 0, 'message' => 'MySQL Error: ' . $msg . ' | ' . $sql));
            }
            application::fatalerror($msg, $sql, 2);
        } else {
            write_error_log(array('MySQL Error', $msg, $sql));
            if (is_ajax()) {
                return_json(array('status' => 0, 'message' => 'MySQL Error!'));
            }
            application::halt('MySQL Error!', 500);
        }
    }
    
    /**
     * 获取主键
     * @param string $table 表名
     * @return string
     */
    public function get_primary($table = '') {
        $table = empty($table) ? $this->get_tablename() : $table;
        $sql = "SHOW COLUMNS FROM $table";
        $listqeury = $this->execute($sql, true);
        $data = $listqeury->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $value) {
            if ($value['Key'] == 'PRI') {
                return $value['Field'];
            }
        }
        return $data[0]['Field'];
    }
    
    /**
     * 获取数据库所有表
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
     * @param string $table 表名
     * @return boolean
     */
    public function table_exists($table) {
        $table = C('db_prefix') . str_replace(C('db_prefix'), '', $table);
        $tables = $this->list_tables();
        return in_array($table, $tables);
    }
    
    /**
     * 获取表字段
     * @param string $table 数据表
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
    
    /**
     * 检查字段是否存在
     * @param string $table 表名
     * @param string $field 字段名
     * @return bool
     */
    public function field_exists($table, $field) {
        $fields = $this->get_fields($table);
        return in_array($field, $fields);
    }
    
    /**
     * 获取数据库版本
     * @return string
     */
    public function version() {
        return self::$link->getAttribute(PDO::ATTR_SERVER_VERSION);
    }
    
    /**
     * 关闭数据库连接
     * @return bool
     */
    public function close() {
        self::$link = null;
        return true;
    }
    
    /**
     * 设置查询条件
     * @param mixed $where 条件
     * @return $this
     */
    public function where($where) {
        if (is_array($where)) {
            $this->key['where'] = array();
            $bind = array();
            $sql = '';
            
            foreach ($where as $key => $val) {
                if (is_array($val)) {
                    // 处理范围查询
                    if (isset($val[0]) && isset($val[1])) {
                        $sql .= " AND `$key` BETWEEN ? AND ?";
                        $bind[] = $val[0];
                        $bind[] = $val[1];
                    }
                } else {
                    $sql .= " AND `$key` = ?";
                    $bind[] = $val;
                }
            }
            
            if (!empty($sql)) {
                $sql = substr($sql, 5); // 去掉开头的" AND "
                $this->key['where']['sql'] = $sql;
                $this->key['where']['bind'] = $bind;
            }
        } else {
            $this->key['where'] = $where;
        }
        return $this;
    }
    
    /**
     * 设置排序
     * @param string $order 排序条件
     * @return $this
     */
    public function order($order) {
        $this->key['order'] = $order;
        return $this;
    }
    
    /**
     * 设置限制
     * @param int $limit 限制数量
     * @param int $offset 偏移量
     * @return $this
     */
    public function limit($limit, $offset = 0) {
        $this->key['limit'] = $offset . ',' . $limit;
        return $this;
    }
    
    /**
     * 设置表别名
     * @param string $alias 别名
     * @return $this
     */
    public function alias($alias) {
        $this->key['alias'] = $alias;
        return $this;
    }
    
    /**
     * 查询单条记录
     * @param string $fields 查询字段
     * @return array|false
     */
    public function find($fields = '*') {
        $this->limit(1);
        $result = $this->select($fields);
        return !empty($result) ? $result[0] : false;
    }
    
    /**
     * 查询多条记录
     * @param string $fields 查询字段
     * @return array
     */
    public function select($fields = '*') {
        $table = $this->get_tablename();
        $sql = "SELECT {$fields} FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        // 添加ORDER BY
        if (isset($this->key['order'])) {
            $sql .= " ORDER BY " . $this->key['order'];
        }
        
        // 添加LIMIT
        if (isset($this->key['limit'])) {
            $sql .= " LIMIT " . $this->key['limit'];
        }
        
        try {
            $statement = $this->execute($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 插入数据
     * @param array $data 插入的数据
     * @return int|false 返回插入ID或false
     */
    public function insert($data) {
        if (!is_array($data) || empty($data)) {
            return false;
        }
        
        $table = $this->get_tablename();
        $keys = array_keys($data);
        $values = array_values($data);
        
        $fields = '`' . implode('`, `', $keys) . '`';
        $placeholders = rtrim(str_repeat('?, ', count($values)), ', ');
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        
        try {
            $this->key['where']['bind'] = $values;
            $statement = $this->execute($sql);
            return self::$link->lastInsertId();
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 批量插入数据
     * @param array $data 插入的数据数组
     * @return int|false 返回插入的记录数或false
     */
    public function insertAll($data) {
        if (!is_array($data) || empty($data)) {
            return false;
        }
        
        $table = $this->get_tablename();
        $keys = array_keys($data[0]);
        $fields = '`' . implode('`, `', $keys) . '`';
        
        // 构建占位符
        $placeholders = '';
        $bind = array();
        foreach ($data as $row) {
            $placeholders .= '(' . rtrim(str_repeat('?, ', count($row)), ', ') . '), ';
            $bind = array_merge($bind, array_values($row));
        }
        $placeholders = rtrim($placeholders, ', ');
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES {$placeholders}";
        
        try {
            $this->key['where']['bind'] = $bind;
            $statement = $this->execute($sql);
            return $statement->rowCount();
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 更新数据
     * @param array $data 更新的数据
     * @return int|false 返回影响的行数或false
     */
    public function update($data) {
        if (!is_array($data) || empty($data)) {
            return false;
        }
        
        $table = $this->get_tablename();
        $set = '';
        $bind = array();
        
        foreach ($data as $key => $val) {
            $set .= "`{$key}` = ?, ";
            $bind[] = $val;
        }
        $set = rtrim($set, ', ');
        
        $sql = "UPDATE {$table} SET {$set}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
                $bind = array_merge($bind, $this->key['where']['bind']);
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $this->key['where']['bind'] = $bind;
            $statement = $this->execute($sql);
            return $statement->rowCount();
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 删除数据
     * @return int|false 返回影响的行数或false
     */
    public function delete() {
        $table = $this->get_tablename();
        $sql = "DELETE FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
                $this->key['where']['bind'] = $this->key['where']['bind'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        } else {
            // 防止误删所有数据
            throw new DbException('Delete operation requires a WHERE condition', 0, 'delete_error');
        }
        
        try {
            $statement = $this->execute($sql);
            return $statement->rowCount();
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 统计记录数
     * @param string $field 字段名
     * @return int
     */
    public function count($field = '*') {
        $table = $this->get_tablename();
        $sql = "SELECT COUNT({$field}) as count FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $statement = $this->execute($sql);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 求和
     * @param string $field 字段名
     * @return int|float
     */
    public function sum($field) {
        $table = $this->get_tablename();
        $sql = "SELECT SUM(`{$field}`) as sum FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $statement = $this->execute($sql);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['sum'] ? (is_numeric($result['sum']) ? (int)$result['sum'] : (float)$result['sum']) : 0;
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 求平均值
     * @param string $field 字段名
     * @return float
     */
    public function avg($field) {
        $table = $this->get_tablename();
        $sql = "SELECT AVG(`{$field}`) as avg FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $statement = $this->execute($sql);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['avg'] ? (float)$result['avg'] : 0;
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 求最大值
     * @param string $field 字段名
     * @return mixed
     */
    public function max($field) {
        $table = $this->get_tablename();
        $sql = "SELECT MAX(`{$field}`) as max FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $statement = $this->execute($sql);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['max'];
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 求最小值
     * @param string $field 字段名
     * @return mixed
     */
    public function min($field) {
        $table = $this->get_tablename();
        $sql = "SELECT MIN(`{$field}`) as min FROM {$table}";
        
        // 添加WHERE条件
        if (isset($this->key['where'])) {
            if (is_array($this->key['where'])) {
                $sql .= " WHERE " . $this->key['where']['sql'];
            } else {
                $sql .= " WHERE " . $this->key['where'];
            }
        }
        
        try {
            $statement = $this->execute($sql);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['min'];
        } catch (DbException $e) {
            throw $e;
        }
    }
    
    /**
     * 开始事务
     * @return bool
     */
    public function startTransaction() {
        try {
            $result = self::$link->beginTransaction();
            if ($result) {
                $this->transaction = true;
            }
            return $result;
        } catch (PDOException $e) {
            throw new DbException('Start transaction failed: ' . $e->getMessage(), 0, 'transaction_error');
        }
    }
    
    /**
     * 提交事务
     * @return bool
     */
    public function commit() {
        try {
            $result = self::$link->commit();
            if ($result) {
                $this->transaction = false;
            }
            return $result;
        } catch (PDOException $e) {
            throw new DbException('Commit transaction failed: ' . $e->getMessage(), 0, 'transaction_error');
        }
    }
    
    /**
     * 回滚事务
     * @return bool
     */
    public function rollback() {
        try {
            $result = self::$link->rollBack();
            if ($result) {
                $this->transaction = false;
            }
            return $result;
        } catch (PDOException $e) {
            throw new DbException('Rollback transaction failed: ' . $e->getMessage(), 0, 'transaction_error');
        }
    }
    
    /**
     * 获取事务状态
     * @return bool
     */
    public function inTransaction() {
        return $this->transaction;
    }
    
    /**
     * 获取最后执行的SQL语句
     * @return string
     */
    public function getLastSql() {
        return $this->lastsql;
    }
}