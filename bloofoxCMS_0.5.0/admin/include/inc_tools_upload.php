<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_tools_upload.php -
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
	default:
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['tools']['write'] == 1) {
			$error = "";
			$folder = $_POST['dir'];
			if(substr($folder,-1) != "/") {
				$folder .= "/";
			}
			if(substr($folder,0,13) == "../templates/" && $_POST['tmplimage']) {
				$folder .= "images/";
			}
			
			if(!is_dir($folder)) {
				if(!mkdir($folder,0777)) {
					$error = $ac->show_error_message(get_caption('9010',"You have not the permissions (chmod 777) to write in this folder.")." (".$folder.")");
				} else {
					@chmod($folder,0777);
				}
			}

			$x = 0;
			while($x < 5)
			{
				$x++;
				if(mandatory_field($_FILES["file".$x]["name"]) && file_exists($folder.$_FILES["file".$x]["name"])) {
					$error .= $ac->show_error_message(get_caption('9020',"The file already exists on server.")." ".$_FILES["file".$x]["name"]);
				}
			}
			
			if($error == "") {
				if(!upload_files($folder)) { $error = $ac->show_error_message(get_caption('9010',"You have not the permissions (chmod 777) to write in this folder.")." (".$folder.")"); }
			}
			
			if($error == "") {
				CreateConfirmMessage(1,get_caption('5230',"Files have been uploaded successful."));
				load_url("index.php?mode=tools&page=upload");
			}
		}
		
		// Set variables
		$tpl->set_var(array(
			"tools_title"    => "<h2>".get_caption('5000','Tools')." / ".get_caption('5020','Upload')."</h2>",
			"tools_action"      => "index.php?mode=tools&page=upload",
			"tools_error"       => $error,
			"confirm_message"   => $ac->show_ok_message(GetConfirmMessage()),
			"tools_folder"      => get_caption('0150','Directory'),
			"tools_folder_input"=> "<select name='dir'>".$ac->get_folders($_POST['dir'])."</select>",
			"tools_image"       => get_caption('5190','Template Image'),
			"tools_image_input" => "<input type='checkbox' name='tmplimage' />",
			"tools_file1"       => get_caption('0160','File'),
			"tools_file1_input" => "<input type='file' name='file1' size='30' maxlength='250' />",
			"tools_file2"       => get_caption('0160','File'),
			"tools_file2_input" => "<input type='file' name='file2' size='30' maxlength='250' />",
			"tools_file3"       => get_caption('0160','File'),
			"tools_file3_input" => "<input type='file' name='file3' size='30' maxlength='250' />",
			"tools_file4"       => get_caption('0160','File'),
			"tools_file4_input" => "<input type='file' name='file4' size='30' maxlength='250' />",
			"tools_file5"       => get_caption('0160','File'),
			"tools_file5_input" => "<input type='file' name='file5' size='30' maxlength='250' />",
			"tools_button_send" => "<input class='btn' type='submit' name='send' value='".get_caption('5180','Upload Files')."' />"
			));
	break;
}
?>