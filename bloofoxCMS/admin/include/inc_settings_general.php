<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_general.php -
//
// Copyrights (c) 2008-2012 Alexander Lang, Germany
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

if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_general']['write'] == 1) {
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u1']."' WHERE sid = '1' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u2']."' WHERE sid = '2' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u3']."' WHERE sid = '3' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u4']."' WHERE sid = '4' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u5']."' WHERE sid = '5' LIMIT 1");
	
	if(!empty($_POST['u6'])) {
		if(email_is_valid($_POST['u6'])) {
			$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u6']."' WHERE sid = '6' LIMIT 1");
		} else {
			$error = $ac->show_error_message(get_caption('9400','You must enter a valid e-mail.'));
		}
	}
	
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u7']."' WHERE sid = '7' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u8']."' WHERE sid = '8' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u9']."' WHERE sid = '9' LIMIT 1");
	
	if(!empty($_POST['u10'])) {
		if(CheckInteger($_POST['u10'])) {
			$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u10']."' WHERE sid = '10' LIMIT 1");
		} else {
			if($error == "") {
				$error = $ac->show_error_message(get_caption('9410','You may insert integer values only.'));
			}
		}
	}
	
	if(!empty($_POST['u12'])) {
		if(CheckInteger($_POST['u12'])) {
			$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u12']."' WHERE sid = '12' LIMIT 1");
		} else {
			if($error == "") {
				$error = $ac->show_error_message(get_caption('9410','You may insert integer values only.'));
			}
		}
	}
	
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u20']."' WHERE sid = '20' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u30']."' WHERE sid = '30' LIMIT 1");
	$db->query("UPDATE ".$tbl_prefix."sys_setting SET setting_value = '".$_POST['u40']."' WHERE sid = '40' LIMIT 1");
	
	CreateConfirmMessage(0,'');
	if(empty($error)) {
		CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
		load_url("index.php?mode=settings");
	}
}

// select record
if(isset($_POST['sid'])) {
	$_GET['sid'] = $_POST['sid'];
}
$db->query("SELECT * FROM ".$tbl_prefix."sys_setting ORDER BY sid");
while($db->next_record()):
	$sid = $db->f("sid");
	$setting[$sid] = $db->f("setting_value");
endwhile;

if($setting[1] == "1") { $u1_select['1'] = "checked='checked'"; } else { $u1_select['0'] = "checked='checked'"; }
if($setting[2] == "1") { $u2_select['1'] = "checked='checked'"; } else { $u2_select['0'] = "checked='checked'"; }
if($setting[3] == "1") { $u3_select['1'] = "checked='checked'"; } else { $u3_select['0'] = "checked='checked'"; }
if($setting[4] == "1") { $u4_select['1'] = "checked='checked'"; } else { $u4_select['0'] = "checked='checked'"; }
if($setting[5] == "1") { $u5_select['1'] = "checked='checked'"; } else { $u5_select['0'] = "checked='checked'"; }
if($setting[7] == "1") { $u7_select['1'] = "checked='checked'"; } else { $u7_select['0'] = "checked='checked'"; }
if($setting[8] == "1") { $u8_select['1'] = "checked='checked'"; } else { $u8_select['0'] = "checked='checked'"; }
if($setting[9] == "1") { $u9_select['1'] = "checked='checked'"; } elseif($setting[9] == "2") { $u9_select['2'] = "checked='checked'"; } else { $u9_select['0'] = "checked='checked'"; }
if($setting[20] == "1") { $u20_select['1'] = "checked='checked'"; } else { $u20_select['0'] = "checked='checked'"; }
if($setting[40] == "1") { $u40_select['1'] = "checked='checked'"; } else { $u40_select['0'] = "checked='checked'"; }

