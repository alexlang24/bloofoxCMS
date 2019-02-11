<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_content_pages.php -
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

// init structure class
$tree = new structure($s_level_no);
// init filter class
$filters = new filter();

switch($action)
{
	case 'new':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_pages']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['link_type'] = $ac->clean_int_values($_POST['link_type']);
			$_POST['link_target'] = validate_text($_POST['link_target']);
			$_POST['link_url'] = validate_text($_POST['link_url']);
			$_POST['link_plugin'] = validate_text($_POST['link_plugin']);
			$_POST['link_param'] = validate_text($_POST['link_param']);
			$_POST['keywords'] = validate_text($_POST['keywords']);
			$_POST['startdate'] = validate_date($_POST['startdate']);
			$_POST['enddate'] = validate_date($_POST['enddate']);
			$_POST['template_id'] = validate_text($_POST['template_id']);
			$_POST['description'] = validate_text($_POST['description']);
			$_POST['title'] = validate_text($_POST['title']);
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			if($_POST['startdate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9430','The starting date is invalid. Please consider the format.')); $_POST['startdate'] = ""; }
			if($_POST['enddate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9440','The ending date is invalid. Please consider the format.')); $_POST['enddate'] = ""; }
			
			$groups = $ac->get_group_names_for_insert();
						
			if($_POST['link_type'] == 2 && $_POST['link_eid'] == 0 && $error == "") {
				$error = $ac->show_error_message(get_caption('9450','You must select a shortcut, if type = Shortcut.'));
			}
			
			if($_POST['link_type'] != 3 && $_POST['link_plugin'] != 0 && $error == "") {
				$error = $ac->show_error_message(get_caption('9460','You must select type = Plugin, if you have chosen a plugin.'));
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				$s = $tree->new_line($db,$_POST['insert'],$_POST['insert_where']);
				$created_by = $_SESSION["username"];
				$created_at = time();
				
				if($s['config_id'] != 0) {
					$db->query("INSERT INTO ".$tbl_prefix."sys_explorer 
						(`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`,`description`,`title`)
						VALUES (
						'','".$s['level']."','".$s['preid']."','".$s['sorting']."','".$s['config_id']."','".$_POST['name']."','".$_POST['link_type']."','".$_POST['link_target']."','".$_POST['link_url']."','".$_POST['link_eid']."','".$_POST['link_plugin']."','".$_POST['link_param']."','".$_POST['blocked']."','".$_POST['invisible']."','".$groups."','0','".$_POST['startdate']."','".$_POST['enddate']."','".$created_by."','".$created_at."','','','".$_POST['keywords']."',".$_POST['template_id'].",'".$_POST['description']."','".$_POST['title']."')");
				}
				load_url("index.php?mode=content&page=pages");
			}
		}
		
		$sys_usergroups = $ac->get_sys_usergroups("","new");
		
		$blocked = mark_selected_value($_POST['blocked']);
		$invisible = mark_selected_value($_POST['invisible']);
		
		if(!empty($_POST['startdate'])) {
			$_POST['startdate'] = date("d.m.Y",$_POST['startdate']);
		}
		if(!empty($_POST['enddate'])) {
			$_POST['enddate'] = date("d.m.Y",$_POST['enddate']);
		}
		
		$type_checked = array();
		$type_checked['1'] = "";
		$type_checked['2'] = "";
		$type_checked['3'] = "";
		if($_POST['link_type'] == 1) { $type_checked['1'] = "selected='selected'"; }
		if($_POST['link_type'] == 2) { $type_checked['2'] = "selected='selected'"; }
		if($_POST['link_type'] == 3) { $type_checked['3'] = "selected='selected'"; }
		
		$sys_link_type = "";
		$sys_link_type .= "<option value='0'>".get_caption('2100','Standard')."</option>";
		$sys_link_type .= "<option value='1' ".$type_checked['1'].">".get_caption('2110','URL')."</option>";
		$sys_link_type .= "<option value='2' ".$type_checked['2'].">".get_caption('2120','Shortcut')."</option>";
		$sys_link_type .= "<option value='3' ".$type_checked['3'].">".get_caption('2130','Plugin')."</option>";

		$link_eid = "";
		$insert_eid = "";
		
		$plugins = "";
		$db->query("SELECT pid,name FROM ".$tbl_prefix."sys_plugin WHERE plugin_type = '1' ORDER BY pid");
		while($db->next_record()):
			if($_POST['link_plugin'] == $db->f("pid")) {
				$plugins .= "<option value='".$db->f("pid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$plugins .= "<option value='".$db->f("pid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$sys_templates = $ac->get_sys_templates($_POST['template_id']);
		
		// Set variables
		$tpl->set_var(array(
			"content_title"          => "<h2>".get_caption('2000','Contents')." / ".get_caption('2050','Add Page')."</h2>",
			"tab_general"            => get_caption('0170','General'),
			"tab_options"            => get_caption('0180','Options'),
			"tab_security"           => get_caption('0190','Security'),
			"expl_action"            => "index.php?mode=content&page=pages&action=new",
			"expl_error"             => $error,
			"expl_name"              => get_caption('0110','Name'),
			"expl_name_input"        => "<input type='text' name='name' size='30' maxlength='250' value='".$_POST['name']."' />",
			"expl_title"             => get_caption('2400','Title'),
			"expl_title_input"       => "<input type='text' name='title' size='30' maxlength='250' value='".$_POST['title']."' />",
			"expl_link_type"         => get_caption('2140','Type'),
			"expl_link_type_input"   => "<select name='link_type'>".$sys_link_type."</select>",
			"expl_link_target"       => get_caption('2150','Target'),
			"expl_link_target_input" => "<input type='text' name='link_target' size='20' maxlength='250' value='".$_POST['link_target']."' />",
			"expl_link_url"          => get_caption('2110','URL'),
			"expl_link_url_input"    => "<input type='text' name='link_url' size='40' maxlength='250' value='".$_POST['link_url']."' />",
			"expl_link_eid"          => get_caption('2120','Shortcut'),
			"expl_link_eid_input"    => "<select name='link_eid'><option value='0'>".get_caption('2160','- None -')."</option>".get_structure($content_expl,0,$_POST['link_eid'])."</select>",
			"expl_link_plugin"       => get_caption('2130','Plugin'),
			"expl_link_plugin_input" => "<select name='link_plugin'><option value='0'>".get_caption('2160','- None -')."</option>".$plugins."</select>",
			"expl_link_param"        => get_caption('2290','Parameter'),
			"expl_link_param_input"  => "<input type='text' name='link_param' size='20' maxlength='80' value='".$_POST['link_param']."' />",
			"expl_blocked"           => get_caption('2170','Blocked'),
			"expl_blocked_input"     => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
			"expl_invisible"         => get_caption('2180','Hidden'),
			"expl_invisible_input"   => "<select name='invisible'><option value='0' ".$invisible['1'].">".get_caption('0140','No')."</option><option value='1' ".$invisible['2'].">".get_caption('0130','Yes')."</option></select>",
			"expl_groups"            => get_caption('4020','User Groups'),
			"expl_groups_input"      => $sys_usergroups,
			"expl_startdate"         => get_caption('2200','Starting Date'),
			"expl_startdate_input"   => "<input type='text' name='startdate' size='10' maxlength='10' value='".$_POST['startdate']."' /> ".get_caption('0500','DD.MM.YYYY'),
			"expl_enddate"           => get_caption('2210','Ending Date'),
			"expl_enddate_input"     => "<input type='text' name='enddate' size='10' maxlength='10' value='".$_POST['enddate']."' /> ".get_caption('0500','DD.MM.YYYY'),
			"expl_keywords"          => get_caption('2220','Keywords'),
			"expl_keywords_input"    => "<textarea name='keywords' cols='50' rows='4'>".$_POST['keywords']."</textarea>",
			"expl_template"          => get_caption('3240','Template'),
			"expl_template_input"    => "<select name='template_id'><option value='0'>".get_caption('2160','- None -')."</option>".$sys_templates."</select>",
			"expl_description"       => get_caption('3690','Description'),
			"expl_description_input" => "<textarea name='description' cols='50' rows='4'>".$_POST['description']."</textarea>",
			"expl_insert"            => get_caption('2230','Insert After'),
			"expl_insert_input"      => "<select name='insert'>".get_structure($content_expl,0,$_POST['insert'])."</select>",
			"expl_insert_where"      => "<input type='radio' name='insert_where' value='0' checked='checked' /> ".get_caption('2300','Same Level')." <input type='radio' name='insert_where' value='1' /> ".get_caption('2310','Underneath'),
			"expl_button_send"       => $ac->create_form_button("submit",get_caption('2050','Add Page'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_pages']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['link_target'] = validate_text($_POST['link_target']);
			$_POST['link_url'] = validate_text($_POST['link_url']);
			$_POST['link_plugin'] = validate_text($_POST['link_plugin']);
			$_POST['link_param'] = validate_text($_POST['link_param']);
			$_POST['keywords'] = validate_text($_POST['keywords']);
			$_POST['template_id'] = validate_text($_POST['template_id']);
			$_POST['sorting'] = validate_text($_POST['sorting']);
			$_POST['description'] = validate_text($_POST['description']);
			$_POST['title'] = validate_text($_POST['title']);
			
			if(!empty($_POST['startdate'])) {
				$_POST['startdate'] = validate_date($_POST['startdate']);
			}
			if(!empty($_POST['enddate'])) {
				$_POST['enddate'] = validate_date($_POST['enddate']);
			}
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			if($_POST['startdate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9430','The starting date is invalid. Please consider the format.')); }
			if($_POST['enddate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9440','The ending date is invalid. Please consider the format.')); }
			
			if(mandatory_field($_POST['name'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET name = '".$_POST['name']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			}
			
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET title = '".$_POST['title']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			if($_POST['link_type'] == 2 && $_POST['link_eid'] == 0 && $error == "") {
				$error = $ac->show_error_message(get_caption('9450','You must select a shortcut, if type = Shortcut.'));
			}
			if(($_POST['link_type'] == 2 && $_POST['link_eid'] != 0) || $_POST['link_type'] != 2) {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_type = '".$_POST['link_type']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			}
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_target = '".$_POST['link_target']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_url = '".$_POST['link_url']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_eid = '".$_POST['link_eid']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			if($_POST['link_type'] != 3 && $_POST['link_plugin'] != 0 && $error == "") {
				$error = $ac->show_error_message(get_caption('9460','You must select type = Plugin, if you have chosen a plugin.'));
			} else {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_plugin = '".$_POST['link_plugin']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			}
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET link_param = '".$_POST['link_param']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET blocked = '".$_POST['blocked']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET invisible = '".$_POST['invisible']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			if(strlen($_POST['startdate']) == 0 || $_POST['startdate'] > 0) {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET startdate = '".$_POST['startdate']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			}
			if(strlen($_POST['enddate']) == 0 || $_POST['enddate'] > 0) {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET enddate = '".$_POST['enddate']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			}
			
			$changed_by = $_SESSION["username"];
			$changed_at = time();
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET changed_by = '".$changed_by."', changed_at = '".$changed_at."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET keywords = '".$_POST['keywords']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET template_id = '".$_POST['template_id']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET description = '".$_POST['description']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			$groups = $ac->get_group_names_for_insert();
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET groups = '".$groups."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET sorting = '".$_POST['sorting']."' WHERE eid = '".$_POST['eid']."' LIMIT 1");
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=content&page=pages");
			}
		}
		
		// select record
		if(isset($_POST['eid'])) {
			$_GET['eid'] = $_POST['eid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$_GET['eid']."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$eid = $db->f("eid");
			$name = $db->f("name");
			$title = $db->f("title");
			$link_type = $db->f("link_type");
			$link_target = $db->f("link_target");
			$link_url = $db->f("link_url");
			$link_eid = $db->f("link_eid");
			$link_plugin = $db->f("link_plugin");
			$link_param = $db->f("link_param");
			$blocked = $db->f("blocked");
			$invisible = $db->f("invisible");
			$groups = $db->f("groups");
			$groups = explode(",",$groups);
			$startdate = $db->f("startdate");
			$enddate = $db->f("enddate");
			$keywords = $db->f("keywords");
			$template_id = $db->f("template_id");
			$sorting = $db->f("sorting");
			$description = $db->f("description");
			$created_by = $db->f("created_by");
			if($db->f("created_at") != 0) {
				$created_at = date($sys_vars['datetime'],$db->f("created_at"));
			}
			$changed_by = $db->f("changed_by");
			if($db->f("changed_at") != 0) {
				$changed_at = date($sys_vars['datetime'],$db->f("changed_at"));
			}
		endwhile;
		
		$sys_usergroups = $ac->get_sys_usergroups($groups,"edit");
				
		$blocked = mark_selected_value($blocked);
		$invisible = mark_selected_value($invisible);
		
		if(!empty($startdate)) {
			$startdate = date("d.m.Y",$startdate);
		}
		if(!empty($enddate)) {
			$enddate = date("d.m.Y",$enddate);
		}
		
		$type_checked = array();
		$type_checked['1'] = "";
		$type_checked['2'] = "";
		$type_checked['3'] = "";
		if($link_type == 1) { $type_checked['1'] = "selected='selected'"; }
		if($link_type == 2) { $type_checked['2'] = "selected='selected'"; }
		if($link_type == 3) { $type_checked['3'] = "selected='selected'"; }
		
		$sys_link_type = "";
		$sys_link_type .= "<option value='0'>".get_caption('2100','Standard')."</option>";
		$sys_link_type .= "<option value='1' ".$type_checked['1'].">".get_caption('2110','URL')."</option>";
		$sys_link_type .= "<option value='2' ".$type_checked['2'].">".get_caption('2120','Shortcut')."</option>";
		$sys_link_type .= "<option value='3' ".$type_checked['3'].">".get_caption('2130','Plugin')."</option>";
		
		$sys_link_eid = "";
		
		$plugins = "";
		$db->query("SELECT pid,name FROM ".$tbl_prefix."sys_plugin WHERE plugin_type = '1' ORDER BY pid");
		while($db->next_record()):
			if($link_plugin == $db->f("pid")) {
				$plugins .= "<option value='".$db->f("pid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$plugins .= "<option value='".$db->f("pid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$sys_templates = $ac->get_sys_templates($template_id);
				
		// Set variables
		$tpl->set_var(array(
			"content_title"          => "<h2>".get_caption('2000','Contents')." / ".get_caption('2060','Edit Page')."</h2>",
			"tab_general"            => get_caption('0170','General'),
			"tab_options"            => get_caption('0180','Options'),
			"tab_security"           => get_caption('0190','Security'),
			"expl_action"            => "index.php?mode=content&page=pages&action=edit",
			"expl_error"             => $error,
			"expl_name"              => get_caption('0110','Name'),
			"expl_name_input"        => "<input type='text' name='name' size='30' maxlength='250' value='".$name."' />",
			"expl_title"             => get_caption('2400','Title'),
			"expl_title_input"       => "<input type='text' name='title' size='30' maxlength='250' value='".$title."' />",
			"expl_link_type"         => get_caption('2140','Type'),
			"expl_link_type_input"   => "<select name='link_type'>".$sys_link_type."</select>",
			"expl_link_target"       => get_caption('2150','Target'),
			"expl_link_target_input" => "<input type='text' name='link_target' size='20' maxlength='250' value='".$link_target."' />",
			"expl_link_url"          => get_caption('2110','URL'),
			"expl_link_url_input"    => "<input type='text' name='link_url' size='40' maxlength='250' value='".$link_url."' />",
			"expl_link_eid"          => get_caption('2120','Shortcut'),
			"expl_link_eid_input"    => "<select name='link_eid'><option value='0'>".get_caption('2160','- None -')."</option>".get_structure($content_expl,0,$link_eid)."</select>",
			"expl_link_plugin"       => get_caption('2130','Plugin'),
			"expl_link_plugin_input" => "<select name='link_plugin'><option value='0'>".get_caption('2160','- None -')."</option>".$plugins."</select>",
			"expl_link_param"        => get_caption('2290','Parameter'),
			"expl_link_param_input"  => "<input type='text' name='link_param' size='20' maxlength='80' value='".$link_param."' />",
			"expl_blocked"           => get_caption('2170','Blocked'),
			"expl_blocked_input"     => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
			"expl_invisible"         => get_caption('2180','Hidden'),
			"expl_invisible_input"   => "<select name='invisible'><option value='0' ".$invisible['1'].">".get_caption('0140','No')."</option><option value='1' ".$invisible['2'].">".get_caption('0130','Yes')."</option></select>",
			"expl_groups"            => get_caption('4020','User Groups'),
			"expl_groups_input"      => $sys_usergroups,
			"expl_startdate"         => get_caption('2200','Starting Date'),
			"expl_startdate_input"   => "<input type='text' name='startdate' size='10' maxlength='10' value='".$startdate."' /> ".get_caption('0500','DD.MM.YYYY'),
			"expl_enddate"           => get_caption('2210','Ending Date'),
			"expl_enddate_input"     => "<input type='text' name='enddate' size='10' maxlength='10' value='".$enddate."' /> ".get_caption('0500','DD.MM.YYYY'),
			"expl_keywords"          => get_caption('2220','Keywords'),
			"expl_keywords_input"    => "<textarea name='keywords' cols='50' rows='4'>".$keywords."</textarea>",
			"expl_template"          => get_caption('3240','Template'),
			"expl_template_input"    => "<select name='template_id'><option value='0'>".get_caption('2160','- None -')."</option>".$sys_templates."</select>",
			"expl_sorting"           => get_caption('2460','Sorting'),
			"expl_sorting_input"     => "<input type='text' name='sorting' size='10' maxlength='10' value='".$sorting."' />",
			"expl_description"       => get_caption('3690','Description'),
			"expl_description_input" => "<textarea name='description' cols='50' rows='4' maxlength='500'>".$description."</textarea>",
			"expl_created_by"        => get_caption('2320','Created by'),
			"expl_created_by_info"   => $created_by,
			"expl_created_at"        => get_caption('2330','Created at'),
			"expl_created_at_info"   => $created_at,
			"expl_changed_by"        => get_caption('2340','Changed by'),
			"expl_changed_by_info"   => $changed_by,
			"expl_changed_at"        => get_caption('2350','Changed at'),
			"expl_changed_at_info"   => $changed_at,
			"expl_eid"               => "<input type='hidden' name='eid' value='".$eid."' />",
			"expl_button_send"       => $ac->create_form_button("submit",get_caption('0120','Save')),
			"expl_button_reset"      => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_pages']['delete'] == 1) {
			$db->query("DELETE FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$_POST['eid']."' LIMIT 1");
			$db->query("DELETE FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$_POST['eid']."' ORDER BY explorer_id");
			// modify direct underneath entrie(s)
			if($_POST['preid'] > 0) {
				$db->query("UPDATE ".$tbl_prefix."sys_explorer SET preid = '".$_POST['preid']."',level = level-1 WHERE preid = '".$_POST['eid']."'");
			}
			CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			load_url("index.php?mode=content&page=pages");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=content&page=pages");
		
		// select record
		if(isset($_POST['eid'])) {
			$_GET['eid'] = $_POST['eid'];
		}
		$db->query("SELECT eid,name,preid,level FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$_GET['eid']."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$eid = $db->f("eid");
			$name = $db->f("name");
			$preid = $db->f("preid");
			$level = $db->f("level");
		endwhile;
		
		if($level == 1) {
			load_url("index.php?mode=content&page=pages");
		}
		
		// Set variables
		$tpl->set_var(array(
			"content_title"    => "<h2>".get_caption('2000','Contents')." / ".get_caption('2070','PageDelete')."</h2>",
			"expl_action"      => "index.php?mode=content&page=pages&action=del",
			"expl_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"expl_name"        => "<p class='bold'>".$name."</p>",
			"expl_eid"         => "<input type='hidden' name='eid' value='".$eid."' /><input type='hidden' name='preid' value='".$preid."' />",
			"expl_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;

	default:
		// Create explorer overview
		$content_expl = "";
		
		// Filter
		if(isset($_POST["send"])) {
			if($_POST["filter_content"] == "") {
				unset($_SESSION["filter_content"]);
			} else {
				$_SESSION["filter_content"] = $_POST["filter_content"];
			}
			load_url("index.php?mode=content&page=pages");
		}
		
		$content_filter = $filters->create_filter_content($db);
		
		// Headline
		$content_expl .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('','ID')."</p></td>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('2140','Type')."</p></td>"
			."<td><p class='bold'>".get_caption('2170','Blocked')."</p></td>"
			."<td><p class='bold'>".get_caption('2180','Hidden')."</p></td>"
			//."<td><p class='bold'>".get_caption('2320','Created by')."</p></td>"
			//."<td><p class='bold'>".get_caption('2330','Created at')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Init libraries
		$db = new DB_Tpl();
		
		// settings
		$start = $_GET['start'];
		$limit = $s_limit; // default: 20
		
		if(isset($_SESSION["filter_content"])) {
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_SESSION["filter_content"]."'");
		} else {
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer");
		}
		$total = $db->num_rows();
		
		// create page handling
		$page_html_code = $ac->create_page_handling($total,$limit,"index.php?mode=content&page=pages&start=",$start);
		$start = $ac->get_page_start($start,$limit);
		
		// Lines
		if(isset($_SESSION["filter_content"])) {
			$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_SESSION["filter_content"]."' ORDER BY name LIMIT ".$start.",".$limit."");
		} else {
			$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer ORDER BY name LIMIT ".$start.",".$limit."");
		}
		
		while($db->next_record()):
			switch($db->f("link_type"))
			{
				case '3': $link_type = get_caption('2130','Plugin'); break;
				case '2': $link_type = get_caption('2120','Shortcut'); break;
				case '1': $link_type = get_caption('2110','URL'); break;
				default: $link_type = get_caption('2100','Standard'); break;
			}
			
			$created_at = "";
			if($db->f("created_at") != "") {
				$created_at = date($sys_vars['datetime'],$db->f("created_at"));
			}
			
			$content_expl .= "<tr class='bg_color2'>"
				."<td>".$db->f("eid")."</td>"
				."<td><p><a href='index.php?mode=content&page=content&eid=".$db->f("eid")."' title='".get_caption('2330','Created at').": ".date($sys_vars['datetime'],$db->f("created_at"))." - ".get_caption('2320','Created by').": ".$db->f("created_by")."'><span".$bold.">".$db->f("name")."</span></a></p></td>"
				."<td>".$link_type."</td>"
				."<td>".translate_yesno($db->f("blocked"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".translate_yesno($db->f("invisible"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				//."<td>".$db->f("created_by")."</td>"
				//."<td>".$created_at."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=content&page=pages&action=edit&eid=".$db->f("eid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=content&page=pages&action=del&eid=".$db->f("eid"),get_caption('0211','Delete'))
				.$ac->create_editcontent_icon("index.php?mode=content&page=content&eid=".$db->f("eid"),get_caption('0221','Articles'))
				.$ac->create_preview_icon("../index.php?page=".$db->f("eid"),get_caption('0421','Preview'))
				."</td>"
				."</tr>";
		endwhile;
		$content_expl .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"content_title"    => "<h2>".get_caption('2000','Contents')." / ".get_caption('2020','Pages')."</h2>",
			"confirm_message"  => $ac->show_ok_message(GetConfirmMessage()),
			"expl_overview"    => $content_expl,
			"expl_filter"      => $content_filter,
			"page_handling"    => $page_html_code,
			"expl_new"         => $ac->create_add_icon("index.php?mode=content&page=pages&action=new",get_caption('2050','Add Page'))
			));
	break;
}

// Parse template with variables
$tpl->parse("content_handle", "content", true);
?>