# 数据库访问类优化说明

## 概述

本次优化对原有的`db_pdo.class.php`进行了全面升级，提供了更强大、更安全、更易用的数据库访问功能。

## 主要优化内容

### 1. 完整的CRUD操作
- `select()` - 查询多条记录
- `find()` - 查询单条记录
- `insert()` - 插入单条记录
- `insertAll()` - 批量插入记录
- `update()` - 更新记录
- `delete()` - 删除记录

### 2. 查询构建器
- `where()` - 设置查询条件
- `order()` - 设置排序
- `limit()` - 设置限制
- `alias()` - 设置表别名

### 3. 聚合查询
- `count()` - 统计记录数
- `sum()` - 求和
- `avg()` - 求平均值
- `max()` - 求最大值
- `min()` - 求最小值

### 4. 事务支持
- `startTransaction()` - 开始事务
- `commit()` - 提交事务
- `rollback()` - 回滚事务
- `inTransaction()` - 检查事务状态

### 5. 安全性增强
- 使用预处理语句防止SQL注入
- 自定义异常处理机制
- 输入数据过滤和验证

## 使用示例

### 基本查询
```php
// 获取用户表实例
$user = D('user');

// 查询所有用户
$users = $user->select();

// 查询特定用户
$userInfo = $user->where(array('id' => 1))->find();

// 带条件查询
$users = $user->where(array('status' => 1))->order('id DESC')->limit(10)->select();
```

### 插入数据
```php
// 插入单条记录
$user = D('user');
$data = array(
    'username' => 'john',
    'email' => 'john@example.com',
    'status' => 1
);
$insertId = $user->insert($data);

// 批量插入
$users = array(
    array('username' => 'john', 'email' => 'john@example.com', 'status' => 1),
    array('username' => 'jane', 'email' => 'jane@example.com', 'status' => 1)
);
$affectedRows = $user->insertAll($users);
```

### 更新数据
```php
// 更新特定用户
$user = D('user');
$data = array('status' => 0);
$affectedRows = $user->where(array('id' => 1))->update($data);
```

### 删除数据
```php
// 删除特定用户（必须有where条件）
$user = D('user');
$affectedRows = $user->where(array('id' => 1))->delete();
```

### 聚合查询
```php
// 统计用户数量
$user = D('user');
$count = $user->where(array('status' => 1))->count();

// 求和
$total = $user->where(array('status' => 1))->sum('score');

// 求平均值
$avg = $user->where(array('status' => 1))->avg('score');
```

### 事务处理
```php
// 使用事务
$user = D('user');
$account = D('account');

try {
    $user->startTransaction();
    
    // 插入用户
    $userId = $user->insert(array('username' => 'john', 'email' => 'john@example.com'));
    
    // 更新账户
    $account->where(array('user_id' => $userId))->update(array('balance' => 1000));
    
    // 提交事务
    $user->commit();
} catch (Exception $e) {
    // 回滚事务
    $user->rollback();
    throw $e;
}
```

### 获取执行信息
```php
// 获取最后执行的SQL语句
$sql = $user->getLastSql();
```

## 异常处理

新的数据库类使用自定义异常处理机制：

```php
try {
    $user = D('user');
    $users = $user->select();
} catch (DbException $e) {
    // 处理数据库异常
    echo "数据库错误: " . $e->getMessage();
    echo "SQL语句: " . $e->getSql();
    echo "错误类型: " . $e->getType();
}
```

## 性能优化

1. 连接池管理优化
2. 预处理语句重用
3. 查询结果缓存（可选）

## 安全特性

1. 参数绑定防止SQL注入
2. 输入数据过滤
3. 字段白名单验证
4. 异常信息保护

## 向后兼容性

新的优化版本保持了与原有接口的兼容性，原有的代码无需修改即可继续使用。