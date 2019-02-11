<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_home_myprofile.php -
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
		$db->query("UPDATE ".$tbl_prefix."sys_profile SET picture = '' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
		if($_POST['old_picture'] != "standard.gif") {
			delete_file($_POST['old_picture'],$path_to_profiles_folder);
		}
	}
	
	// upload picture
	$_FILES["filename"]["name"] = validate_text($_FILES["filename"]["name"]);
			
	if(mandatory_field($_FILES["filename"]["name"])) {
		$_FILES["filename"]["name"] = $_SESSION["username"]."_".$_FILES["filename"]["name"];
		$picture_error = "";
		if($_FILES["filename"]["size"] > 15360) {
			$picture_error = $ac->show_error_message(get_caption('9480','The file size may amount to maximally 15 KB.'));
		}
		if($_FILES["filename"]["type"] != "image/gif" && $_FILES["filename"]["type"] != "image/jpeg" && $_FILES["filename"]["type"] != "image/pjpeg") {
			$picture_error = $ac->show_error_message(get_caption('9490','You can only upload pictures of type GIF and JPEG.'));
		}
		if(!file_exists($path_to_profiles_folder.$_FILES["filename"]["name"])) {
			//$picture_error = $ac->show_error_message(get_caption('ErrorPictureExist'));
			if($picture_error == "") {
				if($_POST['old_picture'] != "standard.gif") {
					delete_file($_POST['old_picture'],$path_to_profiles_folder);
				}
			}
		}
		
		if($picture_error == "") {
			upload_file($path_to_profiles_folder);
			$db->query("UPDATE ".$tbl_prefix."sys_profile SET picture = '".$_FILES["filename"]["name"]."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
		}
	}
	
	// save changes in database
	if(mandatory_field($_POST['firstname'])) {
		$db->query("UPDATE ".$tbl_prefix."sys_profile SET firstname = '".$_POST['firstname']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	}
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET lastname = '".$_POST['lastname']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET address1 = '".$_POST['address1']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET address2 = '".$_POST['address2']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET city = '".$_POST['city']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET zip_code = '".$_POST['zip_code']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	if(mandatory_field($_POST['email']) && email_is_valid($_POST['email'])) {
		$db->query("UPDATE ".$tbl_prefix."sys_profile SET email = '".$_POST['email']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
		// user changed mail -> send activation key
	}
	if(strlen($_POST['birthday']) == 0 || $_POST['birthday'] > 0) {
		$db->query("UPDATE ".$tbl_prefix."sys_profile SET birthday = '".$_POST['birthday']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	}
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET gender = '".$_POST['gender']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET be_lang = '".$_POST['be_lang']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET be_tmpl = '".$_POST['be_tmpl']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_profile SET show_email = '".$_POST['showemail']."' WHERE user_id = '".$_SESSION["uid"]."' ORDER BY user_id LIMIT 1");
	
	if($error == "" && $picture_error == "") {
		CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
		load_url("index.php?page=myprofile");
	}
}
		
// Get user profile data
$sys_profile = $perm->get_user_profile($db,$_SESSION["uid"]);

$showemail = mark_selected_value($sys_profile['show_email']);

$gender = mark_selected_value($sys_profile['gender']);
if(empty($gender[1])) { $selected_gender = get_caption('4270','Female'); }
if(empty($gender[2])) { $selected_gender = get_caption('4260','Male'); }

if(!empty($sys_profile['birthday'])) {
	$sys_profile['birthday'] = date("d.m.Y",$sys_profile['birthday']);
}
if($sys_profile['picture'] == "") {
	$sys_profile['current_picture'] = "";
	$sys_profile['picture'] = "standard.gif";
} else {
	$sys_profile['current_picture'] = $sys_profile['picture'];
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

// Set variables
$tpl->set_var(array(
	"home_title"           => "<h2>".get_caption('4620','My Account')." / ".get_caption('4630','My Profile')."</h2>",
	"tab_general"          => get_caption('0170','General'),
	"tab_options"          => get_caption('0180','Options'),
	"tab_preview"          => get_caption('0421','Preview'),
	"home_action"          => "index.php?page=myprofile",
	"home_error"           => $error.$picture_error,
	"confirm_message"      => $ac->show_ok_message(GetConfirmMessage()),
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
	"home_zip_code_title"  => get_caption('4190','ZipCode'),
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
	"home_picture"         => "<img src='".$path_to_profiles_folder.$sys_profile['picture']."' border='0' alt='".$sys_profile['picture']."' class='profile_image' /><br /><input type='file' name='filename' size='30' maxlength='250' /><input type='hidden' name='old_picture' value='".$sys_profile['current_picture']."' />",
	"home_deletepic_title" => get_caption('0211','Delete'),
	"home_deletepic"       => "<input type='checkbox' name='deletepic' />",
	"home_be_lang_title"   => get_caption('4240','Admincenter Language'),
	"home_be_lang"         => "<select name='be_lang'>".$sys_lang."</select>",
	"home_be_tmpl_title"   => get_caption('4250','Admincenter Template'),
	"home_be_tmpl"         => "<select name='be_tmpl'><option value='0'>".get_caption('0280','Default')."</option>".$sys_tmpl."</select>",
	"home_button_send"     => $ac->create_form_button("submit",get_caption('0120','Save')),
	"home_button_reset"    => $ac->create_form_button("reset",get_caption('0125','Reset')),
	// ShowProfile
	"home2_contact"         => get_caption('4670','Contact Information'),
	"home2_firstname_title" => get_caption('4140','First Name'),
	"home2_firstname"       => $sys_profile['firstname'],
	"home2_lastname_title"  => get_caption('4150','Last Name'),
	"home2_lastname"        => $sys_profile['lastname'],
	"home2_address1_title"  => get_caption('4160','Address'),
	"home2_address1"        => $sys_profile['address1'],
	"home2_address2_title"  => get_caption('4170','Address 2'),
	"home2_address2"        => $sys_profile['address2'],
	"home2_city_title"      => get_caption('4180','City'),
	"home2_city"            => $sys_profile['city'],
	"home2_zip_code_title"  => get_caption('4190','Zip-Code'),
	"home2_zip_code"        => $sys_profile['zip_code'],
	"home2_email_title"     => get_caption('4200','E-Mail'),
	"home2_email"           => $sys_profile['email'],
	"home2_birthday_title"  => get_caption('4210','Date of Birth'),
	"home2_birthday"        => $sys_profile['birthday'],
	"home2_gender_title"    => get_caption('4220','Gender'),
	"home2_gender"          => $selected_gender,
	"home2_picture_title"   => get_caption('4230','Profile Picture'),
	"home2_picture"         => "<img src='".$path_to_profiles_folder.$sys_profile['picture']."' border='0' alt='".$sys_profile['picture']."' class='profile_image' />"
	));
?>