<?php
/**
 * 数据库优化功能测试文件
 */

// 引入框架入口文件
require_once '../../ryphp.php';

// 模拟配置函数
if (!function_exists('C')) {
    function C($key = '', $default = '') {
        $config = array(
            'db_type' => 'pdo',
            'db_host' => '127.0.0.1',
            'db_name' => 'test',
            'db_user' => 'root',
            'db_pwd' => '3.3.',
            'db_port' => 3306,
            'db_charset' => 'utf8',
            'db_prefix' => 'lry_'
        );
        
        if (empty($key)) {
            return $config;
        } elseif (isset($config[$key])) {
            return $config[$key];
        } else {
            return $default;
        }
    }
}

// 模拟调试函数
if (!defined('RY_DEBUG')) {
    define('RY_DEBUG', true);
}

// 模拟应用类
if (!class_exists('application')) {
    class application {
        public static function halt($message, $code = 500) {
            echo "Application Halt: " . $message . " (Code: " . $code . ")\n";
            exit;
        }
        
        public static function fatalerror($message, $sql = '', $type = 0) {
            echo "Fatal Error: " . $message . " SQL: " . $sql . "\n";
            exit;
        }
    }
}

// 模拟调试类
if (!class_exists('debug')) {
    class debug {
        public static function addmsg($message, $type = 1, $time = 0) {
            echo "Debug: " . (is_array($message) ? json_encode($message) : $message) . "\n";
        }
    }
}

// 模拟JSON返回函数
if (!function_exists('return_json')) {
    function return_json($arr = array()) {
        echo "JSON Response: " . json_encode($arr, JSON_UNESCAPED_UNICODE) . "\n";
    }
}

// 模拟is_ajax函数
if (!function_exists('is_ajax')) {
    function is_ajax() {
        return false;
    }
}

// 模拟write_error_log函数
if (!function_exists('write_error_log')) {
    function write_error_log($err_array, $path = '') {
        echo "Error Log: " . (is_array($err_array) ? implode(' | ', $err_array) : $err_array) . "\n";
    }
}

// 模拟ryphp类的load_sys_class方法
if (!class_exists('ryphp')) {
    class ryphp {
        public static function load_sys_class($classname, $path = '', $initialize = 1) {
            // 在实际环境中，这个方法会加载类文件
            // 这里我们只是模拟
            return true;
        }
    }
}

// 测试代码
try {
    echo "开始测试数据库优化功能...\n";
    
    // 测试DbException类
    echo "1. 测试DbException类...\n";
    ryphp::load_sys_class('DbException', '', 0);
    
    if (class_exists('DbException')) {
        $exception = new DbException('测试异常', 1001, 'test_type', 'SELECT * FROM test');
        echo "  - 异常信息: " . $exception->getMessage() . "\n";
        echo "  - 异常类型: " . $exception->getType() . "\n";
        echo "  - SQL语句: " . $exception->getSql() . "\n";
        echo "  - 详细信息: " . $exception->getDetailedMessage() . "\n";
        echo "  ✓ DbException类测试通过\n";
    } else {
        echo "  ✗ DbException类测试失败\n";
    }
    
    echo "\n数据库优化功能测试完成。\n";
    echo "请在实际项目环境中测试完整的数据库操作功能。\n";
    
} catch (Exception $e) {
    echo "测试过程中发生错误: " . $e->getMessage() . "\n";
}