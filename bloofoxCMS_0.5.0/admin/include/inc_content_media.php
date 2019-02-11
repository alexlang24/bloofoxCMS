<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_content_media.php -
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

// init filter class
$filters = new filter();

switch($action)
{
	case 'new':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_media']['write'] == 1) {
			if(!mandatory_field($_FILES["filename"]["name"]) && $error == "") { $error = $ac->show_error_message(get_caption('9470','You must select a file.')); }
			
			if($_POST['media_type'] == 0 && $error == "") {
				if(file_exists($path_to_files_folder.$_FILES["filename"]["name"])) {
					$error = "<p class='error'>".get_caption('ErrorFileExist')."</p>";
				} else {
					if(!upload_file($path_to_files_folder)) { $error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')); }
				}
			} 
			if($_POST['media_type'] == 1 && $error == "") {
				if(file_exists($path_to_image_folder.$_FILES["filename"]["name"])) {
					$error = "<p class='error'>".get_caption('ErrorFileExist')."</p>";
				} else {
					if(!upload_file($path_to_image_folder)) { $error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')); }
				}
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
				load_url("index.php?mode=content&page=media");
			}
		} else {
			if(!upload_file($path_to_files_folder)) { $error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')." (".$path_to_files_folder.")"); }
			if(!upload_file($path_to_image_folder)) { $error .= $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')." (".$path_to_image_folder.")"); }
		}
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"       => "<h2>".get_caption('2000','Contents')." / ".get_caption('2470','Add Mediafile')."</h2>",
			"tab_general"          => get_caption('0170','General'),
			"media_action"         => "index.php?mode=content&page=media&action=new",
			"media_error"          => $error,
			"media_filename"       => get_caption('0160','File'),
			"media_filename_input" => "<input type='file' name='filename' size='30' maxlength='250' />",
			"media_type"           => get_caption('2140','Type'),
			"media_type_input"     => "<select name='media_type'><option value='0'>".get_caption('0160','File')."</option><option value='1'>".get_caption('2500','Image')."</option></select>",
			"media_button_send"    => $ac->create_form_button("submit",get_caption('2470','Add Mediafile'))
			));
	break;
	
	case 'edit':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_media']['write'] == 1) {
								
			if(mandatory_field($_FILES["filename"]["name"])) {
				// upload file
				if($_POST['media_type'] == 0) {
					if(!upload_file($path_to_files_folder)) { $error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')); }
				} else {
					if(!upload_file($path_to_image_folder)) { $error = $ac->show_error_message(get_caption('9010','You have not the permissions (chmod 777) to write in this folder.')); }
				}
				
				if($_POST['media_type'] != $_POST['media_type_old']) {
					if($_POST['media_type_old'] == 0) {
						delete_file($_POST['media_file'],$path_to_files_folder);
					} else {
						delete_file($_POST['media_file'],$path_to_image_folder);
					}
				}
			}
			
			if(!mandatory_field($_FILES["filename"]["name"])) {
				if($_POST['media_type'] != $_POST['media_type_old']) {
					if($_POST['media_type_old'] == 0) {
						@copy($path_to_files_folder.$_POST['media_file'],$path_to_image_folder.$_POST['media_file']);
						delete_file($_POST['media_file'],$path_to_files_folder);
					} else {
						@copy($path_to_image_folder.$_POST['media_file'],$path_to_files_folder.$_POST['media_file']);
						delete_file($_POST['media_file'],$path_to_image_folder);
					}
				}
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
				load_url("index.php?mode=content&page=media");
			}
		}
		
		// select record
		$filename = $_GET["file"];
		$type = mark_selected_value($_GET["type"]);
		$media_type = $_GET["type"];
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('2000','Contents')." / ".get_caption('2480','Edit Mediafile')."</h2>",
			"tab_general"       => get_caption('0170','General'),
			"media_action"      => "index.php?mode=content&page=media&action=edit",
			"media_error"       => $error,
			"media_filename"    => get_caption('0160','File'),
			"media_filename_input" => $filename."<br /><input type='file' name='filename' size='30' maxlength='250' />",
			"media_type"        => get_caption('2140','Type'),
			"media_type_input"  => "<select name='media_type'><option value='0' ".$type['1'].">".get_caption('0160','File')."</option><option value='1' ".$type['2'].">".get_caption('2500','Image')."</option></select>",
			"media_file"        => "<input type='hidden' name='media_file' value='".$filename."' />",
			"media_type_old"    => "<input type='hidden' name='media_type_old' value='".$media_type."' />",
			"media_button_send" => $ac->create_form_button("submit",get_caption('0120','Save'))
			));
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_media']['delete'] == 1) {
			if($_POST['media_type'] == 0) {
				delete_file($_POST['media_file'],$path_to_files_folder);
			} else {
				delete_file($_POST['media_file'],$path_to_image_folder);
			}
			CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
			load_url("index.php?mode=content&page=media");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=content&page=media");
		
		// select record
		$media_type = $_GET["type"];
		$filename = $_GET["file"];
		
		// Set variables
		$tpl->set_var(array(
			"settings_title"    => "<h2>".get_caption('2000','Contents')." / ".get_caption('2490','Delete Mediafile')."</h2>",
			"media_action"      => "index.php?mode=content&page=media&action=del",
			"media_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"media_filename"    => "<p class='bold'>".$filename."</p>",
			"media_file"        => "<input type='hidden' name='media_file' value='".$filename."' />",
			"media_type"        => "<input type='hidden' name='media_type' value='".$media_type."' />",
			"media_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;
	
	default:
		// Create media overview
		
		// Filter
		if(isset($_POST["send"])) {
			if($_POST["filter_media"] == "") {
				$_SESSION["filter_media"] = "%";
			} else {
				$_SESSION["filter_media"] = $_POST["filter_media"];
			}
			load_url("index.php?mode=content&page=media");
		}

		$media_filter = $filters->create_filter_media($db);
		
		// Headline
		$settings_media .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('2140','Type')."</p></td>"
			."<td><p class='bold'>".get_caption('0160','File')."</p></td>"
			."<td><p class='bold'>".get_caption('2510','File Size')."</p></td>"
			."<td><p class='bold'>".get_caption('2520','Dimension')."</p></td>"
			."<td><p class='bold'>".get_caption('2350','Changed at')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
		
		// Lines
		if(!isset($_SESSION["filter_media"])) {
			$_SESSION["filter_media"] = "%";
		}
		
		// Read dir
		function get_mediacenter($folder)
		{
			$dir=opendir($folder);
			$dir_list = array();

			while($file = readdir($dir))
			{
				if($file != "." && $file != ".." && $file != "index.htm" && !is_dir($folder.$file))
				{
					array_push($dir_list,$file);
				}
			}

			closedir($dir);
			
			return($dir_list);
		}
		
		if($_SESSION["filter_media"] == 0 || $_SESSION["filter_media"] == "%") {
			$array_files = get_mediacenter($path_to_files_folder);
			asort($array_files);
			
			while(list($key,$val) = each($array_files))
			{
				$path = $path_to_files_folder;
				
				$settings_media .= "<tr class='bg_color2'>"
					."<td>".get_caption("File")."</td>"
					."<td><a href='".$path.$val."' target='_blank'>".$val."</a></td>";
					if(file_exists($path.$val)) {
						$settings_media .= "<td>".round(filesize($path.$val)/1024,2)." KByte</td>";
						$settings_media .= "<td></td>";
						$settings_media .= "<td>".date($sys_vars['datetime'], filemtime($path.$val))."</td>";
					} else {
						$settings_media .= "<td>0 KByte</td>";
						$settings_media .= "<td></td>";
						$settings_media .= "<td></td>";
					}
				$settings_media .= "<td>"
					.$ac->create_edit_icon("index.php?mode=content&page=media&action=edit&file=".$val."&type=0",get_caption('0201','Edit'))
					.$ac->create_delete_icon("index.php?mode=content&page=media&action=del&file=".$val."&type=0",get_caption('0211','Delete'))
					."</td>"
					."</tr>";
			}
		}
		
		if($_SESSION["filter_media"] == 1 || $_SESSION["filter_media"] == "%") {
			$array_images = get_mediacenter($path_to_image_folder);
			asort($array_images);
			
			while(list($key,$val) = each($array_images))
			{
				$path = $path_to_image_folder;
				
				$settings_media .= "<tr class='bg_color2'>"
					."<td>".get_caption("Image")."</td>"
					."<td><a href='".$path.$val."' target='_blank'>".$val."</a></td>";
					if(file_exists($path.$val)) {
						$settings_media .= "<td>".round(filesize($path.$val)/1024,2)." KByte</td>";
						$imgsize = getimagesize($path.$val);
						$settings_media .= "<td>".$imgsize[0]."x".$imgsize[1]."</td>";
						$settings_media .= "<td>".date($sys_vars['datetime'], filemtime($path.$val))."</td>";
					} else {
						$settings_media .= "<td>0 KByte</td>";
						$settings_media .= "<td>0x0</td>";
						$settings_media .= "<td></td>";
					}
				
				$settings_media .= "<td>"
					.$ac->create_edit_icon("index.php?mode=content&page=media&action=edit&file=".$val."&type=1",get_caption('0201','Edit'))
					.$ac->create_delete_icon("index.php?mode=content&page=media&action=del&file=".$val."&type=1",get_caption('0211','Delete'))
					."</td>"
					."</tr>";
			}
		}
		
		$settings_media .= "</table>";

		// Set variables
		$tpl->set_var(array(
			"settings_title"        => "<h2>".get_caption('2000','Contents')." / ".get_caption('2040','Mediacenter')."</h2>",
			"confirm_message"       => $ac->show_ok_message(GetConfirmMessage()),
			"settings_media_new"    => $ac->create_add_icon("index.php?mode=content&page=media&action=new",get_caption('2470','Add Mediafile')),
			"settings_media"        => $settings_media,
			"settings_media_filter" => $media_filter
			));
	break;
}
?>