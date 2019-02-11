<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_tmpl.php -
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
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_tmpl']['write'] == 1) {
			$_POST['name2'] = validate_text($_POST['name2']);
			
			if($_POST['name'] == "new") {
				if(!mandatory_field($_POST['name2']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
				
				if(is_dir($path_to_template_files.$_POST['name2'])) {
					if($error == "") {
						$error = $ac->show_error_message(get_caption('9025','The folder already exists on server.'));
					}
				} else {
					$oldperms = fileperms($path_to_template_files);
					@chmod($path_to_template_files,$oldperms | 0222);
					if(is_writeable($path_to_template_files)) {
						if(!mkdir($path_to_template_files.$_POST['name2'],0777)) {
							if($error == "") {
								$error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')." (".$path_to_template_files.")");
							}
						} else {
							@chmod($path_to_template_files.$_POST['name2'],0777);
							$_POST['name'] = $_POST['name2'];
						}
					} else {
						if($error == "") {
							$error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')." (".$path_to_template_files.")");
						}
					}
					@chmod($path_to_template_files,$oldperms);
				}
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				$db->query("INSERT INTO ".$tbl_prefix."sys_template (`tid`,`name`,`template`,`css`,`be`)
					VALUES ('','".$_POST['name']."','".$_POST['template']."','".$_POST['css']."','".$_POST['be']."')");
				$db->query("SELECT tid FROM ".$tbl_prefix."sys_template ORDER BY tid DESC LIMIT 1");
				while($db->next_record()):
					$tid = $db->f("tid");
				endwhile;
				load_url("index.php?mode=settings&page=tmpl&action=edit&tid=".$tid);
				//load_url("index.php?mode=settings&page=tmpl");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3070','Add Template')."</h2>",
			"tab_general"       => get_caption('0170','General'),
			"tmpl_action"       => "index.php?mode=settings&page=tmpl&action=new",
			"tmpl_error"        => $error,
			"tmpl_name"         => get_caption('0110','Name'),
			"tmpl_name_input"   => "<select name='name'><option value='new'>".get_caption('3100','Create new folder...')."</option>".$ac->get_files($path_to_template_files,$_POST['name'],"")."</select>",
			"tmpl_newname"      => get_caption('3110','Folder Name'),
			"tmpl_newname_input"=> "<input type='text' name='name2' size='20' maxlength='80' value='".$_POST['name2']."' />",
			"tmpl_be"           => get_caption('3160','Backend Template'),
			"tmpl_be_input"     => "<select name='be'><option value='0'>".get_caption('0140','No')."</option><option value='1'>".get_caption('0130','Yes')."</option></select>",
			"tmpl_button_send"  => $ac->create_form_button("submit",get_caption('3070','Add Template'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_tmpl']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['template'] = validate_text($_POST['template']);
			$_POST['css'] = validate_text($_POST['css']);
			$_POST['be'] = validate_text($_POST['be']);
			$_POST['template_print'] = validate_text($_POST['template_print']);
			$_POST['template_print_css'] = validate_text($_POST['template_print_css']);
			$_POST['template_login'] = validate_text($_POST['template_login']);
			$_POST['template_text'] = validate_text($_POST['template_text']);
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			if(!mandatory_field($_POST['template']) && $error == "") { $error = $ac->show_error_message(get_caption('9270','You must select a main template file.')); }
			if(!mandatory_field($_POST['css']) && $error == "") { $error = $ac->show_error_message(get_caption('9280','You must select a main CSS file.')); }
			if(!$_POST['be']) {
				if(!mandatory_field($_POST['template_print']) && $error == "") { $error = $ac->show_error_message(get_caption('9290','You must select a print view template file.')); }
				if(!mandatory_field($_POST['template_print_css']) && $error == "") { $error = $ac->show_error_message(get_caption('9300','You must select a print view CSS file.')); }
				if(!mandatory_field($_POST['template_login']) && $error == "") { $error = $ac->show_error_message(get_caption('9310','You must select a login template file.')); }
				if(!mandatory_field($_POST['template_text']) && $error == "") { $error = $ac->show_error_message(get_caption('9320','You must select a content (text) template file.')); }
			}
			
			$dir_exist = 1;
			if(!is_dir($path_to_template_files.$_POST['name'])) {
				if($error == "") {
					$error = $ac->show_error_message(get_caption('9350','The folder does not exist on the server.'));
				}
				$dir_exist = 0;
			}
			
			$file2_exist = 1;
			if(!file_exists($path_to_template_files.$_POST['name']."/".$_POST['template'])) {
				if($error == "") {
					$error = $ac->show_error_message(get_caption('9330','The template file does not exist on the server.'));
				}
				$file2_exist = 0;
			}
			
			$file_exist = 1;
			if(!file_exists($path_to_template_files.$_POST['name']."/".$_POST['css'])) {
				if($error == "") {
					$error = $ac->show_error_message(get_caption('9340','The CSS file does not exist on the server.'));
				}
				$file_exist = 0;
			}
			
			if(mandatory_field($_POST['name']) && $dir_exist == 1) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET name = '".$_POST['name']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
				if($_POST['oldname'] != $_POST['name']) {
					$db->query("UPDATE ".$tbl_prefix."sys_template SET template = '".$_POST['template']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
					$db->query("UPDATE ".$tbl_prefix."sys_template SET css = '".$_POST['css']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
				}
			}
			if(mandatory_field($_POST['template']) && $file2_exist == 1) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET template = '".$_POST['template']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['css']) && $file_exist == 1) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET css = '".$_POST['css']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			
			$db->query("UPDATE ".$tbl_prefix."sys_template SET be = '".$_POST['be']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");

			if(mandatory_field($_POST['template_print'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET template_print = '".$_POST['template_print']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['template_print_css'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET template_print_css = '".$_POST['template_print_css']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['template_login'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET template_login = '".$_POST['template_login']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['template_text'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_template SET template_text = '".$_POST['template_text']."' WHERE tid = '".$_POST['tid']."' LIMIT 1");
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=settings&page=tmpl");
			}
		}
		
		// select record
		if(isset($_POST['tid'])) {
			$_GET['tid'] = $_POST['tid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_template WHERE tid = '".$_GET['tid']."' ORDER BY tid LIMIT 1");
		while($db->next_record()):
			$tid = $db->f("tid");
			$name = $db->f("name");
			$template = $db->f("template");
			$css = $db->f("css");
			$be = mark_selected_value($db->f("be"));
			$template_print = $db->f("template_print");
			$template_print_css = $db->f("template_print_css");
			$template_login = $db->f("template_login");
			$template_text = $db->f("template_text");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3080','Edit Template')."</h2>",
			"tab_general"         => get_caption('0170','General'),
			"tmpl_action"         => "index.php?mode=settings&page=tmpl&action=edit",
			"tmpl_error"          => $error,
			"tmpl_name"           => get_caption('0110','Name'),
			"tmpl_name_input"     => "<select name='name' disabled>".$ac->get_files($path_to_template_files,$name,"")."</select><input type='hidden' name='name' value='".$name."' />",
			"tmpl_template"       => get_caption('3120','Main Template File'),
			"tmpl_template_input" => "<select name='template'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$template,".htm")."</select>",
			"tmpl_template_edit"  => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$template,get_caption('0201','Edit')),
			"tmpl_css"            => get_caption('3130','Main CSS File'),
			"tmpl_css_input"      => "<select name='css'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$css,".css")."</select>",
			"tmpl_css_edit"       => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$css,get_caption('0201','Edit')),
			"tmpl_be"             => get_caption('3160','Backend Template'),
			"tmpl_be_input"       => "<select name='be'><option value='0' ".$be['1'].">".get_caption('0140','No')."</option><option value='1' ".$be['2'].">".get_caption('0130','Yes')."</option></select>",
			"tmpl_print"          => get_caption('3140','Print Template File'),
			"tmpl_print_input"    => "<select name='template_print'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$template_print,".html")."</select>",
			"tmpl_print_edit"     => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$template_print,get_caption('0201','Edit')),
			"tmpl_printcss"       => get_caption('3150','Print CSS File'),
			"tmpl_printcss_input" => "<select name='template_print_css'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$template_print_css,".css")."</select>",
			"tmpl_printcss_edit"  => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$template_print_css,get_caption('0201','Edit')),
			"tmpl_login"          => get_caption('3170','Login Template File'),
			"tmpl_login_input"    => "<select name='template_login'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$template_login,".html")."</select>",
			"tmpl_login_edit"     => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$template_login,get_caption('0201','Edit')),
			"tmpl_text"           => get_caption('3180','Text Template File'),
			"tmpl_text_input"     => "<select name='template_text'><option value=''></option>".$ac->get_files($path_to_template_files.$name,$template_text,".html")."</select>",
			"tmpl_text_edit"      => $ac->create_edit_icon("index.php?mode=settings&page=editor&fileurl=templates/".$name."/".$template_text,get_caption('0201','Edit')),
			"tmpl_tid"            => "<input type='hidden' name='tid' value='".$tid."' />",
			"tmpl_button_send"    => $ac->create_form_button("submit",get_caption('0120','Save')),
			"tmpl_button_reset"   => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_tmpl']['delete'] == 1) {
			$db->query("DELETE FROM ".$tbl_prefix."sys_template WHERE tid = '".$_POST['tid']."' LIMIT 1");
			CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			load_url("index.php?mode=settings&page=tmpl");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=settings&page=tmpl");
		
		// select record
		if(isset($_POST['tid'])) {
			$_GET['tid'] = $_POST['tid'];
		}
		$db->query("SELECT tid,name FROM ".$tbl_prefix."sys_template WHERE tid = '".$_GET['tid']."' ORDER BY tid LIMIT 1");
		while($db->next_record()):
			$tid = $db->f("tid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3090','Delete Template')."</h2>",
			"tmpl_action"       => "index.php?mode=settings&page=tmpl&action=del",
			"tmpl_question"     => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"tmpl_name"         => "<p class='bold'>".$name."</p>",
			"tmpl_tid"          => "<input type='hidden' name='tid' value='".$tid."' />",
			"tmpl_button_send"  => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create template overview
		
		// Headline
		$settings_tmpl .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('3120','Main Template File')."</p></td>"
			."<td><p class='bold'>".get_caption('3130','Main CSS File')."</p></td>"
			."<td><p class='bold'>".get_caption('3160','Backend Template')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_template ORDER BY tid");
		while($db->next_record()):
			$settings_tmpl .= "<tr class='bg_color2'>"
				."<td>".$db->f("name")."</td>"
				."<td><a href='index.php?mode=settings&page=editor&fileurl=templates/".$db->f("name")."/".$db->f("template")."'>".$db->f("template")."</a></td>"
				."<td><a href='index.php?mode=settings&page=editor&fileurl=templates/".$db->f("name")."/".$db->f("css")."'>".$db->f("css")."</a></td>"
				."<td>".translate_yesno($db->f("be"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=settings&page=tmpl&action=edit&tid=".$db->f("tid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=settings&page=tmpl&action=del&tid=".$db->f("tid"),get_caption('0211','Delete'))
				."</td>"
				."</tr>";
		endwhile;
		$settings_tmpl .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3040','Templates')."</h2>",
			"confirm_message"   => $ac->show_ok_message(GetConfirmMessage()),
			"settings_tmpl"     => $settings_tmpl,
			"settings_tmpl_new" => $ac->create_add_icon("index.php?mode=settings&page=tmpl&action=new",get_caption('3070','Add Template'))
			));
	break;
}
?>