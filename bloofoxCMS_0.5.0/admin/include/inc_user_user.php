<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_user_sessions.php -
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

// init filter class
$filters = new filter();
// include pages class
require_once(SYS_FOLDER."/class_pages.php");
		
switch($action)
{
	case 'new':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user']['write'] == 1) {
			$_POST['username'] = validate_text($_POST['username']);
			$_POST['username'] = strtolower($_POST['username']);
			$_POST['password'] = validate_text($_POST['password']);
			$_POST['pwdconfirm'] = validate_text($_POST['pwdconfirm']);
			
			if(!mandatory_field($_POST['username']) && $error == "") { $error = $ac->show_error_message(get_caption('9030',"Username must be filled out.")); }
			if(!check_string_rules($_POST['username'],3) && $error == "") { $error = $ac->show_error_message(get_caption('9040',"The username must consist of at least 3 signs.")); }
			if(!available_username($db,$_POST['username']) && $error == "") { $error = $ac->show_error_message(get_caption('9050',"The username already exists.")); }
			if(!mandatory_field($_POST['password']) && $error == "") { $error = $ac->show_error_message(get_caption('9060',"Password must be filled out.")); }
			if(!check_string_rules($_POST['password'],6) && $error == "") { $error = $ac->show_error_message(get_caption('9070',"The password must consist of at least 6 signs.")); }
			if(!check_string_rules($_POST['password'],6,$sys_setting_vars['pw_rule']) && $error == "") { 
				if($sys_setting_vars['pw_rule'] == 1) {
					$error = $ac->show_error_message(get_caption('9080',"The password must consist of numbers and letters."));
				} else { // 2
					$error = $ac->show_error_message(get_caption('9090',"The password must consist of numbers, letters and special signs (e.g. @!?|-_:+*.)."));
				}
			}
			if($_POST['password'] != $_POST['pwdconfirm'] && $error == "") { $error = $ac->show_error_message(get_caption('9100',"Your password confirmation is different to your password.")); }
			
			$groups = $ac->get_group_names_for_insert();
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				// generate key
				mt_srand((double)microtime()*1000000);
				$reg_vars['signs'] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
				$reg_vars['key'] = "";
				while(strlen($reg_vars['key']) < 10)
				{
					$reg_vars['key'] .= substr($reg_vars['signs'],(rand()%(strlen($reg_vars['signs']))),1);
				}
				// insert record
				$_POST['password'] = md5($_POST['password']);
				$timestamp = time();
				$db->query("INSERT INTO ".$tbl_prefix."sys_user VALUES ('','".$_POST['username']."','".$_POST['password']."','".$groups."','0','".$_POST['blocked']."','".$_POST['deleted']."','".$_POST['status']."','".$timestamp."','0','0','".$reg_vars['key']."','".$_POST['login_page']."')");
				$db->query("SELECT uid FROM ".$tbl_prefix."sys_user ORDER BY uid DESC LIMIT 1");
				while($db->next_record()):
					$user_id = $db->f("uid");
				endwhile;
				$db->query("INSERT INTO ".$tbl_prefix."sys_profile VALUES ('','".$user_id."','','','','','','','','','0','','0','0','0')");
				load_url("index.php?mode=user");
			}
		}
		
		$sys_usergroups = $ac->get_sys_usergroups("","new");
		
		$blocked = mark_selected_value($_POST['blocked']);
		$deleted = mark_selected_value($_POST['deleted']);
		$status = mark_selected_value($_POST['status']);
		
		// Selection for Login Page
		$expl = new pages();
		$content_explorer = $expl->get_structure($content_explorer);
		
		// Set variables
		$tpl->set_var(array(
			"user_title"            => "<h2>".get_caption('4000','Security')." / ".get_caption('4050','Add User')."</h2>",
			"tab_general"           => get_caption('0170','General'),
			"user_action"           => "index.php?mode=user&action=new",
			"user_error"            => $error,
			"user_username"         => get_caption('4060','Username'),
			"user_username_input"   => "<input type='text' name='username' size='25' maxlength='250' value='".$_POST['username']."' />",
			"user_password"         => get_caption('4070','Password'),
			"user_password_input"   => "<input type='password' name='password' size='25' maxlength='250' value='".$_POST['password']."' />",
			"user_pwdconfirm"       => get_caption('4080','Password Confirmation'),
			"user_pwdconfirm_input" => "<input type='password' name='pwdconfirm' size='25' maxlength='250' value='".$_POST['pwdconfirm']."' />",
			"user_groups"           => get_caption('4020','User Groups'),
			"user_groups_input"     => $sys_usergroups,
			"user_blocked"          => get_caption('0230','Blocked'),
			"user_blocked_input"    => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_deleted"          => get_caption('0240','Deleted'),
			"user_deleted_input"    => "<select name='deleted'><option value='0' ".$deleted['1'].">".get_caption('0140','No')."</option><option value='1' ".$deleted['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_status"           => get_caption('0250','Status'),
			"user_status_input"     => "<select name='status'><option value='0' ".$status['1'].">".get_caption('0270','Inactive')."</option><option value='1' ".$status['2'].">".get_caption('0260','Active')."</option></select>",
			"user_page"             => get_caption('4100','User Login Page'),
			"user_page_input"       => "<select name='login_page'><option value='0'>- ".get_caption('0280','Default')." -</option>".$content_explorer."</select>",
			"user_button_send"      => $ac->create_form_button("submit",get_caption('4050','Add User'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user']['write'] == 1) {
			$_POST['username'] = validate_text($_POST['username']);
			$_POST['password'] = validate_text($_POST['password']);
			$_POST['pwdconfirm'] = validate_text($_POST['pwdconfirm']);
			
			if(!mandatory_field($_POST['username']) && $error == "") { $error = $ac->show_error_message(get_caption('9030',"Username must be filled out.")); }
			if(!check_string_rules($_POST['username'],3) && $error == "") { $error = $ac->show_error_message(get_caption('9040',"The username must consist of at least 3 signs.")); }
			if(!available_username($db,$_POST['username'],$_POST['userid']) && $error == "") { $error = $ac->show_error_message(get_caption('9050',"The username already exists.")); }
			
			if(mandatory_field($_POST['username']) && available_username($db,$_POST['username'],$_POST['userid'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_user SET username = '".$_POST['username']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['password'])) {
				if(!check_string_rules($_POST['password'],6) && $error == "") { $error = $ac->show_error_message(get_caption('9070',"Password must consist of at least 6 signs.")); }
				if(!check_string_rules($_POST['password'],6,$sys_setting_vars['pw_rule']) && $error == "") { 
					if($sys_setting_vars['pw_rule'] == 1) {
						$error = $ac->show_error_message(get_caption('9080',"The password must consist of numbers and letters."));
					} else { // 2
						$error = $ac->show_error_message(get_caption('9090',"The password must consist of numbers, letters and special signs (e.g. @!?|-_:+*.)."));
					}
				}

				if($_POST['password'] != $_POST['pwdconfirm'] && $error == "") { $error = $ac->show_error_message(get_caption('9100',"Your password confirmation is different to your password.")); }
				if($error == "") {
					$_POST['password'] = md5($_POST['password']);
					$db->query("UPDATE ".$tbl_prefix."sys_user SET password = '".$_POST['password']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
				}
			}
			
			$groups = $ac->get_group_names_for_insert();
			$db->query("UPDATE ".$tbl_prefix."sys_user SET groups = '".$groups."' WHERE uid = '".$_POST['userid']."' LIMIT 1");

			$db->query("UPDATE ".$tbl_prefix."sys_user SET blocked = '".$_POST['blocked']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_user SET deleted = '".$_POST['deleted']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_user SET status = '".$_POST['status']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_user SET login_page = '".$_POST['login_page']."' WHERE uid = '".$_POST['userid']."' LIMIT 1");
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption('0390','Changes have been saved.'));
				load_url("index.php?mode=user");
			}
		}
		
		// select record
		if(isset($_POST['userid'])) {
			$_GET['userid'] = $_POST['userid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_user WHERE uid = '".$_GET['userid']."' ORDER BY uid LIMIT 1");
		while($db->next_record()):
			$userid = $db->f("uid");
			$user = $db->f("username");
			$groups = $db->f("groups");
			$groups = explode(",",$groups);
			$blocked = mark_selected_value($db->f("blocked"));
			$deleted = mark_selected_value($db->f("deleted"));
			$status = mark_selected_value($db->f("status"));
			$login_page = $db->f("login_page");
		endwhile;
		
		$sys_usergroups = $ac->get_sys_usergroups($groups,"edit");
		
		// Selection for Login Page
		$expl = new pages();
		$content_explorer = $expl->get_structure($content_explorer,0,$login_page);
		
		// Set variables
		$tpl->set_var(array(
			"user_title"           => "<h2>".get_caption('4000','Security')." / ".get_caption('4110','Edit User')."</h2>",
			"tab_general"          => get_caption('0170','General'),
			"user_action"          => "index.php?mode=user&action=edit",
			"user_error"           => $error,
			"user_username"        => get_caption('4060','Username'),
			"user_username_input"  => "<input type='text' name='username' size='25' maxlength='250' value='".$user."' />",
			"user_password"        => get_caption('4070','Password'),
			"user_password_input"  => "<input type='password' name='password' size='25' maxlength='250' value='' />",
			"user_pwdconfirm"       => get_caption('4080','Password Confirmation'),
			"user_pwdconfirm_input" => "<input type='password' name='pwdconfirm' size='25' maxlength='250' value='' />",
			"user_groups"          => get_caption('4020','User Groups'),
			"user_groups_input"    => $sys_usergroups,
			"user_blocked"         => get_caption('0230','Blocked'),
			"user_blocked_input"   => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_deleted"         => get_caption('0240','Deleted'),
			"user_deleted_input"   => "<select name='deleted'><option value='0' ".$deleted['1'].">".get_caption('0140','No')."</option><option value='1' ".$deleted['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_status"          => get_caption('0250','Status'),
			"user_status_input"    => "<select name='status'><option value='0' ".$status['1'].">".get_caption('0270','Inactive')."</option><option value='1' ".$status['2'].">".get_caption('0260','Active')."</option></select>",
			"user_page"           => get_caption('4100','User Login Page'),
			"user_page_input"     => "<select name='login_page'><option value='0'>- ".get_caption('0280','Default')." -</option>".$content_explorer."</select>",
			"user_uid"             => "<input type='hidden' name='userid' value='".$userid."' />",
			"user_button_send"     => $ac->create_form_button("submit",get_caption('0120','Save')),
			"user_button_reset"    => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user']['delete'] == 1) {
			if($_POST['userid'] != $_SESSION["uid"]) {
				$db->query("DELETE FROM ".$tbl_prefix."sys_user WHERE uid = '".$_POST['userid']."' LIMIT 1");
				$db->query("DELETE FROM ".$tbl_prefix."sys_profile WHERE user_id = '".$_POST['userid']."' ORDER BY user_id LIMIT 1");
				CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			}
			load_url("index.php?mode=user");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=user");
		
		// select record
		if(isset($_POST['userid'])) {
			$_GET['userid'] = $_POST['userid'];
		}
		$db->query("SELECT uid,username FROM ".$tbl_prefix."sys_user WHERE uid = '".$_GET['userid']."' ORDER BY uid LIMIT 1");
		while($db->next_record()):
			$userid = $db->f("uid");
			$user = $db->f("username");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"user_title"        => "<h2>".get_caption('4000','Security')." / ".get_caption('4120','Delete User')."</h2>",
			"user_action"       => "index.php?mode=user&action=del",
			"user_question"     => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"user_username"     => "<p class='bold'>".$user."</p>",
			"user_uid"          => "<input type='hidden' name='userid' value='".$userid."' />",
			"user_button_send"  => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	case 'profile':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user']['delete'] == 1) {
			// format input fields
			$_POST['firstname'] = validate_text($_POST['firstname']);
			$_POST['lastname'] = validate_text($_POST['lastname']);
			$_POST['address1'] = validate_text($_POST['address1']);
			$_POST['address2'] = validate_text($_POST['address2']);
			$_POST['city'] = validate_text($_POST['city']);
			$_POST['zip_code'] = validate_text($_POST['zip_code']);
			$_POST['email'] = validate_text($_POST['email']);
			if(!empty($_POST['birthday'])) {
				$_POST['birthday'] = validate_date($_POST['birthday']);
			}
			$_POST['deletepic'] = validate_text($_POST['deletepic']);
			
			if(!mandatory_field($_POST['firstname']) && $error == "") { $error = $ac->show_error_message(get_caption('9110','You must enter a first name.')); }
			if(!mandatory_field($_POST['email']) && $error == "") { $error = $ac->show_error_message(get_caption('9120','You must enter an e-mail.')); }
			if(!email_is_valid($_POST['email']) && $error == "") { $error = $ac->show_error_message(get_caption('9130','You must enter a valid e-mail.')); }
			
			// delete existing picture
			if($_POST['deletepic']) {
				$db->query("UPDATE ".$tbl_prefix."sys_profile SET picture = '' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
				if($_POST['old_picture'] != "standard.gif") {
					delete_file($_POST['old_picture'],$path_to_profiles_folder);
				}
			}
			
			// upload picture
			$_FILES["filename"]["name"] = validate_text($_FILES["filename"]["name"]);
					
			if(mandatory_field($_FILES["filename"]["name"])) {
				$_FILES["filename"]["name"] = $_POST["username"]."_".$_FILES["filename"]["name"];
				$picture_error = "";
				if($_FILES["filename"]["size"] > 15360) {
					$picture_error = $ac->show_error_message(get_caption('9140','The file size may amount to maximally 15 KB.'));
				}
				if($_FILES["filename"]["type"] != "image/gif" && $_FILES["filename"]["type"] != "image/jpeg" && $_FILES["filename"]["type"] != "image/pjpeg") {
					$picture_error = $ac->show_error_message(get_caption('9150','You can upload only pictures with type GIF and JPEG.'));
				}
				if(!file_exists($path_to_profiles_folder.$_FILES["filename"]["name"])) {
					if($_POST['old_picture'] != "standard.gif") {
						delete_file($_POST['old_picture'],$path_to_profiles_folder);
					}
				}
				
				if($picture_error == "") {
					upload_file($path_to_profiles_folder);
					$db->query("UPDATE ".$tbl_prefix."sys_profile SET picture = '".$_FILES["filename"]["name"]."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
				}
			}
			
			// save changes in database
			if(mandatory_field($_POST['firstname'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_profile SET firstname = '".$_POST['firstname']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			}
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET lastname = '".$_POST['lastname']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET address1 = '".$_POST['address1']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET address2 = '".$_POST['address2']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET city = '".$_POST['city']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET zip_code = '".$_POST['zip_code']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			if(mandatory_field($_POST['email']) && email_is_valid($_POST['email'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_profile SET email = '".$_POST['email']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			}
			if(strlen($_POST['birthday']) == 0 || $_POST['birthday'] > 0) {
				$db->query("UPDATE ".$tbl_prefix."sys_profile SET birthday = '".$_POST['birthday']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			}
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET gender = '".$_POST['gender']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET be_lang = '".$_POST['be_lang']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET be_tmpl = '".$_POST['be_tmpl']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET show_email = '".$_POST['showemail']."' WHERE user_id = '".$_GET["userid"]."' ORDER BY user_id LIMIT 1");
			
			if($error == "" && $picture_error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=user");
			}
		}
		
		// select record
		if(isset($_POST['userid'])) {
			$_GET['userid'] = $_POST['userid'];
		}
		
		$sys_profile = $perm->get_user_profile($db,$_GET["userid"]);

		$gender = mark_selected_value($sys_profile['gender']);
		if(!empty($sys_profile['birthday'])) {
			$sys_profile['birthday'] = date("d.m.Y",$sys_profile['birthday']);
		}
		if($sys_profile['picture'] == "") {
			$sys_profile['picture'] = "standard.gif";
		}

		$sys_lang = "";
		$db->query("SELECT lid,name FROM ".$tbl_prefix."sys_lang ORDER BY lid");
		while($db->next_record()):
			if($sys_profile['be_lang'] == $db->f("lid")) {
				$sys_lang.= "<option value='".$db->f("lid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_lang.= "<option value='".$db->f("lid")."'>".$db->f("name")."</option>";
			}
		endwhile;

		$sys_tmpl = "";
		$db->query("SELECT tid,name FROM ".$tbl_prefix."sys_template WHERE be = '1' ORDER BY tid");
		while($db->next_record()):
			if($sys_profile['be_tmpl'] == $db->f("tid")) {
				$sys_tmpl.= "<option value='".$db->f("tid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_tmpl.= "<option value='".$db->f("tid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$db->query("SELECT username FROM ".$tbl_prefix."sys_user WHERE uid = '".$sys_profile['user_id']."' LIMIT 1");
		while($db->next_record()):
			$sys_profile['username'] = $db->f("username");
		endwhile;

		// Set variables
		$tpl->set_var(array(
			"home_title"           => "<h2>".get_caption('4000','Security')." / ".get_caption('4010','User')." / ".get_caption('4130','Edit User Profile')." / ".$sys_profile['username']."</h2>",
			"tab_general"          => get_caption('0170','General'),
			"tab_options"          => get_caption('0180','Options'),
			"home_action"          => "index.php?mode=user&action=profile&userid=".$sys_profile['user_id'],
			"home_error"           => $error.$picture_error,
			"home_firstname_title" => get_caption('4140','First Name'),
			"home_firstname"       => "<input type='text' name='firstname' value='".$sys_profile['firstname']."' size='30' maxlength='250' />",
			"home_lastname_title"  => get_caption('4150','Last Name'),
			"home_lastname"        => "<input type='text' name='lastname' value='".$sys_profile['lastname']."' size='30' maxlength='250' />",
			"home_address1_title"  => get_caption('4160','Address'),
			"home_address1"        => "<input type='text' name='address1' value='".$sys_profile['address1']."' size='40' maxlength='250' />",
			"home_address2_title"  => get_caption('4170','Address 2'),
			"home_address2"        => "<input type='text' name='address2' value='".$sys_profile['address2']."' size='40' maxlength='250' />",
			"home_city_title"      => get_caption('4180','City'),
			"home_city"            => "<input type='text' name='city' value='".$sys_profile['city']."' size='30' maxlength='250' />",
			"home_zip_code_title"  => get_caption('4190','Zip Code'),
			"home_zip_code"        => "<input type='text' name='zip_code' value='".$sys_profile['zip_code']."' size='10' maxlength='20' />",
			"home_email_title"     => get_caption('4200','E-Mail'),
			"home_email"           => "<input type='text' name='email' value='".$sys_profile['email']."' size='50' maxlength='250' />",
			"home_showemail_title" => get_caption('4201','Show E-Mail'),
			"home_showemail"       => "<select name='showemail'><option value='0' ".$showemail['1'].">".get_caption('0140','No')."</option><option value='1' ".$showemail['2'].">".get_caption('0130','Yes')."</option></select>",
			"home_birthday_title"  => get_caption('4210','Date of Birth'),
			"home_birthday"        => "<input type='text' name='birthday' value='".$sys_profile['birthday']."' size='10' maxlength='10' /> ".get_caption('0500','DD.MM.YYYY'),
			"home_gender_title"    => get_caption('4220','Gender'),
			"home_gender"          => "<select name='gender'><option value='0' ".$gender['1'].">".get_caption('4260','Male')."</option><option value='1' ".$gender['2'].">".get_caption('4270','Female')."</option></select>",
			"home_picture_title"   => get_caption('4230','Profile Picture'),
			"home_picture"         => "<img src='".$path_to_profiles_folder.$sys_profile['picture']."' border='0' alt='Foto' /><br /><input type='file' name='filename' size='30' maxlength='250' /><input type='hidden' name='old_picture' value='".$sys_profile['picture']."' />",
			"home_deletepic_title" => get_caption('0211','Delete'),
			"home_deletepic"       => "<input type='checkbox' name='deletepic' />",
			"home_be_lang_title"   => get_caption('4240','Admincenter Language'),
			"home_be_lang"         => "<select name='be_lang'>".$sys_lang."</select>",
			"home_be_tmpl_title"   => get_caption('4250','Admincenter Template'),
			"home_be_tmpl"         => "<select name='be_tmpl'><option value='0'>Default</option>".$sys_tmpl."</select>",
			"home_button_send"     => "<input type='hidden' name='username' value='".$sys_profile['username']."' />".$ac->create_form_button("submit",get_caption('0120','Save')),
			"home_button_reset"    => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	default:
		// Create user overview
		
		// Filter
		if(isset($_POST["send"])) {
			if($_POST["filter_user"] == "") {
				$_SESSION["filter_user"] = "%";
			} else {
				$_SESSION["filter_user"] = $_POST["filter_user"];
			}
			load_url("index.php?mode=user");
		}
		
		$user_filter = $filters->create_filter_user($db);
		
		// Headline
		$user_user .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('4060','Username')."</p></td>"
			."<td><p class='bold'>".get_caption('4020','Groups')."</p></td>"
			."<td><p class='bold'>".get_caption('0230','Blocked')."</p></td>"
			."<td><p class='bold'>".get_caption('0240','Deleted')."</p></td>"
			."<td><p class='bold'>".get_caption('0250','Status')."</p></td>"
			."<td><p class='bold'>".get_caption('4090','Register Date')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
		
		// Filter
		if(!isset($_SESSION["filter_user"])) {
			$_SESSION["filter_user"] = "%";
		}
		
		// settings
		$start = $_GET['start'];
		$limit = $s_limit; // default: 20
		
		$db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE status LIKE '".$_SESSION["filter_user"]."'");
		$total = $db->num_rows();
		
		// create page handling
		$page_html_code = $ac->create_page_handling($total,$limit,"index.php?mode=user&start=",$start);
		$start = $ac->get_page_start($start,$limit);
		
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_user WHERE status LIKE '".$_SESSION["filter_user"]."' ORDER BY uid LIMIT ".$start.",".$limit."");
		while($db->next_record()):
			if($db->f("user_since") != 0) {
				$register_date = date($sys_vars["date"],$db->f("user_since"));
			}
			$user_user .= "<tr class='bg_color2'>"
				."<td>".$db->f("username")."</td>"
				."<td>".$db->f("groups")."</td>"
				."<td>".translate_yesno($db->f("blocked"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("deleted"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("status"),get_caption('0260','Active'),get_caption('0270','Inactive'))."</td>"
				."<td>".$register_date."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=user&action=edit&userid=".$db->f("uid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=user&action=del&userid=".$db->f("uid"),get_caption('0211','Delete'))
				.$ac->create_editprofile_icon("index.php?mode=user&action=profile&userid=".$db->f("uid"),get_caption('4130','Edit User Profile'))
				."</td>"
				."</tr>";
		endwhile;
		$user_user .= "</table>";
		
		// Set variables
		$tpl->set_var(array(
			"user_title"       => "<h2>".get_caption('4000','Security')." / ".get_caption('4010','User')."</h2>",
			"confirm_message"  => $ac->show_ok_message(GetConfirmMessage()),
			"user_user"        => $user_user,
			"user_user_filter" => $user_filter,
			"page_handling"    => $page_html_code,
			"user_user_new"    => $ac->create_add_icon("index.php?mode=user&action=new",get_caption('4050','Add User'))
			));
	break;
}
?>