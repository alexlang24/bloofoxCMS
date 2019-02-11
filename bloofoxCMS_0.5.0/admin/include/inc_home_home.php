<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_home_home.php -
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

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

// Get latest login for current user
$last_login = $perm->get_last_login($db);
$failed_logins = $perm->count_failed_session($db,$last_login);

// Create Who is online box
$next = 0;
$db->query("SELECT uid,username,curr_login FROM ".$tbl_prefix."sys_user WHERE online_status = '1' ORDER BY online_status LIMIT 10");
while($db->next_record()):
	if($next == 1) {
		$home_online_users .= " | ";
	}
	$home_online_users .= $ac->create_link("index.php?page=profiles&user_id=",$db->f("uid"),$db->f("username"),"",$db->f("username"))
		." (".date($sys_vars['datetime'],$db->f("curr_login")).")";
	$next = 1;
endwhile;

// Create myAccount section
$sys_profile = $perm->get_user_profile($db,$_SESSION["uid"]);

// Do version check
if($sys_setting_vars['update_check'] == "1") {
	$version = $ac->get_update_message();
	$home['upd_warning'] = $version['check'];
	$home['upd_version'] = $version['current'];
}

$database_version = get_database_version($db);
if(get_current_version() != $database_version) {
	$home['upd_warning'] = get_caption("9180","You have to update your bloofoxCMS database.");
}

// Create IP Log
$home['iplog'] = "<p>".get_caption("9210","Logged IP address")." ".$_SERVER['REMOTE_ADDR']."</p>";
if($sys_setting_vars['login_protection'] == "1") {
	$home['iplog'] .= $ac->show_info_message(get_caption("9200","Your IP address was logged for security reasons."));
}

// Create password warning
if($_SESSION["username"] == "admin" && $perm->get_current_password($db) == md5("admin")) {
	$home['pwd_warning'] = get_caption("9190","Warning: It is strongly recommended to change the admin's password.");
}

// Set variables
if(empty($last_login)): $last_login = 0; endif;
$tpl->set_var(array(
	"home_title"              => "<h2>".get_caption("1000","Welcome to the Admincenter")."</h2>",
	"home_system_title"       => "<p class='bold'>".get_caption('1030','System Information')."</p>",
	"home_database_label"     => get_caption('1100','Database'),
	"home_database_info"      => $sys_vars['database'],
	"home_server_label"       => get_caption('1110','Server'),
	"home_server_info"        => $_SERVER['SERVER_NAME'],
	"home_bfcmsfile_label"    => get_caption('1130','File Version'),
	"home_bfcmsfile_info"     => get_current_version(),
	"home_bfcmsdb_label"      => get_caption('1120','Database Version'),
	"home_bfcmsdb_info"       => $database_version,
	"home_mysql_label"        => "MySQL",
	"home_mysql_info"         => mysql_get_client_info(),
	"home_php_label"          => "PHP",
	"home_php_info"           => phpversion(),
	"home_account_title"      => "<p class='bold'>".get_caption('4620','My Account')."</p>",
	"home_loggedin_label"     => "<p>".get_caption('1050','You are logged in. Username:')." ".$_SESSION['username']."</p>",
	"home_loggedin_info"      => "<p><a href='index.php?mode=logout'>".get_caption('4650','Logout')."</a></p>",
	"home_lastlogin_label"    => "<p>".get_caption('1060','Last login:')."</p>",
	"home_lastlogin_info"     => "<p>".date($sys_vars['datetime'],$last_login)."</p>",
	"home_failedlogins_label" => "<p>".get_caption('1070','Failed logins since last login:')."</p>",
	"home_failedlogins_info"  => "<p>".$failed_logins."</p>",
	"home_online_title"       => "<p class='bold'>".get_caption('1080','Who is Online')."</p>",
	"home_online_users"       => $home_online_users,
	"home_iplog_title"        => "<p class='bold'>".get_caption('1090','IP Log')."</p>",
	"home_iplog"              => $home['iplog'],
	"home_pwd_warning"        => $ac->show_info_message($home['pwd_warning']),
	"home_upd_warning"        => $ac->show_info_message($home['upd_warning']),
	"home_upd_version"        => $home['upd_version'],
	"legend_title"            => "<p class='bold'>".get_caption('1040','Legend')."</p>",
	"legend_new"              => $ac->create_legend_icon(1)." ".get_caption('0431','Add'),
	"legend_edit"             => $ac->create_legend_icon(2)." ".get_caption('0201','Edit'),
	"legend_delete"           => $ac->create_legend_icon(3)." ".get_caption('0211','Delete'),
	"legend_content"          => $ac->create_legend_icon(4)." ".get_caption('0221','Edit Contents'),
	"legend_preview"          => $ac->create_legend_icon(5)." ".get_caption('0421','Preview'),
	"legend_profile"          => $ac->create_legend_icon(6)." ".get_caption('4281','User Profile'),
	"legend_quit"             => $ac->create_legend_icon(7)." ".get_caption('0351','Quit')."/".get_caption('4650','Logout')
	));

?>