<?php
// 测试db_pdo_optimized.class.php在PHP 8+中的兼容性

define('RYPHP_DEBUG', true);
define('IN_RYPHP', true);
define('RYPHP_ROOT', __DIR__ . '/');

// 模拟必要的常量和函数
define('MAGIC_QUOTES_GPC', false);

function is_ajax() {
    return false;
}

function debug() {
    // 模拟debug函数
}

function application() {
    // 模拟application类
}

// 模拟C函数
function C($key = '', $default = '') {
    $config = [
        'db_prefix' => 'test_'
    ];
    return isset($config[$key]) ? $config[$key] : $default;
}

// 模拟ryphp类
class ryphp {
    public static function load_sys_class($classname, $path = '', $initialize = 1) {
        // 在测试环境中，我们需要手动加载 DbException 类
        if ($classname === 'DbException') {
            $filepath = __DIR__ . '/ryphp/core/class/DbException.class.php';
            if (file_exists($filepath) && !class_exists('DbException', false)) {
                require_once $filepath;
            }
        }
        return true;
    }
}

// 包含要测试的文件
require_once __DIR__ . '/ryphp/core/class/DbException.class.php';
require_once __DIR__ . '/ryphp/core/class/db_pdo_optimized.class.php';

echo "File included successfully.\n";

// 测试类是否存在
if (class_exists('db_pdo')) {
    echo "db_pdo class exists.\n";
} else {
    echo "ERROR: db_pdo class does not exist.\n";
    exit(1);
}

// 测试DbException类
if (class_exists('DbException')) {
    echo "DbException class exists.\n";
} else {
    echo "ERROR: DbException class does not exist.\n";
    exit(1);
}

echo "All tests passed. No syntax errors found.\n";
?>