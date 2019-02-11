<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.4-0.3.5.php -
//
// Copyrights (c) 2007-2012 Alexander Lang, Germany
// info@bloofox.com
// http://www.bloofox.com
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!<br /><a href='index.php'>Go to Home</a>");
}

$db = new DB_Tpl();

// Check current version
if(get_database_version($db) != "bloofoxCMS 0.3.5") {
	die("Sorry, your current version does not belong to this update process!<br /><a href='index.php'>Back</a>");
}

// update database version to table sys_setting
$sql = "UPDATE ".$tbl_prefix."sys_setting SET setting_value = 'bloofoxCMS 0.4.0' WHERE `sid` = '11'";
$db->query($sql);
echo "Database version updated to table sys_setting.<br />\n";

// add textbox height setting to table sys_setting
$sql = "INSERT INTO `".$tbl_prefix."sys_setting` (`sid`,`setting_property`,`setting_value`) VALUES(12, 'textbox_height', '250');";
$db->query($sql);
echo "Insert of textbox_height to table sys_setting done.<br />\n";

// add manual register setting to table sys_setting
$sql = "INSERT INTO ".$tbl_prefix."sys_setting VALUES (20, 'manual_register', '0');";
$db->query($sql);
echo "Insert of manual_register to table sys_setting done.<br />\n";

//**
// add new fields to tables
$sql = "ALTER TABLE `".$tbl_prefix."sys_content` ADD `startdate` varchar(20) NOT NULL DEFAULT '', ADD `enddate` varchar(20) NOT NULL DEFAULT '';";
$db->query($sql);
echo "Fields to table sys_content added.<br />\n";

//**
// update finished
echo "<h2>Update from 0.3.5 to 0.4.0 finished</h2>\n";
echo "<p>Now you can delete this folder <b>/update</b> from your server!</p>\n";
echo "<p><a href='index.php'>Update Home</a></p>\n";
?>