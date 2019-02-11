<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.4.1-0.5.0.php -
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
$db2 = new DB_Tpl();

// Check current version
if(get_database_version($db) != "bloofoxCMS 0.4.1") {
	die("Sorry, your current version does not belong to this update process!<br /><a href='index.php'>Back</a>");
}

// update database version to table sys_setting
$sql = "UPDATE ".$tbl_prefix."sys_setting SET setting_value = 'bloofoxCMS 0.5.0' WHERE `sid` = '11'";
$db->query($sql);
echo "Database version updated to table sys_setting.<br />\n";

// update table sys_content
$sql = "ALTER TABLE ".$tbl_prefix."sys_content ADD `config_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `sorting`";
$db->query($sql);
echo "Table sys_content: new field 'config_id' added after 'sorting'.<br />\n";

// update table data of sys_content
require_once("../system/class_content.php");
$contents = new content();

$db->query("SELECT cid,explorer_id FROM ".$tbl_prefix."sys_content");
while($db->next_record()):
	$current_id = $db->f("cid");
	$config_id = $contents->get_config_id_from_eid($db2,$db->f("explorer_id"));
	$sql = "UPDATE ".$tbl_prefix."sys_content SET config_id = '".$config_id."' WHERE cid = '".$current_id."'";
	$db2->query($sql);
endwhile;
echo "Table sys_content: Data updated.<br />\n";

// update table_sys_explorer
$sql = "ALTER TABLE ".$tbl_prefix."sys_explorer ADD `title` VARCHAR(250) NOT NULL";
$db->query($sql);
echo "Table sys_explorer: new field 'title' added after 'description'.<br />\n";

//**
// update finished
echo "<h2>Update from 0.4.0 to 0.4.1 finished</h2>\n";
echo "<p>Now you can delete this folder <b>/update</b> from your server!</p>\n";
echo "<p><a href='index.php'>Update Home</a></p>\n";
?>