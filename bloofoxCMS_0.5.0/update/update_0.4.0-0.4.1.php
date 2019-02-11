<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.4.0-0.4.1.php -
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
if(get_database_version($db) != "bloofoxCMS 0.4.0") {
	die("Sorry, your current version does not belong to this update process!<br /><a href='index.php'>Back</a>");
}

// update database version to table sys_setting
$sql = "UPDATE ".$tbl_prefix."sys_setting SET setting_value = 'bloofoxCMS 0.4.1' WHERE `sid` = '11'";
$db->query($sql);
echo "Database version updated to table sys_setting.<br />\n";

// add phpmyadmin url setting to table sys_setting
$sql = "INSERT INTO ".$tbl_prefix."sys_setting VALUES (30, 'phpmyadmin_url', '');";
$db->query($sql);
echo "Insert of phpmyadmin url to table sys_setting done.<br />\n";

// add maintenance setting to table sys_setting
$sql = "INSERT INTO ".$tbl_prefix."sys_setting VALUES (40, 'maintenance', '0');";
$db->query($sql);
echo "Insert of maintenance to table sys_setting done.<br />\n";

//**
// update finished
echo "<h2>Update from 0.4.0 to 0.4.1 finished</h2>\n";
echo "<p>Now you can delete this folder <b>/update</b> from your server!</p>\n";
echo "<p><a href='index.php'>Update Home</a></p>\n";
?>