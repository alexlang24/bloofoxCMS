-- phpMyAdmin SQL Dump
-- version 2.9.0
-- http://www.phpmyadmin.net
-- 
-- --------------------------------------------------------

CREATE TABLE `bfCMS_sys_charset` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `description` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_charset` VALUES (1, 'utf-8','');
INSERT INTO `bfCMS_sys_charset` VALUES (2, 'ISO-8859-1','');
INSERT INTO `bfCMS_sys_charset` VALUES (3, 'ISO-8859-2','');
INSERT INTO `bfCMS_sys_charset` VALUES (4, 'ISO-8859-3','');
INSERT INTO `bfCMS_sys_charset` VALUES (5, 'ISO-8859-4','');
INSERT INTO `bfCMS_sys_charset` VALUES (6, 'ISO-8859-5','');
INSERT INTO `bfCMS_sys_charset` VALUES (7, 'windows-1250','');
INSERT INTO `bfCMS_sys_charset` VALUES (8, 'windows-1251','');
INSERT INTO `bfCMS_sys_charset` VALUES (9, 'windows-1252','');
INSERT INTO `bfCMS_sys_charset` VALUES (10, 'windows-1253','');
INSERT INTO `bfCMS_sys_charset` VALUES (11, 'windows-1254','');
INSERT INTO `bfCMS_sys_charset` VALUES (12, 'windows-1255','');

CREATE TABLE `bfCMS_sys_config` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `lang_id` int(10) unsigned NOT NULL default '0',
  `urls` text NOT NULL,
  `tmpl_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  `root_id` int(10) unsigned NOT NULL default '0',
  `user_groups` text NOT NULL,
  `meta_title` varchar(250) NOT NULL default '',
  `meta_desc` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_author` varchar(250) NOT NULL default '',
  `meta_charset` varchar(250) NOT NULL default '',
  `meta_doctype` varchar(250) NOT NULL default '',
  `meta_copyright` varchar(250) NOT NULL default '',
  `mod_rewrite` enum('0','1') NOT NULL default '0',
  `mail` varchar(250) NOT NULL default '',
  `default_group` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_content` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `explorer_id` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `config_id` int(10) unsigned NOT NULL default '0',
  `content_type` int(2) unsigned NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `text` longtext NOT NULL,
  `blocked` enum('0','1') NOT NULL,
  `created_by` VARCHAR(250) NOT NULL,
  `created_at` VARCHAR(20) NOT NULL,
  `changed_by` VARCHAR(250) NOT NULL,
  `changed_at` VARCHAR(20) NOT NULL,
  `startdate` varchar(20) NOT NULL default '',
  `enddate` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`cid`),
  KEY `explorer_id` (`explorer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_explorer` (
  `eid` int(10) unsigned NOT NULL auto_increment,
  `level` int(5) NOT NULL default '0',
  `preid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) NOT NULL default '0',
  `config_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  `link_type` int(1) unsigned NOT NULL default '0',
  `link_target` varchar(250) NOT NULL default '',
  `link_url` varchar(250) NOT NULL default '',
  `link_eid` int(10) unsigned NOT NULL default '0',
  `link_plugin` int(10) unsigned NOT NULL default '0',
  `link_param` varchar(80) NOT NULL default '',
  `blocked` enum('0','1') NOT NULL default '0',
  `invisible` enum('0','1') NOT NULL default '0',
  `groups` text NOT NULL,
  `sub_perm` enum('0','1') NOT NULL default '0',
  `startdate` varchar(20) NOT NULL default '',
  `enddate` varchar(20) NOT NULL default '',
  `created_by` varchar(250) NOT NULL default '',
  `created_at` varchar(20) NOT NULL default '',
  `changed_by` varchar(250) NOT NULL default '',
  `changed_at` varchar(20) NOT NULL default '',
  `keywords` text NOT NULL,
  `template_id` int(10) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `title` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`eid`),
  KEY `preid` (`preid`,`sorting`),
  KEY `config` (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_lang` (
  `lid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(250) NOT NULL default '',
  `flag` varchar(250) NOT NULL default '',
  `filename` varchar(250) NOT NULL default '',
  `date` varchar(20) NOT NULL default 'd.m.Y',
  `datetime` varchar(20) NOT NULL default 'd.m.Y - H:i',
  `token` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`lid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_lang` VALUES (1, 'English', 'en.gif', 'english.php', 'm/d/Y', 'm/d/Y - H:i', 'en');
INSERT INTO `bfCMS_sys_lang` VALUES (2, 'Deutsch', 'de.gif', 'deutsch.php', 'd.m.Y', 'd.m.Y - H:i', 'de');

CREATE TABLE `bfCMS_sys_media` (
  `mid` int(10) unsigned NOT NULL auto_increment,
  `media_type` int(10) unsigned NOT NULL default '0',
  `filename` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_permission` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '0',
  `page` varchar(80) NOT NULL default '',
  `object_w` enum('0','1') NOT NULL default '0',
  `object_d` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`pid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_plugin` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(250) NOT NULL default '',
  `index_file` varchar(250) NOT NULL default '',
  `index_tmpl` varchar(250) NOT NULL default '',
  `tmpl_handler` varchar(250) NOT NULL default '',
  `admin_file` varchar(250) NOT NULL default '',
  `admin_handler` VARCHAR(250) NOT NULL default '',
  `install_path` varchar(250) NOT NULL default '',
  `plugin_type` int(1) unsigned NOT NULL default '0',
  `status` enum('0','1') NOT NULL default '0',
  `plugin_version` varchar(10) NOT NULL,
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_plugin` VALUES (1, 'module_date', 'date.php', 'date.html', '', '', '', 'module_date/', 0, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (1004, 'sitemap', 'sitemap.php', 'sitemap.html', '', '', '', 'sitemap/', 1, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (1003, 'search_simple', 'search.php', 'search.html', '', '', '', 'search_simple/', 1, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (4, 'module_searchbox', 'searchbox.php', 'searchbox.html', '', '', '', 'module_searchbox/', 0, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (1001, 'contact_form_simple', 'contact.php', 'contact.html', '', '', '', 'contact_form_simple/', 1, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (7, 'module_location_bar', 'location.php', 'location.html', '', '', '', 'module_location_bar/', 0, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (10, 'navigation_menu_default', 'menu.php', 'menu.html', '', '', '', 'navigation_menu_default/', 0, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (11, 'navigation_submenu_default', 'submenu.php', 'submenu.html', '', '', '', 'navigation_submenu_default/', 0, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (1002, 'register_form_simple', 'register.php', 'register_account.html', 'tmpl_handler.php', '', '', 'register_form_simple/', 1, '1', '2.0');
INSERT INTO `bfCMS_sys_plugin` VALUES (16, 'module_loginbox', 'login.php', 'login.html', '', '', '', 'module_loginbox/', 0, '1', '2.0');

CREATE TABLE `bfCMS_sys_profile` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(250) NOT NULL default '',
  `lastname` varchar(250) NOT NULL default '',
  `address1` varchar(250) NOT NULL default '',
  `address2` varchar(250) NOT NULL default '',
  `city` varchar(250) NOT NULL default '',
  `zip_code` varchar(20) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `birthday` varchar(20) NOT NULL default '',
  `gender` enum('0','1') NOT NULL default '0',
  `picture` varchar(250) NOT NULL default '',
  `be_lang` int(10) unsigned NOT NULL default '0',
  `be_tmpl` int(10) unsigned NOT NULL default '0',
  `show_email` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`pid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_profile` VALUES (1, 1, 'admin', '', '', '', '', '', '', '', '0', '', 1, 0, '0');

CREATE TABLE `bfCMS_sys_session` (
  `sid` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `type` TINYINT( 1 ) UNSIGNED NOT NULL ,
  `uid` INT( 10 ) UNSIGNED NOT NULL ,
  `date` DATE NOT NULL ,
  `time` TIME NOT NULL ,
  `status` TINYINT( 1 ) UNSIGNED NOT NULL ,
  `timestamp` VARCHAR( 20 ) NOT NULL ,
  `ip` VARCHAR( 80 ) NOT NULL ,
  `session_id` VARCHAR( 80 ) NOT NULL
) ENGINE = MYISAM AUTO_INCREMENT=1 ;

CREATE TABLE `bfCMS_sys_setting` (
	`sid` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`setting_property` VARCHAR( 30 ) NOT NULL ,
	`setting_value` VARCHAR( 80 ) NOT NULL
) ENGINE = MYISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_setting` VALUES (1, 'update_check', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (2, 'register_notify', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (3, 'login_protection', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (4, 'online_status', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (5, 'user_content_only', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (6, 'admin_mail', '');
INSERT INTO `bfCMS_sys_setting` VALUES (7, 'html_mails', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (8, 'htmlentities_off', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (9, 'pw_rule', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (10, 'textbox_width', '500');
INSERT INTO `bfCMS_sys_setting` VALUES (11, 'database_version', 'bloofoxCMS 0.5.2');
INSERT INTO `bfCMS_sys_setting` VALUES (12, 'textbox_height', '250');
INSERT INTO `bfCMS_sys_setting` VALUES (20, 'manual_register', '0');
INSERT INTO `bfCMS_sys_setting` VALUES (30, 'phpmyadmin_url', '');
INSERT INTO `bfCMS_sys_setting` VALUES (40, 'maintenance', '0');

CREATE TABLE `bfCMS_sys_template` (
  `tid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(250) NOT NULL default '',
  `template` varchar(250) NOT NULL default '',
  `css` varchar(250) NOT NULL default '',
  `be` enum('0','1') NOT NULL default '0',
  `template_print` VARCHAR(80) NOT NULL,
  `template_print_css` VARCHAR(80) NOT NULL,
  `template_login` VARCHAR(80) NOT NULL,
  `template_text` VARCHAR(80) NOT NULL,
  PRIMARY KEY  (`tid`),
  KEY `be` (`be`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_template` VALUES (1, 'default', 'default.html', 'default.css', '0', 'print.html', 'print.css', 'login.html', 'text.html');

CREATE TABLE `bfCMS_sys_user` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(250) NOT NULL default '',
  `password` varchar(250) NOT NULL default '',
  `groups` text NOT NULL,
  `online_status` enum('0','1') NOT NULL default '0',
  `blocked` enum('0','1') NOT NULL default '0',
  `deleted` enum('0','1') NOT NULL default '0',
  `status` enum('0','1') NOT NULL default '0',
  `user_since` varchar(20) NOT NULL default '',
  `last_login` varchar(20) NOT NULL default '',
  `curr_login` varchar(20) NOT NULL default '',
  `key` varchar(30) NOT NULL default '',
  `login_page` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `status` (`status`),
  KEY `online` (`online_status`,`last_login`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_user` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', '0', '0', '0', '1', '0', '0', '0', 'fcghv876zg', '0');

CREATE TABLE `bfCMS_sys_usergroup` (
  `gid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(250) NOT NULL default '',
  `backend` enum('0','1') NOT NULL default '0',
  `content` enum('0','1') NOT NULL default '0',
  `settings` enum('0','1') NOT NULL default '0',
  `configure` enum('0','1') NOT NULL default '0',
  `permissions` enum('0','1') NOT NULL default '0',
  `tools` enum('0','1') NOT NULL default '0',
  `demo` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `bfCMS_sys_usergroup` VALUES (1, 'User', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `bfCMS_sys_usergroup` VALUES (2, 'Editor', '1', '1', '0', '0', '0', '0', '0');
INSERT INTO `bfCMS_sys_usergroup` VALUES (3, 'Admin', '1', '1', '1', '1', '1', '1', '0');
