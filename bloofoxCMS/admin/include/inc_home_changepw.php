<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_home_changepw.php -
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

if(isset($_POST['send']) && $sys_group_vars['demo'] == 0) {
	// format input fields
	$_POST['old_pw'] = validate_text($_POST['old_pw']);
	$_POST['new_pw'] = validate_text($_POST['new_pw']);
	$_POST['new_pw_confirm'] = validate_text($_POST['new_pw_confirm']);
	$old_password = md5($_POST['old_pw']);
	
	// check valid old pwd
	if($old_password != $perm->get_current_password($db)) {
		$error = $ac->show_error_message(get_caption('9420','Your current password is wrong.'));
	}
	
	// check new pwd
	if(!mandatory_field($_POST['new_pw']) && $error == "") { $error = $ac->show_error_message(get_caption('9060','Password must be filled out.')); }
	if(!check_string_rules($_POST['new_pw'],6) && $error == "") { $error = $ac->show_error_message(get_caption('9070','The password must consist of at least 6 signs.')); }
	if(!check_string_rules($_POST['new_pw'],6,$sys_setting_vars['pw_rule']) && $error == "") { 
		if($sys_setting_vars['pw_rule'] == 1) {
			$error = $ac->show_error_message(get_caption('9080','The password must consist of numbers and letters.'));
		} else {
			$error = $ac->show_error_message(get_caption('9090','The password must consist of numbers, letters and special signs (e.g. @!?|-_:+*.).'));
		}
	}
	if($_POST['new_pw'] != $_POST['new_pw_confirm'] && $error == "") { $error = $ac->show_error_message(get_caption('9100','Your password confirmation is different to your password.')); }
	
	if($error == "") {
		// save changes in database
		$db->query("UPDATE ".$tbl_prefix."sys_user SET password = '".md5($_POST['new_pw'])."' WHERE uid = '".$_SESSION["uid"]."' ORDER BY uid LIMIT 1");
		
		//load_url("index.php?page=changepw");
		$error = $ac->show_ok_message(get_caption('0390','Changes have been saved.'));
		unset($_POST);
	}
}
		
// Set variables
$tpl->set_var(array(
	"home_title"            => "<h2>".get_caption('4620','My Account')." / ".get_caption('1140','Change Password')."</h2>",
	"tab_general"           => get_caption('0170','General'),
	"home_error"            => $error,
	"home_action"           => "index.php?page=changepw",
	"home_old_pw"           => get_caption('1150','Password (current)'),
	"home_old_pw_input"     => "<input type='password' name='old_pw' size='25' maxlength='250' value='".$_POST['old_pw']."' />",
	"home_new_pw"           => get_caption('1160','New Password'),
	"home_new_pw_input"     => "<input type='password' name='new_pw' size='25' maxlength='250' value='".$_POST['new_pw']."' />",
	"home_pw_confirm"       => get_caption('1170','New Password (Confirmation)'),
	"home_pw_confirm_input" => "<input type='password' name='new_pw_confirm' size='25' maxlength='250' value='".$_POST['new_pw_confirm']."' />",
	"home_button_send"      => $ac->create_form_button("submit",get_caption('0120','Save'))
	));
?>