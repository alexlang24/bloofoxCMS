<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/step1.php -
//
// Copyrights (c) 2006-2007 Alexander Lang, Germany
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
//
// You should have received a copy of the GNU General Public License
// along with bloofoxCMS; if not, please contact the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

if(isset($_POST['send'])) {
	$error = "";
	if(!mandatory_field($_POST['host']) && $error == "") { $error = "<p style='color:red;'>$strErrorHost</p>"; }
	if(!mandatory_field($_POST['database']) && $error == "") { $error = "<p style='color:red;'>$strErrorDatabase</p>"; }
	if(!mandatory_field($_POST['username']) && $error == "") { $error = "<p style='color:red;'>$strErrorUser</p>"; }
	if(!mandatory_field($_POST['password']) && $error == "") { $error = "<p style='color:red;'>$strErrorPw</p>"; }
	
	if(@mysqli_connect($_POST['host'],$_POST['username'],$_POST['password']) == FALSE && $error == "") {
		$error = "<p style='color:red;'>".$strErrorConnection."</p>";
	}
	
	if($error == "") {
		// copy file from default
		$fp = fopen("class_mysqli.default.php","r");
		$str = "";
		while(!feof($fp))
		{
			$cstr = fgets($fp);
			$str .= $cstr;
		}
		$str = str_replace("###host###",$_POST['host'],$str);     // ###host###
		$str = str_replace("###db###",$_POST['database'],$str);   // ###database###
		$str = str_replace("###user###",$_POST['username'],$str); // ###user###
		$str = str_replace("###pw###",$_POST['password'],$str);   // ###password###
		fclose($fp);
		
		if(!file_exists("../system/class_mysqli.php")) {
			if(is_writeable("../system")) {
				$fp = fopen("../system/class_mysqli.php","w");
				fwrite($fp,$str);
				fclose($fp);
				@chmod("../system/class_mysqli.php",0666);
			}
		}
  
		if(!file_exists("../system/class_mysqli.php")) {
			$error = "<p style='color:red;'>".$strNoFile."</p>";
		} else {
			load_url("index.php?page=2");
		}
	}
}

$tpl->set_block("content", "step1", "step1_handle");

if(!isset($_POST['host'])) {
	$_POST['host'] = "localhost";
}

$tpl->set_var(array(
	"step1_action"        => "index.php?page=1",
	"step1_host"          => $strHost,
	"host_value"          => $_POST['host'],
	"step1_database"      => $strDatabase,
	"database_value"      => $_POST['database'],
	"step1_username"      => $strUser,
	"username_value"      => $_POST['username'],
	"step1_password"      => $strPw,
	"step1_text"          => "<p>".$strEnterData."</p>",
	"step1_error"         => $error,
	"step1_next_step"     => "<input type='submit' name='send' value='".$strNextStep2."' />"
	));

$tpl->parse("step1_handle", "step1", true);
?>