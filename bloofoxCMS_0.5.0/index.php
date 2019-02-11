<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - index.php -
//
// Copyrights (c) 2006-2013 Alexander Lang, Germany
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

// Set php error reporting
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE);

// Define constants
define('SYS_WORK_DIR', getcwd());
define('SYS_INDEX', 1);
define('SYS_FOLDER', '/system');
define('SYS_FILE', 'index.php');

// Load required files
include_once(SYS_WORK_DIR."/config.php");
require_once(SYS_WORK_DIR."/functions.php");

// Check setup and some existing folders
check_setup_and_folders();

// Load required libraries
require_once($mysql_config_path);
require_once(SYS_WORK_DIR.SYS_FOLDER."/class_template.php");
require_once(SYS_WORK_DIR.SYS_FOLDER."/class_config.php");
require_once(SYS_WORK_DIR.SYS_FOLDER."/class_pages.php");
require_once(SYS_WORK_DIR.SYS_FOLDER."/class_content.php");
require_once(SYS_WORK_DIR.SYS_FOLDER."/class_permissions.php");

// Init mysql and config library
$db = new DB_Tpl();
$config = new config();

// Check versions
check_versions($db);

// Search for valid config
$sys_config_vars = $config->get_config_vars($db);

// Get general settings
$sys_setting_vars = $config->get_setting_vars($db);

// Get language parameters
$sys_lang_vars = $config->get_language_vars($db,$sys_config_vars);
include(SYS_WORK_DIR."/languages/".$sys_lang_vars['language']);

// Init permissions and handle logout
$perm = new permissions();
$perm->logout($db,SYS_FILE);

// unblock account after register confirmation, source is email
$perm->unblock_user_account($db);

// check if login is required for content or for project
$sys_vars['login_status'] = 0; // 0=init; 1=ok; 2=failed;

if(isset($_POST['login']) || $_GET['login'] == "true") {
	if($perm->login($db,validate_text($_POST['username']),validate_text($_POST['password'])) == 1) {
		$sys_vars['login_status'] = 1;
		if($_GET['login'] == "true") {
			// load specific page for user after successful login
			load_url(SYS_FILE."?account=overview");
		}
	} else {
		$sys_vars['login_status'] = 2;
	}
}

// check if user is already logged in
$sys_vars['usertext'] = get_caption("A100","You are not logged in!");
if($perm->check_session() == 1) {
	// session already exists => load sys_vars from session
	$sys_group_vars = $perm->get_group_vars($db,$_SESSION['usergroups']);
	$sys_vars = $perm->get_vars_from_session($sys_vars);
}

// do group comparison for current user
$group_found = 0;
$group_found = $perm->compare_groups($sys_config_vars['user_groups']);

// get explorer entry for content
$sys_explorer_id = $_GET['page'];
if(empty($sys_explorer_id)) {
	$sys_explorer_id = $sys_config_vars['root_id'];
}

// check if parameter $page is a valid integer value
if(CheckInteger($sys_explorer_id) == FALSE) {
	unset($sys_explorer_id);
	$sys_explorer_id = $sys_config_vars['root_id'];
}

// check if parameter $start is a valid integer value
if(CheckInteger($_GET['start']) == FALSE) {
	$_GET['start'] = 1;
}

// check if parameter $print is a valid integer value
if(CheckInteger($_GET['print']) == FALSE || $_GET['print'] > 1) {
	$print = 0;
} else {
	$print = 1;
}

// get page vars
$pages = new pages();
$sys_explorer_vars = $pages->get_explorer_vars($db,$sys_config_vars,$sys_explorer_id);

// get explorer vars for shortcut entry
$sys_explorer_vars2 = $sys_explorer_vars;
if($sys_explorer_vars['link_type'] == 2) {
	$sys_explorer_vars = $pages->get_explorer_vars($db,$sys_config_vars,$sys_explorer_vars['link_eid']);
}

// change template for explorer page
if($sys_explorer_vars['template_id'] != 0) {
	$sys_config_vars['tmpl_id'] = $sys_explorer_vars['template_id'];
}

// compare groups for content access
if($sys_explorer_vars['groups'] != "") {
	$group_found = $perm->compare_groups($sys_explorer_vars['groups']);
}

