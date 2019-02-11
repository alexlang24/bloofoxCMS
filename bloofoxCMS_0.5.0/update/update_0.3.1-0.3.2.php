<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.1-0.3.2.php -
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
// rename fields in tables
$sql = "ALTER TABLE `".$tbl_prefix."sys_config` CHANGE `company_name` `name` VARCHAR( 250 ) NOT NULL";
$db->query($sql);

$sql = "ALTER TABLE `".$tbl_prefix."sys_content` CHANGE `invisible` `blocked` ENUM( '0', '1' ) NOT NULL";
$db->query($sql);

//**
// add new fields to tables
$sql = "ALTER TABLE `".$tbl_prefix."sys_config` ADD `default_group` INT(10) NOT NULL, ADD `user_deleted` ENUM('0','1') NOT NULL DEFAULT '0';";
$db->query($sql);

//**
// update finished
echo "<h2>Update from 0.3.1 to 0.3.2 finished</h2>";
echo "<p>Now you can delete folder <b>/update</b> from your server!</p>";
?>