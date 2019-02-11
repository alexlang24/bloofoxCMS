<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_charset.php -
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

switch($action)
{
	case 'new':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_charsets']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['description'] = validate_text($_POST['description']);
			
			if(mandatory_field($_POST['name'])) {
				$db->query("INSERT INTO ".$tbl_prefix."sys_charset (`cid`,`name`,`description`) VALUES ('','".$_POST['name']."','".$_POST['description']."')");
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				load_url("index.php?mode=settings&page=charset");
			} else {
				$error = $ac->show_error_message(get_caption('9160','You must enter a name.'));
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3660','Add Charset')."</h2>",
			"tab_general"         => get_caption('0170','General'),
			"charset_action"      => "index.php?mode=settings&page=charset&action=new",
			"charset_error"       => $error,
			"charset_name"        => get_caption('0110','Name'),
			"charset_name_input"  => "<input type='text' name='name' size='30' maxlength='30' />",
			"charset_desc"        => get_caption('3690','Description'),
			"charset_desc_input"  => "<input type='text' name='description' size='50' maxlength='50' />",
			"charset_button_send" => $ac->create_form_button("submit",get_caption('3660','Add Charset'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_charsets']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['description'] = validate_text($_POST['description']);
			
			if(mandatory_field($_POST['name'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_charset SET name = '".$_POST['name']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
				$db->query("UPDATE ".$tbl_prefix."sys_charset SET description = '".$_POST['description']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=settings&page=charset");
			} else {
				$error = $ac->show_error_message(get_caption('9160','You must enter a name.'));
			}
		}
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_charset WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$name = $db->f("name");
			$description = $db->f("description");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"       => "<h2>".get_caption('3000','Administration')." / ".get_caption('3670','Edit Charset')."</h2>",
			"tab_general"          => get_caption('0170','General'),
			"charset_action"       => "index.php?mode=settings&page=charset&action=edit",
			"charset_error"        => $error,
			"charset_name"         => get_caption('0110','Name'),
			"charset_name_input"   => "<input type='text' name='name' size='30' maxlength='250' value='".$name."' />",
			"charset_desc"         => get_caption('3690','Description'),
			"charset_desc_input"   => "<input type='text' name='description' size='50' maxlength='50' value='".$description."' />",
			"charset_cid"          => "<input type='hidden' name='cid' value='".$cid."' />",
			"charset_button_send"  => $ac->create_form_button("submit",get_caption('0120','Save')),
			"charset_button_reset" => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_charsets']['delete'] == 1) {
			$db->query("DELETE FROM ".$tbl_prefix."sys_charset WHERE cid = '".$_POST['cid']."' LIMIT 1");
			CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			load_url("index.php?mode=settings&page=charset");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=settings&page=charset");
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_charset WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3680','Delete Charset')."</h2>",
			"charset_action"      => "index.php?mode=settings&page=charset&action=del",
			"charset_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"charset_name"        => "<p class='bold'>".$name."</p>",
			"charset_cid"         => "<input type='hidden' name='cid' value='".$cid."' />",
			"charset_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		$settings_charset = "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('3690','Description')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td></tr>";
			
		$db->query("SELECT * FROM ".$tbl_prefix."sys_charset ORDER BY name");
		while($db->next_record()):
			$settings_charset .= "<tr class='bg_color2'><td>".$db->f("name")."</td>"
				."<td>".$db->f("description")."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=settings&page=charset&action=edit&cid=".$db->f("cid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=settings&page=charset&action=del&cid=".$db->f("cid"),get_caption('0211','Delete'))
				."</td></tr>";
		endwhile;
		$settings_charset .= "</table>";
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"       => "<h2>".get_caption('3000','Administration')." / ".get_caption('3060','Charsets')."</h2>",
			"confirm_message"      => $ac->show_ok_message(GetConfirmMessage()),
			"settings_charset"     => $settings_charset,
			"settings_charset_new" => $ac->create_add_icon("index.php?mode=settings&page=charset&action=new",get_caption('3660','Add Charset'))
			));
	break;
}
?>