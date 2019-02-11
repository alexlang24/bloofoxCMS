<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_lang.php -
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
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_lang']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['flag'] = validate_text($_POST['flag']);
			$_POST['filename'] = validate_text($_POST['filename']);
			$_POST['filename2'] = validate_text($_POST['filename2']);
			$_POST['date'] = validate_text($_POST['date']);
			$_POST['datetime'] = validate_text($_POST['datetime']);
			$_POST['token'] = validate_text($_POST['token']);
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			//if(!mandatory_field($_POST['flag']) && $error == "") { $error = $ac->show_error_message(get_caption('ErrorFlag')); }
			if($_POST['filename'] == "new") {
				if(!mandatory_field($_POST['filename2']) && $error == "") { $error = $ac->show_error_message(get_caption('9380','You must enter a filename.')); }
								
				if(file_exists($path_to_lang_files.$_POST['filename2'])) {
					if($error == "") {
						$error = $ac->show_error_message(get_caption('9020','The file already exists on server.'));
					}
				} else {
					$oldperms = fileperms($path_to_lang_files);
					@chmod($path_to_lang_files,$oldperms | 0222);
					if(is_writeable($path_to_lang_files)) {
						// create new empty file
						$f = fopen($path_to_lang_files.$_POST['filename2'],"w+");
						fwrite($f,"<?php\n?>");
						fclose($f);
						@chmod($path_to_lang_files.$_POST['filename2'],0666);
						$_POST['filename'] = $_POST['filename2'];
					} else {
						if($error == "") {
							$error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')." (".$path_to_lang_files.")");
						}
					}
					@chmod($path_to_lang_files,$oldperms);
				}
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				$db->query("INSERT INTO ".$tbl_prefix."sys_lang VALUES ('','".$_POST['name']."','".$_POST['flag']."','".$_POST['filename']."','".$_POST['date']."','".$_POST['datetime']."','".$_POST['token']."')");
				load_url("index.php?mode=settings&page=lang");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3430','Add Language')."</h2>",
			"tab_general"         => get_caption('0170','General'),
			"lang_action"         => "index.php?mode=settings&page=lang&action=new",
			"lang_error"          => $error,
			"lang_name"           => get_caption('0110','Name'),
			"lang_name_input"     => "<input type='text' name='name' size='30' maxlength='250' value='".$_POST['name']."' />",
			"lang_flag"           => get_caption('3460','Flag'),
			"lang_flag_input"     => "<select name='flag'><option value=''></option>".$ac->get_files($path_to_image_folder,$_POST['flag'],".")."</select>",
			"lang_filename"       => get_caption('0160','File'),
			"lang_filename_input" => "<select name='filename'><option value='new'>".get_caption('3480','Create new file...')."</option>".$ac->get_files($path_to_lang_files,$_POST['filename'],'.php')."</select> ".get_caption('0161','File Name')." <input type='text' name='filename2' size='20' maxlength='80' value='".$_POST['filename2']."' />",
			"lang_date"           => get_caption('0540','Date Formula'),
			"lang_date_input"     => "<input type='text' name='date' size='10' maxlength='20' value='".$_POST['date']."' />",
			"lang_datetime"       => get_caption('0550','Date-/Time Formula'),
			"lang_datetime_input" => "<input type='text' name='datetime' size='10' maxlength='20' value='".$_POST['datetime']."' />",
			"lang_token"          => get_caption('3470','Token'),
			"lang_token_input"    => "<input type='text' name='token' size='5' maxlength='3' value='".$_POST['token']."' />",
			"lang_button_send"    => $ac->create_form_button("submit",get_caption('3430','Add Language'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_lang']['write'] == 1) {
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['flag'] = validate_text($_POST['flag']);
			$_POST['filename'] = validate_text($_POST['filename']);
			$_POST['date'] = validate_text($_POST['date']);
			$_POST['datetime'] = validate_text($_POST['datetime']);
			$_POST['token'] = validate_text($_POST['token']);
			
			if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
			//if(!mandatory_field($_POST['flag']) && $error == "") { $error = $ac->show_error_message(get_caption('ErrorFlag')); }
			if(!mandatory_field($_POST['filename']) && $error == "") { $error = $ac->show_error_message(get_caption('9380','You must enter a filename.')); }
			$file_exist = 1;
			if(!file_exists($path_to_lang_files.$_POST['filename'])) {
				if($error == "") {
					$error = $ac->show_error_message(get_caption('9390','The file does not exist on the server.'));
				}
				$file_exist = 0;
			}
			
			if(mandatory_field($_POST['name'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_lang SET name = '".$_POST['name']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['flag'])) {
				$db->query("UPDATE ".$tbl_prefix."sys_lang SET flag = '".$_POST['flag']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			}
			if(mandatory_field($_POST['filename']) && $file_exist == 1) {
				$db->query("UPDATE ".$tbl_prefix."sys_lang SET filename = '".$_POST['filename']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			}
			
			$db->query("UPDATE ".$tbl_prefix."sys_lang SET date = '".$_POST['date']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_lang SET datetime = '".$_POST['datetime']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			$db->query("UPDATE ".$tbl_prefix."sys_lang SET token = '".$_POST['token']."' WHERE lid = '".$_POST['lid']."' LIMIT 1");
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=settings&page=lang");
			}
		}
		
		// select record
		if(isset($_POST['lid'])) {
			$_GET['lid'] = $_POST['lid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_lang WHERE lid = '".$_GET['lid']."' ORDER BY lid LIMIT 1");
		while($db->next_record()):
			$lid = $db->f("lid");
			$name = $db->f("name");
			$flag = $db->f("flag");
			$filename = $db->f("filename");
			$date = $db->f("date");
			$datetime = $db->f("datetime");
			$token = $db->f("token");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3440','Edit Language')."</h2>",
			"tab_general"         => get_caption('0170','General'),
			"lang_action"         => "index.php?mode=settings&page=lang&action=edit",
			"lang_error"          => $error,
			"lang_name"           => get_caption('0110','Name'),
			"lang_name_input"     => "<input type='text' name='name' size='30' maxlength='250' value='".$name."' />",
			"lang_flag"           => get_caption('3460','Flag'),
			"lang_flag_input"     => "<select name='flag'><option value=''></option>".$ac->get_files($path_to_image_folder,$flag,".")."</select>",
			"lang_filename"       => get_caption('0160','File'),
			"lang_filename_input" => "<select name='filename'>".$ac->get_files($path_to_lang_files,$filename,'.php')."</select>",
			"lang_date"           => get_caption('0540','Date Formula'),
			"lang_date_input"     => "<input type='text' name='date' size='10' maxlength='20' value='".$date."' />",
			"lang_datetime"       => get_caption('0550','Date-/Time Formula'),
			"lang_datetime_input" => "<input type='text' name='datetime' size='10' maxlength='20' value='".$datetime."' />",
			"lang_token"          => get_caption('3470','Token'),
			"lang_token_input"    => "<input type='text' name='token' size='5' maxlength='3' value='".$token."' />",
			"lang_lid"            => "<input type='hidden' name='lid' value='".$lid."' />",
			"lang_button_send"    => $ac->create_form_button("submit",get_caption('0120','Save')),
			"lang_button_reset"   => $ac->create_form_button("reset",get_caption('0125','Reset'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_lang']['delete'] == 1) {
			$db->query("DELETE FROM ".$tbl_prefix."sys_lang WHERE lid = '".$_POST['lid']."' LIMIT 1");
			CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			load_url("index.php?mode=settings&page=lang");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=settings&page=lang");
		
		// select record
		if(isset($_POST['lid'])) {
			$_GET['lid'] = $_POST['lid'];
		}
		$db->query("SELECT lid,name FROM ".$tbl_prefix."sys_lang WHERE lid = '".$_GET['lid']."' ORDER BY lid LIMIT 1");
		while($db->next_record()):
			$lid = $db->f("lid");
			$name = $db->f("name");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3450','Delete Language')."</h2>",
			"lang_action"       => "index.php?mode=settings&page=lang&action=del",
			"lang_question"     => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"lang_name"         => "<p class='bold'>".$name."</p>",
			"lang_lid"          => "<input type='hidden' name='lid' value='".$lid."' />",
			"lang_button_send"  => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create lang overview
		
		// Headline
		$settings_lang .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
			."<td><p class='bold'>".get_caption('3460','Flag')."</p></td>"
			."<td><p class='bold'>".get_caption('0160','File')."</p></td>"
			."<td><p class='bold'>".get_caption('0540','Date Formula')."</p></td>"
			."<td><p class='bold'>".get_caption('3470','Token')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_lang ORDER BY lid");
		while($db->next_record()):
			$settings_lang .= "<tr class='bg_color2'>"
				."<td>".$db->f("name")."</td>"
				."<td>".$db->f("flag")."</td>"
				."<td><a href='index.php?mode=settings&page=editor&fileurl=languages/".$db->f("filename")."'>".$db->f("filename")."</a></td>"
				."<td>".$db->f("date")."</td>"
				."<td>".$db->f("token")."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=settings&page=lang&action=edit&lid=".$db->f("lid"),get_caption('0212','Edit'))
				.$ac->create_delete_icon("index.php?mode=settings&page=lang&action=del&lid=".$db->f("lid"),get_caption('0211','Delete'))
				."</td>"
				."</tr>";
		endwhile;
		$settings_lang .= "</table>";
	
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('3000','Administration')." / ".get_caption('3030','Languages')."</h2>",
			"confirm_message"   => $ac->show_ok_message(GetConfirmMessage()),
			"settings_lang"     => $settings_lang,
			"settings_lang_new" => $ac->create_add_icon("index.php?mode=settings&page=lang&action=new",get_caption('3430','Add Language'))
			));
	break;
}
?>