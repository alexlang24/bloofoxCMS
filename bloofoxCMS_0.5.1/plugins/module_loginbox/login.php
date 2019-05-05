<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/module_loginbox/login.php -
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

// translations
include("plugins/module_loginbox/languages/".$sys_lang_vars['language']);

// set template block
$tpl->set_block("plugin_module_loginbox", "login", "login_handle");

if($sys_vars['login_status'] == 0) {
	// get page with register form
	$db2->query("SELECT eid,name FROM ".$tbl_prefix."sys_explorer WHERE link_type = '3' && link_plugin = '1002' && config_id = '".$sys_explorer_vars['config_id']."' LIMIT 1");
	while($db2->next_record()):
		$sys_vars['login_register'] = "<p class='small'><a href='".create_url($db2->f("eid"),$db2->f("name"),$sys_config_vars['mod_rewrite'])."' title='".$db2->f("name")."'>".$db2->f("name")."</a></p>";
	endwhile;
	
	// create login form
	$login_vars['form'] = ""
		."<form action=\"index.php?login=true\" method=\"post\">"
		.get_caption('A010','E-Mail / Username')."<br />"
		."<input type='text' name='username' size='20' /><br />"
		.get_caption('A020','Password')."<br />"
		."<input type='password' name='password' size='20' /><br />"
		."<input type='submit' value='".get_caption('A030','Login')."' name='login' /><br />"
		.$sys_vars['login_register']
		."</form>";
	
	// assign variables to template
	$tpl->set_var(array(
		"login_title"      => get_caption('A030','Login'),
		"login_form"       => $login_vars['form']
	));
}

if($sys_vars['login_status'] == 1) {
	// url to admincenter
	if(!empty($sys_vars['admin_url'])) {
		$sys_vars['account_menu'] = "<li>".$sys_vars['admin_url']."</li>";
	}
	// url to change password form
	$sys_vars['login_changepw'] = "<li><a href='index.php?account=changepwd' title='".get_caption('A220','Change Password')."'>".get_caption('A220','Change Password')."</a></li>";
	
	// create account menu
	$login_form = "<ul class='login_menu'>"
		.$sys_vars['account_menu']
		."<li><a href='index.php?account=overview' title='".get_caption('A040','My Account')."'>".get_caption('A040','My Account')."</a></li>"
		.$sys_vars['login_changepw']
		."<li>".$sys_vars['logout_url']."</li>"
		."</ul>";
		
	// assign variables to template
	$tpl->set_var(array(
		"login_title"      => get_caption('A050','My Account'),
		"login_form"       => $login_form
	));
}

// parse template
$tpl->parse("login_handle", "login", true);
?>