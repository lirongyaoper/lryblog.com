DROP TABLE IF EXISTS `ry_category`;
CREATE TABLE `rycms_category` (
  `catid` smallint(5) NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `siteid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `catname` varchar(60) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `arrparentid` varchar(255) NOT NULL DEFAULT '' COMMENT '父级路径',
  `arrchildid` mediumtext NOT NULL COMMENT '子栏目id集合',
  `catdir` varchar(50) NOT NULL DEFAULT '' COMMENT '栏目目录',
  `catimg` varchar(150) NOT NULL DEFAULT '' COMMENT '栏目图片',
  `cattype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '栏目类型:0普通栏目1单页2外部链接',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目排序',
  `target` char(10) NOT NULL DEFAULT '' COMMENT '打开方式',
  `member_publish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否会员投稿',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '在导航显示',
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb3;

INSERT INTO `rycms_config` VALUES (1,'site_name',0,'站点名称','荣耀历程--一个有激情，有梦想，不断探索未知世界的热血青年','','',1),(2,'site_url',0,'站点根网址','https://lirongyaoper.com/','','',1),(3,'site_keyword',0,'站点关键字','个人博客,精彩人生','','',1),(4,'site_description',0,'站点描述','这是来自一个编程爱好者的个人博客，他热爱生活，热爱工作与学习，愿陪大家一起走过每一个春夏秋冬！','','',1),(5,'site_copyright',0,'版权信息','版权归属 荣耀小科技 © 2014-2026','','',1),(6,'site_filing',0,'站点备案号','豫ICP备17033830号-1','','',1),(7,'site_code',0,'统计代码','','','',1),(8,'site_theme',0,'站点模板主题','rongyao','','',1),(9,'site_logo',0,'站点logo','','','',1),(10,'url_mode',0,'前台URL模式','1','','',1),(11,'is_words',0,'是否开启前端留言功能','1','','',1),(12,'upload_maxsize',0,'文件上传最大限制','2048','','',1),(13,'upload_types',0,'允许上传附件类型','zip|rar|ppt|doc|xls','','',1),(14,'upload_image_types',0,'允许上传图片类型','png|jpg|jpeg|gif','','',1),(15,'watermark_enable',0,'是否开启图片水印','1','','',1),(16,'watermark_name',0,'水印图片名称','mark.png','','',1),(17,'watermark_position',0,'水印的位置','7','','',1),(18,'mail_server',1,'SMTP服务器','ssl://smtp.163.com','','',1),(19,'mail_port',1,'SMTP服务器端口','465','','',1),(20,'mail_from',1,'SMTP服务器的用户邮箱','lirongyaoper@163.com','','',1),(21,'mail_auth',1,'AUTH LOGIN验证','1','','',1),(22,'mail_user',1,'SMTP服务器的用户帐号','lirongyaoper@163.com','','',1),(23,'mail_pass',1,'SMTP服务器的用户密码','RUj8fgZHNpxQFGxy','','',1),(24,'mail_inbox',1,'收件邮箱地址','lirongyaoper@163.com','','',1),(25,'admin_log',2,'启用后台管理操作日志','0','','',1),(26,'admin_prohibit_ip',2,'禁止登录后台的IP','95.72.*.*,46.161.11.199','','',1),(27,'prohibit_words',2,'屏蔽词','她妈|它妈|他妈|你妈|去死|贱人','','',1),(28,'comment_check',2,'是否开启评论审核','1','','',1),(29,'comment_tourist',2,'是否允许游客评论','0','','',1),(30,'is_link',2,'允许用户申请友情链接','1','','',1),(31,'member_register',3,'是否开启会员注册','1','','',1),(32,'member_email',3,'新会员注册是否需要邮件验证','1','','',1),(33,'member_check',3,'新会员注册是否需要管理员审核','1','','',1),(34,'member_point',3,'新会员默认积分','20','','',1),(35,'member_lry',3,'是否开启会员登录验证码','0','','',1),(36,'rmb_point_rate',3,'1元人民币购买积分数量','10','','',1),(37,'login_point',3,'每日登录奖励积分','1','','',1),(38,'comment_point',3,'发布评论奖励积分','1','','',1),(39,'publish_point',3,'投稿奖励积分','3','','',1),(40,'qq_app_id',3,'QQ App ID','101905949','','',1),(41,'qq_app_key',3,'QQ App key','ad5393b046c2cd323c8581734e505c9f','','',1),(42,'weibo_key',4,'微博登录App Key','','','',1),(43,'weibo_secret',4,'微博登录App Secret','','','',1),(44,'wx_appid',4,'微信开发者ID','wx5a85749dffa38259','','',1),(45,'wx_secret',4,'微信开发者密码','8e2ebe8e3f72039d7344e6ea52e4ddbc','','',1),(46,'wx_token',4,'微信Token签名','tlbra8x474k','','',1),(47,'wx_encodingaeskey',4,'微信EncodingAESKey','HkMrjxv5ih3mNujRPh7uGhyMIpMcug8qAwGTuSTDsgX','','',1),(48,'wx_relation_model',4,'微信关联模型','article','','',1),(49,'baidu_push_token',0,'百度推送token','','','',1),(50,'thumb_width',2,'缩略图默认宽度','500','','',1),(51,'thumb_height',2,'缩略图默认高度','300','','',1),(52,'site_seo_division',0,'站点标题分隔符','_','','',1),(53,'keyword_link',2,'是否启用关键字替换','0','','',1),(54,'keyword_replacenum',2,'关键字替换次数','1','','',1),(55,'error_log_save',2,'是否保存系统错误日志','1','','',1),(56,'comment_code',2,'是否开启评论验证码','1','','',1),(57,'site_wap_open',0,'是否启用手机站点','0','','',1),(58,'site_wap_theme',0,'WAP端模板风格','default','','',1),(59,'member_theme',3,'会员中心模板风格','default','','',1),(60,'att_relation_content',1,'是否开启内容附件关联','0','','',1),(61,'site_seo_suffix',0,'站点SEO后缀','','','',1),(62,'site_security_number',0,'公安备案号','','',' ',1),(63,'words_code',3,'是否开启留言验证码','1','','',1),(64,'watermark_minwidth',2,'添加水印最小宽度','300','','',1),(65,'watermark_minheight',2,'添加水印最小高度','300','','',1),(66,'auto_down_imag',2,'自动下载远程图片','1','','',1),(67,'down_ignore_domain',2,'下载远程图片忽略的域名','','','',1),(68,'content_click_random',2,'内容默认点击量','1','','',1),(69,'blacklist_ip',3,' 前端IP黑名单','','','',1),(70,'advertise',99,'首页广告位','免费又好用的CMS建站系统，就选RyPHP!','textarea','',1),(71,'about',99,'作者微信','/uploads/201905/21/190521123856840.jpg','image','',1),(72,'guestbook_email_verify',2,'是否开启留言板邮箱验证码','1','radio','1|开启|0|关闭',1),(73,'link_email_verify',2,'是否开启友情链接邮箱验证码','1','radio','1|开启|0|关闭',1);



DROP TABLE IF EXISTS `rycms_urlrule`;
CREATE TABLE `rycms_urlrule` (
  `urlruleid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `urlrule` varchar(100) NOT NULL DEFAULT '' COMMENT 'URL规则',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '指向的路由',
  `listorder` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '优先级排序',
  PRIMARY KEY (`urlruleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
