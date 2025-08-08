DROP TABLE IF EXISTS `ry_category`;
CREATE TABLE `ry_category` (
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
INSERT INTO `ry_category` VALUES ('1', '0', '新闻中心', '1', '0', '0', '1,2,3', 'xinwenzhongxin', '', '0', '0', '_self', '0', '1', '/xinwenzhongxin/', '', '', '', '新闻中心', 'category_article', 'list_article', 'show_article', '', '', '');
INSERT INTO `ry_category` VALUES ('2', '0', 'RYCMS新闻', '1', '1', '0,1', '2', 'rycmsxinwen', '', '0', '0', '_self', '0', '1', '/rycmsxinwen/', '', '', '', '官方新闻', 'category_article', 'list_article_img', 'show_article', '', '', '');
INSERT INTO `ry_category` VALUES ('3', '0', '其他新闻', '1', '1', '0,1', '3', 'qitaxinwen', '', '0', '0', '_self', '1', '1', '/qitaxinwen/', '', '', '', '其他新闻', 'category_article', 'list_article', 'show_article', '', '', '');
INSERT INTO `ry_category` VALUES ('4', '0', '关于我们', '0', '0', '0', '4', 'guanyuwomen', '', '1', '0', '_self', '0', '1', '/guanyuwomen/', '', '', '', '关于我们', 'category_page', '', '', '', '', '');
INSERT INTO `ry_category` VALUES ('5', '0', '官方网站', '0', '0', '0', '5', '', '', '2', '0', '_blank', '0', '1', 'https://www.lrycms.com/', '', '', '', '官方网站', '', '', '', '', '', '');
