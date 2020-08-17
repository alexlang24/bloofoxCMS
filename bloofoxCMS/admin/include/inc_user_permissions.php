<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_user_permissions.php -
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
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_permissions']['write'] == 1) {
			CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
			$db->query("INSERT INTO ".$tbl_prefix."sys_permission VALUES ('','".$_POST['group_id']."','".$_POST['page']."','".$_POST['object_w']."','".$_POST['object_d']."')");
			load_url("index.php?mode=user&page=permissions");
		}

		$sys_usergroups = "";
		$db->query("SELECT gid,name FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			if($_POST["group_id"] == $db->f("gid")) {
				$sys_usergroups .= "<option value='".$db->f("gid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_usergroups .= "<option value='".$db->f("gid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$sys_pages = "";
		$sys_pages .= "<option value='projects'>".get_caption('4380','Administration: Projects')."</option>";
		$sys_pages .= "<option value='set_lang'>".get_caption('4390','Administration: Languages')."</option>";
		$sys_pages .= "<option value='set_tmpl'>".get_caption('4400','Administration: Templates')."</option>";
		$sys_pages .= "<option value='set_plugins'>".get_caption('4410','Administration: Plugins')."</option>";
		$sys_pages .= "<option value='set_charsets'>".get_caption('4420','Administration: Charsets')."</option>";
		$sys_pages .= "<option value='set_general'>".get_caption('4430','Administration: Settings')."</option>";
		$sys_pages .= "<option value='content_pages'>".get_caption('4440','Contents: Pages')."</option>";
		$sys_pages .= "<option value='content_default'>".get_caption('4450','Contents: Articles')."</option>";
		$sys_pages .= "<option value='content_plugins'>".get_caption('4460','Contents: Plugins')."</option>";
		$sys_pages .= "<option value='content_media'>".get_caption('4470','Contents: Media')."</option>";
		$sys_pages .= "<option value='content_levels'>".get_caption('4480','Contents: Sorting')."</option>";
		$sys_pages .= "<option value='user'>".get_caption('4500','Security: Users')."</option>";
		$sys_pages .= "<option value='user_groups'>".get_caption('4510','Security: User Groups')."</option>";
		$sys_pages .= "<option value='user_permissions'>".get_caption('4520','Security: Permissions')."</option>";
		$sys_pages .= "<option value='user_sessions'>".get_caption('4530','Security: Sessions')."</option>";
		$sys_pages .= "<option value='tools'>".get_caption('4540','Tools')."</option>";

		// Set variables
		$tpl->set_var(array(
			"user_title"            => "<h2>".get_caption('4000','Security')." / ".get_caption('4320','Add Permission')."</h2>",
			"tab_general"           => get_caption('0170','General'),
			"user_action"           => "index.php?mode=user&page=permissions&action=new",
			"user_error"            => $error,
			"user_group_id"         => get_caption('4021','User Group'),
			"user_group_id_input"   => "<select name='group_id'>".$sys_usergroups."</select>",
			"user_page"             => get_caption('4350','Area'),
			"user_page_input"       => "<select name='page'>".$sys_pages."</select>",
			"user_object_w"         => get_caption('4360','Write'),
			"user_object_w_input"   => "<select name='object_w'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"user_object_d"         => get_caption('4370','Delete'),
			"user_object_d_input"   => "<select name='object_d'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"user_button_send"      => $ac->create_form_button("submit",get_caption('4320','Add Permission'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_permissions']['write'] == 1) {
			CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
			$db->query("UPDATE ".$tbl_prefix."sys_permission SET group_id = '".$_POST['group_id']."' WHERE pid = '".$_POST['pid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_permission SET page = '".$_POST['page']."' WHERE pid = '".$_POST['pid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_permission SET object_w = '".$_POST['object_w']."' WHERE pid = '".$_POST['pid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_permission SET object_d = '".$_POST['object_d']."' WHERE pid = '".$_POST['pid']."' LIMIT 1");
		
			load_url("index.php?mode=user&page=permissions");
		}
		
		// select record
		if(isset($_POST['pid'])) {
			$_GET['pid'] = $_POST['pid'];
		}
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_permission WHERE pid = '".$_GET['pid']."' ORDER BY pid LIMIT 1");
		while($db->next_record()):
			$pid = $db->f("pid");
			$group_id = $db->f("group_id");
			$page = $db->f("page");
			$w = mark_selected_value($db->f("object_w"));
			$d = mark_selected_value($db->f("object_d"));
		endwhile;

		$sys_usergroups = "";
		$db->query("SELECT gid,name FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			if($group_id == $db->f("gid")) {
				$sys_usergroups .= "<option value='".$db->f("gid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_usergroups .= "<option value='".$db->f("gid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$sys_pages = "";
		
		$sys_pages .= "<option value='projects'>".get_caption('4380','Administration: Projects')."</option>";
		if($page == "set_lang") {
			$sys_pages .= "<option value='set_lang' selected='selected'>".get_caption('4390','Administration: Languages')."</option>";
		} else {
			$sys_pages .= "<option value='set_lang'>".get_caption('4390','Administration: Languages')."</option>";
		}
		if($page == "set_tmpl") {
			$sys_pages .= "<option value='set_tmpl' selected='selected'>".get_caption('4400','Administration: Templates')."</option>";
		} else {
			$sys_pages .= "<option value='set_tmpl'>".get_caption('4400','Administration: Templates')."</option>";
		}
		if($page == "set_plugins") {
			$sys_pages .= "<option value='set_plugins' selected='selected'>".get_caption('4410','Administration: Plugins')."</option>";
		} else {
			$sys_pages .= "<option value='set_plugins'>".get_caption('4410','Administration: Plugins')."</option>";
		}
		if($page == "set_charsets") {
			$sys_pages .= "<option value='set_charsets' selected='selected'>".get_caption('4420','Administration: Charsets')."</option>";
		} else {
			$sys_pages .= "<option value='set_charsets'>".get_caption('4420','Administration: Charsets')."</option>";
		}
		if($page == "set_general") {
			$sys_pages .= "<option value='set_general' selected='selected'>".get_caption('4430','Administration: Settings')."</option>";
		} else {
			$sys_pages .= "<option value='set_general'>".get_caption('4430','Administration: Settings')."</option>";
		}
		if($page == "content_explorer") {
			$sys_pages .= "<option value='content_pages' selected='selected'>".get_caption('4440','Contents: Pages')."</option>";
		} else {
			$sys_pages .= "<option value='content_pages'>".get_caption('4440','Contents: Pages')."</option>";
		}
		if($page == "content_default") {
			$sys_pages .= "<option value='content_default' selected='selected'>".get_caption('4450','Contents: Articles')."</option>";
		} else {
			$sys_pages .= "<option value='content_default'>".get_caption('4450','Contents: Articles')."</option>";
		}
		if($page == "content_plugins") {
			$sys_pages .= "<option value='content_plugins' selected='selected'>".get_caption('4460','Contents: Plugins')."</option>";
		} else {
			$sys_pages .= "<option value='content_plugins'>".get_caption('4460','Contents: Plugins')."</option>";
		}
		if($page == "content_media") {
			$sys_pages .= "<option value='content_media' selected='selected'>".get_caption('4470','Contents: Media')."</option>";
		} else {
			$sys_pages .= "<option value='content_media'>".get_caption('4470','Contents: Media')."</option>";
		}
		if($page == "content_levels") {
			$sys_pages .= "<option value='content_levels' selected='selected'>".get_caption('4480','Contents: Sorting')."</option>";
		} else {
			$sys_pages .= "<option value='content_levels'>".get_caption('4480','Contents: Sorting')."</option>";
		}
		if($page == "user") {
			$sys_pages .= "<option value='user' selected='selected'>".get_caption('4500','Security: Users')."</option>";
		} else {
			$sys_pages .= "<option value='user'>".get_caption('4500','Security: Users')."</option>";
		}
		if($page == "user_groups") {
			$sys_pages .= "<option value='user_groups' selected='selected'>".get_caption('4510','Security: User Groups')."</option>";
		} else {
			$sys_pages .= "<option value='user_groups'>".get_caption('4510','Security: User Groups')."</option>";
		}
		if($page == "user_permissions") {
			$sys_pages .= "<option value='user_permissions' selected='selected'>".get_caption('4520','Security: Permissions')."</option>";
		} else {
			$sys_pages .= "<option value='user_permissions'>".get_caption('4520','Security: Permissions')."</option>";
		}
		if($page == "user_sessions") {
			$sys_pages .= "<option value='user_sessions' selected='selected'>".get_caption('4530','Security: Sessions')."</option>";
		} else {
			$sys_pages .= "<option value='user_sessions'>".get_caption('4530','Security: Sessions')."</option>";
		}
		if($page == "tools") {
			$sys_pages .= "<option value='tools' selected='selected'>".get_caption('4540','Tools')."</option>";
		} else {
			$sys_pages .= "<option value='tools'>".get_caption('4540','Tools')."</option>";
		}
		
		// Set variables
		$tpl->set_var(array(
			"user_title"            => "<h2>".get_caption('4000','Security')." / ".get_caption('4330','Edit Permission')."</h2>",
			"tab_general"           => get_caption('0170','General'),
			"user_action"           => "index.php?mode=user&page=permissions&action=edit",
			"user_error"            => $error,
			"user_group_id"         => get_caption('4021','User Group'),
			"user_group_id_input"   => "<select name='group_id'>".$sys_usergroups."</select>",
			"user_page"             => get_caption('4350','Area'),
			"user_page_input"       => "<select name='page'>".$sys_pages."</select>",
			"user_object_w"         => get_caption('4360','Write'),
			"user_object_w_input"   => "<select name='object_w'><option value='0' ".$w['1'].">".get_caption('0140','No')."</option><option value='1' ".$w['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_object_d"         => get_caption('4370','Delete'),
			"user_object_d_input"   => "<select name='object_d'><option value='0' ".$d['1'].">".get_caption('0140','No')."</option><option value='1' ".$d['2'].">".get_caption('0130','Yes')."</option></select>",
			"user_pid"              => "<input type='hidden' name='pid' value='".$pid."' />",
			"user_button_send"      => $ac->create_form_button("submit",get_caption('0120','Save')),
			"user_button_reset"     => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_permissions']['delete'] == 1) {
			CreateConfirmMessage(1,get_caption("0410","Entry was successfully deleted."));
			$db->query("DELETE FROM ".$tbl_prefix."sys_permission WHERE pid = '".$_POST['pid']."' LIMIT 1");
			load_url("index.php?mode=user&page=permissions");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=user&page=permissions");
		
		// select record
		if(isset($_POST['pid'])) {
			$_GET['pid'] = $_POST['pid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_permission WHERE pid = '".$_GET['pid']."' ORDER BY pid LIMIT 1");
		while($db->next_record()):
			$pid = $db->f("pid");
			$name = $db->f("group_id");
			$page = $db->f("page");
		endwhile;
		
		$db->query("SELECT name FROM ".$tbl_prefix."sys_usergroup WHERE gid = '".$name."' LIMIT 1");
		while($db->next_record()):
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"user_title"       => "<h2>".get_caption('4000','Security')." / ".get_caption('4340','Delete Permission')."</h2>",
			"user_action"      => "index.php?mode=user&page=permissions&action=del",
			"user_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"user_name"        => "<p class='bold'>".$name." - ".$page."</p>",
			"user_pid"         => "<input type='hidden' name='pid' value='".$pid."' />",
			"user_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create permissions overview
		
		// Headline
		$user_permissions .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('4021','User Group')."</p></td>"
			."<td><p class='bold'>".get_caption('4350','Area')."</p></td>"
			."<td><p class='bold'>".get_caption('4360','Write')."</p></td>"
			."<td><p class='bold'>".get_caption('4370','Delete')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_usergroup INNER JOIN ".$tbl_prefix."sys_permission ON ".$tbl_prefix."sys_usergroup.gid = ".$tbl_prefix."sys_permission.group_id ORDER BY pid");
		while($db->next_record()):
			$user_permissions .= "<tr class='bg_color2'>"
				."<td>".$db->f("name")."</td>"
				."<td>".$db->f("page")."</td>"
				."<td>".translate_yesno($db->f("object_w"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("object_d"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=user&page=permissions&action=edit&pid=".$db->f("pid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=user&page=permissions&action=del&pid=".$db->f("pid"),get_caption('0211','Delete'))
				."</td>"
				."</tr>";
		endwhile;
		$user_permissions .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"user_title"           => "<h2>".get_caption('4000','Security')." / ".get_caption('4030','Permissions')."</h2>",
			"confirm_message"      => $ac->show_ok_message(GetConfirmMessage()),
			"user_permissions"     => $user_permissions,
			"user_permissions_new" => $ac->create_add_icon("index.php?mode=user&page=permissions&action=new",get_caption('4320','Add Permission'))
			));
	break;
}
?>