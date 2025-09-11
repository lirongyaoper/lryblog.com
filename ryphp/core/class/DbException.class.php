<?php
/**
 * DbException.class.php	 数据库异常类
 * 
 * @author           RY
 * @license          http://www.lryper.com
 * @lastmodify       2025-08-07
 */

class DbException extends Exception {
    
    /**
     * 异常类型
     * @var string
     */
    private $type;
    
    /**
     * SQL语句
     * @var string
     */
    private $sql;
    
    /**
     * 构造函数
     * @param string $message 错误信息
     * @param int $code 错误代码
     * @param string $type 异常类型
     * @param string $sql 相关SQL语句
     * @param Exception $previous 前一个异常
     */
    public function __construct($message = "", $code = 0, $type = 'unknown', $sql = '', Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->type = $type;
        $this->sql = $sql;
    }
    
    /**
     * 获取异常类型
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * 获取SQL语句
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }
    
    /**
     * 获取详细的错误信息
     * @return string
     */
    public function getDetailedMessage() {
        $message = $this->getMessage();
        if (!empty($this->sql)) {
            $message .= ' SQL: ' . $this->sql;
        }
        return $message;
    }
    
    /**
     * 转换为字符串
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}