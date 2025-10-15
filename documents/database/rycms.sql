DROP TABLE IF EXISTS `rycms_category`;
CREATE TABLE `rycms_category` (
  `catid` smallint NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `siteid` tinyint unsigned NOT NULL DEFAULT '0',
  `catname` varchar(60) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `modelid` smallint unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `parentid` smallint unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `arrparentid` varchar(255) NOT NULL DEFAULT '' COMMENT '父级路径',
  `arrchildid` mediumtext NOT NULL COMMENT '子栏目id集合',
  `catdir` varchar(50) NOT NULL DEFAULT '' COMMENT '栏目目录',
  `catimg` varchar(150) NOT NULL DEFAULT '' COMMENT '栏目图片',
  `cattype` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '栏目类型:0普通栏目1单页2外部链接',
  `listorder` smallint unsigned NOT NULL DEFAULT '0' COMMENT '栏目排序',
  `target` char(10) NOT NULL DEFAULT '' COMMENT '打开方式',
  `member_publish` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否会员投稿',
  `display` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '在导航显示',
  `pclink` varchar(100) NOT NULL DEFAULT '' COMMENT '电脑版地址',
  `domain` varchar(100) NOT NULL DEFAULT '' COMMENT '绑定域名',
  `entitle` varchar(80) NOT NULL DEFAULT '' COMMENT '英文标题',
  `subtitle` varchar(60) NOT NULL DEFAULT '' COMMENT '副标题',
  `mobname` varchar(50) NOT NULL DEFAULT '' COMMENT '手机版名称',
  `category_template` varchar(30) NOT NULL DEFAULT '' COMMENT '频道页模板',
  `list_template` varchar(30) NOT NULL DEFAULT '' COMMENT '列表页模板',
  `show_template` varchar(30) NOT NULL DEFAULT '' COMMENT '内容页模板',
  `seo_title` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  `seo_description` varchar(250) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  PRIMARY KEY (`catid`),
  KEY `siteid` (`siteid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ry_category
-- ----------------------------
INSERT INTO `rycms_category` VALUES ('1', '0', '新闻中心', '1', '0', '0', '1,2,3', 'xinwenzhongxin', '', '0', '0', '_self', '0', '1', '/xinwenzhongxin/', '', '', '', '新闻中心', 'category_article', 'list_article', 'show_article', '', '', '');
INSERT INTO `rycms_category` VALUES ('2', '0', 'RYCMS新闻', '1', '1', '0,1', '2', 'rycmsxinwen', '', '0', '0', '_self', '0', '1', '/rycmsxinwen/', '', '', '', '官方新闻', 'category_article', 'list_article_img', 'show_article', '', '', '');
INSERT INTO `rycms_category` VALUES ('3', '0', '其他新闻', '1', '1', '0,1', '3', 'qitaxinwen', '', '0', '0', '_self', '1', '1', '/qitaxinwen/', '', '', '', '其他新闻', 'category_article', 'list_article', 'show_article', '', '', '');
INSERT INTO `rycms_category` VALUES ('4', '0', '关于我们', '0', '0', '0', '4', 'guanyuwomen', '', '1', '0', '_self', '0', '1', '/guanyuwomen/', '', '', '', '关于我们', 'category_page', '', '', '', '', '');
INSERT INTO `rycms_category` VALUES ('5', '0', '官方网站', '0', '0', '0', '5', '', '', '2', '0', '_blank', '0', '1', 'https://www.lrycms.com/', '', '', '', '官方网站', '', '', '', '', '', '');


DROP TABLE IF EXISTS `rycms_config`;
CREATE TABLE `rycms_config` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '配置说明',
  `value` text NOT NULL COMMENT '配置值',
  `fieldtype` varchar(20) NOT NULL DEFAULT '' COMMENT '字段类型',
  `setting` text COMMENT '字段设置',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4;

-- 使用完整列名的单行插入语句
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (1, 'site_name', 0, '站点名称', '荣耀历程--一个有激情，有梦想，不断探索未知世界的热血青年', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (2, 'site_url', 0, '站点根网址', 'https://lirongyaoper.com/', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (3, 'site_keyword', 0, '站点关键字', '个人博客,精彩人生', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (4, 'site_description', 0, '站点描述', '这是来自一个编程爱好者的个人博客，他热爱生活，热爱工作与学习，愿陪大家一起走过每一个春夏秋冬！', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (5, 'site_copyright', 0, '版权信息', '版权归属 荣耀小科技 © 2014-2026', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (6, 'site_filing', 0, '站点备案号', '豫ICP备17033830号-1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (7, 'site_code', 0, '统计代码', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (8, 'site_theme', 0, '站点模板主题', 'rongyao', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (9, 'site_logo', 0, '站点logo', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (10, 'url_mode', 0, '前台URL模式', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (11, 'is_words', 0, '是否开启前端留言功能', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (12, 'upload_maxsize', 0, '文件上传最大限制', '2048', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (13, 'upload_types', 0, '允许上传附件类型', 'zip|rar|ppt|doc|xls', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (14, 'upload_image_types', 0, '允许上传图片类型', 'png|jpg|jpeg|gif', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (15, 'watermark_enable', 0, '是否开启图片水印', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (16, 'watermark_name', 0, '水印图片名称', 'mark.png', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (17, 'watermark_position', 0, '水印的位置', '7', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (18, 'mail_server', 1, 'SMTP服务器', 'ssl://smtp.163.com', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (19, 'mail_port', 1, 'SMTP服务器端口', '465', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (20, 'mail_from', 1, 'SMTP服务器的用户邮箱', 'lirongyaoper@163.com', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (21, 'mail_auth', 1, 'AUTH LOGIN验证', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (22, 'mail_user', 1, 'SMTP服务器的用户帐号', 'lirongyaoper@163.com', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (23, 'mail_pass', 1, 'SMTP服务器的用户密码', 'RUj8fgZHNpxQFGxy', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (24, 'mail_inbox', 1, '收件邮箱地址', 'lirongyaoper@163.com', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (25, 'admin_log', 2, '启用后台管理操作日志', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (26, 'admin_prohibit_ip', 2, '禁止登录后台的IP', '95.72.*.*,46.161.11.199', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (27, 'prohibit_words', 2, '屏蔽词', '她妈|它妈|他妈|你妈|去死|贱人', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (28, 'comment_check', 2, '是否开启评论审核', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (29, 'comment_tourist', 2, '是否允许游客评论', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (30, 'is_link', 2, '允许用户申请友情链接', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (31, 'member_register', 3, '是否开启会员注册', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (32, 'member_email', 3, '新会员注册是否需要邮件验证', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (33, 'member_check', 3, '新会员注册是否需要管理员审核', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (34, 'member_point', 3, '新会员默认积分', '20', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (35, 'member_lry', 3, '是否开启会员登录验证码', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (36, 'rmb_point_rate', 3, '1元人民币购买积分数量', '10', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (37, 'login_point', 3, '每日登录奖励积分', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (38, 'comment_point', 3, '发布评论奖励积分', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (39, 'publish_point', 3, '投稿奖励积分', '3', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (40, 'qq_app_id', 3, 'QQ App ID', '101905949', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (41, 'qq_app_key', 3, 'QQ App key', 'ad5393b046c2cd323c8581734e505c9f', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (42, 'weibo_key', 4, '微博登录App Key', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (43, 'weibo_secret', 4, '微博登录App Secret', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (44, 'wx_appid', 4, '微信开发者ID', 'wx5a85749dffa38259', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (45, 'wx_secret', 4, '微信开发者密码', '8e2ebe8e3f72039d7344e6ea52e4ddbc', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (46, 'wx_token', 4, '微信Token签名', 'tlbra8x474k', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (47, 'wx_encodingaeskey', 4, '微信EncodingAESKey', 'HkMrjxv5ih3mNujRPh7uGhyMIpMcug8qAwGTuSTDsgX', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (48, 'wx_relation_model', 4, '微信关联模型', 'article', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (49, 'baidu_push_token', 0, '百度推送token', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (50, 'thumb_width', 2, '缩略图默认宽度', '500', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (51, 'thumb_height', 2, '缩略图默认高度', '300', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (52, 'site_seo_division', 0, '站点标题分隔符', '_', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (53, 'keyword_link', 2, '是否启用关键字替换', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (54, 'keyword_replacenum', 2, '关键字替换次数', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (55, 'error_log_save', 2, '是否保存系统错误日志', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (56, 'comment_code', 2, '是否开启评论验证码', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (57, 'site_wap_open', 0, '是否启用手机站点', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (58, 'site_wap_theme', 0, 'WAP端模板风格', 'default', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (59, 'member_theme', 3, '会员中心模板风格', 'default', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (60, 'att_relation_content', 1, '是否开启内容附件关联', '0', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (61, 'site_seo_suffix', 0, '站点SEO后缀', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (62, 'site_security_number', 0, '公安备案号', '', '', ' ', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (63, 'words_code', 3, '是否开启留言验证码', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (64, 'watermark_minwidth', 2, '添加水印最小宽度', '300', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (65, 'watermark_minheight', 2, '添加水印最小高度', '300', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (66, 'auto_down_imag', 2, '自动下载远程图片', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (67, 'down_ignore_domain', 2, '下载远程图片忽略的域名', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (68, 'content_click_random', 2, '内容默认点击量', '1', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (69, 'blacklist_ip', 3, ' 前端IP黑名单', '', '', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (70, 'advertise', 99, '首页广告位', '免费又好用的CMS建站系统，就选RyPHP!', 'textarea', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (71, 'about', 99, '作者微信', '/uploads/201905/21/190521123856840.jpg', 'image', '', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (72, 'guestbook_email_verify', 2, '是否开启留言板邮箱验证码', '1', 'radio', '1|开启|0|关闭', 1);
INSERT INTO `rycms_config` (`id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status`) VALUES (73, 'link_email_verify', 2, '是否开启友情链接邮箱验证码', '1', 'radio', '1|开启|0|关闭', 1);


DROP TABLE IF EXISTS `rycms_urlrule`;
CREATE TABLE `rycms_urlrule` (
  `urlruleid` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `urlrule` varchar(100) NOT NULL DEFAULT '' COMMENT 'URL规则',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '指向的路由',
  `listorder` tinyint unsigned NOT NULL DEFAULT '50' COMMENT '优先级排序',
  PRIMARY KEY (`urlruleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `rycms_admin`;

CREATE TABLE `rycms_admin` (
  `adminid` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `roleid` tinyint unsigned NOT NULL DEFAULT '1',
  `rolename` varchar(30) NOT NULL DEFAULT '',
  `realname` varchar(30) NOT NULL DEFAULT '',
  `nickname` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(30) NOT NULL DEFAULT '',
  `logintime` int unsigned NOT NULL DEFAULT '0',
  `loginip` varchar(15) NOT NULL DEFAULT '',
  `addtime` int unsigned NOT NULL DEFAULT '0',
  `errnum` tinyint unsigned NOT NULL DEFAULT '0',
  `addpeople` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`adminid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

LOCK TABLES `rycms_admin` WRITE;

INSERT INTO `rycms_admin` VALUES (1,'lirongyaoper','e6398f4dc35c4f37acb2f7f3abe0012d',1,'超级管理员','','轩鸿青','',1754740961,'42.227.0.186',1557731527,0,'系统');
UNLOCK TABLES;



#第4表

DROP TABLE IF EXISTS `rycms_admin_login_log`;

CREATE TABLE `rycms_admin_login_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) NOT NULL DEFAULT '',
  `logintime` int unsigned NOT NULL DEFAULT 0 COMMENT '登录时间戳',
  `loginip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `address` varchar(30) NOT NULL DEFAULT '' COMMENT '地理位置',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT '尝试的密码(仅记录错误)',
  `loginresult` tinyint NOT NULL DEFAULT 0 COMMENT '登录结果:1-成功,0-失败',
  `cause` varchar(20) NOT NULL DEFAULT '' COMMENT '失败原因',
  PRIMARY KEY (`id`),
  KEY `idx_adminname_result` (`adminname`, `loginresult`),
  KEY `idx_logintime` (`logintime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员登录日志表';

INSERT INTO `rycms_admin_login_log` (`id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause`) VALUES (1, 'lirongyaoper', 1751433587, '127.0.0.1', '', 'lryadin01.', 0, '密码错误！');
INSERT INTO `rycms_admin_login_log` (`id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause`) VALUES (2, 'lirongyaoper', 1751433614, '127.0.0.1', '', '', 1, '登录成功！');
INSERT INTO `rycms_admin_login_log` (`id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause`) VALUES (3, 'lirongyaoper', 1752252773, '117.159.129.34', '', '', 1, '登录成功！');
INSERT INTO `rycms_admin_login_log` (`id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause`) VALUES (4, 'lirongyaoper', 1752269209, '117.159.129.34', '', '', 1, '登录成功！');




DROP TABLE IF EXISTS `rycms_guestbook`;

CREATE TABLE `rycms_guestbook` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `siteid` tinyint unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '' COMMENT '主题',
  `booktime` int unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名字',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '留言人电子邮箱',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '留言人电话',
  `qq` varchar(11) NOT NULL DEFAULT '' COMMENT '留言人qq',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '留言人地址',
  `bookmsg` text NOT NULL COMMENT '内容',
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `ischeck` tinyint NOT NULL DEFAULT '0' COMMENT '是否审核',
  `isread` tinyint NOT NULL DEFAULT '0' COMMENT '是否读过',
  `ispc` tinyint NOT NULL DEFAULT '1' COMMENT '1电脑,0手机',
  `replyid` int unsigned NOT NULL DEFAULT '0' COMMENT '回复的id',
  PRIMARY KEY (`id`),
  KEY `index_booktime` (`booktime`),
  KEY `index_replyid` (`replyid`),
  KEY `index_ischeck` (`siteid`,`ischeck`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `rycms_menu`;
CREATE TABLE `rycms_menu` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL DEFAULT '',
  `parentid` smallint NOT NULL DEFAULT '0',
  `m` char(20) NOT NULL DEFAULT '',
  `c` char(20) NOT NULL DEFAULT '',
  `a` char(30) NOT NULL DEFAULT '',
  `data` char(100) NOT NULL DEFAULT '',
  `listorder` smallint unsigned NOT NULL DEFAULT '0',
  `display` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `listorder` (`listorder`),
  KEY `parentid` (`parentid`),
  KEY `module` (`m`,`c`,`a`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=utf8mb4;


INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (1,'内容管理',0,'lry_admin_center','content','top','lry-iconneirong',1,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (2,'会员管理',0,'member','member','top','lry-iconyonghu',2,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (3,'模块管理',0,'lry_admin_center','module','top','lry-icondaohang',3,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (4,'管理员管理',0,'lry_admin_center','admin_manage','top','lry-iconguanliyuan',4,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (5,'个人信息',0,'lry_admin_center','admin_manage','top','lry-iconrizhi',5,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (6,'系统管理',0,'lry_admin_center','system_manage','top','lry-iconshezhi',6,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (7,'数据管理',0,'lry_admin_center','database','top','lry-iconshujuku',7,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (8,'稿件管理',1,'lry_admin_center','admin_content','init','',13,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (9,'稿件浏览',8,'lry_admin_center','admin_content','public_preview','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (10,'稿件删除',8,'lry_admin_center','admin_content','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (11,'通过审核',8,'lry_admin_center','admin_content','adopt','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (12,'退稿',8,'lry_admin_center','admin_content','rejection','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (13,'后台操作日志',6,'lry_admin_center','admin_log','init','',66,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (14,'操作日志删除',13,'lry_admin_center','admin_log','del_log','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (15,'操作日志搜索',13,'lry_admin_center','admin_log','search_log','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (16,'后台登录日志',6,'lry_admin_center','admin_log','admin_login_log_list','',67,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (17,'登录日志删除',16,'lry_admin_center','admin_log','del_login_log','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (18,'管理员管理',4,'lry_admin_center','admin_manage','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (19,'删除管理员',18,'lry_admin_center','admin_manage','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (20,'添加管理员',18,'lry_admin_center','admin_manage','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (21,'编辑管理员',18,'lry_admin_center','admin_manage','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (22,'变更角色',18,'lry_admin_center','admin_manage','change_role','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (23,'修改密码',18,'lry_admin_center','admin_manage','public_edit_pwd','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (24,'栏目管理',1,'lry_admin_center','category','init','',11,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (25,'排序栏目',24,'lry_admin_center','category','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (26,'删除栏目',24,'lry_admin_center','category','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (27,'添加栏目',24,'lry_admin_center','category','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (28,'编辑栏目',24,'lry_admin_center','category','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (29,'编辑单页内容',24,'lry_admin_center','category','page_content','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (30,'内容管理',1,'lry_admin_center','content','init','',10,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (31,'内容搜索',30,'lry_admin_center','content','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (32,'添加内容',30,'lry_admin_center','content','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (33,'修改内容',30,'lry_admin_center','content','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (34,'删除内容',30,'lry_admin_center','content','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (35,'数据备份',7,'lry_admin_center','database','init','',70,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (36,'数据还原',7,'lry_admin_center','database','databack_list','',71,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (37,'优化表',35,'lry_admin_center','database','public_optimize','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (38,'修复表',35,'lry_admin_center','database','public_repair','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (39,'备份文件删除',36,'lry_admin_center','database','databack_del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (40,'备份文件下载',36,'lry_admin_center','database','databack_down','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (41,'数据导入',36,'lry_admin_center','database','import','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (42,'字段管理',54,'lry_admin_center','model_field','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (43,'添加字段',42,'lry_admin_center','model_field','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (44,'修改字段',42,'lry_admin_center','model_field','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (45,'删除字段',42,'lry_admin_center','model_field','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (46,'排序字段',42,'lry_admin_center','model_field','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (47,'模块管理',3,'lry_admin_center','module','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (48,'模块安装',47,'lry_admin_center','module','install','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (49,'模块卸载',47,'lry_admin_center','module','uninstall','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (50,'角色管理',4,'lry_admin_center','role','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (51,'删除角色',50,'lry_admin_center','role','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (52,'添加角色',50,'lry_admin_center','role','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (53,'编辑角色',50,'lry_admin_center','role','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (54,'模型管理',1,'lry_admin_center','sitemodel','init','',15,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (55,'删除模型',54,'lry_admin_center','sitemodel','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (56,'添加模型',54,'lry_admin_center','sitemodel','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (57,'编辑模型',54,'lry_admin_center','sitemodel','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (58,'系统设置',6,'lry_admin_center','system_manage','init','',60,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (59,'会员中心设置',2,'lry_admin_center','system_manage','member_set','',26,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (60,'屏蔽词管理',6,'lry_admin_center','system_manage','prohibit_words','',63,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (61,'自定义配置',6,'lry_admin_center','system_manage','user_config_list','',62,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (62,'添加配置',61,'lry_admin_center','system_manage','user_config_add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (63,'配置编辑',61,'lry_admin_center','system_manage','user_config_edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (64,'配置删除',61,'lry_admin_center','system_manage','user_config_del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (65,'TAG管理',1,'lry_admin_center','tag','init','',16,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (66,'添加TAG',65,'lry_admin_center','tag','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (67,'编辑TAG',65,'lry_admin_center','tag','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (68,'删除TAG',65,'lry_admin_center','tag','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (69,'批量更新URL',1,'lry_admin_center','update_urls','init','',17,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (70,'附件管理',1,'attachment','index','init','',14,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (71,'附件搜索',70,'attachment','index','search_list','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (72,'附件浏览',70,'attachment','index','public_att_view','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (73,'删除单个附件',70,'attachment','index','del_one','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (74,'删除多个附件',70,'attachment','index','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (75,'评论管理',1,'comment','comment','init','',12,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (76,'评论搜索',75,'comment','comment','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (77,'删除评论',75,'comment','comment','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (78,'评论审核',75,'comment','comment','adopt','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (79,'留言管理',3,'guestbook','guestbook','init','',1,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (80,'查看及回复留言',79,'guestbook','guestbook','read','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (81,'留言审核',79,'guestbook','guestbook','toggle','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (82,'删除留言',79,'guestbook','guestbook','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (88,'会员管理',2,'member','member','init','',20,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (89,'会员搜索',88,'member','member','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (90,'添加会员',88,'member','member','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (91,'修改会员信息',88,'member','member','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (92,'修改会员密码',88,'member','member','password','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (93,'删除会员',88,'member','member','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (94,'审核会员',2,'member','member','check','',21,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (95,'通过审核',94,'member','member','adopt','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (96,'锁定用户',88,'member','member','lock','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (97,'解锁用户',88,'member','member','unlock','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (98,'账单管理',2,'member','member','pay','',22,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (99,'入账记录搜索',98,'member','member','pay_search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (100,'入账记录删除',98,'member','member','pay_del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (101,'消费记录',98,'member','member','pay_spend','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (102,'消费记录搜索',98,'member','member','pay_spend_search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (103,'消费记录删除',98,'member','member','pay_spend_del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (104,'会员组管理',2,'member','member_group','init','',25,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (105,'添加组别',104,'member','member_group','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (106,'修改组别',104,'member','member_group','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (107,'删除组别',104,'member','member_group','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (108,'消息管理',2,'member','member_message','init','',23,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (109,'消息搜索',108,'member','member_message','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (110,'删除消息',108,'member','member_message','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (111,'发送单个消息',108,'member','member_message','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (112,'群发消息',2,'member','member_message','messages_list','',23,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (113,'新建群发',112,'member','member_message','add_messages','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (114,'删除群发消息',112,'member','member_message','del_messages','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (115,'权限管理',50,'lry_admin_center','role','role_priv','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (116,'后台菜单管理',6,'lry_admin_center','menu','init','',64,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (117,'删除菜单',116,'lry_admin_center','menu','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (118,'添加菜单',116,'lry_admin_center','menu','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (119,'编辑菜单',116,'lry_admin_center','menu','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (120,'菜单排序',116,'lry_admin_center','menu','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (121,'邮箱配置',6,'lry_admin_center','system_manage','init','tab=4',61,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (122,'修改资料',5,'lry_admin_center','admin_manage','public_edit_info','',51,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (123,'修改密码',5,'lry_admin_center','admin_manage','public_edit_pwd','',52,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (134,'友情链接管理',3,'link','link','init','',6,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (135,'添加友情链接',134,'link','link','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (136,'修改友情链接',134,'link','link','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (137,'删除单个友情链接',134,'link','link','del_one','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (138,'删除多个友情链接',134,'link','link','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (139,'URL规则管理',6,'lry_admin_center','urlrule','init','',65,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (140,'添加URL规则',139,'lry_admin_center','urlrule','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (141,'删除URL规则',139,'lry_admin_center','urlrule','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (142,'编辑URL规则',139,'lry_admin_center','urlrule','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (143,'移动分类',30,'lry_admin_center','content','remove','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (144,'SQL命令行',6,'lry_admin_center','sql','init','',63,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (145,'提交SQL命令',144,'lry_admin_center','sql','do_sql','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (156,'轮播图管理',3,'banner','banner','init','',1,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (157,'添加轮播',156,'banner','banner','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (158,'修改轮播',156,'banner','banner','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (159,'删除轮播',156,'banner','banner','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (160,'添加轮播分类',156,'banner','banner','cat_add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (161,'管理轮播分类',156,'banner','banner','cat_manage','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (162,'会员统计',2,'member','member','member_count','',24,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (165,'采集管理',3,'collection','collection_content','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (166,'添加采集节点',165,'collection','collection_content','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (167,'编辑采集节点',165,'collection','collection_content','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (168,'删除采集节点',165,'collection','collection_content','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (169,'采集测试',165,'collection','collection_content','collection_test','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (170,'采集网址',165,'collection','collection_content','collection_list_url','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (171,'采集内容',165,'collection','collection_content','collection_article_content','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (172,'内容导入',165,'collection','collection_content','collection_content_import','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (173,'新建内容发布方案',165,'collection','collection_content','create_programme','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (174,'采集列表',165,'collection','collection_content','collection_list','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (175,'删除采集列表',165,'collection','collection_content','collection_list_del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (200,'微信管理',0,'wechat','wechat','top','lry-iconweixin',3,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (201,'微信配置',200,'wechat','config','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (202,'保存配置',201,'wechat','config','save','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (203,'微信用户',200,'wechat','user','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (204,'关注者搜索',203,'wechat','user','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (205,'获取分组名称',203,'wechat','user','get_groupname','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (206,'同步微信服务器用户',203,'wechat','user','synchronization','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (207,'批量移动用户分组',203,'wechat','user','move_user_group','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (208,'设置用户备注',203,'wechat','user','set_userremark','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (209,'查询用户所在组',203,'wechat','user','select_user_group','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (210,'分组管理',200,'wechat','group','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (211,'创建分组',210,'wechat','group','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (212,'修改分组',210,'wechat','group','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (213,'删除分组',210,'wechat','group','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (214,'查询所有分组',210,'wechat','group','select_group','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (215,'微信菜单',200,'wechat','menu','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (216,'添加菜单',215,'wechat','menu','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (217,'编辑菜单',215,'wechat','menu','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (218,'删除菜单',215,'wechat','menu','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (219,'菜单排序',215,'wechat','menu','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (220,'创建菜单提交微信',215,'wechat','menu','create_menu','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (221,'查询远程菜单',215,'wechat','menu','select_menu','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (222,'删除所有菜单提交微信',215,'wechat','menu','delete_menu','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (223,'消息回复',200,'wechat','reply','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (224,'自动回复/关注回复',223,'wechat','reply','reply_list','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (225,'添加关键字回复',223,'wechat','reply','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (226,'修改关键字回复',223,'wechat','reply','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (227,'删除关键字回复',223,'wechat','reply','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (228,'选择文章',223,'wechat','reply','select_article','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (229,'消息管理',200,'wechat','message','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (230,'用户发送信息',229,'wechat','message','send_message','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (231,'标识已读',229,'wechat','message','read','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (232,'删除消息',229,'wechat','message','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (233,'微信场景',200,'wechat','scan','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (234,'添加场景',233,'wechat','scan','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (235,'编辑场景',233,'wechat','scan','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (236,'删除场景',233,'wechat','scan','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (237,'素材管理',200,'wechat','material','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (238,'素材搜索',237,'wechat','material','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (239,'添加素材',237,'wechat','material','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (240,'添加图文素材',237,'wechat','material','add_news','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (241,'删除素材',237,'wechat','material','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (242,'选择缩略图',237,'wechat','material','select_thumb','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (243,'获取永久素材列表',237,'wechat','material','get_material_list','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (244,'高级群发',200,'wechat','mass','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (245,'新建群发',244,'wechat','mass','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (246,'查询群发状态',244,'wechat','mass','select_status','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (247,'删除群发',244,'wechat','mass','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (248,'选择素材',244,'wechat','mass','select_material','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (249,'选择用户',244,'wechat','mass','select_user','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (250,'自定义表单',3,'diyform','diyform','init','',2,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (251,'添加表单',250,'diyform','diyform','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (252,'编辑表单',250,'diyform','diyform','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (253,'删除表单',250,'diyform','diyform','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (254,'字段列表',250,'diyform','diyform_field','init','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (255,'添加字段',254,'diyform','diyform_field','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (256,'修改字段',254,'diyform','diyform_field','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (257,'删除字段',254,'diyform','diyform_field','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (258,'排序排序',254,'diyform','diyform_field','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (259,'表单信息列表',250,'diyform','diyform_info','init','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (260,'查看表单信息',259,'diyform','diyform_info','view','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (261,'删除表单信息',259,'diyform','diyform_info','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (262,'广告管理',3,'adver','adver','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (263,'添加广告',262,'adver','adver','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (264,'修改广告',262,'adver','adver','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (265,'删除广告',262,'adver','adver','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (266,'网站地图',1,'lry_admin_center','sitemap','init','',16,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (267,'生成地图',266,'lry_admin_center','sitemap','make_sitemap','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (268,'导出模型',54,'lry_admin_center','sitemodel','import','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (269,'导入模型',54,'lry_admin_center','sitemodel','export','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (270,'导出配置',61,'lry_admin_center','system_manage','user_config_export','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (271,'导入配置',61,'lry_admin_center','system_manage','user_config_import','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (283,'支付模块',3,'pay','pay','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (284,'支付配置',283,'pay','pay','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (285,'订单管理',2,'member','order','init','',22,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (286,'订单搜索',285,'member','order','order_search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (287,'订单改价',285,'member','order','change_price','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (288,'订单删除',285,'member','order','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (289,'订单详情',285,'member','order','order_details','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (290,'推送至百度',30,'lry_admin_center','content','baidu_push','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (291,'内容属性变更',30,'lry_admin_center','content','attribute_operation','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (292,'更改model',69,'lry_admin_center','update_urls','change_model','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (293,'更新栏目URL',69,'lry_admin_center','update_urls','update_category_url','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (294,'更新内容页URL',69,'lry_admin_center','update_urls','update_content_url','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (295,'留言搜索',79,'guestbook','guestbook','search','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (296,'内容关键字',3,'lry_admin_center','keyword_link','init','',1,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (297,'添加关键字',296,'lry_admin_center','keyword_link','add','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (298,'编辑关键字',296,'lry_admin_center','keyword_link','edit','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (299,'删除关键字',296,'lry_admin_center','keyword_link','del','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (300,'应用商店',3,'lry_admin_center','store','init','',0,1);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (301,'批量添加栏目',24,'lry_admin_center','category','adds','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (302,'内容状态变更',30,'lry_admin_center','content','status_operation','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (303,'关联内容',65,'lry_admin_center','tag','content','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (304,'加入/移除Tag',65,'lry_admin_center','tag','content_oper','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (305,'删除地图',266,'lry_admin_center','sitemap','delete','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (306,'保存配置',58,'lry_admin_center','system_manage','save','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (307,'立即备份',35,'lry_admin_center','database','export_list','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (308,'复制内容',30,'lry_admin_center','content','copy','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (309,'在线充值',88,'member','member','recharge','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (310,'登录到任意会员中心',88,'member','member','login_user','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (311,'友情链接排序',134,'link','link','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (312,'友情链接审核',134,'link','link','adopt','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (313,'标识已读',79,'guestbook','guestbook','set_read','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (314,'删除管理员回复',79,'guestbook','guestbook','del_reply','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (315,'轮播图排序',156,'banner','banner','order','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (316,'管理非自己发布的内容',30,'lry_admin_center','content','all_content','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (317,'删除所有未审核',75,'comment','comment','del_all','',0,0);
INSERT INTO `rycms_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES (318,'删除所有未审核',79,'guestbook','guestbook','del_all','',0,0);




DROP TABLE IF EXISTS `rycms_all_content`;

CREATE TABLE `rycms_all_content` (
  `allid` int unsigned NOT NULL AUTO_INCREMENT,
  `siteid` tinyint unsigned NOT NULL DEFAULT '0',
  `modelid` tinyint unsigned NOT NULL DEFAULT '0',
  `catid` smallint unsigned NOT NULL DEFAULT '0',
  `id` int unsigned NOT NULL DEFAULT '0' COMMENT 'relation_content_id(文章id)',
  `userid` mediumint unsigned NOT NULL DEFAULT '0',
  `username` char(30) NOT NULL DEFAULT '',
  `title` varchar(150) NOT NULL DEFAULT '',
  `inputtime` int unsigned NOT NULL DEFAULT '0',
  `updatetime` int unsigned NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  `thumb` varchar(150) NOT NULL DEFAULT '',
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `issystem` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`allid`),
  KEY `userid_index` (`userid`,`issystem`,`status`),
  KEY `modelid_index` (`modelid`,`id`),
  KEY `status` (`siteid`,`status`),
  KEY `issystem` (`siteid`,`issystem`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `rycms_all_content` WRITE;
INSERT INTO `rycms_all_content` (`allid`, `siteid`, `modelid`, `catid`, `id`, `userid`, `username`, `title`, `inputtime`, `updatetime`, `url`, `thumb`, `status`, `issystem`) VALUES (1,0,1,2,1,1,'lirongyaoper','RYPHP轻量级开源框架 V1.0',1710000726,1710000726,'/guanfangxinwen/1.html','',1,1);
INSERT INTO `rycms_all_content` (`allid`, `siteid`, `modelid`, `catid`, `id`, `userid`, `username`, `title`, `inputtime`, `updatetime`, `url`, `thumb`, `status`, `issystem`) VALUES (2,0,1,2,2,1,'lirongyaoper','RYCMS v1.0正式版发布',1748145623,1748145623,'/guanfangxinwen/2.html','',1,1);
UNLOCK TABLES;



DROP TABLE IF EXISTS `rycms_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rycms_member` (
  `userid` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `regdate` int unsigned NOT NULL DEFAULT '0',
  `lastdate` int unsigned NOT NULL DEFAULT '0',
  `regip` char(15) NOT NULL DEFAULT '',
  `lastip` char(15) NOT NULL DEFAULT '',
  `loginnum` smallint unsigned NOT NULL DEFAULT '0',
  `email` char(32) NOT NULL DEFAULT '',
  `groupid` tinyint unsigned NOT NULL DEFAULT '0',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '金钱',
  `experience` smallint unsigned NOT NULL DEFAULT '0' COMMENT '经验',
  `point` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0待审核,1正常,2锁定,3拒绝',
  `vip` tinyint unsigned NOT NULL DEFAULT '0',
  `overduedate` int unsigned NOT NULL DEFAULT '0',
  `email_status` tinyint unsigned NOT NULL DEFAULT '0',
  `problem` varchar(39) NOT NULL DEFAULT '' COMMENT '安全问题',
  `answer` varchar(30) NOT NULL DEFAULT '' COMMENT '答案',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;





DROP TABLE IF EXISTS `rycms_module`;

CREATE TABLE `rycms_module` (
  `module` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `iscore` tinyint unsigned NOT NULL DEFAULT '0',
  `version` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `setting` text,
  `listorder` tinyint unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint unsigned NOT NULL DEFAULT '0',
  `installdate` date NOT NULL DEFAULT '2016-01-01',
  `updatedate` date NOT NULL DEFAULT '2016-01-01',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

LOCK TABLES `rycms_module` WRITE;
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('lry_admin_center','后台模块',1,'3.0','后台模块','',0,0,'2016-08-27','2023-05-12');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('index','前台模块',1,'2.0','前台模块','',0,0,'2016-09-21','2023-01-21');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('api','接口模块',1,'2.0','为整个系统提供接口','',0,0,'2016-08-28','2022-08-28');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('install','安装模块',1,'2.0','CMS安装模块','',0,0,'2016-10-28','2022-10-28');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('attachment','附件模块',1,'2.0','附件模块','',0,0,'2016-10-10','2023-05-10');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('member','会员模块',1,'3.0','会员模块','',0,0,'2016-09-21','2023-02-21');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('guestbook','留言模块',1,'2.0','留言板模块','',0,0,'2016-10-25','2022-10-25');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('search','搜索模块',1,'2.0','搜索模块','',0,0,'2016-11-21','2023-01-21');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('link','友情链接',0,'2.0','友情链接模块','',0,0,'2016-12-11','2023-02-10');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('comment','评论模块',1,'2.0','全站评论','',0,0,'2017-01-05','2022-01-05');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('mobile','手机模块',1,'2.0','手机模块','',0,0,'2017-04-05','2022-04-05');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('banner','轮播图管理',0,'2.0','轮播图管理模块','',0,0,'2017-05-12','2023-02-10');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('collection','采集模块',1,'1.0','采集模块','',0,0,'2017-08-16','2022-08-16');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('wechat','微信模块',1,'2.0','微信模块','',0,0,'2017-11-03','2022-11-03');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('diyform','自定义表单模块',1,'2.0','自定义表单模块','',0,0,'2018-01-15','2023-05-11');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('adver','广告管理',0,'2.0','广告管理模块','',0,0,'2018-01-18','2023-01-18');
INSERT INTO `rycms_module` (`module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate`) VALUES ('pay','支付模块',1,'1.0','支付模块','',0,0,'2018-07-03','2022-07-03');

UNLOCK TABLES;


DROP TABLE IF EXISTS `rycms_admin_role_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rycms_admin_role_auth` (
  `roleid` tinyint unsigned NOT NULL DEFAULT '0',
  `m` char(20) NOT NULL DEFAULT '',
  `c` char(20) NOT NULL DEFAULT '',
  `a` char(30) NOT NULL DEFAULT '',
  `data` char(100) NOT NULL DEFAULT '',
  KEY `roleid` (`roleid`,`m`,`c`,`a`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rycms_admin_role_auth`
--

LOCK TABLES `rycms_admin_role_auth` WRITE;
/*!40000 ALTER TABLE `rycms_admin_role_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `rycms_admin_role_auth` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `rycms_admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rycms_admin_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL DEFAULT '',
  `controller` varchar(20) NOT NULL DEFAULT '',
  `querystring` varchar(255) NOT NULL DEFAULT '',
  `adminid` mediumint unsigned NOT NULL DEFAULT '0',
  `adminname` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `logtime` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `logtime` (`logtime`),
  KEY `adminid` (`adminid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;





DROP TABLE IF EXISTS `rycms_model`;
CREATE TABLE `rycms_model` (
  `modelid` int unsigned NOT NULL AUTO_INCREMENT,
  `siteid` tinyint unsigned NOT NULL DEFAULT '0',
  `name` char(30) NOT NULL DEFAULT '',
  `tablename` varchar(30) NOT NULL DEFAULT '',
  `alias` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  `setting` text,
  `inputtime` int unsigned NOT NULL DEFAULT '0',
  `items` smallint unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint unsigned NOT NULL DEFAULT '0',
  `type` tinyint unsigned NOT NULL DEFAULT '0',
  `sort` tinyint unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint unsigned NOT NULL DEFAULT '0',
  `isdefault` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`modelid`),
  KEY `siteid` (`siteid`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;


--
-- Dumping data for table `rycms_model`
-- 

LOCK TABLES `rycms_model` WRITE;
/*!40000 ALTER TABLE `rycms_model` DISABLE KEYS */;
INSERT INTO `rycms_model` (`modelid`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`, `sort`, `issystem`, `isdefault`) VALUES (1,0,'文章模型','article','article','文章模型','',1466393786,0,0,0,0,1,1);
INSERT INTO `rycms_model` (`modelid`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`, `sort`, `issystem`, `isdefault`) VALUES (2,0,'产品模型','product','product','产品模型','',1466393786,0,0,0,0,1,0);
INSERT INTO `rycms_model` (`modelid`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`, `sort`, `issystem`, `isdefault`) VALUES (3,0,'下载模型','download','download','下载模型','',1466393786,0,0,0,0,1,0);
INSERT INTO `rycms_model` (`modelid`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`, `sort`, `issystem`, `isdefault`) VALUES (4,0,'单页模型','page','page','单页模型','',1683775806,0,0,2,0,1,0);
/*!40000 ALTER TABLE `rycms_model` ENABLE KEYS */;
UNLOCK TABLES;