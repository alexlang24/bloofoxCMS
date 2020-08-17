<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.2-0.3.3.php -
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
// add new fields to table sys_setting
$sql = "ALTER TABLE `".$tbl_prefix."sys_setting` ADD `admin_mail` varchar(250) NOT NULL, ADD `html_mails` ENUM('0','1') NOT NULL DEFAULT '0';";
$db->query($sql);

$sql = "ALTER TABLE `".$tbl_prefix."sys_setting` ADD `textbox_width` int(10) NOT NULL, ADD `htmlentities_off` ENUM('0','1') NOT NULL DEFAULT '0';";
$db->query($sql);

$sql = "ALTER TABLE `".$tbl_prefix."sys_setting` ADD `pw_rule` ENUM('0','1','2') NOT NULL DEFAULT '0';";
$db->query($sql);

//**
// add new fields to table sys_profile
$sql = "ALTER TABLE `".$tbl_prefix."sys_profile` ADD `show_email` ENUM('0','1') NOT NULL DEFAULT '0';";
$db->query($sql);

//**
// add new fields to table sys_user
$sql = "ALTER TABLE `".$tbl_prefix."sys_user` ADD `login_page` int(10) NOT NULL DEFAULT '0';";
$db->query($sql);

//**
// create new sorting for contents
$db2 = new DB_Tpl();
$db->query("SELECT * FROM `".$tbl_prefix."sys_content` ORDER BY `explorer_id`,`sorting`");
while($db->next_record()):
	$new_explorerid = $db->f("explorer_id");
	if($new_explorerid != $old_explorerid) {
		$new_sorting = 100000;
	} else {
		$new_sorting += 1;
	}
	$db2->query("UPDATE `".$tbl_prefix."sys_content` SET `sorting` = '".$new_sorting."' WHERE cid = '".$db->f("cid")."'");
	$old_explorerid = $db->f("explorer_id");
endwhile;

//**
// delete all mediacenter entries because they are not needed any more
$sql = "DELETE FROM `".$tbl_prefix."sys_media`;";
$db->query($sql);

//**
// update finished
echo "<h2>Update from 0.3.2 to 0.3.3 finished</h2>";
echo "<p>Now you can delete folder <b>/update</b> from your server!</p>";
?>