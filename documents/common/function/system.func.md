# system.func.php 函数列表

本文档列出了 `common/function/system.func.php` 文件中的所有函数，并按功能分类。

## 目录

- [站点配置函数](#站点配置函数)
- [SEO相关函数](#seo相关函数)
- [栏目管理函数](#栏目管理函数)
- [模型管理函数](#模型管理函数)
- [内容管理函数](#内容管理函数)
- [会员管理函数](#会员管理函数)
- [附件管理函数](#附件管理函数)
- [URL路由函数](#url路由函数)
- [主题模板函数](#主题模板函数)
- [广告相关函数](#广告相关函数)
- [邮件短信函数](#邮件短信函数)
- [工具函数](#工具函数)

---

## 站点配置函数

### get_config($key = '')
获取系统配置信息。

**参数：**
- `$key` - 配置键名，为空时返回所有配置

**返回：** 配置值或配置数组

**用法：**
```php
// 获取单个配置
$site_name = get_config('site_name');

// 获取所有配置
$configs = get_config();
```

**说明：** 配置会被缓存，提高性能。

---

### get_siteid()
获取当前站点ID。

**返回：** 站点ID（整数）

**说明：** 用于站群功能，如果不存在站群模块则返回 0。

---

### get_site($siteid = 0, $parameter = '')
获取站点信息（站群功能）。

**参数：**
- `$siteid` - 站点ID
- `$parameter` - 获取的字段名，为空时返回所有字段

**返回：** 站点信息数组或指定字段值

**兼容性：** 兼容站群模块，如未安装站群模块则返回空。

---

### get_site_url()
获取当前站点首页URL。

**返回：** 站点URL字符串

**用法：**
```php
$site_url = get_site_url();
// 例如: https://example.com/
```

---

### get_urlrule()
获取URL路由规则。

**返回：** 路由规则数组

**说明：** 从数据库读取并缓存URL重写规则。

---

### set_mapping($m)
设置站点映射规则。

**参数：**
- `$m` - 模块名

**返回：** 映射规则数组

**说明：** 生成栏目URL映射规则，用于URL路由。

---

## SEO相关函数

### get_seo_suffix()
获取系统SEO后缀。

**返回：** SEO后缀字符串

**用法：**
```php
$suffix = get_seo_suffix();
// 例如: " - 网站名称"
```

---

### get_site_seo($siteid = null, $title = '', $keyword = '', $site_description = '')
获取当前站点SEO信息。

**参数：**
- `$siteid` - 站点ID，默认为当前站点
- `$title` - 页面标题
- `$keyword` - 关键词
- `$site_description` - 描述

**返回：** 数组 [标题, 关键词, 描述]

**用法：**
```php
list($site_name, $site_keyword, $site_description) = get_site_seo();
list($page_title, $keywords, $desc) = get_site_seo(null, '文章标题', '关键词');
```

---

## 栏目管理函数

### get_category($catid = 0, $parameter = '', $all = false)
获取栏目信息。

**参数：**
- `$catid` - 栏目ID，为0时返回所有栏目
- `$parameter` - 获取的字段名，为空时返回所有字段
- `$all` - 是否获取所有站点的栏目（默认只获取当前站点）

**返回：** 栏目信息数组或指定字段值

**用法：**
```php
// 获取所有栏目
$categories = get_category();

// 获取指定栏目信息
$catinfo = get_category(1);

// 获取栏目名称
$catname = get_category(1, 'catname');

// 获取栏目模型ID
$modelid = get_category(1, 'modelid', true);
```

**说明：** 数据会被缓存，提高性能。

---

### get_catname($catid)
根据栏目ID获取栏目名称。

**参数：**
- `$catid` - 栏目ID

**返回：** 栏目名称字符串

**用法：**
```php
$name = get_catname(1);
```

---

### get_childcat($catid, $is_show = false, $limit = 0)
根据栏目ID获取子栏目信息。

**参数：**
- `$catid` - 栏目ID
- `$is_show` - 前端不显示的栏目是否显示
- `$limit` - 限制数量，0为不限制

**返回：** 子栏目数组

**用法：**
```php
// 获取所有子栏目
$child_cats = get_childcat(1);

// 获取前5个子栏目
$child_cats = get_childcat(1, false, 5);
```

---

### get_location($catid, $is_mobile = false, $self = true, $symbol = ' &gt; ')
根据栏目ID获取面包屑导航（当前位置）。

**参数：**
- `$catid` - 栏目ID
- `$is_mobile` - 是否为手机版
- `$self` - 是否包含当前栏目
- `$symbol` - 栏目间隔符

**返回：** HTML格式的面包屑导航

**用法：**
```php
// PC版面包屑
echo get_location(5);
// 输出: <a href="/">首页</a> > <a href="/news">新闻</a> > <a href="/news/tech">科技</a>

// 手机版面包屑
echo get_location(5, true);

// 不包含当前栏目
echo get_location(5, false, false);
```

---

### select_category($name = 'parentid', $value = '0', $root = '', $member_publish = 0, $attribute = '', $parent_disabled = true, $disabled = true, $modelid = 0)
获取栏目下拉选择框HTML。

**参数：**
- `$name` - select的name属性
- `$value` - 选中的栏目ID
- `$root` - 顶级分类名称
- `$member_publish` - 是否仅显示允许投稿的栏目
- `$attribute` - 附加HTML属性
- `$parent_disabled` - 是否禁用父级栏目
- `$disabled` - 是否禁用单页和外部链接
- `$modelid` - 指定模型ID（只显示该模型的栏目）

**返回：** HTML select标签

**用法：**
```php
// 基本用法
echo select_category();

// 指定选中值
echo select_category('catid', 5);

// 只显示文章模型的栏目
echo select_category('catid', 0, '请选择栏目', 0, '', true, true, 1);
```

---

### is_childid($data)
判断是否存在子栏目。

**参数：**
- `$data` - 栏目信息数组或栏目ID

**返回：** 布尔值

**用法：**
```php
if(is_childid(1)) {
    echo "该栏目有子栏目";
}

// 或传入栏目数组
$catinfo = get_category(1);
if(is_childid($catinfo)) {
    echo "该栏目有子栏目";
}
```

---

## 模型管理函数

### get_modelinfo($typeall = 0)
获取模型信息。

**参数：**
- `$typeall` - 0只包含内容模型，1包含全部模型

**返回：** 模型信息数组

**用法：**
```php
// 获取内容模型
$models = get_modelinfo();

// 获取所有模型
$all_models = get_modelinfo(1);
```

**说明：** 数据会被缓存，提高性能。

---

### get_site_modelinfo()
获取当前站点的模型信息。

**返回：** 模型信息数组

**说明：** 只返回当前站点启用的内容模型。

---

### get_model($modelid, $parameter = 'tablename', $is_site = false)
根据模型ID获取模型信息。

**参数：**
- `$modelid` - 模型ID
- `$parameter` - 获取的字段名，为空时返回所有字段
- `$is_site` - 是否只获取当前站点的模型

**返回：** 模型信息或指定字段值

**用法：**
```php
// 获取模型表名
$table = get_model(1);
// 或
$table = get_model(1, 'tablename');

// 获取模型名称
$modelname = get_model(1, 'name');

// 获取完整模型信息
$modelinfo = get_model(1, false);
```

---

### get_default_model($key = false)
获取当前站点默认模型。

**参数：**
- `$key` - 获取的字段名，为false时返回所有字段

**返回：** 默认模型信息或指定字段值

**用法：**
```php
// 获取默认模型信息
$default_model = get_default_model();

// 获取默认模型ID
$modelid = get_default_model('modelid');
```

---

## 内容管理函数

### get_content_url($catid, $id)
获取内容页URL。

**参数：**
- `$catid` - 栏目ID
- `$id` - 内容ID

**返回：** 内容URL

**用法：**
```php
$url = get_content_url(5, 100);
// 例如: /news/100.html
```

---

### page_content($catid = 1, $limit = 300, $strip = true, $field = 'content')
单页面内容标签，用于在首页或频道页调取单页的简介。

**参数：**
- `$catid` - 栏目ID
- `$limit` - 截取长度
- `$strip` - 是否去除HTML标签
- `$field` - 获取的字段名

**返回：** 内容字符串

**用法：**
```php
// 获取单页简介
echo page_content(10, 200);

// 获取完整内容（不截取）
echo page_content(10, 0, false);
```

---

### get_thumb($thumb, $default = '')
获取内容缩略图。

**参数：**
- `$thumb` - 缩略图URL
- `$default` - 默认图片URL

**返回：** 图片URL

**用法：**
```php
// 有缩略图时返回缩略图，否则返回默认图
$img = get_thumb($article['thumb']);

// 指定默认图
$img = get_thumb($article['thumb'], '/images/default.jpg');
```

---

### title_color($title, $color = '')
渲染标题颜色。

**参数：**
- `$title` - 标题文字
- `$color` - 颜色值（如 #FF0000）

**返回：** HTML字符串

**用法：**
```php
echo title_color('重要通知', '#FF0000');
// 输出: <span class="title_color" style="color:#FF0000">重要通知</span>
```

---

### content_list_tag($catid = 0, $id = 0, $limit = 5)
获取内容列表页TAG标签。

**参数：**
- `$catid` - 栏目ID
- `$id` - 内容ID
- `$limit` - 限制数量

**返回：** TAG数组

**用法：**
```php
$tags = content_list_tag(5, 100, 10);
foreach($tags as $tag) {
    echo $tag['tag'];
}
```

---

### content_total($modelid, $catid = 0)
获取内容总数。

**参数：**
- `$modelid` - 模型ID
- `$catid` - 栏目ID，0为不限制栏目

**返回：** 内容数量（整数）

**用法：**
```php
// 获取文章模型的总数
$total = content_total(1);

// 获取指定栏目的文章总数
$total = content_total(1, 5);
```

---

### get_content_keyword()
获取内容关键词链接。

**返回：** 关键词数组

**说明：** 用于自动为内容添加关键词链接。

---

### get_comment_total($id, $catid = 0, $modelid = 1)
获取内容评论数。

**参数：**
- `$id` - 内容ID
- `$catid` - 栏目ID
- `$modelid` - 模型ID

**返回：** 评论数（整数）

**用法：**
```php
$comment_count = get_comment_total(100, 5, 1);
```

---

### get_field_val($value, $field, $modelid)
获取自定义多选字段的显示值。

**参数：**
- `$value` - 字段值（如 1、2、3）
- `$field` - 字段名
- `$modelid` - 模型ID

**返回：** 显示值（字符串）

**用法：**
```php
// 将 1 转换为 "Windows"
$os_name = get_field_val($systems, 'systems', 1);
```

**说明：** 用于将存储的键值转换为可读的显示文本。

---

### get_copyfrom($modelid = 1)
获取来源列表。

**参数：**
- `$modelid` - 模型ID

**返回：** 来源数组

**用法：**
```php
$copyfrom_list = get_copyfrom(1);
// 返回: ['网络', '原创', '新浪', '搜狐', ...]
```

**说明：** 返回最近100条内容的来源 + 默认来源（网络、原创）。

---

## 会员管理函数

### get_memberavatar($user, $type = 1, $default = true)
获取用户头像。

**参数：**
- `$user` - 用户ID或用户名
- `$type` - 1为根据userid查询，其他为根据username查询
- `$default` - 如果用户头像为空，是否显示默认头像

**返回：** 头像URL

**用法：**
```php
// 根据用户ID获取头像
$avatar = get_memberavatar(1);

// 根据用户名获取头像
$avatar = get_memberavatar('admin', 2);

// 不显示默认头像
$avatar = get_memberavatar(1, 1, false);
```

---

### get_memberinfo($userid, $additional = false)
获取用户所有信息。

**参数：**
- `$userid` - 用户ID
- `$additional` - 是否获取附表信息（member_detail）

**返回：** 用户信息数组或false

**用法：**
```php
// 获取基本信息
$member = get_memberinfo(1);

// 获取完整信息（包括附表）
$member = get_memberinfo(1, true);
```

**返回字段：** username, regdate, lastdate, lastip, loginnum, email, groupid, amount, experience, point, status, vip, overduedate, email_status 等。

---

### get_groupinfo($groupid = '')
获取会员组别信息。

**参数：**
- `$groupid` - 组别ID，为空时返回所有组别

**返回：** 组别信息数组

**用法：**
```php
// 获取所有组别
$groups = get_groupinfo();

// 获取指定组别
$group = get_groupinfo(1);
```

---

### get_groupname($groupid)
根据组别ID获取组别名称。

**参数：**
- `$groupid` - 组别ID

**返回：** 组别名称字符串

**用法：**
```php
$group_name = get_groupname(1);
```

---

### password($pass)
对用户密码进行加密。

**参数：**
- `$pass` - 明文密码

**返回：** 加密后的密码

**用法：**
```php
$encrypted_pwd = password('123456');
```

**加密方式：** md5(substr(md5(trim($pass)), 3, 26))

---

## 附件管理函数

### file_icon($file)
返回附件类型图标。

**参数：**
- `$file` - 附件名称或路径

**返回：** 图标URL

**用法：**
```php
$icon = file_icon('document.pdf');
// 返回: /common/static/images/ext/pdf.png
```

**支持格式：** code, css, dir, doc, docx, gif, html, jpeg, jpg, js, mp3, mp4, pdf, php, png, ppt, pptx, psd, rar, sql, swf, txt, xls, xlsx, xml, zip

---

### update_attachment($modelid, $id)
更新内容附件关联。

**参数：**
- `$modelid` - 模型ID
- `$id` - 内容ID

**返回：** 布尔值

**说明：** 将临时上传的附件关联到具体内容。

---

### delete_attachment($modelid, $id)
删除内容附件。

**参数：**
- `$modelid` - 模型ID
- `$id` - 内容ID

**返回：** 布尔值

**说明：** 删除内容时同时删除关联的附件文件。

---

### down_remote_img($content, $targeturl = '')
下载远程图片到本地。

**参数：**
- `$content` - 文章内容
- `$targeturl` - 远程网站URL（用于处理相对路径）

**返回：** 处理后的内容

**用法：**
```php
$content = down_remote_img($content, 'https://example.com');
```

**功能：**
- 自动识别内容中的远程图片
- 下载到本地服务器
- 替换图片链接
- 记录到附件表

---

### handle_upload_types($type = 1)
处理上传文件类型。

**参数：**
- `$type` - 1为图片类型，2为附件类型

**返回：** 允许的文件类型数组

**用法：**
```php
// 获取允许的图片类型
$image_types = handle_upload_types(1);

// 获取允许的附件类型
$file_types = handle_upload_types(2);
```

**允许的类型：** png, gif, jpg, jpeg, webp, bmp, ico, zip, rar, 7z, gz, doc, docx, xls, xlsx, ppt, pptx, pdf, txt, csv, mp3, mp4, avi, wmv, rmvb, flv, wma, wav, amr, ogg, ogv, webm, swf, mkv, torrent

---

## URL路由函数

### mobile_url($catid, $id)
生成手机版内容URL。

**参数：**
- `$catid` - 栏目ID
- `$id` - 内容ID

**返回：** 手机版URL

**用法：**
```php
$mobile_url = mobile_url(5, 100);
```

---

### tag_url($tid, $domain = null)
生成TAG标签URL。

**参数：**
- `$tid` - TAG ID
- `$domain` - 域名，为null时使用当前域名

**返回：** TAG URL

**用法：**
```php
$url = tag_url(10);
// 或指定域名
$url = tag_url(10, 'https://example.com');
```

---

### url_referer($is_login = 1, $referer = '')
会员登录/退出跳转URL。

**参数：**
- `$is_login` - 1为登录，0为退出
- `$referer` - 跳转地址，为空时使用当前页

**返回：** 跳转URL

**用法：**
```php
// 登录URL
$login_url = url_referer(1);

// 退出URL
$logout_url = url_referer(0);

// 指定跳转地址
$login_url = url_referer(1, '/user/center');
```

---

## 主题模板函数

### get_theme_list($m = 'index')
获取模板主题列表。

**参数：**
- `$m` - 模块名

**返回：** 主题名称数组

**用法：**
```php
$themes = get_theme_list('index');
// 返回: ['default', 'blue', 'green', ...]
```

**说明：** 扫描模块view目录下的所有主题文件夹。

---

## 广告相关函数

### adver($id)
广告调用。

**参数：**
- `$id` - 广告位ID

**返回：** 广告代码或提示信息

**用法：**
```php
echo adver(1);
```

**返回值：**
- 广告代码（正常）
- "广告位不存在"
- "广告未开始"
- "广告已过期"

---

## 邮件短信函数

### sendmail($email, $title = '', $content = '', $mailtype = 'HTML')
发送邮件。

**参数：**
- `$email` - 收件人邮箱
- `$title` - 邮件标题
- `$content` - 邮件内容
- `$mailtype` - 邮件内容类型（HTML/TEXT）

**返回：** 布尔值

**用法：**
```php
$result = sendmail('user@example.com', '欢迎注册', '注册成功！');
```

**前提条件：** 需要在后台配置好SMTP邮箱信息。

---

## 工具函数

### ismobile()
判断是否为手机访问。

**返回：** 布尔值

**用法：**
```php
if(ismobile()) {
    // 手机端逻辑
} else {
    // PC端逻辑
}
```

**检测方式：**
- 检查 HTTP_X_WAP_PROFILE
- 检查 User-Agent 中的关键词（android, iphone, ipod, mobile等）

---

### field_order($field)
字段排序链接生成。

**参数：**
- `$field` - 字段名

**返回：** 排序图标HTML

**用法：**
```php
echo field_order('id');
echo field_order('createtime');
```

**说明：** 用于后台列表页面生成可点击的排序图标。

---

## 函数统计

- **站点配置函数：** 6个
- **SEO相关函数：** 2个
- **栏目管理函数：** 6个
- **模型管理函数：** 4个
- **内容管理函数：** 9个
- **会员管理函数：** 5个
- **附件管理函数：** 5个
- **URL路由函数：** 3个
- **主题模板函数：** 1个
- **广告相关函数：** 1个
- **邮件短信函数：** 1个
- **工具函数：** 2个

**总计：** 45个函数

---

## 核心函数详解

### 1. get_category() - 栏目获取核心函数

这是系统中最常用的函数之一，用于获取栏目信息。

**特点：**
- 支持缓存机制，性能优秀
- 支持单站点和站群模式
- 可以获取所有栏目或单个栏目
- 可以获取完整信息或单个字段

**使用场景：**
```php
// 场景1: 获取所有栏目（用于导航菜单）
$categories = get_category();
foreach($categories as $cat) {
    echo $cat['catname'];
}

// 场景2: 获取单个栏目信息（用于面包屑）
$catinfo = get_category($catid);
echo $catinfo['catname'];

// 场景3: 只需要某个字段（性能最佳）
$modelid = get_category($catid, 'modelid');
```

---

### 2. get_config() - 配置获取核心函数

获取系统配置信息的核心函数。

**特点：**
- 从数据库config表读取
- 自动缓存，避免重复查询
- 支持获取单个或全部配置

**常用配置项：**
- `site_name` - 网站名称
- `site_url` - 网站地址
- `site_keyword` - 网站关键词
- `site_description` - 网站描述
- `upload_file` - 上传目录
- `upload_image_types` - 允许上传的图片类型
- `url_mode` - URL模式

---

### 3. select_category() - 栏目选择器

生成栏目下拉选择框，支持树形结构显示。

**应用场景：**
- 后台发布内容时选择栏目
- 后台添加栏目时选择父栏目
- 会员投稿时选择栏目

**高级用法：**
```php
// 只显示允许投稿的栏目
echo select_category('catid', 0, '请选择栏目', 1);

// 只显示文章模型的栏目
echo select_category('catid', 0, '请选择栏目', 0, '', true, true, 1);

// 禁用父级栏目（只能选择终极栏目）
echo select_category('catid', 0, '请选择栏目', 0, '', true);
```

---

### 4. down_remote_img() - 远程图片下载

采集或复制文章时，自动下载远程图片到本地。

**工作流程：**
1. 从内容中提取所有图片URL
2. 检查是否为远程图片
3. 下载到本地服务器
4. 保存附件记录
5. 替换原链接为本地链接

**配置项：**
- `down_ignore_domain` - 忽略的域名（不下载）

---

## 函数依赖关系

### 栏目相关函数依赖树
```
get_category() [核心]
├── get_catname() [依赖]
├── get_childcat() [依赖]
├── get_location() [依赖]
├── is_childid() [依赖]
└── select_category() [依赖]
```

### 站点相关函数依赖树
```
get_siteid() [基础]
├── get_site() [依赖]
├── get_site_url() [依赖]
├── get_site_seo() [依赖]
└── get_category() [间接依赖]
```

### 模型相关函数依赖树
```
get_modelinfo() [核心]
├── get_site_modelinfo() [依赖]
├── get_model() [依赖]
└── get_default_model() [依赖]
```

---

## 性能优化建议

### 1. 合理使用参数避免过度查询
```php
// ❌ 不推荐：多次查询
$catname = get_category($catid)['catname'];
$modelid = get_category($catid)['modelid'];

// ✅ 推荐：一次查询
$catinfo = get_category($catid);
$catname = $catinfo['catname'];
$modelid = $catinfo['modelid'];

// ✅ 最佳：只获取需要的字段
$catname = get_category($catid, 'catname');
```

### 2. 利用缓存机制
以下函数都使用了缓存，可以放心调用：
- `get_config()`
- `get_category()`
- `get_modelinfo()`
- `get_groupinfo()`
- `get_urlrule()`

### 3. 避免在循环中调用数据库函数
```php
// ❌ 不推荐
foreach($list as $item) {
    $item['catname'] = get_catname($item['catid']);
}

// ✅ 推荐
$categories = get_category(); // 获取所有栏目
foreach($list as $item) {
    $item['catname'] = $categories[$item['catid']]['catname'];
}
```

---

## 版本信息

- **文件路径：** `common/function/system.func.php`
- **文档生成时间：** 2025-10-10
- **项目：** lryblog.com / RYCMS
- **作者：** lirongyaoper

---

## 附录：常见使用场景

### 场景1: 获取文章详情页所需数据
```php
// 获取栏目信息
$catinfo = get_category($catid);

// 获取面包屑导航
$location = get_location($catid);

// 获取SEO信息
list($title, $keywords, $description) = get_site_seo(null, $article['title'], $article['keywords'], $article['description']);

// 获取评论数
$comment_total = get_comment_total($id, $catid, $modelid);
```

### 场景2: 后台栏目管理
```php
// 获取所有栏目
$categories = get_category();

// 生成栏目选择器
$category_select = select_category('parentid', $catinfo['parentid']);

// 检查是否有子栏目
if(is_childid($catid)) {
    echo "该栏目有子栏目，不能删除";
}
```

### 场景3: 会员中心
```php
// 获取会员信息
$member = get_memberinfo($userid, true);

// 获取会员头像
$avatar = get_memberavatar($userid);

// 获取会员组名称
$groupname = get_groupname($member['groupid']);
```

### 场景4: 内容发布
```php
// 下载远程图片
$content = down_remote_img($content, $source_url);

// 更新附件关联
update_attachment($modelid, $id);

// 生成内容URL
$url = get_content_url($catid, $id);
```

