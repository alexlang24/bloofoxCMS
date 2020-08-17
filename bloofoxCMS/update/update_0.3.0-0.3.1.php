<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.0-0.3.1.php -
//
// Copyrights (c) 2007-2008 Alexander Lang, Germany
// info@bloofox.com
// http://www.bloofox.com
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!<br /><a href='index.php'>Go to Home</a>");
}

$db = new DB_Tpl();
//**
// add new fields to table sys_explorer
// template_id
$sql = "ALTER TABLE `".$tbl_prefix."sys_explorer` ADD `template_id` INT(10) UNSIGNED NOT NULL;";
$db->query($sql);

//**
// create new table in database
//sys_setting
$sql = "CREATE TABLE `".$tbl_prefix."sys_setting` ("
	." `sid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	." `update_check` ENUM('0','1') NOT NULL,"
	." `register_notify` ENUM('0','1') NOT NULL,"
	." `login_protection` ENUM('0','1') NOT NULL,"
	." `online_status` ENUM('0','1') NOT NULL,"
	." `user_content_only` ENUM('0','1') NOT NULL"
	." )"
    ." TYPE = MyISAM;";
$db->query($sql);

$db->query("INSERT INTO `".$tbl_prefix."sys_setting` VALUES (NULL , '0', '0', '1', '0', '0');");

//**
// update finished
echo "<h2>Update from 0.3.0 to 0.3.1 finished</h2>";
echo "<p>Now you can delete folder <b>/update</b> from your server!</p>";
?>