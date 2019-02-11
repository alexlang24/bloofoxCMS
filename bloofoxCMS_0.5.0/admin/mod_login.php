<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/mod_login.php -
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

// Check valid login
$login_error = "";

if($_POST['action'] == "login") {
	if($perm->login($db,validate_text($_POST['username']),validate_text($_POST['password'])) == 1) {
		load_url("index.php");
	} else {
		$login_error = $ac->show_error_message(get_caption('9250','Login failed! Please check your username and password.'));
	}
}

// Set template block
$tpl->set_block("tmpl_content", "login", "login_handle");

// Set variables
$tpl->set_var(array(
	"login_title"     => "bloofoxCMS Admincenter",
	"login_action"    => "index.php",
	"login_username"  => get_caption('0440','E-Mail / Username'),
	"login_password"  => get_caption('0450','Password'),
	"login_button"    => get_caption('0460','Login'),
	"login_error"     => $login_error,
	"confirm_message" => $ac->show_info_message(GetConfirmMessage())
	));

// Parse template with variables
$tpl->parse("login_handle", "login", true);
?>