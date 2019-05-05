<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/step2.php -
//
// Copyrights (c) 2006-2012 Alexander Lang, Germany
// info@bloofox.com
// http://www.bloofox.com
//
// bloofoxCMS is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// any later version.
//
// bloofoxCMS is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

if(!file_exists("../system/class_mysqli.php")) {
	load_url("index.php?page=1");
}

if(isset($_POST['send'])) {
	$error = "";
	
	require_once("../system/class_mysqli.php");
	$db = new DB_Tpl();
	
	$db->query("SHOW TABLES LIKE '".$tbl_prefix."sys_charset'");
	if($db->num_rows() != 0) {
		load_url("index.php?page=0");
	}
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_charset` ("
		."`cid` int(10) unsigned NOT NULL auto_increment,"
		."`name` varchar(30) NOT NULL default '',"
		."`description` varchar(50) NOT NULL default '',"
		."PRIMARY KEY  (`cid`)"
		.") ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (1, 'utf-8');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (2, 'ISO-8859-1');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (3, 'ISO-8859-2');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (4, 'ISO-8859-3');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (5, 'ISO-8859-4');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (6, 'ISO-8859-5');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (7, 'windows-1250');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (8, 'windows-1251');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (9, 'windows-1252');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (10, 'windows-1253');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (11, 'windows-1254');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_charset` (`cid`,`name`) VALUES (12, 'windows-1255');");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_config` ("
		."`cid` int(10) unsigned NOT NULL auto_increment,"
		."`lang_id` int(10) unsigned NOT NULL default '0',"
		."`urls` text NOT NULL,"
		."`tmpl_id` int(10) unsigned NOT NULL default '0',"
		."`name` varchar(250) NOT NULL default '',"
		."`root_id` int(10) unsigned NOT NULL default '0',"
		."`user_groups` text NOT NULL,"
		."`meta_title` varchar(250) NOT NULL default '',"
		."`meta_desc` text NOT NULL,"
		."`meta_keywords` text NOT NULL,"
		."`meta_author` varchar(250) NOT NULL default '',"
		."`meta_charset` varchar(250) NOT NULL default '',"
		."`meta_doctype` varchar(250) NOT NULL default '',"
		."`meta_copyright` varchar(250) NOT NULL default '',"
		."`mod_rewrite` enum('0','1') NOT NULL default '0',"
		."`mail` varchar(250) NOT NULL default '',"
		."`default_group` int(10) unsigned NOT NULL default '0',"
		."PRIMARY KEY  (`cid`)"
		.") ENGINE=MyISAM AUTO_INCREMENT=1 ;");
		
	$db->query("CREATE TABLE `".$tbl_prefix."sys_content` (
		`cid` int(10) unsigned NOT NULL auto_increment,
		`explorer_id` int(10) unsigned NOT NULL default '0',
		`sorting` int(10) unsigned NOT NULL default '0',
		`config_id` int(10) unsigned NOT NULL default '0',
		`content_type` int(2) unsigned NOT NULL default '0',
		`title` varchar(250) NOT NULL default '',
		`text` longtext NOT NULL,
		`blocked` ENUM('0','1') NOT NULL,
		`created_by` VARCHAR(250) NOT NULL,
		`created_at` VARCHAR(20) NOT NULL,
		`changed_by` VARCHAR(250) NOT NULL,
		`changed_at` VARCHAR(20) NOT NULL,
		`startdate` varchar(20) NOT NULL default '',
		`enddate` varchar(20) NOT NULL default '',
		PRIMARY KEY  (`cid`),
		KEY `explorer_id` (`explorer_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_explorer` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_lang` (
		`lid` int(10) unsigned NOT NULL auto_increment,
		`name` varchar(250) NOT NULL default '',
		`flag` varchar(250) NOT NULL default '',
		`filename` varchar(250) NOT NULL default '',
		`date` varchar(20) NOT NULL default 'd.m.Y',
		`datetime` varchar(20) NOT NULL default 'd.m.Y - H:i',
		`token` varchar(5) NOT NULL default '',
		PRIMARY KEY  (`lid`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	if($_SESSION['lang'] == "de") {
		$db->query("INSERT INTO `".$tbl_prefix."sys_lang` (`lid`,`name`,`flag`,`filename`,`date`,`datetime`,`token`) VALUES (1, 'Deutsch', 'de.gif', 'deutsch.php', 'd.m.Y', 'd.m.Y - H:i', 'de');");
		$db->query("INSERT INTO `".$tbl_prefix."sys_lang` (`lid`,`name`,`flag`,`filename`,`date`,`datetime`,`token`) VALUES (2, 'English', 'en.gif', 'english.php', 'm/d/Y', 'm/d/Y - H:i', 'en');");
	} else {
		$db->query("INSERT INTO `".$tbl_prefix."sys_lang` (`lid`,`name`,`flag`,`filename`,`date`,`datetime`,`token`) VALUES (1, 'English', 'en.gif', 'english.php', 'm/d/Y', 'm/d/Y - H:i', 'en');");
		$db->query("INSERT INTO `".$tbl_prefix."sys_lang` (`lid`,`name`,`flag`,`filename`,`date`,`datetime`,`token`) VALUES (2, 'Deutsch', 'de.gif', 'deutsch.php', 'd.m.Y', 'd.m.Y - H:i', 'de');");
	}
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_media` (
		`mid` int(10) unsigned NOT NULL auto_increment,
		`media_type` int(10) unsigned NOT NULL default '0',
		`filename` varchar(250) NOT NULL default '',
		PRIMARY KEY  (`mid`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_permission` (
		`pid` int(10) unsigned NOT NULL auto_increment,
		`group_id` int(10) unsigned NOT NULL default '0',
		`page` varchar(80) NOT NULL default '',
		`object_w` enum('0','1') NOT NULL default '0',
		`object_d` enum('0','1') NOT NULL default '0',
		PRIMARY KEY  (`pid`),
		KEY `group_id` (`group_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_plugin` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
		
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (1, 'module_date', 'date.php', 'date.html', '', '', '', 'module_date/', 0, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (1004, 'sitemap', 'sitemap.php', 'sitemap.html', '', '', '', 'sitemap/', 1, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (1003, 'search_simple', 'search.php', 'search.html', '', '', '', 'search_simple/', 1, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (4, 'module_searchbox', 'searchbox.php', 'searchbox.html', '', '', '', 'module_searchbox/', 0, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (1001, 'contact_form_simple', 'contact.php', 'contact.html', 'tmpl_handler.php', '', '', 'contact_form_simple/', 1, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (7, 'module_location_bar', 'location_bar.php', 'location_bar.html', '', '', '', 'module_location_bar/', 0, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (10, 'navigation_menu_default', 'menu.php', 'menu.html', '', '', '', 'navigation_menu_default/', 0, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (11, 'navigation_submenu_default', 'submenu.php', 'submenu.html', '', '', '', 'navigation_submenu_default/', 0, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (1002, 'register_form_simple', 'register.php', 'register_account.html', 'tmpl_handler.php', '', '', 'register_form_simple/', 1, '1', '2.0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_plugin` (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`) VALUES (16, 'module_loginbox', 'login.php', 'login.html', '', '', '', 'module_loginbox/', 0, '1', '2.0');");

	$db->query("CREATE TABLE `".$tbl_prefix."sys_profile` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
		
	$db->query("INSERT INTO `".$tbl_prefix."sys_profile` (`pid`,`user_id`,`firstname`,`lastname`,`address1`,`address2`,`city`,`zip_code`,`email`,`birthday`,`gender`,`picture`,`be_lang`,`be_tmpl`,`show_email`) VALUES (1, 1, '', 'Administrator', '', '', '', '', '', '', '0', '', 1, 0, '0');");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_session` (
        `sid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `type` TINYINT(1) UNSIGNED NOT NULL,
        `uid` INT(10) UNSIGNED NOT NULL,
        `date` DATE NOT NULL,
        `time` TIME NOT NULL,
        `status` TINYINT(1) UNSIGNED NOT NULL,
        `timestamp` VARCHAR(20) NOT NULL,
        `ip` VARCHAR(80) NOT NULL,
        `session_id` VARCHAR(80) NOT NULL,
		PRIMARY KEY (`sid`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
		
	$db->query("CREATE TABLE ".$tbl_prefix."sys_setting (
		`sid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`setting_property` VARCHAR(30) NOT NULL,
		`setting_value` VARCHAR(80) NOT NULL,
		PRIMARY KEY  (`sid`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES (1, 'update_check', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(2, 'register_notify', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(3, 'login_protection', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(4, 'online_status', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(5, 'user_content_only', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(6, 'admin_mail', '');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(7, 'html_mails', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(8, 'htmlentities_off', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(9, 'pw_rule', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(10, 'textbox_width', '500');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(11, 'database_version', '".$db_version."');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(12, 'textbox_height', '250');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES (20, 'manual_register', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES (30, 'phpmyadmin_url', '');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES (40, 'maintenance', '0');");
	
	$db->query("CREATE TABLE `".$tbl_prefix."sys_template` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("INSERT INTO `".$tbl_prefix."sys_template` (`tid`,`name`,`template`,`css`,`be`,`template_print`,`template_print_css`,`template_login`,`template_text`) VALUES (1, 'default', 'default.html', 'default.css', '0', 'print.html', 'print.css', 'login.html', 'text.html');");
	//$db->query("INSERT INTO `".$tbl_prefix."sys_template` (`tid`,`name`,`template`,`css`,`be`,`template_print`,`template_print_css`,`template_login`,`template_text`) VALUES (2, 'blue3columns', 'default.html', 'default.css', '0', 'print.html', 'print.css', 'login.html', 'text.html');");
	//$db->query("INSERT INTO `".$tbl_prefix."sys_template` (`tid`,`name`,`template`,`css`,`be`,`template_print`,`template_print_css`,`template_login`,`template_text`) VALUES (3, 'grey2columns', 'default.html', 'default.css', '0', 'print.html', 'print.css', 'login.html', 'text.html');");

	$db->query("CREATE TABLE `".$tbl_prefix."sys_user` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
	
	$db->query("INSERT INTO `".$tbl_prefix."sys_user` (`uid`,`username`,`password`,`groups`,`online_status`,`blocked`,`deleted`,`status`,`user_since`,`last_login`,`curr_login`,`key`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', '0', '0', '0', '1', '0', '0', '0', 'fcghv876zg');");

	$db->query("CREATE TABLE `".$tbl_prefix."sys_usergroup` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;");
		
	$db->query("INSERT INTO `".$tbl_prefix."sys_usergroup` (`gid`,`name`,`backend`,`content`,`settings`,`configure`,`permissions`,`tools`,`demo`) VALUES (1, 'User', '0', '0', '0', '0', '0', '0', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_usergroup` (`gid`,`name`,`backend`,`content`,`settings`,`configure`,`permissions`,`tools`,`demo`) VALUES (2, 'Editor', '1', '1', '0', '0', '0', '0', '0');");
	$db->query("INSERT INTO `".$tbl_prefix."sys_usergroup` (`gid`,`name`,`backend`,`content`,`settings`,`configure`,`permissions`,`tools`,`demo`) VALUES (3, 'Admin', '1', '1', '1', '1', '1', '1', '0');");
	
	if($_POST['demodata'] == TRUE) {
		// insert example records
		// config
		$folder = substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],"/install"));
		$db->query("INSERT INTO `".$tbl_prefix."sys_config` (`cid`,`lang_id`,`urls`,`tmpl_id`,`name`,`root_id`,`user_groups`,`meta_title`,`meta_desc`,`meta_keywords`,`meta_author`,`meta_charset`,`meta_doctype`,`meta_copyright`,`mod_rewrite`,`mail`,`default_group`) VALUES (1, 1, 'http://".$_SERVER['SERVER_NAME'].$folder."', 1, 'bloofoxCMS', 1, '', 'bloofoxCMS', '', 'bloofoxCMS', 'Alexander Lang', 'ISO-8859-1', 'XHTML 1.0 Transitional', 'bloofox.com - All rights reserved.', '0', 'info@bloofox.com', '1');");
		// contents
		$timestamp = time();
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (1, 1, 0, 10000, 1, 'My Website', 2, '', '', 2, 0, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (2, 2, 1, 10000, 1, 'Home', 0, '', '', 0, 0, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (3, 2, 1, 20000, 1, 'First Page', 0, '', '', 0, 0, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (4, 3, 3, 10000, 1, 'Contact', 3, '', '', 0, 5, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (5, 2, 1, 30000, 1, 'Photos', 0, '', '', 0, 0, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (6, 2, 1, 40000, 1, 'Sitemap', 3, '', '', 0, 2, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_explorer` (`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES (7, 3, 6, 10000, 1, 'Search', 3, '', '', 0, 3, '', '0', '0', '', '0', '', '', 'admin', '".$timestamp."', '', '', '',0);");
		$db->query("INSERT INTO `".$tbl_prefix."sys_content` (`cid`,`explorer_id`,`sorting`,`config_id`,`content_type`,`title`,`text`,`blocked`,`created_by`,`created_at`,`changed_by`,`changed_at`) VALUES (1, 2, 1000, 1, 0, 'Welcome to bloofoxCMS', 'Congratulations! You successfully installed bloofoxCMS on your webserver.\r<br />\r<br />First Steps with bloofoxCMS\r<br />---------------------------\r<br />\r<br />1. Change the admin&#039;s password\r<br />\r<br />The default admin user is installed by default with password \"admin\". It is strongly recommended to change this password.\r<br />\r<br />2. Go to Admincenter, Administration, General\r<br />\r<br />Check all general settings and change them in the way you like. Read documentation for further information.\r<br />\r<br />3. Configure your website (project)\r<br />\r<br />Go to Admincenter, Administration, Projects and click the Edit button. Check all preferences and change them in the way you like.\r<br />\r<br />=> Now you&#039;re ready to create content.', '0', 'admin', '1229776973', 'admin', '1229777492');");
		$db->query("INSERT INTO `".$tbl_prefix."sys_content` (`cid`,`explorer_id`,`sorting`,`config_id`,`content_type`,`title`,`text`,`blocked`,`created_by`,`created_at`,`changed_by`,`changed_at`) VALUES (2, 3, 1000, 1, 0, 'First page', 'This is the bloofoxCMS example content. It seems there will be some other contents in future... just try out the different functions and other plugins, e.g. news or blog.', '0', 'admin', '".$timestamp."', '', '');");
		$db->query("INSERT INTO `".$tbl_prefix."sys_content` (`cid`,`explorer_id`,`sorting`,`config_id`,`content_type`,`title`,`text`,`blocked`,`created_by`,`created_at`,`changed_by`,`changed_at`) VALUES (3, 5, 1000, 1, 0, 'Photos', 'This could be a place to publish some photographs, pictures or whatever. Did you already install the gallery plugin? With this plugin you can manage your pictures in albums and galleries.', '0', 'admin', '".$timestamp."', '', '');");
	}
	
	if($error == "") {
		// *** Create install.txt Report ***
		if(is_writeable("../media/txt")) {
			$report = fopen("../media/txt/install.txt","w");
			fwrite($report,date("m/d/Y",time())." - bloofoxCMS was installed.\n\r");
			fclose($report);
		}
		load_url("index.php?page=3");
	}
}

$tpl->set_block("content", "step2", "step2_handle");

$tpl->set_var(array(
	"step2_action"        => "index.php?page=2",
	"step2_text"          => "<p>".$strCreateTables."</p>",
	"step2_error"         => $error,
	"step2_tables"        => "<p class='bold'>".$strSysTables."</p>",
	"step2_tablelist"     => $strTablelist,
	"step2_options"       => "<p class='bold'>".$strOptions."</p>",
	"step2_demodata"      => "<p><input type='checkbox' name='demodata' /> ".$strDemoData."</p>",
	"step2_next_step"     => "<input type='submit' name='send' value='".$strNextStep3."' />"
	));

$tpl->parse("step2_handle", "step2", true);
?>