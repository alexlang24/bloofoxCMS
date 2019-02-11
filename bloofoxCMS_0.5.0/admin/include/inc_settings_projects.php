<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_projects.php -
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
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['projects']['write'] == 1) {
			$ac->validate_post_vars_project();
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			if(!mandatory_field($_POST['meta_title']) && $error == "") { $error = $ac->show_error_message(get_caption('9360','You must enter a title.')); }
			
			$groups = $ac->get_group_names_for_insert();
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				// insert config
				$db->query("INSERT INTO ".$tbl_prefix."sys_config
					(`cid`,`lang_id`,`urls`,`tmpl_id`,`name`,`root_id`,`user_groups`,`meta_title`,`meta_desc`,`meta_keywords`,`meta_author`,`meta_charset`,`meta_doctype`,`meta_copyright`,`mod_rewrite`,`mail`,`default_group`) VALUES
					('','".$_POST['lang_id']."','".$_POST['urls']."','".$_POST['tmpl_id']."','".$_POST['name']."','','".$groups."','".$_POST['meta_title']."','".$_POST['meta_desc']."','".$_POST['meta_keywords']."','".$_POST['meta_author']."','".$_POST['meta_charset']."','".$_POST['meta_doctype']."','".$_POST['meta_copyright']."','".$_POST['mod_rewrite']."','".$_POST['mail']."','".$_POST['default_group']."')");
				$db->query("SELECT cid,name FROM ".$tbl_prefix."sys_config ORDER BY cid DESC LIMIT 1");
				while($db->next_record()):
					$config_id = $db->f("cid");
					$config_name = $db->f("name");
				endwhile;
				// insert explorer
				$created_by = $_SESSION["username"];
				$created_at = time();
				$db->query("INSERT INTO ".$tbl_prefix."sys_explorer 
					(`eid`,`level`,`preid`,`sorting`,`config_id`,`name`,`link_type`,`link_target`,`link_url`,`link_eid`,`link_plugin`,`link_param`,`blocked`,`invisible`,`groups`,`sub_perm`,`startdate`,`enddate`,`created_by`,`created_at`,`changed_by`,`changed_at`,`keywords`,`template_id`) VALUES
					('','1','','10000','".$config_id."','".$config_name."','0','','','','','','0','','','','','','".$created_by."','".$created_at."','','','',0)");
				// update config with root page
				$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$config_id."' ORDER BY eid DESC LIMIT 1");
				while($db->next_record()):
					$expl_id = $db->f("eid");
				endwhile;
				$db->query("UPDATE ".$tbl_prefix."sys_config SET root_id = '".$expl_id."' WHERE cid = '".$config_id."' LIMIT 1");
				// header page
				load_url("index.php?mode=settings&page=projects");
			}
		}
		
		$sys_usergroups = $ac->get_sys_usergroups("","new");
		$sys_lang = $ac->get_sys_languages($_POST['lang_id']);
		$sys_tmpl = $ac->get_sys_templates($_POST['tmpl_id']);
		$sys_charset = $ac->get_sys_charset($_POST['meta_charset']);
		$sys_groups = $ac->get_sys_groups($_POST['default_group']);
		$doctypes = $ac->get_doctypes($_POST['meta_doctype']);
		
		$mod_rewrite = mark_selected_value($_POST['mod_rewrite']);
		$user_deleted = mark_selected_value($_POST['user_deleted']);
		
		// Set variables
		$tpl->set_var(array(
			"config_title"                => "<h2>".get_caption('3000','Administration')." / ".get_caption('3200','Add Project')."</h2>",
			"tab_general"                 => get_caption('0170','General'),
			"tab_options"                 => get_caption('0180','Options'),
			"tab_security"                => get_caption('0190','Security'),
			"config_action"               => "index.php?mode=settings&page=projects&action=new",
			"config_error"                => $error,
			"config_name"                 => get_caption('0110','Name'),
			"config_name_input"           => "<input type='text' name='name' size='30' maxlength='250' value='".$_POST['name']."' />",
			"config_lang_id"              => get_caption('3230','Language'),
			"config_lang_id_input"        => "<select name='lang_id'>".$sys_lang."</select>",
			"config_templ_id"             => get_caption('3240','Template'),
			"config_templ_id_input"       => "<select name='tmpl_id'>".$sys_tmpl."</select>",
			"config_urls"                 => get_caption('3250','URL(s)'),
			"config_urls_input"           => "<textarea name='urls' cols='50' rows='4'>".$_POST['urls']."</textarea>",
			"config_groups"               => get_caption('4020','User Groups'),
			"config_groups_input"         => $sys_usergroups,
			"config_meta_title"           => get_caption('3270','Title'),
			"config_meta_title_input"     => "<input type='text' name='meta_title' size='50' maxlength='250' value='".$_POST['meta_title']."' />",
			"config_meta_desc"            => get_caption('3280','Meta-Description'),
			"config_meta_desc_input"      => "<textarea name='meta_desc' cols='50' rows='4' maxlength='500'>".$_POST['meta_desc']."</textarea>",
			"config_meta_keywords"        => get_caption('3290','Meta-Keywords'),
			"config_meta_keywords_input"  => "<textarea name='meta_keywords' cols='50' rows='4' maxlength='500'>".$_POST['meta_keywords']."</textarea>",
			"config_meta_author"          => get_caption('3300','Meta-Author'),
			"config_meta_author_input"    => "<input type='text' name='meta_author' size='30' maxlength='250' value='".$_POST['meta_author']."' />",
			"config_meta_charset"         => get_caption('3310','Meta-Charset'),
			"config_meta_charset_input"   => "<select name='meta_charset'>".$sys_charset."</select>",
			"config_meta_doctype"         => get_caption('3320','Meta-DocType'),
			"config_meta_doctype_input"   => "<select name='meta_doctype'>".$doctypes."</select>",
			"config_meta_copyright"       => get_caption('3330','(Meta-)Copyright'),
			"config_meta_copyright_input" => "<input type='text' name='meta_copyright' size='50' maxlength='250' value='".$_POST['meta_copyright']."' />",
			"config_mod_rewrite"          => get_caption('3340','mod_rewrite (URL rewrite rules)'),
			"config_mod_rewrite_input"    => "<select name='mod_rewrite'><option value='0' ".$mod_rewrite['1'].">".get_caption('0140','No')."</option><option value='1' ".$mod_rewrite['2'].">".get_caption('0130','Yes')."</option></select>",
			"config_mail"                 => get_caption('3370','E-Mail'),
			"config_mail_input"           => "<input type='text' name='mail' size='50' maxlength='250' value='".$_POST['mail']."' />",
			"config_defaultgroup"         => get_caption('3350','DefaultGroup'),
			"config_defaultgroup_input"   => "<select name='default_group'>".$sys_groups."</select>",
			"config_button_send"          => $ac->create_form_button("submit",get_caption('3200','Add Project'))
			));
	break;
	
	case 'edit':
		if((isset($_POST['send']) || isset($_POST['sendclose'])) && $sys_group_vars['demo'] == 0 && $sys_rights['projects']['write'] == 1) {
			$ac->validate_post_vars_project();
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			if(!mandatory_field($_POST['meta_title']) && $error == "") { $error = $ac->show_error_message(get_caption('9360','You must enter a title.')); }
			
			if(mandatory_field($_POST['name'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_config SET name = '".$_POST['name']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			}
			
			$db->query("UPDATE ".$tbl_prefix."sys_config SET lang_id = '".$_POST['lang_id']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET tmpl_id = '".$_POST['tmpl_id']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET urls = '".$_POST['urls']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET root_id = '".$_POST['root_id']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			
			$groups = $ac->get_group_names_for_insert();
			$db->query("UPDATE ".$tbl_prefix."sys_config SET user_groups = '".$groups."' WHERE cid = '".$_POST['cid']."' LIMIT 1");

			if(mandatory_field($_POST['meta_title'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_title = '".$_POST['meta_title']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			}
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_desc = '".$_POST['meta_desc']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_keywords = '".$_POST['meta_keywords']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_author = '".$_POST['meta_author']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_charset = '".$_POST['meta_charset']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_doctype = '".$_POST['meta_doctype']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET meta_copyright = '".$_POST['meta_copyright']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			
			$db->query("UPDATE ".$tbl_prefix."sys_config SET mod_rewrite = '".$_POST['mod_rewrite']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_config SET mail = '".$_POST['mail']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			
			$db->query("UPDATE ".$tbl_prefix."sys_config SET default_group = '".$_POST['default_group']."' WHERE cid = '".$_POST['cid']."' LIMIT 1");
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				if(isset($_POST['sendclose'])) {
					load_url("index.php?mode=settings&page=projects");
				} else {
					load_url("index.php?mode=settings&page=projects&action=edit&cid=".$_POST['cid']);
				}
			}
		}
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_config WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$name = $db->f("name");
			$lang_id = $db->f("lang_id");
			$tmpl_id = $db->f("tmpl_id");
			$urls = $db->f("urls");
			$root_id = $db->f("root_id");
			$groups = $db->f("user_groups");
			$groups = explode(",",$groups);
			$meta_title = $db->f("meta_title");
			$meta_desc = $db->f("meta_desc");
			$meta_keywords = $db->f("meta_keywords");
			$meta_author = $db->f("meta_author");
			$meta_charset = $db->f("meta_charset");
			$meta_doctype = $db->f("meta_doctype");
			$meta_copyright = $db->f("meta_copyright");
			$mod_rewrite = mark_selected_value($db->f("mod_rewrite"));
			$mail = $db->f("mail");
			$default_group = $db->f("default_group");
		endwhile;
		
		$sys_usergroups = $ac->get_sys_usergroups($groups,"edit");
		$sys_lang = $ac->get_sys_languages($lang_id);
		$sys_tmpl = $ac->get_sys_templates($tmpl_id);
		$sys_charset = $ac->get_sys_charset($meta_charset);
		$sys_groups = $ac->get_sys_groups($default_group);
		$doctypes = $ac->get_doctypes($meta_doctype);
		
		$db->query("SELECT eid,name FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$root_id."' LIMIT 1");
		while($db->next_record()):
			$sys_expl .= "<option value='".$db->f("eid")."'>".$db->f("name")."</option>";
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"config_title"                => "<h2>".get_caption('3000','Administration')." / ".get_caption('3210','Edit Project')."</h2>",
			"confirm_message"             => $ac->show_ok_message(GetConfirmMessage()),
			"tab_general"                 => get_caption('0170','General'),
			"tab_options"                 => get_caption('0180','Options'),
			"tab_security"                => get_caption('0190','Security'),
			"config_action"               => "index.php?mode=settings&page=projects&action=edit",
			"config_error"                => $error,
			"config_name"                 => get_caption('0110','Name'),
			"config_name_input"           => "<input type='text' name='name' size='30' maxlength='250' value='".$name."' />",
			"config_lang_id"              => get_caption('3230','Language'),
			"config_lang_id_input"        => "<select name='lang_id'>".$sys_lang."</select>",
			"config_templ_id"             => get_caption('3240','Template'),
			"config_templ_id_input"       => "<select name='tmpl_id'>".$sys_tmpl."</select>",
			"config_urls"                 => get_caption('3250','URL(s) (Type one per line without slash at the end)'),
			"config_urls_input"           => "<textarea name='urls' cols='50' rows='4'>".$urls."</textarea>",
			"config_root_id"              => get_caption('3260','Root Page'),
			"config_root_id_input"        => "<select name='root_id'>".$sys_expl."</select>",
			"config_groups"               => get_caption('4020','User Groups'),
			"config_groups_input"         => $sys_usergroups,
			"config_meta_title"           => get_caption('3270','Title'),
			"config_meta_title_input"     => "<input type='text' name='meta_title' size='50' maxlength='250' value='".$meta_title."' />",
			"config_meta_desc"            => get_caption('3280','Meta-Description'),
			"config_meta_desc_input"      => "<textarea name='meta_desc' cols='50' rows='4' maxlength='500'>".$meta_desc."</textarea>",
			"config_meta_keywords"        => get_caption('3290','Meta-Keywords'),
			"config_meta_keywords_input"  => "<textarea name='meta_keywords' cols='50' rows='4' maxlength='500'>".$meta_keywords."</textarea>",
			"config_meta_author"          => get_caption('3300','Meta-Author'),
			"config_meta_author_input"    => "<input type='text' name='meta_author' size='30' maxlength='250' value='".$meta_author."' />",
			"config_meta_charset"         => get_caption('3310','Meta-Charset'),
			"config_meta_charset_input"   => "<select name='meta_charset'>".$sys_charset."</select>",
			"config_meta_doctype"         => get_caption('3320','Meta-DocType'),
			"config_meta_doctype_input"   => "<select name='meta_doctype'>".$doctypes."</select>",
			"config_meta_copyright"       => get_caption('3330','Meta-Copyright'),
			"config_meta_copyright_input" => "<input type='text' name='meta_copyright' size='50' maxlength='250' value='".$meta_copyright."' />",
			"config_mod_rewrite"          => get_caption('3340','mod_rewrite (URL rewrite rules)'),
			"config_mod_rewrite_input"    => "<select name='mod_rewrite'><option value='0' ".$mod_rewrite['1'].">".get_caption('No')."</option><option value='1' ".$mod_rewrite['2'].">".get_caption('Yes')."</option></select>",
			"config_mail"                 => get_caption('3370','E-Mail'),
			"config_mail_input"           => "<input type='text' name='mail' size='50' maxlength='250' value='".$mail."' />",
			"config_defaultgroup"         => get_caption('3350','Default User Group'),
			"config_defaultgroup_input"   => "<select name='default_group'>".$sys_groups."</select>",
			"config_cid"                  => "<input type='hidden' name='cid' value='".$cid."' />",
			"config_button_send"          => $ac->create_form_button("submit",get_caption('0120','Save'))." ".$ac->create_form_button("submit",get_caption('0121','Save & Close'),"btn","sendclose"),
			"config_button_reset"         => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['projects']['delete'] == 1) {
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_POST['cid']."'");
			$total = $db->num_rows();
			if($total < 2) {
				while($db->next_record()):
					$eid = $db->f("eid");
				endwhile;
				// delete contents
				$db->query("DELETE FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$eid."'");
				// delete root page
				$db->query("DELETE FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_POST['cid']."'");
				// delete config
				$db->query("DELETE FROM ".$tbl_prefix."sys_config WHERE cid = '".$_POST['cid']."' LIMIT 1");
				CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			}
			load_url("index.php?mode=settings&page=projects");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=settings&page=projects");
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		$db->query("SELECT cid,name FROM ".$tbl_prefix."sys_config WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"config_title"       => "<h2>".get_caption('3000','Administration')." / ".get_caption('3220','Delete Project')."</h2>",
			"config_action"      => "index.php?mode=settings&page=projects&action=del",
			"config_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"config_name"        => "<p class='bold'>".$name."</p>",
			"config_cid"         => "<input type='hidden' name='cid' value='".$cid."' />",
			"config_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;

	default: // Projects overview
		
		// Create config overview
		$config_table .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('3230','Language')."</p></td>"
			."<td><p class='bold'>".get_caption('3240','Template')."</p></td>"
			."<td><p class='bold'>".get_caption('3340','mod_rewrite')."</p></td>"
			."<td><p class='bold'>".get_caption('4020','User Groups')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_config ORDER BY name");
		while($db->next_record()):
			$url = explode("\n",$db->f("urls"));
			$config_table .= "<tr class='bg_color2'>"
				."<td><a href='".$url[0]."' target='_blank' title='".$db->f("meta_title")."'>".$db->f("name")."</a></td>"
				."<td>".$ac->get_lang_name($db->f("lang_id"))."</td>"
				."<td>".$ac->get_tmpl_name($db->f("tmpl_id"))."</td>"
				."<td>".translate_yesno($db->f("mod_rewrite"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".$db->f("user_groups")."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=settings&page=projects&action=edit&cid=".$db->f("cid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=settings&page=projects&action=del&cid=".$db->f("cid"),get_caption('0211','Delete'))
				.$ac->create_preview_icon($url[0],get_caption('0421','Preview'))
				."</td>"
				."</tr>";
		endwhile;
		$config_table .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"config_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3190','Projects')."</h2>",
			"confirm_message" => $ac->show_ok_message(GetConfirmMessage()),
			"config_table"    => $config_table,
			"config_new"      => $ac->create_add_icon("index.php?mode=settings&page=projects&action=new",get_caption('3200','Add Project'))
			));
	break;
}
?>