// set login is required or not; init=0; required=1; required
$sys_vars['login_required'] = 0;
if($group_found == 0 || $sys_vars['login_status'] == 2) {
	$sys_vars['login_required'] = 1;
}

// user account handling
if(isset($_GET['account']) && $sys_vars['login_status'] == 1) {
	$sys_vars['login_required'] = 2;
}

// init content class
$content = new content();
if($sys_vars['login_required'] == 0) {
	$content->set_eid($sys_explorer_vars['eid']);
}

// init template class and handle template errors
$tpl = new Template();
$tpl->set_unknowns("remove");

// get template vars
$sys_tmpl_vars = $config->get_tmpl_vars($db,$sys_config_vars,$print);

// set content template or login window template if type is not "external link"
if($sys_explorer_vars['link_type'] != 3) {
	switch($sys_explorer_vars['link_type'])
	{
		default:
			// default content view
			$sys_tmpl_vars['content'] = "/templates/".$sys_tmpl_vars['name']."/".$sys_tmpl_vars['text'];
		break;
	}
}

// check if var cid is given
if(isset($_GET['cid'])) {
	if(CheckInteger($_GET['cid']) == FALSE) {
		unset($_GET['cid']);
	}
	
	// if var cid is really defined then load single content plugin
	if(isset($_GET['cid'])) {
		$my_plugin_id = 33;
		if($content->plugin_available($my_plugin_id) == 1) {
			$sys_explorer_vars['link_type'] = 3; // handles not to load default text but specific plugin
			$sys_explorer_vars['link_plugin'] = $my_plugin_id; // text details
		}
	}
}

// set login template and if needed login error message
if($sys_vars['login_required'] == 1) {
	if(isset($_POST['login']) && $sys_vars['login_status'] == 2) {
		$login_error = "<p class='error'>".get_caption('9250','Login failed!')."</p>";
	} else {
		if(isset($_POST['login']) && $group_found == 0) {
			$login_error = "<p class='error'>".get_caption('9260','You have not the permissions to view this page.')."</p>";
		} else {
			if($sys_vars['login_status'] == 2) {
				$login_error = "<p class='error'>".get_caption('9260','You have not the permissions to view this page.')."</p>";
			} else {
				$login_error = "";
			}
		}
	}
	
	$content->set_login_vars(get_caption('A030','Login'),$_SERVER['REQUEST_URI'],get_caption('A010','E-Mail / Username'),get_caption('A020','Password'),$login_error);
	$sys_tmpl_vars['content'] = "/templates/".$sys_tmpl_vars['name']."/".$sys_tmpl_vars['login'];
}

// set print variables; can be overwritten by a content plugin later
if($print == 0) {
	$sys_print_vars['print'] = "<div class='print'><a href='index.php?page=".$sys_explorer_id."&amp;start=".$_GET['start']."&amp;print=1' target='print' title='".get_caption('B010','Print')."'>".get_caption('B010','Print')."</a></div>";
}

// handle account templates
$account = validate_text($_GET['account']);
if($sys_vars['login_required'] == 2) {
	switch($account)
	{
		case 'changepwd':
			// account change password
			$sys_tmpl_vars['account'] = "account_password.html";
			$sys_tmpl_vars['content'] = "/templates/".$sys_tmpl_vars['name']."/".$sys_tmpl_vars['account'];
		break;
		
		default:
			// account overview
			$sys_tmpl_vars['account'] = "account_overview.html";
			$sys_tmpl_vars['content'] = "/templates/".$sys_tmpl_vars['name']."/".$sys_tmpl_vars['account'];
		break;
	}
	
	$sys_print_vars['print'] = "";
}

// Plugin Templates
$plugins_tmpl = array();

// Load all active module plugin templates in plugin template array var
if($print == 0) {
	$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin WHERE status = '1' && plugin_type = '0'");
	
	while($db->next_record()):
		$plugins_tmpl['plugin_'.$db->f("name")] = SYS_WORK_DIR."/plugins/".$db->f("install_path").$db->f("index_tmpl");
	endwhile;
}

