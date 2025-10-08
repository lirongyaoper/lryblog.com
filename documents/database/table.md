| 表名 | 字段列表 |
| :--- | :--- |
| lry_admin | `adminid`, `adminname`, `password`, `roleid`, `rolename`, `realname`, `nickname`, `email`, `logintime`, `loginip`, `addtime`, `errnum`, `addpeople` |
| lry_admin_log | `id`, `module`, `controller`, `querystring`, `adminid`, `adminname`, `ip`, `logtime` |
| lry_admin_login_log | `id`, `adminname`, `logintime`, `loginip`, `address`, `password`, `loginresult`, `cause` |
| lry_admin_role | `roleid`, `rolename`, `description`, `system`, `disabled` |
| lry_admin_role_auth | `roleid`, `m`, `c`, `a`, `data` |
| lry_adver | `id`, `type`, `title`, `url`, `text`, `img`, `code`, `describe`, `inputtime`, `start_time`, `end_time` |
| lry_all_content | `allid`, `siteid`, `modelid`, `catid`, `id`, `userid`, `username`, `title`, `inputtime`, `updatetime`, `url`, `thumb`, `status`, `issystem` |
| lry_article | `id`, `catid`, `userid`, `username`, `nickname`, `title`, `color`, `inputtime`, `updatetime`, `keywords`, `description`, `click`, `content`, `copyfrom`, `thumb`, `url`, `flag`, `status`, `issystem`, `listorder`, `groupids_view`, `readpoint`, `paytype`, `is_push`, `up`, `down` |
| lry_attachment | `id`, `siteid`, `module`, `contentid`, `originname`, `filename`, `filepath`, `filesize`, `fileext`, `isimage`, `downloads`, `userid`, `username`, `uploadtime`, `uploadip` |
| lry_banner | `id`, `title`, `image`, `url`, `introduce`, `inputtime`, `listorder`, `typeid`, `status` |
| lry_banner_type | `tid`, `name` |
| lry_category | `catid`, `siteid`, `catname`, `modelid`, `parentid`, `arrparentid`, `arrchildid`, `catdir`, `catimg`, `cattype`, `listorder`, `target`, `member_publish`, `display`, `pclink`, `domain`, `entitle`, `subtitle`, `mobname`, `category_template`, `list_template`, `show_template`, `seo_title`, `seo_keywords`, `seo_description` |
| lry_collection_content | `id`, `nodeid`, `status`, `url`, `title`, `data` |
| lry_collection_node | `nodeid`, `name`, `lastdate`, `sourcecharset`, `sourcetype`, `urlpage`, `pagesize_start`, `pagesize_end`, `par_num`, `url_contain`, `url_except`, `url_start`, `url_end`, `title_rule`, `title_html_rule`, `time_rule`, `time_html_rule`, `content_rule`, `content_html_rule`, `down_attachment`, `watermark`, `coll_order` |
| lry_comment | `id`, `siteid`, `commentid`, `userid`, `username`, `userpic`, `inputtime`, `ip`, `content`, `status`, `reply` |
| lry_comment_data | `commentid`, `siteid`, `title`, `url`, `total`, `catid`, `modelid` |
| lry_config | `id`, `name`, `type`, `title`, `value`, `fieldtype`, `setting`, `status` |
| lry_download | `id`, `catid`, `userid`, `username`, `nickname`, `title`, `color`, `inputtime`, `updatetime`, `keywords`, `description`, `click`, `content`, `copyfrom`, `thumb`, `url`, `flag`, `status`, `issystem`, `listorder`, `groupids_view`, `readpoint`, `paytype`, `is_push`, `down_url`, `copytype`, `systems`, `language`, `version`, `filesize`, `classtype`, `stars` |
| lry_favorite | `id`, `userid`, `title`, `url`, `inputtime` |
| lry_forum_attitude | `id`, `forumid`, `commentid`, `userid`, `type`, `inputtime` |
| lry_forum_comment | `id`, `forumid`, `userid`, `username`, `userpic`, `inputtime`, `ip`, `content`, `status`, `reply`, `praise` |
| lry_forum_config | `id`, `name`, `title`, `value`, `status` |
| lry_forum_plate | `plate_id`, `plate_name`, `listorder`, `groupids_view`, `groupids_add`, `keywords`, `description` |
| lry_forum_post | `id`, `plate_id`, `title`, `userid`, `username`, `inputtime`, `updatetime`, `keywords`, `ip`, `click`, `status`, `attachment`, `point`, `paytype`, `tags`, `is_comment`, `comment`, `praise`, `tread`, `groupids_view`, `content` |
| lry_guestbook | `id`, `siteid`, `title`, `booktime`, `name`, `email`, `phone`, `qq`, `address`, `bookmsg`, `ip`, `ischeck`, `isread`, `ispc`, `replyid` |
| lry_keyword_link | `id`, `keyword`, `url` |
| lry_link | `id`, `siteid`, `typeid`, `linktype`, `name`, `url`, `logo`, `msg`, `username`, `email`, `listorder`, `status`, `addtime` |
| lry_member | `userid`, `username`, `password`, `regdate`, `lastdate`, `regip`, `lastip`, `loginnum`, `email`, `groupid`, `amount`, `experience`, `point`, `status`, `vip`, `overduedate`, `email_status`, `problem`, `answer` |
| lry_member_authorization | `id`, `userid`, `authname`, `token`, `userinfo`, `inputtime` |
| lry_member_detail | `userid`, `sex`, `realname`, `nickname`, `qq`, `mobile`, `phone`, `userpic`, `birthday`, `industry`, `area`, `motto`, `introduce`, `guest`, `fans`, `posts` |
| lry_member_follow | `id`, `userid`, `followid`, `followname`, `inputtime` |
| lry_member_group | `groupid`, `name`, `experience`, `icon`, `authority`, `max_amount`, `description`, `is_system` |
| lry_member_guest | `id`, `space_id`, `guest_id`, `guest_name`, `guest_pic`, `inputtime`, `ip` |
| lry_member_sign | `id`, `userid`, `inputtime`, `continuity_day`, `point` |
| lry_menu | `id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display` |
| lry_message | `messageid`, `send_from`, `send_to`, `message_time`, `subject`, `content`, `replyid`, `status`, `isread`, `issystem` |
| lry_message_data | `id`, `userid`, `group_message_id` |
| lry_message_group | `id`, `groupid`, `subject`, `content`, `inputtime`, `status` |
| lry_model | `modelid`, `siteid`, `name`, `tablename`, `alias`, `description`, `setting`, `inputtime`, `items`, `disabled`, `type`, `sort`, `issystem`, `isdefault` |
| lry_model_field | `fieldid`, `modelid`, `field`, `name`, `tips`, `setting_catid`, `minlength`, `maxlength`, `errortips`, `fieldtype`, `defaultvalue`, `setting`, `isrequired`, `issystem`, `isunique`, `isadd`, `listorder`, `disabled`, `type`, `status` |
| lry_module | `module`, `name`, `iscore`, `version`, `description`, `setting`, `listorder`, `disabled`, `installdate`, `updatedate` |
| lry_notes | `id`, `title`, `year`, `time`, `image`, `details` |
| lry_order | `id`, `order_sn`, `status`, `userid`, `username`, `addtime`, `paytime`, `paytype`, `transaction`, `money`, `quantity`, `type`, `ip`, `desc` |
| lry_page | `catid`, `title`, `introduce`, `content`, `updatetime` |
| lry_pay | `id`, `trade_sn`, `userid`, `username`, `money`, `creat_time`, `msg`, `type`, `ip`, `status`, `remarks`, `adminnote` |
| lry_pay_mode | `id`, `name`, `logo`, `desc`, `config`, `enabled`, `author`, `version`, `action`, `template` |
| lry_pay_spend | `id`, `trade_sn`, `userid`, `username`, `money`, `creat_time`, `msg`, `type`, `ip`, `remarks` |
| lry_product | `id`, `catid`, `userid`, `username`, `nickname`, `title`, `color`, `inputtime`, `updatetime`, `keywords`, `description`, `click`, `content`, `copyfrom`, `thumb`, `url`, `flag`, `status`, `issystem`, `listorder`, `groupids_view`, `readpoint`, `paytype`, `is_push`, `brand`, `standard`, `yieldly`, `pictures`, `price`, `unit`, `stock` |
| lry_spider_log | `id`, `spider`, `ip`, `url`, `referer`, `addtime` |
| lry_tag | `id`, `siteid`, `catid`, `tag`, `total`, `click`, `seo_title`, `seo_keywords`, `seo_description`, `inputtime` |
| lry_tag_content | `siteid`, `modelid`, `catid`, `aid`, `tagid` |
| lry_urlrule | `urlruleid`, `name`, `urlrule`, `route`, `listorder` |
| lry_view_count | `id`, `count`, `today`, `updatetime` |
| lry_view_log | `id`, `inputtime`, `ip`, `referer`, `url`, `user_agent` |
| lry_wechat_auto_reply | `id`, `type`, `keyword`, `keyword_type`, `relation_id`, `content` |
| lry_wechat_group | `id`, `name`, `count` |
| lry_wechat_mass | `id`, `message_type`, `type`, `media_id`, `msg_id`, `msg_data_id`, `receive`, `status`, `masstime` |
| lry_wechat_media | `id`, `originname`, `filename`, `filepath`, `type`, `media_type`, `media_id`, `created_at`, `url` |
| lry_wechat_menu | `id`, `parentid`, `name`, `type`, `keyword`, `url`, `event`, `listorder` |
| lry_wechat_message | `id`, `openid`, `issystem`, `inputtime`, `msgtype`, `isread`, `content` |
| lry_wechat_scan | `id`, `scan`, `type`, `expire_time`, `ticket`, `remarks` |
| lry_wechat_user | `wechatid`, `openid`, `groupid`, `subscribe`, `nickname`, `sex`, `city`, `province`, `country`, `headimgurl`, `subscribe_time`, `remark`, `scan` |
