<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_settings_editor.php -
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

// text editor
$backlink = $_SERVER["HTTP_REFERER"];

if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['set_tmpl']['write'] == 1) {
	
	$newfile = str_replace("\\","",$_POST['file']);
	$fileurl = $_POST['fileurl'];
	$backlink = $_POST['backlink'];
	
	//write $newfile to file
	if(file_exists($fileurl)) {
		$oldperms = fileperms($fileurl);
		@chmod($fileurl,$oldperms | 0222);
		if(is_writeable($fileurl)==false) {
			$error = $ac->show_error_message(get_caption('9015','You have not the permissions (chmod 666) to write in this file.'));
		} else {
			$wf = fopen($fileurl,"w");
			fwrite($wf,$newfile);
			fclose($wf);
		}
		@chmod($fileurl,$oldperms);
	}
	
	// redirect back
	if($error == "") {
		CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
		load_url($_POST['backlink']);
	}
}

// show file
if(isset($_POST["fileurl"])) {
	$fileurl = $_POST["fileurl"];
}
if(isset($_GET["fileurl"])) {
	$fileurl = "../".$_GET["fileurl"];
}

if(file_exists($fileurl)) {
	$filelength = filesize($fileurl);
	$readfile = fopen($fileurl,"r");
	$file = fread($readfile,$filelength);
	fclose($readfile);
}

if(is_writeable($fileurl)==false) {
	$error = $ac->show_error_message(get_caption('9015','You have not the permissions (chmod 666) to write in this file.'));
}

// Set variables
$tpl->set_var(array(
	"settings_title"      => "<h2>".get_caption('3000','Administration')." / ".get_caption('3650','Editor')."</h2>",
	"confirm_message"     => $ac->show_ok_message(GetConfirmMessage()),
	"editor_action"       => "index.php?mode=settings&page=editor",
	"editor_error"        => $error,
	"editor_file"         => $fileurl,
	"editor_file_input"   => "<textarea name='file' cols='80' rows='20'>".$file."</textarea>",
	"editor_fileurl"      => "<input type='hidden' name='fileurl' value='".$fileurl."' />",
	"editor_backlink"     => "<input type='hidden' name='backlink' value='".$backlink."' />",
	"editor_button_send"  => $ac->create_form_button("submit",get_caption('0120','Save')),
	"editor_button_reset" => $ac->create_form_button("reset",get_caption('0125','Reset'))
	));
?>