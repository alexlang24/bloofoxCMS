<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_plugins.php -
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

// Plugin class required
require_once(SYS_FOLDER."/class_plugins.php");
$pm = new plugins();

switch($action)
{
	case 'new':
		// setup new plugin
		switch($_POST['post'])
		{
			case '2': // check install.php and insert into db
				$error = $pm->install($db,$_POST['folder']);
				
				if($error == "") {
					CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
					load_url("index.php?mode=settings&page=plugins");
				}
									
				// Set variables
				$tpl->set_var(array(
					"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3380','Add Plugin')."</h2>",
					"plugins_text"        => $error
					));
			break;
			
			case '1': // select name and confirm install
				if($sys_group_vars['demo'] == 1 || $sys_rights['set_plugins']['write'] == 0) {
					load_url("index.php?mode=settings&page=plugins");
				}
				
				if($pm->install_file_exist($_POST["folder"]."/") == 0) {
					load_url("index.php?mode=settings&page=plugins");
				}
				
				$pm->set_install_values($_POST["folder"]."/");
				if($pm->plugin_exist($db) == 0) {
					$plugin_text = "<form method='post'>"
						."<table>"
						."<tr class='bg_color2'><td>".get_caption('0150','Directory')."</td><td><input type='text' name='dir' size='30' value='/plugins/".$_POST['folder']."' disabled /></td></tr>"
						."<tr class='bg_color2'><td>".get_caption('0110','Name')."</td><td><input type='text' name='name' size='20' value='".$_POST['folder']."' disabled /></td></tr>"
						."<tr><td><input type='hidden' name='post' value='2' /></td><td><input type='hidden' name='folder' value='".$_POST['folder']."' /></td></tr>"
						."</table>"
						.$ac->create_form_button("submit",get_caption('3420','Install'))
						."</form>";
				} else {
					$plugin_text = $ac->show_error_message(get_caption('9370','Plugin (ID) already exists.')." ".$tbl_prefix."sys_plugin");
				}
				
				// Set variables
				$tpl->set_var(array(
					"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3380','Add Plugin')."</h2>",
					"plugins_text"        => $plugin_text
					));
			break;
			
			default:
				$folder = opendir($pm->plugin_dir);
				$plugin_folders = "<table class='list'>";
				$plugin_folders .= "<tr class='bg_color3'>"
					."<td width='200'><p class='bold'>".get_caption('0110','Name')."</p></td>"
					."<td><p class='bold'>".get_caption('3410','Version')."</p></td>"
					."<td><p class='bold'>".get_caption('0150','Directory')."</p></td>"
					."<td><p class='bold'>".get_caption('0100','Action')."</p></td></tr>";
				
				$plugin_array = array();
				while($dir = readdir($folder))
				{
					if($dir != "." && $dir != ".."  && is_dir($pm->plugin_dir.$dir)) // only directories
					{
						unset($pm->plugin_vars);
						if($pm->install_file_exist($dir."/")) {
							$pm->set_install_values($dir."/");
							if($pm->plugin_exist($db) == 0) {
								$plugin_array[$dir] = $pm->plugin_vars['version'];
							}
						}
					}
				}
				closedir($folder);
				
				ksort($plugin_array);
				foreach($plugin_array as $key => $val) {
					$plugin_folders .= "<tr class='bg_color2'><td>".$key."</td>"
								."<td>".$val."</td>"
								."<td>/plugins/".$key."</td>"
								."<td><form method='post'><input type='hidden' name='folder' value='".$key."' />"
								."<input type='hidden' name='post' value='1' />".$ac->create_form_button("submit",get_caption('3420','Install'))
								."</form></td></tr>";
				}
				$plugin_folders .= "</table>";
				
				// Set variables
				$tpl->set_var(array(
					"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3380','Add Plugin')."</h2>",
					"plugins_text"        => $plugin_folders
					));
			break;
		}
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_plugins']['write'] == 1) {
			$db->query("UPDATE ".$tbl_prefix."sys_plugin SET status = '".$_POST['status']."' WHERE pid = '".$_POST['pid']."' LIMIT 1");
			CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
			load_url("index.php?mode=settings&page=plugins");
		}
		
		// select record
		if(isset($_POST['pid'])) {
			$_GET['pid'] = $_POST['pid'];
		}
		$db->query("SELECT pid,name,status FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$_GET['pid']."' ORDER BY pid LIMIT 1");
		while($db->next_record()):
			$pid = $db->f("pid");
			$name = $db->f("name");
			$status = mark_selected_value($db->f("status"));
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"       => "<h2>".get_caption('3000','Administration')." / ".get_caption('3390','Edit Plugin')."</h2>",
			"tab_general"          => get_caption('0170','General'),
			"plugins_action"       => "index.php?mode=settings&page=plugins&action=edit",
			"plugins_name"         => "<p class='bold'>".$name."</p>",
			"plugins_status"       => get_caption('0250','Status'),
			"plugins_status_input" => "<select name='status'><option value='0' ".$status['1'].">".get_caption('0270','Inactive')."</option><option value='1' ".$status['2'].">".get_caption('0260','Active')."</option></select>",
			"plugins_pid"          => "<input type='hidden' name='pid' value='".$pid."' />",
			"plugins_button_send"  => $ac->create_form_button("submit",get_caption('0120','Save')),
			"plugins_button_reset" => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_plugins']['delete'] == 1) {
			$db->query("SELECT install_path FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$_POST['pid']."' LIMIT 1");
			while($db->next_record()):
				$plugin_vars['folder'] = $db->f("install_path");
			endwhile;
			
			$error = $pm->uninstall($db,$plugin_vars['folder'],$_POST['pid']);
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
				load_url("index.php?mode=settings&page=plugins");
			}
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=settings&page=plugins");
		
		// select record
		if(isset($_POST['pid'])) {
			$_GET['pid'] = $_POST['pid'];
		}
		$db->query("SELECT pid,name FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$_GET['pid']."' ORDER BY pid LIMIT 1");
		while($db->next_record()):
			$pid = $db->f("pid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3400','Delete Plugin')."</h2>",
			"plugins_action"      => "index.php?mode=settings&page=plugins&action=del",
			"plugins_error"       => $error,
			"plugins_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"plugins_name"        => "<p class='bold'>".$name."</p>",
			"plugins_pid"         => "<input type='hidden' name='pid' value='".$pid."' />",
			"plugins_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create plugins overview
		
		// Headline
		$settings_plugins .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('PluginID')."</p></td>"
			."<td width='200'><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('0150','Directory')."</p></td>"
			."<td><p class='bold'>".get_caption('Type')."</p></td>"
			."<td><p class='bold'>".get_caption('0','Version')."</p></td>"
			."<td><p class='bold'>".get_caption('0250','Status')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin ORDER BY pid");
		while($db->next_record()):
			if($db->f("admin_file") != "" && file_exists("../plugins/".$db->f("install_path").$db->f("admin_file"))) {
				$plugin_name = "<a href='index.php?mode=plugins&pluginID=".$db->f("pid")."'>".$db->f("name")."</a>";
			} else {
				$plugin_name = $db->f("name");
			}
			$settings_plugins .= "<tr class='bg_color2'>"
				."<td>".$db->f("pid")."</td>"
				."<td>".$plugin_name."</td>"
				."<td>/plugins/".$db->f("install_path")."</td>"
				."<td>".translate_yesno($db->f("plugin_type"),"Content","Module")."</td>"
				."<td>".$db->f("plugin_version")."</td>"
				."<td>".translate_yesno($db->f("status"),get_caption('0260','Active'),get_caption('0270','Inactive'))."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=settings&page=plugins&action=edit&pid=".$db->f("pid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=settings&page=plugins&action=del&pid=".$db->f("pid"),get_caption('0211','Delete'))
				."</td>"
				."</tr>";
		endwhile;
		$settings_plugins .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"settings_title"       => "<h2>".get_caption('3000','Administration')." / ".get_caption('3050','Plugins')."</h2>",
			"confirm_message"      => $ac->show_ok_message(GetConfirmMessage()),
			"settings_plugins"     => $settings_plugins,
			"settings_plugins_new" => $ac->create_add_icon("index.php?mode=settings&page=plugins&action=new",get_caption('3380','Add Plugin'))
		));
	break;
}
?>