// Load content plugin template in system template array var
if($sys_explorer_vars['link_type'] == 3) {
	if($sys_vars['login_required'] == 0) {
		$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin WHERE status = '1' && plugin_type = '1' && pid = '".$sys_explorer_vars['link_plugin']."' LIMIT 1");
		
		while($db->next_record()):
			$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path").$db->f("index_tmpl");
			if($db->f("tmpl_handler") != "") {
				include(SYS_WORK_DIR."/plugins/".$db->f("install_path").$db->f("tmpl_handler"));
			}
		endwhile;
	}
}

// set templates
$basic_tmpl = array (
	"template"          => SYS_WORK_DIR."/templates/".$sys_tmpl_vars['name']."/".$sys_tmpl_vars['tmpl'], // Layout Template
	"template_content"  => SYS_WORK_DIR.$sys_tmpl_vars['content'] // Content Template
	);
$template_arrays = array_merge($basic_tmpl,$plugins_tmpl);

$tpl->set_file($template_arrays);

// include default content or login window or account module
if($sys_explorer_vars['link_type'] != 3 || $sys_vars['login_required'] == 1) {
	//$content->build_content($db,$login_required);
	include(SYS_WORK_DIR."/system/inc_content.php");
}

// Plugin Code
unset($sys_plugin_vars);

// Load all active module plugin files
if($print == 0) {
	$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin WHERE status = '1' && plugin_type = '0'");
	
	while($db->next_record()):
		include_once(SYS_WORK_DIR."/plugins/".$db->f("install_path").$db->f("index_file"));
	endwhile;
}

// Include active content plugin file
if($sys_explorer_vars['link_type'] == 3) {
	$db->query("SELECT install_path,index_file FROM ".$tbl_prefix."sys_plugin WHERE status = '1' && plugin_type = '1' && pid = '".$sys_explorer_vars['link_plugin']."' LIMIT 1");
	while($db->next_record()):
		include_once(SYS_WORK_DIR."/plugins/".$db->f("install_path").$db->f("index_file"));
	endwhile;
}

// get doctype
$sys_tmpl_vars['doctype'] = create_doctype($sys_config_vars['meta_doctype']);

// get header and replace title bar with current page
$sys_tmpl_vars['header'] = create_header($sys_config_vars,$sys_tmpl_vars,$sys_explorer_vars,$sys_plugin_vars);

// load and parse template
$tpl->set_var(array(
	// default placeholders (should not be changed):
	"template_doctype"       => $sys_tmpl_vars['doctype'],
	"template_header"        => $sys_tmpl_vars['header'],
	"template_url"           => $sys_config_vars['url'],
	"template_title"         => $sys_config_vars['meta_title'],
	"template_copy"          => $sys_config_vars['meta_copyright'],
	"template_print"         => $sys_print_vars['print'],
	"template_pages"         => $sys_vars['pages'],
	"template_logout"        => $sys_vars['logout'],
	"template_body_tag"      => $sys_vars['body_tag'],
	// additional default placeholders:
	"current_username"       => $sys_vars['username'],
	"current_usertext" 		 => $sys_vars['usertext'],
	"current_logout_url"   	 => $sys_vars['logout_url'],
	"current_admin_url"    	 => $sys_vars['admin_url'],
	// these placeholders are for individual usage (such as plugins):
	"addon_1"       		 => "",
	"addon_2"       		 => "",
	"addon_3"       		 => ""
	));

$tpl->parse ("template_handle", array("template"));
$tpl->p ("template_handle");

// who is online
if($sys_setting_vars['online_status'] == 1) {
	// Clear online status for users who didnt log out
	$sys_vars['old_time'] = time() - 3600; // 3600 = 1h
	$db->query("UPDATE ".$tbl_prefix."sys_user SET online_status = '0' WHERE online_status = '1' && curr_login < '".$sys_vars['old_time']."' && uid <> '".$sys_vars['uid']."' ORDER BY online_status");

	// Reactivate own online status if it was deactivated
	$db->query("UPDATE ".$tbl_prefix."sys_user SET online_status = '1' WHERE online_status = '0' && uid = '".$sys_vars['uid']."' ORDER BY uid");
}
?>