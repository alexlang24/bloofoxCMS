<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_user_groups.php -
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
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_groups']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			
			if(!mandatory_field($_POST['name']) && $error == "") {
				$error = $ac->show_error_message(get_caption('9160','You must enter a name.'));
			}
								
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				$db->query("INSERT INTO ".$tbl_prefix."sys_usergroup VALUES ('','".$_POST['name']."','".$_POST['backend']."','".$_POST['content']."','".$_POST['settings']."','".$_POST['configure']."','".$_POST['permissions']."','".$_POST['tools']."','".$_POST['demo']."')");
				load_url("index.php?mode=user&page=groups");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"user_title"            => "<h2>".get_caption('4000','Security')." / ".get_caption('4290','Add User Group')."</h2>",
			"tab_general"           => get_caption('0170','General'),
			"groups_action"         => "index.php?mode=user&page=groups&action=new",
			"groups_error"          => $error,
			"groups_name"           => get_caption('0110','Name'),
			"groups_name_input"     => "<input type='text' name='name' size='25' maxlength='250' value='".$_POST['name']."' />",
			"groups_backend"        => get_caption('0340','Backend'),
			"groups_backend_input"  => "<select name='backend'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_content"        => get_caption('2000','Contents'),
			"groups_content_input"  => "<select name='content'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_settings"       => get_caption('3000','Administration'),
			"groups_settings_input" => "<select name='settings'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_permissions"    => get_caption('4000','Security'),
			"groups_permissions_input" => "<select name='permissions'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_tools"          => get_caption('5000','Tools'),
			"groups_tools_input"    => "<select name='tools'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_demo"           => get_caption('0330','Demo'),
			"groups_demo_input"     => "<select name='demo'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"groups_button_send"    => $ac->create_form_button("submit",get_caption('4290','Add User Group'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_groups']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			
			if(!mandatory_field($_POST['name']) && $error == "") {
				$error = $ac->show_error_message(get_caption('9160','You must enter a name.'));
			}
			
			if(mandatory_field($_POST['name'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET name = '".$_POST['name']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
				if($_POST['name'] != $_POST['name_old']) {
					$perm->rename_group($db,$_POST['name_old'],$_POST['name']);
				}
			}
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET backend = '".$_POST['backend']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET content = '".$_POST['content']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET settings = '".$_POST['settings']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET permissions = '".$_POST['permissions']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET tools = '".$_POST['tools']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_usergroup SET demo = '".$_POST['demo']."' WHERE gid = '".$_POST['gid']."' LIMIT 1");
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=user&page=groups");
			}
		}
		
		// select record
		if(isset($_POST['gid'])) {
			$_GET['gid'] = $_POST['gid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_usergroup WHERE gid = '".$_GET['gid']."' ORDER BY gid LIMIT 1");
		while($db->next_record()):
			$gid = $db->f("gid");
			$name = $db->f("name");
			$backend = mark_selected_value($db->f("backend"));
			$content = mark_selected_value($db->f("content"));
			$settings = mark_selected_value($db->f("settings"));
			//$configure = mark_selected_value($db->f("configure"));
			$permissions = mark_selected_value($db->f("permissions"));
			$tools = mark_selected_value($db->f("tools"));
			$demo = mark_selected_value($db->f("demo"));
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"user_title"            => "<h2>".get_caption('4000','Security')." / ".get_caption('4300','Edit User Group')."</h2>",
			"tab_general"           => get_caption('0170','General'),
			"groups_action"         => "index.php?mode=user&page=groups&action=edit",
			"groups_error"          => $error,
			"groups_name"           => get_caption('0110','Name'),
			"groups_name_input"     => "<input type='text' name='name' size='25' maxlength='250' value='".$name."' />",
			"groups_backend"        => get_caption('0340','Backend'),
			"groups_backend_input"  => "<select name='backend'><option value='0' ".$backend['1'].">".get_caption('0140','No')."</option><option value='1' ".$backend['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_content"        => get_caption('2000','Contents'),
			"groups_content_input"  => "<select name='content'><option value='0' ".$content['1'].">".get_caption('0140','No')."</option><option value='1' ".$content['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_settings"       => get_caption('3000','Administration'),
			"groups_settings_input" => "<select name='settings'><option value='0' ".$settings['1'].">".get_caption('0140','No')."</option><option value='1' ".$settings['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_permissions"    => get_caption('4000','Security'),
			"groups_permissions_input" => "<select name='permissions'><option value='0' ".$permissions['1'].">".get_caption('0140','No')."</option><option value='1' ".$permissions['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_tools"          => get_caption('5000','Tools'),
			"groups_tools_input"    => "<select name='tools'><option value='0' ".$tools['1'].">".get_caption('0140','No')."</option><option value='1' ".$tools['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_demo"           => get_caption('0330','Demo'),
			"groups_demo_input"     => "<select name='demo'><option value='0' ".$demo['1'].">".get_caption('0140','No')."</option><option value='1' ".$demo['2'].">".get_caption('0130','Yes')."</option></select>",
			"groups_gid"            => "<input type='hidden' name='gid' value='".$gid."' /><input type='hidden' name='name_old' value='".$name."' />",
			"groups_button_send"    => $ac->create_form_button("submit",get_caption('0120','Save')),
			"groups_button_reset"   => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_groups']['delete'] == 1) {
			if($perm->delete_group($db,$_POST['name']) == 1) {
				CreateConfirmMessage(1,get_caption("0410","Entry was successfully deleted."));
				$db->query("DELETE FROM ".$tbl_prefix."sys_usergroup WHERE gid = '".$_POST['gid']."' LIMIT 1");
			} else {
				$_SESSION['error'] = get_caption('9170','You cannot delete a group which is still in use.');
			}
			load_url("index.php?mode=user&page=groups");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=user&page=groups");
		
		// select record
		if(isset($_POST['gid'])) {
			$_GET['gid'] = $_POST['gid'];
		}
		$db->query("SELECT gid,name FROM ".$tbl_prefix."sys_usergroup WHERE gid = '".$_GET['gid']."' ORDER BY gid LIMIT 1");
		while($db->next_record()):
			$gid = $db->f("gid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"user_title"         => "<h2>".get_caption('4000','Security')." / ".get_caption('4310','Delete User Group')."</h2>",
			"groups_action"      => "index.php?mode=user&page=groups&action=del",
			"groups_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"groups_name"        => "<p class='bold'>".$name."</p>",
			"groups_gid"         => "<input type='hidden' name='gid' value='".$gid."' /><input type='hidden' name='name' value='".$name."' />",
			"groups_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create groups overview
		if(!empty($_SESSION['error'])) {
			$user_groups = $ac->show_error_message($_SESSION['error']);
			unset($_SESSION['error']);
		}
		
		// Headline
		$user_groups .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('0340','Backend')."</p></td>"
			."<td><p class='bold'>".get_caption('2000','Contents')."</p></td>"
			."<td><p class='bold'>".get_caption('3000','Administration')."</p></td>"
			."<td><p class='bold'>".get_caption('4000','Security')."</p></td>"
			."<td><p class='bold'>".get_caption('5000','Tools')."</p></td>"
			."<td><p class='bold'>".get_caption('0330','Demo')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			$user_groups .= "<tr class='bg_color2'>"
				."<td>".$db->f("name")."</td>"
				."<td>".translate_yesno($db->f("backend"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("content"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("settings"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("permissions"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("tools"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("demo"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=user&page=groups&action=edit&gid=".$db->f("gid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=user&page=groups&action=del&gid=".$db->f("gid"),get_caption('0211','Delete'))
				."</td>"
				."</tr>";
		endwhile;
		$user_groups .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"user_title"        => "<h2>".get_caption('4000','Security')." / ".get_caption('4020','User Groups')."</h2>",
			"confirm_message"   => $ac->show_ok_message(GetConfirmMessage()),
			"user_groups"       => $user_groups,
			"user_groups_new"   => $ac->create_add_icon("index.php?mode=user&page=groups&action=new",get_caption('4290','Add User Group'))
			));
	break;
}
?>