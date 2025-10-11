| 表名 | 字段列表 |
| :--- | :--- |
| rycms_admin | `adminid`, `adminname`, `password`, `roleid`, `rolename`, `realname`, `nickname`, `email`, `logintime`, `loginip`, `addtime`, `errnum`, `addpeople` |
| rycms_admin_log | `id`, `module`, `controller`, `querystring`, `adminid`, `adminname`, `ip`, `logtime` |
| rycms_admin_login_log | `id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause` |
| rycms_admin_role_auth | `roleid`, `m`, `c`, `a`, `data` |
| rycms_all_content | `allid`, `siteid`, `modelid(1=>文章模型,2=>产品模型,3=>下载模型,4=>单页模型)`, `catid`, `id`, `userid`, `username`, `title`, `inputtime`, `updatetime`, `url`, `thumb`, `status`, `issystem` |
| rycms_category | `catid`, `siteid`, `catname`, `modelid(1=>文章模型,2=>产品模型,3=>下载模型,4=>单页模型)`, `parentid`, `arrparentid`, `arrchildid`, `catdir`, `catimg`, `cattype` (0=>普通栏目,1=>单页,2=>外部连接), `listorder`, `target`, `member_publish`, `display`, `pclink`, `domain`, `entitle`, `subtitle`, `mobname`, `category_template`, `list_template`, `show_template`, `seo_title`, `seo_keywords`, `seo_description` |
| rycms_config | `id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status` |
| rycms_guestbook | `id`, `siteid`, `title`, `booktime`, `name`, `email`, `phone`, `qq`, `address`, `bookmsg`, `ip`, `ischeck`, `isread`, `ispc`, `replyid` |
| rycms_member | `userid`, `username`, `password`, `regdate`, `lastdate`, `regip`, `lastip`, `loginnum`, `email`, `groupid`, `amount`, `experience`, `point`, `status`, `vip`, `overduedate`, `email_status`, `problem`, `answer` |
| rycms_menu | `id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display` |
| rycms_model | `modelid(1=>文章模型,2=>产品模型,3=>下载模型,4=>单页模型)`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`(0=>文章模型,产品模型,下载模型 ,2=>单页模型), `sort`, `issystem`, `isdefault` |
| rycms_module | `module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate` |
| rycms_urlrule | `urlruleid`, `name`, `urlrule`, `route`, `listorder` |
|               |      |