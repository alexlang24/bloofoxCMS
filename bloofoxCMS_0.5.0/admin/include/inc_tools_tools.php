<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_tools_tools.php -
//
// Copyrights (c) 2006 - 2012 Alexander Lang, Germany
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
	case 'optimize': // optimize tables
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['tools']['write'] == 1) {
			// run sql command
			$db->query("OPTIMIZE TABLE ".$tbl_prefix."sys_charset, ".$tbl_prefix."sys_config, ".$tbl_prefix."sys_content, ".$tbl_prefix."sys_explorer, ".$tbl_prefix."sys_lang, ".$tbl_prefix."sys_media, ".$tbl_prefix."sys_permission, ".$tbl_prefix."sys_plugin, ".$tbl_prefix."sys_profile, ".$tbl_prefix."sys_session, ".$tbl_prefix."sys_setting, ".$tbl_prefix."sys_template, ".$tbl_prefix."sys_user, ".$tbl_prefix."sys_usergroup");
			// header page
			CreateConfirmMessage(1,get_caption('5070',"Optimization successful done."));
			load_url("index.php?mode=tools");
		}
		
		// Set variables
		$tpl->set_var(array(
			"tools_title"       => "<h2>".get_caption('5000',"Tools")." / ".get_caption('5010',"Maintenance")." / ".get_caption('5030',"Optimize Database")."</h2>",
			"tools_action"      => "index.php?mode=tools&action=optimize",
			"tools_question"    => $ac->show_question_message(get_caption('5110',"Do you like to optimize the database?")),
			"tools_button_send" => "<input class='btn' type='submit' name='send' value='".get_caption('5030',"Optimize Database")."' />"
			));
	break;

	case 'inactive_user': // delete inactive user till $date
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['tools']['delete'] == 1) {
			$_POST['date_to'] = validate_date($_POST['date_to']);
			
			if(!mandatory_field($_POST['date_to']) || $_POST['date_to'] == 0) { $error = $ac->show_error_message(get_caption('9000',"Date is invalid.")); }
			
			if($error == "") {
				// run sql command
				$db->query("DELETE FROM ".$tbl_prefix."sys_user WHERE status = '0' && user_since < '".$_POST['date_to']."'");
				// header page
				CreateConfirmMessage(1,get_caption('5080',"Inactive user successful deleted."));
				load_url("index.php?mode=tools");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"tools_title"       => "<h2>".get_caption('5000',"Tools")." / ".get_caption('5010',"Maintenance")." / ".get_caption('5040',"Clear Inactive User")."</h2>",
			"tools_error"       => $error,
			"tools_action"      => "index.php?mode=tools&action=inactive_user",
			"tools_question"    => $ac->show_question_message(get_caption('5120',"Do you like to clear all inactive user?")),
			"tools_date"        => get_caption('5130',"Registered until date:")." <input type='text' name='date_to' size='10' maxlength='10' value='".date("d.m.Y",time()-3600*24*7)."' /> ".get_caption('0500',"Date Format"),
			"tools_button_send" => "<input class='btn' type='submit' name='send' value='".get_caption('5040',"Clear Inactive User")."' />"
			));
	break;
	
	case 'deleted_user': // delete deleted user
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['tools']['delete'] == 1) {
			// run sql command
			$db->query("DELETE FROM ".$tbl_prefix."sys_user WHERE deleted = '1'");
			// header page
			CreateConfirmMessage(1,get_caption('5090',"Deleted user successful deleted."));
			load_url("index.php?mode=tools");
		}
		
		// Set variables
		$tpl->set_var(array(
			"tools_title"       => "<h2>".get_caption('5000',"Tools")." / ".get_caption('5010',"Maintenance")." / ".get_caption('5050',"Delete Deleted User")."</h2>",
			"tools_action"      => "index.php?mode=tools&action=deleted_users",
			"tools_question"    => $ac->show_question_message(get_caption('5140',"Do you like to clear all deleted user?")),
			"tools_button_send" => "<input class='btn' type='submit' name='send' value='".get_caption('5050',"Delete Deleted User")."' />"
			));
	break;
	
	case 'sessions': // delete failed sessions till $date
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['tools']['delete'] == 1) {
			$_POST['date_to'] = validate_date($_POST['date_to']);
			
			if(!mandatory_field($_POST['date_to']) || $_POST['date_to'] == 0) { $error = $ac->show_error_message(get_caption('9000',"Date is invalid.")); }
			
			if($error == "") {
				// run sql command
				if($_POST['option'] == 1) {
					$db->query("DELETE FROM ".$tbl_prefix."sys_session WHERE status = '0' && timestamp < '".$_POST['date_to']."'");
				} else {
					$db->query("DELETE FROM ".$tbl_prefix."sys_session WHERE timestamp < '".$_POST['date_to']."'");
				}
				// header page
				CreateConfirmMessage(1,get_caption('5100',"Sessions successful deleted."));
				load_url("index.php?mode=tools");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"tools_title"       => "<h2>".get_caption('5000',"Tools")." / ".get_caption('5010',"Maintenance")." / ".get_caption('5060',"Clear Sessions")."</h2>",
			"tools_error"       => $error,
			"tools_action"      => "index.php?mode=tools&action=sessions",
			"tools_question"    => $ac->show_question_message(get_caption('5150',"Do you like to clear sessions?")),
			"tools_date"        => get_caption('5160',"Logins until date:")." <input type='text' name='date_to' size='10' maxlength='10'  value='".date("d.m.Y",time()-3600*24*7)."' /> ".get_caption('0500',"Date Format"),
			"tools_option"      => "<input type='checkbox' name='option' /> ".get_caption('5170',"Failed sessions only"),
			"tools_button_send" => "<input class='btn' type='submit' name='send' value='".get_caption('5060',"Clear Sessions")."' />"
			));
	break;
	
	default: // Create overview
		// Set variables
		$tpl->set_var(array(
			"tools_title"                 => "<h2>".get_caption('5000',"Tools")." / ".get_caption('5010',"Maintenance")."</h2>",
			"confirm_message"             => $ac->show_ok_message(GetConfirmMessage()),
			"tools_action_name"           => get_caption('0100',"Action"),
			"tools_activity_name"         => get_caption('0110',"Name"),
			"tools_optimize"              => "<a href='index.php?mode=tools&action=optimize'>".get_caption('5030',"Optimize Database")."</a>",
			"tools_optimize_action"       => $ac->create_edit_icon("index.php?mode=tools&action=optimize&id=1",get_caption('5030',"Optimize Database")),
			"tools_inactive_users"        => "<a href='index.php?mode=tools&action=inactive_user'>".get_caption('5040',"Clear Inactive User")."</a>",
			"tools_inactive_users_action" => $ac->create_edit_icon("index.php?mode=tools&action=inactive_users&id=1",get_caption('5040',"Clear Inactive User")),
			"tools_deleted_users"         => "<a href='index.php?mode=tools&action=deleted_user'>".get_caption('5050',"Clear Deleted User")."</a>",
			"tools_deleted_users_action"  => $ac->create_edit_icon("index.php?mode=tools&action=deleted_users&id=1",get_caption('5050',"Clear Deleted User")),
			"tools_sessions"              => "<a href='index.php?mode=tools&action=sessions'>".get_caption('5060',"Clear Sessions")."</a>",
			"tools_sessions_action"       => $ac->create_edit_icon("index.php?mode=tools&action=sessions&id=1",get_caption('5060',"Clear Sessions"))
			));
	break;
}
?>