// Set variables
$tpl->set_var(array(
	"settings_title"        => "<h2>".get_caption('3000','Administration')." / ".get_caption('3010','Settings')."</h2>",
	"tab_general"           => get_caption('0170','General'),
	"tab_content"           => get_caption('2000','Contents'),
	"tab_security"          => get_caption('4000','Security'),
	"settings_action"       => "index.php?mode=settings",
	"settings_error"        => $error,
	"confirm_message"       => $ac->show_ok_message(GetConfirmMessage()),
	"settings_u1"           => get_caption('3490','Check for new versions/updates'),
	"settings_u1_input"     => get_caption('0130','Yes')." <input type='radio' name='u1' value='1' ".$u1_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u1' value='0' ".$u1_select['0']." />",
	"settings_u2"           => get_caption('3500','Send notifications to Administrator mail address'),
	"settings_u2_input"     => get_caption('0130','Yes')." <input type='radio' name='u2' value='1' ".$u2_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u2' value='0' ".$u2_select['0']." />",
	"settings_u3"           => get_caption('3510','Use login protection/session logging'),
	"settings_u3_input"     => get_caption('0130','Yes')." <input type='radio' name='u3' value='1' ".$u3_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u3' value='0' ".$u3_select['0']." />",
	"settings_u4"           => get_caption('3520','Refresh online status with each request'),
	"settings_u4_input"     => get_caption('0130','Yes')." <input type='radio' name='u4' value='1' ".$u4_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u4' value='0' ".$u4_select['0']." />",
	"settings_u5"           => get_caption('3530','Content user protection'),
	"settings_u5_input"     => get_caption('0130','Yes')." <input type='radio' name='u5' value='1' ".$u5_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u5' value='0' ".$u5_select['0']." />",
	"settings_u6"           => get_caption('3540','Administrator e-mail address'),
	"settings_u6_input"     => "<input type='text' name='u6' value='".$setting[6]."' size='30' maxlength='80' />",
	"settings_u6_label"     => get_caption('3370','E-Mail'),
	"settings_u7"           => get_caption('3550','Send e-mails in HTML format'),
	"settings_u7_input"     => get_caption('0130','Yes')." <input type='radio' name='u7' value='1' ".$u7_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u7' value='0' ".$u7_select['0']." />",
	"settings_u8"           => get_caption('3560','Deactivate PHP function htmlentities()'),
	"settings_u8_input"     => get_caption('0130','Yes')." <input type='radio' name='u8' value='1' ".$u8_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u8' value='0' ".$u8_select['0']." />",
	"settings_u9"           => get_caption('3570','Password rule for user passwords'),
	"settings_u9_input"     => "<input type='radio' name='u9' value='0' ".$u9_select['0']." /> ".get_caption('3571','None')."<br /><input type='radio' name='u9' value='1' ".$u9_select['1']." /> ".get_caption('3572','Medium (numbers and letters)')."<br /><input type='radio' name='u9' value='2' ".$u9_select['2']." /> ".get_caption('3573','Strict (numbers, letters and special signs)'),
	"settings_u10"          => get_caption('3580','Textbox width of (wysiwyg) editor (Pixel)'),
	"settings_u10_input"    => "<input type='text' name='u10' value='".$setting[10]."' size='5' maxlength='10' />",
	"settings_u10_label"    => get_caption('3590','Width'),
	"settings_u12"          => get_caption('3600','Textbox height of (wysiwyg) editor (Pixel)'),
	"settings_u12_input"    => "<input type='text' name='u12' value='".$setting[12]."' size='5' maxlength='10' />",	
	"settings_u12_label"    => get_caption('3610','Height'),
	"settings_u20"          => get_caption('3620','Confirm new user manually'),
	"settings_u20_input"    => get_caption('0130','Yes')." <input type='radio' name='u20' value='1' ".$u20_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u20' value='0' ".$u20_select['0']." />",
	"settings_u30"          => get_caption('3630','URL to PhpMyAdmin'),
	"settings_u30_input"    => "<input type='text' name='u30' value='".$setting[30]."' size='50' maxlength='80' />",
	"settings_u40"          => get_caption('3640','Maintenance Mode'),
	"settings_u40_input"    => get_caption('0130','Yes')." <input type='radio' name='u40' value='1' ".$u40_select['1']." /> ".get_caption('0140','No')."<input type='radio' name='u40' value='0' ".$u40_select['0']." />",
	"settings_sid"          => "<input type='hidden' name='sid' value='".$sid."' />",
	"settings_button_send"  => $ac->create_form_button("submit",get_caption('0120','Save')),
	"settings_button_reset" => $ac->create_form_button("reset",get_caption('0125','Reset'))
	));
?>