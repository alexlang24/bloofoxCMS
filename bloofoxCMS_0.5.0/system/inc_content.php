<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/inc_content.php -
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

// Set current time
$sys_time = time();

// this code is included in index.php
switch($sys_vars['login_required'])
{
	case '2': // My Account module
		$tpl->set_block("template_content", "content", "content_handle");
		
		switch($account)
		{
			case 'changepwd':
				// account: change password
				require_once(SYS_WORK_DIR.SYS_FOLDER."/class_admincenter.php");
				$ac = new admincenter(1);
	
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

				$tpl->set_var(array(
					"pwd_title"            => "<h1>".get_caption('A050','My Account')." / ".get_caption('1140','Change Password')."</h1>",
					"pwd_action"           => $_SERVER['REQUEST_URI'],
					"pwd_error"            => $error,
					"pwd_old_pw"           => get_caption('1150','Password (current)'),
					"pwd_old_pw_input"     => "<input type='password' name='old_pw' size='25' maxlength='250' value='".$_POST['old_pw']."' />",
					"pwd_new_pw"           => get_caption('1160','New Password'),
					"pwd_new_pw_input"     => "<input type='password' name='new_pw' size='25' maxlength='250' value='".$_POST['new_pw']."' />",
					"pwd_pw_confirm"       => get_caption('1170','New Password (confirmation)'),
					"pwd_pw_confirm_input" => "<input type='password' name='new_pw_confirm' size='25' maxlength='250' value='".$_POST['new_pw_confirm']."' />",
					"pwd_button_send"      => $ac->create_form_button("submit",get_caption('0120','Save'))
					));
			break;
			
			default:
				// account: overview
				$sys_profile = $perm->get_user_profile($db,$_SESSION["uid"]);
				$gender = mark_selected_value($sys_profile['gender']);
				if(empty($gender[1])) { $selected_gender = get_caption('4270','Female'); }
				if(empty($gender[2])) { $selected_gender = get_caption('4260','Male'); }
				
				// Get latest login for current user
				$last_login = $perm->get_last_login($db);
				$failed_logins = $perm->count_failed_session($db,$last_login);

				$tpl->set_var(array(
					"account_title"               => "<h1>".get_caption('A050','My Account')."</h1>",
					"account_salutation"          => "<h2>".get_caption('1020','Hello')." ".$sys_vars['username']."</h2>",
					"account_last_login_label"    => get_caption('1060','Last login:'),
					"account_last_login"          => date($sys_lang_vars['datetime'],$last_login),
					"account_failed_logins_label" => get_caption('1070','Failed logins since last login:'),
					"account_failed_logins"       => $failed_logins,
					"account_profile"             => get_caption('4281','User Profile'),
					"account_username_title"      => get_caption('4060','Username'),
					"account_username"            => $sys_vars['username'],
					"account_firstname_title"     => get_caption('4140','First Name'),
					"account_firstname"           => $sys_profile['firstname'],
					"account_lastname_title"      => get_caption('4150','Last Name'),
					"account_lastname"            => $sys_profile['lastname'],
					"account_email_title"         => get_caption('4200','E-Mail'),
					"account_email"               => $sys_profile['email'],
					"account_birthday_title"      => get_caption('4210','Date of Birth'),
					"account_birthday"            => $sys_profile['birthday'],
					"account_gender_title"        => get_caption('4220','Gender'),
					"account_gender"              => $selected_gender
					));
			break;
		}
		
		$tpl->parse("content_handle", "content", true);
	break;
	
	case '1': // Login required
		$db->query("SELECT eid,name FROM ".$tbl_prefix."sys_explorer WHERE link_type = '3' && link_plugin = '1002' && config_id = '".$sys_explorer_vars['config_id']."' LIMIT 1");
		while($db->next_record()):
			$sys_vars['login_register'] = "<p class='small'><a href='".create_url($db->f("eid"),$db->f("name"),$sys_config_vars['mod_rewrite'])."'>".$db->f("name")."</a></p>";
		endwhile;
		
		$sys_vars['body_tag'] = "onLoad=\"focus_user()\"";
	
		// set template block
		$tpl->set_block("template_content", "content", "content_handle");

		$tpl->set_var(array(
			"login_title"    => $content->title,
			"login_action"   => $content->action,
			"login_user"     => $content->user,
			"login_pass"     => $content->pass,
			"login_error"    => $content->error,
			"login_register" => $sys_vars['login_register']
		));

		// parse template
		$tpl->parse("content_handle", "content", true);
	break;
	
	default: // Select articles for page
		if(!isset($_GET['cid'])) {
			// create page handling
			$sys_vars = $content->create_pages($db,$_GET['start'],$sys_vars);

			// get all sys_contents
			$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$content->eid."' AND blocked = '0' AND startdate < '".$sys_time."' AND (enddate > '".$sys_time."' || enddate = '') ORDER BY sorting LIMIT ".$sys_vars['start'].",".$content->limit."");
		} else {
			// get single sys_content
			$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE cid = '".$_GET['cid']."' AND blocked = '0' AND startdate < '".$sys_time."' AND (enddate > '".$sys_time."' || enddate = '') LIMIT 1");
		}
		$no_of_records = $db->num_rows();
		
		// set template block
		$tpl->set_block("template_content", "content", "content_handle");
		$tpl->set_var(array(
				"page_title"    => $sys_explorer_vars['title']
			));
		
		while($db->next_record()):
			$article_text = $content->format_text($db->f("text"));
			$article_title = $db->f("title");
			
			$tpl->set_var(array(
				"article_title"    => $article_title,
				"article_text"     => $article_text
			));
		
			// parse template
			$tpl->parse("content_handle", "content", true);
		endwhile;
		
		// if no contents were found show this content
		if($no_of_records == 0) {
			$tpl->set_var(array(
				"article_title"    => get_caption("C010","Content Notice"),
				"article_text"     => get_caption("C020","Articles were not found for this page. You may create an article for this page.")
			));
				
			// parse template
			$tpl->parse("content_handle", "content", true);
		}
	break;
}
?>