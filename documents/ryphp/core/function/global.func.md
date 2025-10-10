# global.func.php 函数列表

本文档列出了 `ryphp/core/function/global.func.php` 文件中的所有函数，并按功能分类。

## 目录

- [配置管理函数](#配置管理函数)
- [字符串处理函数](#字符串处理函数)
- [数据库函数](#数据库函数)
- [网络/HTTP函数](#网络http函数)
- [缓存函数](#缓存函数)
- [Cookie函数](#cookie函数)
- [Session函数](#session函数)
- [图片处理函数](#图片处理函数)
- [验证函数](#验证函数)
- [文件处理函数](#文件处理函数)
- [语言/本地化函数](#语言本地化函数)
- [URL函数](#url函数)
- [模板函数](#模板函数)
- [模型函数](#模型函数)
- [消息/响应函数](#消息响应函数)
- [日志函数](#日志函数)
- [队列函数](#队列函数)
- [工具函数](#工具函数)
- [其他函数](#其他函数)

---

## 配置管理函数

### C($key = '', $default = '')
获取系统配置项。

**参数：**
- `$key` - 配置键名
- `$default` - 默认值

**返回：** 配置值

**用法：**
```php
$language = C('language');
```

---

### config($key = '', $default = '')
获取配置文件中的配置项。

**参数：**
- `$key` - 配置键名，格式为 "文件名.配置项" 或 "文件名"
- `$default` - 默认值

**返回：** 配置值

**用法：**
```php
$host = config('database.host');
$database = config('database');
```

---

### env($env_key, $default = '')
获取 .env 文件中的配置。

**参数：**
- `$env_key` - 环境变量键名
- `$default` - 默认值

**返回：** 环境变量值

**用法：**
```php
$apiKey = env('API_KEY', 'default_key');
```

---

## 字符串处理函数

### create_randomstr($lenth = 6)
生成随机字符串。

**参数：**
- `$lenth` - 字符串长度

**返回：** 随机字符串

---

### random($length, $chars = '0123456789')
产生随机字符串。

**参数：**
- `$length` - 输出长度
- `$chars` - 可选字符集

**返回：** 随机字符串

---

### array2string($data, $isformdata = 1)
将数组转换为字符串（JSON格式）。

**参数：**
- `$data` - 数组
- `$isformdata` - 是否使用 new_stripslashes 处理

**返回：** JSON字符串

---

### string2array($data)
将字符串转换为数组。

**参数：**
- `$data` - JSON字符串

**返回：** 数组

---

### new_addslashes($string)
返回经 addslashes 处理过的字符串或数组。

**参数：**
- `$string` - 需要处理的字符串或数组

**返回：** 处理后的字符串或数组

---

### new_stripslashes($string)
返回经 stripslashes 处理过的字符串或数组。

**参数：**
- `$string` - 需要处理的字符串或数组

**返回：** 处理后的字符串或数组

---

### new_html_special_chars($string, $filter = array())
返回经 htmlspecialchars 处理过的字符串或数组。

**参数：**
- `$string` - 需要处理的字符串或数组
- `$filter` - 需要排除的字段

**返回：** 处理后的字符串或数组

---

### safe_replace($string)
对字符串进行安全过滤处理，防止XSS攻击和SQL注入。

**参数：**
- `$string` - 需要处理的字符串

**返回：** 安全字符串

**处理内容：**
- 移除URL编码字符
- 移除特殊字符
- HTML转义尖括号
- 移除花括号

---

### remove_xss($string)
XSS过滤函数。

**参数：**
- `$string` - 需要过滤的字符串

**返回：** 过滤后的字符串

---

### trim_script($str)
转义 JavaScript 代码标记。

**参数：**
- `$str` - 字符串或数组

**返回：** 转义后的字符串

---

### str_cut($string, $length, $dot = '...', $code = 'utf-8')
字符截取。

**参数：**
- `$string` - 要截取的字符串
- `$length` - 截取长度
- `$dot` - 截取之后用什么表示
- `$code` - 编码格式（UTF8/GBK）

**返回：** 截取后的字符串

---

### string_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0)
字符串加密/解密函数。

**参数：**
- `$string` - 字符串
- `$operation` - ENCODE为加密，DECODE为解密
- `$key` - 密钥
- `$expiry` - 过期时间

**返回：** 加密或解密后的字符串

---

### new_json_encode($array, $options = 0, $depth = 0)
兼容PHP低版本的 json_encode。

**参数：**
- `$array` - 要编码的数组
- `$options` - JSON编码选项
- `$depth` - 深度

**返回：** JSON字符串

---

### array_iconv($data, $input = 'gbk', $output = 'utf-8')
对数据进行编码转换。

**参数：**
- `$data` - 数组或字符串
- `$input` - 需要转换的编码
- `$output` - 转换后的编码

**返回：** 转换后的数据

---

### lry_array_column($array, $column_key, $index_key = null)
兼容低版本的 array_column。

**参数：**
- `$array` - 多维数组
- `$column_key` - 需要返回值的列
- `$index_key` - 作为返回数组的索引/键的列

**返回：** 数组

---

## 数据库函数

### D($tablename)
获取数据库实例。

**参数：**
- `$tablename` - 表名

**返回：** 数据库对象

**用法：**
```php
$db = D('users');
```

---

### to_sqls($data, $front = ' AND ', $in_column = false)
生成SQL语句（已弃用）。

**参数：**
- `$data` - 条件数组或字符串
- `$front` - 连接符
- `$in_column` - 字段名称

**返回：** SQL字符串

**注意：** 本函数在最新版本中已被弃用。

---

## 网络/HTTP函数

### getip()
获取客户端IP地址。

**返回：** IP地址字符串

**说明：** 通过多种方法尝试获取客户端真实IP。

---

### get_url()
获取当前完整的URL地址。

**返回：** 完整URL

**用法：**
```php
$current_url = get_url();
// 例如: https://example.com/path/to/page?param=value
```

---

### get_address($ip, $is_array = false)
获取IP地址对应的地理位置。

**参数：**
- `$ip` - IP地址
- `$is_array` - 是否返回数组形式

**返回：** 地址字符串或数组

---

### https_request($url, $data = '', $array = true, $timeout = 2000, $header = array())
发送HTTPS请求。

**参数：**
- `$url` - 请求的URL地址
- `$data` - POST请求的数据
- `$array` - 返回数据是否转换为数组
- `$timeout` - 请求超时时间（毫秒）
- `$header` - 自定义HTTP请求头

**返回：** 响应数据

**示例：**
```php
// GET请求
https_request('https://api.example.com/data');

// POST请求
https_request('https://api.example.com/data', ['name' => 'test']);
```

---

### is_ssl()
检查当前请求是否使用HTTPS/SSL。

**返回：** 布尔值

---

### is_post()
检查当前请求方法是否为POST。

**返回：** 布尔值

---

### is_get()
检查当前请求方法是否为GET。

**返回：** 布尔值

---

### is_put()
检查当前请求方法是否为PUT。

**返回：** 布尔值

---

### is_ajax()
检查当前请求是否为AJAX。

**返回：** 布尔值

---

### check_ip_matching($ip_vague, $ip = '')
检查IP是否匹配。

**参数：**
- `$ip_vague` - 要检查的IP或IP段，用(*)表示
- `$ip` - 被检查IP

**返回：** 布尔值

---

### send_http_status($code)
发送HTTP状态码。

**参数：**
- `$code` - 状态码（如200、404、500等）

**返回：** void

---

### redirect($url, $time = 0, $msg = '')
URL重定向。

**参数：**
- `$url` - 重定向的URL地址
- `$time` - 重定向的等待时间（秒）
- `$msg` - 重定向前的提示信息

**返回：** void

---

### input($key = '', $default = '', $function = '')
获取输入数据。

**参数：**
- `$key` - 获取的变量名（支持 get.name、post.name、request.name）
- `$default` - 默认值
- `$function` - 处理函数

**返回：** 输入值

**用法：**
```php
$name = input('post.name', '', 'trim');
$id = input('get.id', 0, 'intval');
```

---

## 缓存函数

### getcache($name)
获取缓存。

**参数：**
- `$name` - 缓存名称

**返回：** 缓存值

---

### setcache($name, $data, $timeout = 0)
设置缓存。

**参数：**
- `$name` - 缓存名称
- `$data` - 缓存数据
- `$timeout` - 过期时间（秒）

**返回：** 布尔值

---

### delcache($name, $flush = false)
删除缓存。

**参数：**
- `$name` - 缓存名称
- `$flush` - 是否清空所有缓存

**返回：** 布尔值

---

## Cookie函数

### set_cookie($name, $value = '', $time = 0, $httponly = false)
设置Cookie。

**参数：**
- `$name` - 变量名
- `$value` - 变量值
- `$time` - 过期时间
- `$httponly` - 是否仅HTTP访问

**返回：** void

---

### get_cookie($name = '', $default = '')
获取Cookie。

**参数：**
- `$name` - 变量名，如果为空则获取所有cookie
- `$default` - 默认值

**返回：** Cookie值

---

### del_cookie($name = '')
删除Cookie。

**参数：**
- `$name` - 变量名，如果为空则删除所有cookie

**返回：** 布尔值

---

## Session函数

### new_session_start()
以httponly方式开启SESSION，安全性增强的会话启动函数。

**返回：** 布尔值

---

### create_token($isinput = true)
创建TOKEN（需要先开启SESSION）。

**参数：**
- `$isinput` - 是否返回input标签

**返回：** TOKEN字符串或input标签

---

### check_token($token, $delete = false)
验证TOKEN（需要先开启SESSION）。

**参数：**
- `$token` - TOKEN值
- `$delete` - 是否验证后删除

**返回：** 布尔值

---

## 图片处理函数

### thumb($imgurl, $width = 300, $height = 200, $autocut = 0, $smallpic = 'nopic.jpg')
生成缩略图。

**参数：**
- `$imgurl` - 图片路径
- `$width` - 缩略图宽度
- `$height` - 缩略图高度
- `$autocut` - 是否自动裁剪
- `$smallpic` - 无图片时的默认图片路径

**返回：** 缩略图URL

---

### watermark($source, $target = '')
添加水印。

**参数：**
- `$source` - 原图片路径
- `$target` - 生成水印图片路径，默认覆盖原图

**返回：** 水印图片路径

---

### match_img($content)
获取内容中的图片。

**参数：**
- `$content` - 内容

**返回：** 图片URL

---

### grab_image($content, $targeturl = '')
获取远程图片并保存到本地。

**参数：**
- `$content` - 文章内容
- `$targeturl` - 对方网站的网址

**返回：** 处理后的内容

---

### is_img($ext)
检查是否为图片格式。

**参数：**
- `$ext` - 文件扩展名

**返回：** 布尔值

**支持格式：** png, jpg, jpeg, gif, webp, bmp, ico

---

## 验证函数

### is_email($email)
判断email格式是否正确。

**参数：**
- `$email` - 邮箱地址

**返回：** 布尔值

---

### is_mobile($mobile)
判断手机格式是否正确。

**参数：**
- `$mobile` - 手机号码

**返回：** 布尔值

---

### is_badword($string)
检测输入中是否含有错误字符。

**参数：**
- `$string` - 要检查的字符串

**返回：** 布尔值

---

### is_username($username)
检查用户名是否符合规定。

**参数：**
- `$username` - 用户名

**返回：** 布尔值

**规则：**
- 长度：3-30个字符
- 格式：字母、数字、下划线、中文
- 不能以数字开头（除非全是数字）

---

### is_password($password)
检查密码长度是否符合规定。

**参数：**
- `$password` - 密码

**返回：** 布尔值

**规则：** 长度6-20个字符

---

### is_utf8($string)
判断字符串是否为UTF-8编码。

**参数：**
- `$string` - 字符串

**返回：** 布尔值

---

### is_ie()
IE浏览器判断。

**返回：** 布尔值

---

## 文件处理函数

### fileext($filename)
取得文件扩展名。

**参数：**
- `$filename` - 文件名

**返回：** 扩展名（小写）

---

### file_down($filepath, $filename = '')
文件下载。

**参数：**
- `$filepath` - 文件路径
- `$filename` - 下载时的文件名

**返回：** void

---

### sizecount($size, $prec = 2)
计算文件大小并转换为可读格式。

**参数：**
- `$size` - 文件大小（字节）
- `$prec` - 精度

**返回：** 格式化后的大小（如 "1.5 MB"）

---

## 语言/本地化函数

### L($language = '', $module = '')
获取和设置语言定义。

**参数：**
- `$language` - 语言变量
- `$module` - 模块名

**返回：** 语言字符串

---

## URL函数

### U($url = '', $vars = '', $domain = null, $suffix = true)
生成URL。

**参数：**
- `$url` - URL路径
- `$vars` - 参数
- `$domain` - 是否包含域名
- `$suffix` - 是否添加后缀

**返回：** 完整URL

**用法：**
```php
U('index/user/login');
U('index/user/profile', 'id=1');
U('index/user/profile', ['id' => 1]);
```

---

## 模板函数

### template($module = '', $template = 'index', $theme = '')
模板调用。

**参数：**
- `$module` - 模块名
- `$template` - 模板名称
- `$theme` - 强制模板风格

**返回：** 模板编译文件路径

---

## 模型函数

### M($classname, $m = '')
实例化一个model对象。

**参数：**
- `$classname` - 模型名
- `$m` - 模块

**返回：** 模型对象

---

## 消息/响应函数

### showmsg($msg, $gourl = '', $limittime = 3)
提示信息页面跳转。

**参数：**
- `$msg` - 消息提示信息
- `$gourl` - 跳转地址，'stop'为停止
- `$limittime` - 限制时间（秒）

**返回：** void

---

### return_json($arr = array(), $show_debug = false)
返回JSON格式数据。

**参数：**
- `$arr` - 数组
- `$show_debug` - 是否显示调试信息

**返回：** void（直接输出JSON并退出）

---

### return_message($message, $status = 1, $url = '')
根据请求方式自动返回信息。

**参数：**
- `$message` - 消息内容
- `$status` - 状态码
- `$url` - 跳转URL

**返回：** void

**说明：** AJAX请求返回JSON，普通请求显示提示页面。

---

## 日志函数

### write_error_log($err_array, $path = '')
写入错误日志到文件。

**参数：**
- `$err_array` - 错误信息数组或字符串
- `$path` - 日志文件保存路径

**返回：** 布尔值

**功能：**
- 记录错误发生的时间、URL、IP地址
- 记录POST数据
- 自动创建日志目录
- 日志文件超过20M时自动备份

---

### write_log($message, $filename = '', $ext = '.log', $path = '')
记录日志。

**参数：**
- `$message` - 日志信息
- `$filename` - 文件名称
- `$ext` - 文件后缀
- `$path` - 日志路径

**返回：** 布尔值

---

## 队列函数

### dispatch($job, $params = array(), $queue = '')
下发队列任务。

**参数：**
- `$job` - 队列任务类名称
- `$params` - 传入的参数
- `$queue` - 队列名称

**返回：** 任务ID或false

---

## 工具函数

### P($var)
打印各种类型的数据，调试程序时使用。

**参数：**
- `$var` - 变量，支持传入多个

**返回：** null

**用法：**
```php
P($data);
P($data1, $data2, $data3);
```

---

### create_tradenum()
创建订单号。

**返回：** 订单号字符串（格式：YmdHis + 4位随机数）

---

### format_time($date = 0, $type = 1)
传入日期格式或时间戳格式时间，返回与当前时间的差距。

**参数：**
- `$date` - 日期或时间戳
- `$type` - 1为时间戳格式，2为date时间格式

**返回：** 格式化后的时间（如"1分钟前"、"2小时前"）

---

## 其他函数

### debug()
用于临时屏蔽debug信息。

**返回：** null

---

### set_module_theme($theme = 'default')
用于设置模块的主题。

**参数：**
- `$theme` - 主题名称

**返回：** null

---

### make_auth_key($prefix)
生成验证key。

**参数：**
- `$prefix` - 前缀

**返回：** 验证key字符串

---

## 函数统计

- **配置管理函数：** 3个
- **字符串处理函数：** 14个
- **数据库函数：** 2个
- **网络/HTTP函数：** 13个
- **缓存函数：** 3个
- **Cookie函数：** 3个
- **Session函数：** 3个
- **图片处理函数：** 5个
- **验证函数：** 8个
- **文件处理函数：** 3个
- **语言/本地化函数：** 1个
- **URL函数：** 1个
- **模板函数：** 1个
- **模型函数：** 1个
- **消息/响应函数：** 3个
- **日志函数：** 2个
- **队列函数：** 1个
- **工具函数：** 3个
- **其他函数：** 3个

**总计：** 73个函数

---

## 版本信息

- **文件路径：** `ryphp/core/function/global.func.php`
- **文档生成时间：** 2025-10-10
- **项目：** lryblog.com / RYPHP Framework

