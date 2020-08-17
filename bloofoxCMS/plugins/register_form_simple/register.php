<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/register_form_simple/register.php -
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

// check plugin ID
// register must have "1002"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1002) {

	// translations
	include("plugins/register_form_simple/languages/".$sys_lang_vars['language']);
	
	// set template block
	$tpl->set_block("template_content", "content", "content_handle");
	
	// init db connection
	$db2 = new DB_Tpl();
	
	// No print view
	$sys_print_vars['print'] = "";
	
	switch($reg_vars['get'])
	{
		case '2': // register finished
			$sys_plugin_vars['title'] = "- ".get_caption('R020','Registration Successful');
			
			$tpl->set_var(array(
				"reg_title"      => "<h1>".get_caption('R020','Registration Successful')."</h1>",
				"reg_action"     => "",
				"reg_error"      => $error,
				"reg_info"       => "<p>".get_caption('R060','Thank you for signing up an account. We have sent an <b>email</b> with your <b>activation link</b> to your email address, please check your mail account! You have to activate your account before you can use it.')."</p>"
				));
			$tpl->parse("content_handle", "content", true);
		break;
	
		case '3': // registration confirmed, account unblocked/activated
			$sys_plugin_vars['title'] = "- ".get_caption('R030','Registration Confirmation');
			$confirm = "<p>".get_caption('R070','Your account was successfully activated.')."</p>";
			
			$db2->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE `key` = '".$_GET['key']."' && status = '0' && blocked = '0'");
			$num_rows = $db2->num_rows();
			if($sys_setting_vars['manual_register'] == 0) {
				$db2->query("UPDATE ".$tbl_prefix."sys_user SET `status` = '1' WHERE `key` = '".$_GET['key']."' && status = '0' && blocked = '0'");
			}
			if($num_rows == 0) {
				$error = "<p class='error'>".get_caption('R100','Activation failed. Valid account for activation was not found. Please contact the webmaster!')."</p>";
				$confirm = "";
			}
			
			$tpl->set_var(array(
				"reg_title"      => "<h1>".get_caption('R030','Registration Confirmation')."</h1>",
				"reg_action"     => "",
				"reg_error"      => $error,
				"reg_info"       => $confirm
				));
			$tpl->parse("content_handle", "content", true);
		break;
		
		default: // register new account
			$sys_plugin_vars['title'] = "- ".get_caption('R010','Register New Account');
			
			$error = "";
			if(isset($_POST['send'])) {
				// Block external postings
				$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
				if(strpos($HTTP_REFERER,$_SERVER['SERVER_NAME']) == 0) {
					load_url("index.php");
				}
				// Validate input fields
				$_POST['un'] = validate_text($_POST['un']);
				$_POST['un'] = strtolower($_POST['un']);
				$_POST['pwd'] = validate_text($_POST['pwd']);
				$_POST['pwd2'] = validate_text($_POST['pwd2']);
				$_POST['em'] = validate_text($_POST['em']);
				$_POST['gender'] = validate_text($_POST['gender']);
				$_POST['security'] = validate_text($_POST['security']);
				
				if(mandatory_field($_POST['security']) && $error == "") { $error = "<br />"; }
				if(!mandatory_field($_POST['un']) && $error == "") { $error = "<p class='error'>".get_caption('9030','Username must be filled out.')."</p>"; }
				if(!check_string_rules($_POST['un'],3) && $error == "") { $error = "<p class='error'>".get_caption('9040','The username must consist of at least 3 signs.')."</p>"; }
				if(!available_username($db2,$_POST['un']) && $error == "") { $error = "<p class='error'>".get_caption('9050','The username already exists.')."</p>"; }
				if(!mandatory_field($_POST['pwd']) && $error == "") { $error = "<p class='error'>".get_caption('9060','Password must be filled out.')."</p>"; }
				if(!check_string_rules($_POST['pwd'],6) && $error == "") { $error = "<p class='error'>".get_caption('9070','The password must consist of at least 6 signs.')."</p>"; }
				if(!check_string_rules($_POST['pwd'],6,$sys_setting_vars['pw_rule']) && $error == "") { 
					if($sys_setting_vars['pw_rule'] == 1) {
						$error = "<p class='error'>".get_caption('9080','The password must consist of numbers and letters.')."</p>";
					} else {
						$error = "<p class='error'>".get_caption('9090','The password must consist of numbers, letters and special signs (e.g. @!?|-_:+*.).')."</p>";
					}
				}
				if($_POST['pwd'] != $_POST['pwd2'] && $error == "") { $error = "<p class='error'>".get_caption('9100','Your password confirmation is different to your password.')."</p>"; }
				if(!email_is_valid($_POST['em']) && $error == "") { $error = "<p class='error'>".get_caption('9130','You must enter a valid e-mail.')."</p>"; }
				
				// Captcha
				if($content->plugin_available(20) == 1) {
					$captcha = new captcha('temp');
					$error = $captcha->check_code_input($error,$_SESSION["register"]);
				}
				
				if($error == "") {
				// create account in db
					$_POST['pwd'] = md5($_POST['pwd']);
					$reg_vars['timestamp'] = time();
					
					$reg_vars['group'] = "";
					$db2->query("SELECT name FROM ".$tbl_prefix."sys_usergroup WHERE gid = '".$sys_config_vars["default_group"]."' ORDER BY gid");
					while($db2->next_record()):
						$reg_vars['group'] .= $db2->f("name").",";
					endwhile;
					$reg_vars['group'] = substr($reg_vars['group'],0,-1);
					
					// generate key for confirmation
					mt_srand((double)microtime()*1000000);
					$reg_vars['signs'] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
					$reg_vars['key'] = "";
					while(strlen($reg_vars['key']) < 10)
					{
						$reg_vars['key'] .= substr($reg_vars['signs'],(rand()%(strlen($reg_vars['signs']))),1);
					}
					
					$db2->query("INSERT INTO ".$tbl_prefix."sys_user (`uid`,`username`,`password`,`groups`,`online_status`,`blocked`,`deleted`,`status`,`user_since`,`last_login`,`curr_login`,`key`) VALUES ('','".$_POST['un']."','".$_POST['pwd']."','".$reg_vars['group']."','0','1','0','0','".$reg_vars['timestamp']."','0','0','".$reg_vars['key']."')");
					$db2->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE username = '".$_POST['un']."' LIMIT 1");
					while($db2->next_record()):
						$user_id = $db2->f("uid");
					endwhile;
					$db2->query("INSERT INTO ".$tbl_prefix."sys_profile VALUES ('','".$user_id."','','','','','','','".$_POST['em']."','','".$_POST['gender']."','','0','0','0')");
					
					// send email to new user
					$regmsg = get_caption('R090','Hello [USERNAME],\n\r activation link: [HYPERLINK]\n\r Kind regards\n\r [AUTHOR], [URL]');
					$regmsg = str_replace("[USERNAME]",$_POST['un'],$regmsg);
					$regmsg = str_replace("[HYPERLINK]",$sys_config_vars['url']."/index.php?page=".$sys_explorer_vars['eid']."&register=3&key=".$reg_vars['key'],$regmsg);
					$regmsg = str_replace("[AUTHOR]",$sys_config_vars['meta_author'],$regmsg);
					$regmsg = str_replace("[URL]",$sys_config_vars['url'],$regmsg);
					
					send_mail($_POST['em'],$sys_config_vars['mail'],get_caption('R080','Registration / Account Activation'),$regmsg,$sys_setting_vars['html_mails']);
					// send email to admin
					if($sys_setting_vars['register_notify'] == "1") {
						$regmsg = get_caption('R110','New user for [URL]:\n\r Username: [USERNAME]\n\rE-Mail: [EMAIL]');
						$regmsg = str_replace("[USERNAME]",$_POST['un'],$regmsg);
						$regmsg = str_replace("[EMAIL]",$_POST['em'],$regmsg);
						$regmsg = str_replace("[GENDER]",$_POST['gender'],$regmsg);
						$regmsg = str_replace("[URL]",$sys_config_vars['url'],$regmsg);
						if(!empty($sys_setting_vars['admin_mail'])) {
							$sys_config_vars['mail'] = $sys_setting_vars['admin_mail'];
						}
						send_mail($sys_config_vars['mail'],$sys_config_vars['mail'],"NOTIFY: ".get_caption('R080','Registration / Account Activation'),$regmsg,$sys_setting_vars['html_mails']);
					}
					
					// header to step 2
					load_url("index.php?page=".$sys_explorer_vars['eid']."&register=2");
				}
			}
			
			// Captcha
			if($content->plugin_available(20) == 1) {
				$captcha = new captcha('temp');
				$captcha_label = $captcha->get_input_label();
				$captcha_image = $captcha->get_captcha_image('register');
				$captcha_input = $captcha->get_input_field();
			}
			
			$tpl->set_var(array(
				"reg_title"          => "<h1>".get_caption('R010','Register New Account')."</h1>",
				"reg_action"         => $_SERVER['REQUEST_URI'],
				"reg_error"          => $error,
				"reg_info"           => "<p>".get_caption('R050','Please fill out the form, all fields are required!')."</p>",
				"reg_user"           => get_caption('4060','Username'),
				"reg_user_input"     => "<input type='text' name='un' size='30' maxlength='80' value='".$_POST['un']."' />",
				"reg_pass"           => get_caption('4070','Password'),
				"reg_pass_input"     => "<input type='password' name='pwd' size='20' maxlength='80' />",
				"reg_pass2"          => get_caption('4080','Password Confirmation'),
				"reg_pass2_input"    => "<input type='password' name='pwd2' size='20' maxlength='80' />",
				"reg_email"          => get_caption('4200','E-Mail'),
				"reg_email_input"    => "<input type='text' name='em' size='30' maxlength='250' value='".$_POST['em']."' />",
				"reg_gender"         => get_caption('4220','Gender'),
				"reg_gender_input"   => "<input type='radio' name='gender' value='1' checked='checked' />".get_caption('4270','Female')." <input type='radio' name='gender' value='0' />".get_caption('4260','Male'),
				"reg_security"       => get_caption(''),
				"reg_security_input" => "<input type='hidden' name='security' size='30' maxlength='30' value='".$_POST['security']."' />",
				"reg_captcha"        => $captcha_label,
				"reg_captcha_image"  => $captcha_image,
				"reg_captcha_input"  => $captcha_input,
				"reg_submit"         => "<input type='submit' name='send' value='".get_caption('R040','Create New Account')."' />"
				));
			$tpl->parse("content_handle", "content", true);
		break;
	}
}

?>