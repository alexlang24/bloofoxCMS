<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/update_0.3.3-0.3.4.php -
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
$db2 = new DB_Tpl();

//**
// add new fields to table sys_charset
$sql = "ALTER TABLE `".$tbl_prefix."sys_charset` ADD `description` VARCHAR( 50 ) NOT NULL;";
$db->query($sql);
echo "New field added to table sys_charset: description, varchar 50<br />\n";

//**
// add new fields to table sys_template
$sql = "ALTER TABLE `".$tbl_prefix."sys_template` ADD `template_print` VARCHAR(80) NOT NULL, ADD `template_print_css` VARCHAR(80) NOT NULL, ADD `template_login` VARCHAR(80) NOT NULL, ADD `template_text` VARCHAR(80) NOT NULL;";
$db->query($sql);
echo "New fields added to table sys_template: template_print, varchar 80; template_print_css, varchar 80; template_login, varchar 80; template_text, varchar 80<br />\n";

// update existing templates
$db2->query("SELECT * FROM `".$tbl_prefix."sys_template` ORDER BY tid");
while($db2->next_record()):
	$tid = $db2->f("tid");
	$sql = "UPDATE `".$tbl_prefix."sys_template` SET `template_print` = 'print.html', `template_print_css` = 'print.css', `template_login` = 'login.html', `template_text` = 'text.html' WHERE `tid` = '".$tid."' LIMIT 1;";
	$db->query($sql);
endwhile;
echo "Existing templates updated.<br />\n";

//**
// add new field to table sys_plugin
$sql = "ALTER TABLE `".$tbl_prefix."sys_plugin` ADD `plugin_version` VARCHAR(10) NOT NULL;";
$db->query($sql);
echo "New field added to table sys_plugin: plugin_version, varchar 10<br />\n";

// update installed plugins
$sql = "UPDATE `".$tbl_prefix."sys_plugin` SET `plugin_version` = '1.0'";
$db->query($sql);

// update installed plugin spaw2
$sql = "UPDATE `".$tbl_prefix."sys_plugin` SET `plugin_version` = '2.0' WHERE pid = 998";
$db->query($sql);

// update installed plugin submenu/submenu2
$sql = "UPDATE `".$tbl_prefix."sys_plugin` SET `index_file` = 'submenu.php',`install_path` = 'submenu/' WHERE pid = 9";
$db->query($sql);

$sql = "UPDATE `".$tbl_prefix."sys_plugin` SET `index_file` = 'submenu.php',`install_path` = 'submenu2/' WHERE pid = 11";
$db->query($sql);

$sql = "UPDATE `".$tbl_prefix."sys_plugin` SET `install_path` = 'searchbox/' WHERE pid = 4";
$db->query($sql);

echo "All plugins updated with version no., plugin submenu, submenu2 and searchbox updated install_path.<br />\n";

//**
// new explorer fields
$sql = "ALTER TABLE `".$tbl_prefix."sys_explorer` ADD `description` text NOT NULL;";
$db->query($sql);
echo "New field added to table sys_explorer: description, text;<br />\n";

//**
// convert and rebuild sys_setting
$setting_vars = array();
$db->query("SELECT * FROM ".$tbl_prefix."sys_setting ORDER BY sid LIMIT 1");
while($db->next_record()):
	$setting_vars["update_check"] = $db->f("update_check");
	$setting_vars["register_notify"] = $db->f("register_notify");
	$setting_vars["login_protection"] = $db->f("login_protection");
	$setting_vars["online_status"] = $db->f("online_status");
	$setting_vars["user_content_only"] = $db->f("user_content_only");
	$setting_vars["admin_mail"] = $db->f("admin_mail");
	$setting_vars["html_mails"] = $db->f("html_mails");
	$setting_vars["htmlentities_off"] = $db->f("htmlentities_off");
	$setting_vars["pw_rule"] = $db->f("pw_rule");
	$setting_vars["textbox_width"] = $db->f("textbox_width");
	if($setting_vars["textbox_width"] < 500) {
		$setting_vars["textbox_width"] = 500;
	}
endwhile;
echo "Table data sys_setting backuped for converting.<br />\n";

$sql = "DROP TABLE ".$tbl_prefix."sys_setting";
$db->query($sql);
echo "Table sys_setting deleted.<br />\n";

$sql = "CREATE TABLE ".$tbl_prefix."sys_setting (
	`sid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`setting_property` VARCHAR(30) NOT NULL,
	`setting_value` VARCHAR(80) NOT NULL,
	PRIMARY KEY  (`sid`)
	) TYPE = MyISAM AUTO_INCREMENT=1";
$db->query($sql);
echo "Table sys_setting created.<br />\n";

$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('1','update_check','".$setting_vars["update_check"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('2','register_notify','".$setting_vars["register_notify"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('3','login_protection','".$setting_vars["login_protection"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('4','online_status','".$setting_vars["online_status"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('5','user_content_only','".$setting_vars["user_content_only"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('6','admin_mail','".$setting_vars["admin_mail"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('7','html_mails','".$setting_vars["html_mails"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('8','htmlentities_off','".$setting_vars["htmlentities_off"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('9','pw_rule','".$setting_vars["pw_rule"]."');";
$db->query($sql);
$sql = "INSERT INTO ".$tbl_prefix."sys_setting (`sid`,`setting_property`,`setting_value`) VALUES ('10','textbox_width','".$setting_vars["textbox_width"]."');";
$db->query($sql);
echo "Table data inserted in table sys_setting.<br />\n";


//**
// update finished
echo "<h2>Update from 0.3.3 to 0.3.4 finished</h2>\n";
echo "<p>Now you can delete folder <b>/update</b> from your server!</p>\n";
echo "<p><a href='index.php'>Update Home</a></p>\n";
?>