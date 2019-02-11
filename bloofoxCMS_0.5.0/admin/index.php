<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/index.php -
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

// Make or find session
session_name("sid");
session_start();

// Set error reporting
error_reporting (E_ALL ^ E_NOTICE);

// Define constants
define ('SYS_WORK_DIR', getcwd());
define ('SYS_INDEX', 1);
define ('SYS_FOLDER', '../system');

// Load required system files
include_once("../config.php");
require_once("../functions.php");

// Check if setup report was created, if not start setup
if(!file_exists("../media/txt/install.txt")) {
	load_url("../install/index.php");
}

// Check if setup folder still exists, if so then die
if(is_dir("../install")) {
	echo(show_error_screen("Error","The setup folder '/install' still exists! Please delete it from your server."));
	die();
}

// Load required system libraries
require_once("../".$mysql_config_path);
require_once(SYS_FOLDER."/class_template.php");
require_once(SYS_FOLDER."/class_permissions.php");
require_once(SYS_FOLDER."/class_config.php");

// Load required Admincenter libraries
require_once(SYS_FOLDER."/class_admincenter.php");
require_once(SYS_FOLDER."/class_filter.php");
require_once(SYS_FOLDER."/class_pagehandler.php");
require_once(SYS_FOLDER."/class_structure.php");

// Init libraries
$db = new DB_Tpl();
$db2 = new DB_Tpl();
$tpl = new Template();
$tpl->set_unknowns("remove"); // change parameter to show unknowns
$perm = new permissions();
$ac = new admincenter();

// if not logged in then switch mode to login
$mode = $_GET['mode'];
if($perm->check_session() == 0) {
	if(isset($mode)) {
		load_url("index.php");
	}
	$mode = "login";
	$logged_in = 0;
} else {
	$sys_group_vars = $perm->get_group_vars($db,$_SESSION["usergroups"]);
	if($sys_group_vars['backend'] == 0) {
		$mode = "login";
		$logged_in = 0;
	} else {
		$sys_rights = $perm->get_permission($db,$_SESSION["usergroups"]);
		$logged_in = 1;
	}
}

// Search for valid config
$conf = new config();
$sys_config_vars = $conf->get_config_vars($db,0);
include_once(SYS_WORK_DIR."/config.php");

// Get general settings
$sys_setting_vars = $conf->get_setting_vars($db,1);

// load language & template parameters
include($path_to_lang_files.$ac->get_admin_language_file($db));
$sys_vars['date'] = "d.m.Y";
$sys_vars['datetime'] = "d.m.Y - H:i";
if($logged_in == 1) {
	// get user profile settings
	$sys_profile = $perm->get_user_profile($db,$_SESSION["uid"]);
	$sys_vars['database'] = $db->Database;
	
	// get language date settings and token
	$db->query("SELECT date,datetime,token FROM ".$tbl_prefix."sys_lang WHERE lid = '".$sys_profile['be_lang']."' ORDER BY lid LIMIT 1");
	while($db->next_record()):
		if($db->f("date") != "") {
			$sys_vars['date'] = $db->f("date");
		}
		if($db->f("datetime") != "") {
			$sys_vars['datetime'] = $db->f("datetime");
		}
		$sys_vars['token'] = $db->f("token");
	endwhile;
	
	// check for individual admincenter style
	if($sys_profile['be_tmpl'] != 0) {
		$db->query("SELECT name,template,css FROM ".$tbl_prefix."sys_template WHERE tid = '".$sys_profile['be_tmpl']."' LIMIT 1");
		while($db->next_record()):
			if(is_dir($path_to_template_files.$db->f("name")."/")) {
				if(file_exists($path_to_template_files.$db->f("name")."/".$db->f("template")) && file_exists($path_to_template_files.$db->f("name")."/".$db->f("css"))) {
					$tmpl_vars['name'] = $path_to_template_files.$db->f("name")."/".$db->f("template");
					$tmpl_vars['css'] = $path_to_template_files.$db->f("name")."/".$db->f("css");
					$tmpl_vars['path'] = $path_to_template_files.$db->f("name")."/";
				}
			}
		endwhile;
	}
} else {
	$tmpl_vars['name'] = $tmpl_vars['login'];
}
$tmpl_vars['header'] = str_replace("{tmpl_css_path}",$tmpl_vars['css'],$tmpl_vars['header']);

// get installed plugins for admincenter
$db->query("SELECT pid,status FROM ".$tbl_prefix."sys_plugin WHERE plugin_type = '0' ORDER BY pid");
while($db->next_record()):
	$admin_plugin[$db->f("pid")] = $db->f("status");
endwhile;

// handle correct templates and code handler
$ph = new pagehandler($mode,$_GET['page'],$_GET['action'],$tmpl_vars['path']);
$ph->set_tmpl_handling($db);
$tmpl_set = $ph->get_tmpl_parameter();
$page = $_GET['page'];
$action = $_GET['action'];

// set templates
$tpl->set_file( array (
	"template"      => $tmpl_vars['name'],// Layout Template
	"tmpl_menu"     => $tmpl_set['menu'], // Menu Template
	"tmpl_content"  => $tmpl_set['mode']  // Content Template
	));
	
// load modules
include(SYS_WORK_DIR."/mod_menu.php");
include(SYS_WORK_DIR.$tmpl_set['handler']);

// load and parse template
$tmpl_vars['doctype'] = create_doctype("XHTML 1.0 Transitional");

// set be_top
if($logged_in == 1) {
	$tmpl_vars['top'] = "<a href='#'>".get_caption('0480','Back to Top')."</a>";
} else {
	$tmpl_vars['top'] = "";
}

$tpl->set_var(array(
	"tmpl_doctype"  => $tmpl_vars['doctype'],
	"tmpl_header"   => $tmpl_vars['header'],
	"tmpl_footer"   => $tmpl_vars['footer'],
	"tmpl_top"      => $tmpl_vars['top'],
	"tmpl_frontend" => get_caption('0490','Frontend'),
	"tmpl_message"  => $ac->show_demo_error($sys_group_vars['demo']),
	"tmpl_fe_url"   => "<a href='".$sys_config_vars['url']."' title='".$sys_config_vars['url']."'>".$sys_config_vars['url']."</a>",
	"tmpl_plugin_header" => $sys_plugin_scripts['head'],
	"tmpl_plugin_script" => $sys_plugin_scripts['body']
	));

$tpl->parse ("template_handle", array("template"));
$tpl->p ("template_handle");

// Clear online status for users who didnt log out
if(isset($_SESSION["uid"])) {
	$old_time = time() - 3600; // 3600 = 1h
	$db->query("UPDATE ".$tbl_prefix."sys_user SET online_status = '0' WHERE online_status = '1' && curr_login < '".$old_time."' && uid <> '".$_SESSION["uid"]."' ORDER BY online_status");
}

// Reactivate own online status if it was deactivated
if(isset($_SESSION["uid"])) {
	$db->query("UPDATE ".$tbl_prefix."sys_user SET online_status = '1' WHERE online_status = '0' && uid = '".$_SESSION["uid"]."' ORDER BY uid");
}

// unset confirm message
CreateConfirmMessage(0,'');
?>