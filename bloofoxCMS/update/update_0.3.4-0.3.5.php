<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.4-0.3.5.php -
//
// Copyrights (c) 2007-2009 Alexander Lang, Germany
// info@bloofox.com
// http://www.bloofox.com
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!<br /><a href='index.php'>Go to Home</a>");
}

// Check current version
/* will be used for updates from 0.3.5 and later to higher versions
if(get_database_version() != "bloofoxCMS 0.3.4") {
	die("Sorry, your current version does not belong to this update process!<br /><a href='index.php'>Back</a>");
}
*/
$db = new DB_Tpl();

// add database version to table sys_setting
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('11','db_version','bloofoxCMS 0.3.5');";
$db->query($sql);
echo "Database version added to table sys_setting.<br />\n";

//**
// update finished
echo "<h2>Update from 0.3.4 to 0.3.5 finished</h2>\n";
echo "<p>Now you can delete folder <b>/update</b> from your server!</p>\n";
echo "<p><a href='index.php'>Update Home</a></p>\n";
?>