# lryblog.com 项目自定义常量文档

本文档列出了 lryblog.com 项目中所有自定义常量的定义和说明。

---

## 1. 核心入口文件（index.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `RYPHP_DEBUG` | `true` | 调试模式开关，开启后显示详细错误信息 |
| `RYPHP_ROOT` | `dirname(__FILE__).DIRECTORY_SEPARATOR` | 项目根目录的绝对路径 |
| `URL_MODEL` | `'3'` | URL 路由模式设置 |

---

## 2. 框架核心（ryphp/ryphp.php）

### 2.1 框架基础常量

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `IN_RYPHP` | `true` | 框架运行标识，防止文件被直接访问 |
| `RYPHP_RYPHP` | `RYPHP_ROOT . 'ryphp' . DIRECTORY_SEPARATOR` | 框架核心目录路径 |
| `RYPHP_COMMON` | `RYPHP_ROOT . 'common' . DIRECTORY_SEPARATOR` | 公共资源目录路径 |
| `RYPHP_APP` | `RYPHP_ROOT.'application'.DIRECTORY_SEPARATOR` | 应用程序目录路径 |
| `EXT` | `'.class.php'` | 类文件的扩展名 |

### 2.2 版本信息

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `RYPHP_VERSION` | `'1.0.0'` | 框架版本号 |
| `RYPHP_RELEASE` | `'20250707'` | 框架发布日期 |

### 2.3 时间相关

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `SYS_START_TIME` | `microtime(true)` | 系统启动时间（微秒级），用于性能分析 |
| `SYS_TIME` | `time()` | 当前时间戳（秒级） |

### 2.4 服务器与网络

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `SERVER_REQUEST_SCHEME` | `is_ssl() ? 'https://' : 'http://'` | 请求协议（HTTP 或 HTTPS） |
| `HTTP_HOST` | `$_SERVER['HTTP_HOST']` | HTTP 主机名 |
| `HTTP_REFERER` | `$_SERVER['HTTP_REFERER']` | HTTP 来源地址 |
| `IS_CGI` | `0 或 1` | 是否运行在 CGI/FastCGI 模式 |

### 2.5 路径与 URL

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `PHP_FILE` | 动态计算 | 当前 PHP 文件路径 |
| `SITE_PATH` | `str_replace('index.php', '' , PHP_FILE)` | 站点相对路径 |
| `SITE_URL` | `SERVER_REQUEST_SCHEME.HTTP_HOST.SITE_PATH` | 站点完整 URL |
| `STATIC_URL` | `SITE_URL.'common/static/'` | 静态资源 URL 前缀 |

### 2.6 PHP 配置

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `MAGIC_QUOTES_GPC` | 动态检测 | 魔术引号开关状态（兼容旧版 PHP） |

### 2.7 软件信息

| 常量名 | 默认值 | 说明 |
|--------|--------|------|
| `RYCMS_SOFTNAME` | `'RYCMS内容管理系统'` | CMS 软件名称 |

---

## 3. 路由常量（ryphp/core/class/application.class.php）

| 常量名 | 说明 |
|--------|------|
| `ROUTE_M` | 当前路由的模块名（Module） |
| `ROUTE_C` | 当前路由的控制器名（Controller） |
| `ROUTE_A` | 当前路由的方法名（Action） |

---

## 4. 功能函数常量（ryphp/core/function/global.func.php）

| 常量名 | 说明 |
|--------|------|
| `RYPHP_DEBUG_HIDDEN` | 调试信息隐藏标识 |
| `MODULE_THEME` | 当前模块使用的主题名称 |

---

## 5. 工具类常量

### 5.1 树形结构类（tree.class.php, tree_guanfang.class.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `EFFECTED_INIT` | `1` | 影响范围初始化标识 |

### 5.2 表单类（form.class.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `DATETIME` | `1` | 日期时间字段标识 |

### 5.3 分页类（page.class.php）

| 常量名 | 说明 |
|--------|------|
| `TOTAL_PAGE` | 总页数（在分页计算时定义） |

---

## 6. 版本信息（common/data/version.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `RYCMS_VERSION` | `'V1.0'` | RYCMS 系统版本号 |
| `RYCMS_UPDATE` | `'20250808'` | RYCMS 最后更新日期 |

---

## 7. 后台管理（application/lry_admin_center/controller/common.class.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `IN_RYPHP_ADMIN` | `true` | 后台管理区域标识，用于权限控制 |

---

## 8. 安装程序（application/install/index.php）

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `IN_RYPHP` | `true` | 框架运行标识 |
| `APPDIR` | 动态计算 | 应用程序目录的绝对路径 |
| `SITEDIR` | `dirname(APPDIR).DIRECTORY_SEPARATOR` | 站点根目录路径 |
| `VERSION` | `'RYCMS V1.0'` | 安装程序版本标识 |

---

## 9. UEditor 编辑器插件（common/static/plugin/ueditor/php/）

UEditor 插件为了独立运行，重新定义了以下常量：

### 9.1 lry_action.php

| 常量名 | 说明 |
|--------|------|
| `RYPHP_DEBUG` | 调试模式 |
| `URL_MODEL` | URL 模式 |
| `MAGIC_QUOTES_GPC` | 魔术引号状态 |
| `SYS_TIME` | 系统时间 |
| `SERVER_REQUEST_SCHEME` | 请求协议 |
| `HTTP_HOST` | 主机名 |
| `SITE_PATH` | 站点路径 |
| `SITE_URL` | 站点 URL |
| `RYPHP_ROOT` | 项目根目录 |
| `RYPHP_APP` | 应用目录 |
| `EXT` | 类文件扩展名 |
| `IN_ADMIN` | 管理员登录标识（根据 SESSION 判断） |

### 9.2 controller.php

| 常量名 | 值 | 说明 |
|--------|-----|------|
| `IN_RYPHP` | `true` | 框架运行标识（重复定义） |

---

## 常量使用规范

### 1. 安全标识常量
- `IN_RYPHP` - 在所有框架文件开头检查，防止直接访问
- `IN_RYPHP_ADMIN` - 后台文件专用，确保只有管理员可访问
- `IN_ADMIN` - 用于判断当前用户是否为管理员

### 2. 路径常量
所有路径常量均使用 `DIRECTORY_SEPARATOR` 以保证跨平台兼容性。

### 3. 调试相关
- 生产环境应将 `RYPHP_DEBUG` 设置为 `false`
- 调试模式会显示详细的错误信息和性能数据

### 4. URL 模式
`URL_MODEL` 可能的值：
- `'1'` - 普通模式（如：index.php?m=index&c=index&a=index）
- `'2'` - PATHINFO 模式（如：index.php/index/index/index）
- `'3'` - 伪静态模式（如：/index/index/index）

---

## 注意事项

1. **避免重复定义**：使用 `defined()` 函数检查常量是否已定义
   ```php
   defined('CONSTANT_NAME') or define('CONSTANT_NAME', 'value');
   ```

2. **命名规范**：
   - 使用全大写字母
   - 单词间使用下划线分隔
   - 使用有意义的名称

3. **路径安全**：所有涉及文件系统的常量应使用绝对路径

4. **性能考虑**：常量在编译时确定，访问速度快于变量

---

**文档生成时间**：2025-10-05  
**项目版本**：RYCMS V1.0  
**框架版本**：RYPHP 1.0.0
