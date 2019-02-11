<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_home_profiles.php -
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

// Get user profile data
$db->query("SELECT * FROM ".$tbl_prefix."sys_profile WHERE user_id = '".$_GET['user_id']."' ORDER BY user_id LIMIT 1");
while($db->next_record()):
	$username = $ac->get_username($_GET['user_id']);
	$firstname = $db->f("firstname");
	$lastname = $db->f("lastname");
	$address1 = $db->f("address1");
	$address2 = $db->f("address2");
	$city = $db->f("city");
	$zip_code = $db->f("zip_code");
	if($db->f('show_email') == 1) {
		$email = $db->f("email");
	}
	$birthday = $db->f("birthday");
	$gender = translate_yesno($db->f("gender"),get_caption('4270','Female'),get_caption('4260','Male'));
	$picture = $db->f("picture");
endwhile;
		
if(!empty($birthday)) {
	$birthday = date("d.m.Y",$birthday);
}
if(empty($picture)) {
	$picture = "standard.gif";
}

// Set variables
$tpl->set_var(array(
	"home_title"           => "<h2>".get_caption('4281','User Profile')." / ".$username."</h2>",
	"home_contact"         => get_caption('4670','Contact Information'),
	"home_firstname_title" => get_caption('4140','First Name'),
	"home_firstname"       => $firstname,
	"home_lastname_title"  => get_caption('4150','Last Name'),
	"home_lastname"        => $lastname,
	"home_address1_title"  => get_caption('4160','Address'),
	"home_address1"        => $address1,
	"home_address2_title"  => get_caption('4170','Address 2'),
	"home_address2"        => $address2,
	"home_city_title"      => get_caption('4180','City'),
	"home_city"            => $city,
	"home_zip_code_title"  => get_caption('4190','Zip Code'),
	"home_zip_code"        => $zip_code,
	"home_email_title"     => get_caption('4200','E-Mail'),
	"home_email"           => $email,
	"home_birthday_title"  => get_caption('4210','Date of Birth'),
	"home_birthday"        => $birthday,
	"home_gender_title"    => get_caption('4220','Gender'),
	"home_gender"          => $gender,
	"home_picture_title"   => get_caption('4230','Profile Picture'),
	"home_picture"         => "<img src='".$path_to_profiles_folder.$picture."' border='0' alt='".get_caption('4230','Profile Picture')."' />"
	));
?>