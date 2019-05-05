<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - functions.php -
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

// check setup and folders
function check_setup_and_folders()
{
	// Check if setup report was created, if not try to start setup
	if(!file_exists(SYS_WORK_DIR."/media/txt/install.txt")) {
		if(is_dir(SYS_WORK_DIR."/install")) {
			load_url("install/index.php");
		} else {
			echo(show_error_screen("Error","The setup folder '/install' doesn't exist! You have to install bloofoxCMS before you can use it."));
			die();
		}
	}

	// Check if setup folder still exists, if so then die
	if(is_dir(SYS_WORK_DIR."/install")) {
		echo(show_error_screen("Error","The setup folder '/install' still exists! Please delete this folder from your server."));
		die();
	}

	// Check if update folder still exists, if so then die
	if(is_dir(SYS_WORK_DIR."/update")) {
		echo(show_error_screen("Error","The update folder '/update' still exists! Please delete this folder from your server."));
		die();
	}
}

// check setup and folders
function check_versions($db)
{	
	// Compare database version with file version
	if(get_current_version() != get_database_version($db)) {
		echo(show_error_screen("Notice","bloofoxCMS is in maintenance. Please contact the system administrator and try again later."));
		die();
	}
}

// get language caption string
function get_caption($key,$caption="")
{
	global $str;
	
	// Search for caption in language file
	$string = $str[$key];
	// if no caption found use default caption
	if(empty($string)) {
		$string = $caption;
	}
	// if default caption is not set then show identifier
	if(empty($string)) {
		$string = $key;
	}
	
	return($string);
}

// send e-mail (text or html)
function send_mail($recipient,$sender,$subject,$body,$mode=0)
{
	global $sys_config_vars;
	
	switch($mode)
	{
		case '1': // HTML-Mail, Sender is admin, but reply address is real sender
			$body = "
				<html>
				<head>
				<title>".$subject."</title>
				</head>
				<body bgcolor=\"#ffffff\" text=\"#000000\">
				".text_2_html($body)."
				</body>
				</html>
				";
			
			$eol="\n";
			$header = "MIME-Version: 1.0".$eol;
			$header .= "Content-type: text/html; charset=iso-8859-1".$eol;
			$header .= "From: ".$sys_config_vars['mail'].$eol;
			$header .= "Reply-To: ".$sender.$eol;
			$header .= "Return-Path: ".$sender.$eol;
			mail($recipient,$subject,$body,$header);
		break;
		
		default: // Plain Text, Sender is real sender
			mail($recipient,$subject,$body,"From: \"".$sender."\" <".$sender.">\nReply-To: ".$sender,"-f ".$sys_config_vars['mail']);
		break;
	}
}

// create link with/without mod_rewrite
function create_url($eid,$string,$mod_rewrite,$ext_name="",$ext_id="",$page=0)
{
	global $sys_config_vars,$sys_setting_vars;
	
	if($mod_rewrite == 1) {
		$string = strtolower($string);
		$string = str_replace(" ","_",$string);
		$string = str_replace("&#039;","",$string);
		$string = str_replace("&quot;","",$string);
		$string = str_replace("&amp;","",$string);
		$string = str_replace("&auml;","ae",$string);
		$string = str_replace("&Auml;","Ae",$string);
		$string = str_replace("&ouml;","oe",$string);
		$string = str_replace("&Ouml;","Oe",$string);
		$string = str_replace("&uuml;","ue",$string);
		$string = str_replace("&Uuml;","Ue",$string);
		$string = str_replace("&szlig;","ss",$string);
		$string = preg_replace("([\#\?\!\&\;\@\:\/\.]*)","",$string);
		
		if($ext_id == "") {
			if($page == 0) {
				$link = $string.".".$eid.".html";
			} else {
				$link = $string.".".$eid."_".$page.".html";
			}
		} else {
			if($page == 0) {
				$link = $string.".".$eid.".".$ext_name.".".$ext_id.".html";
			} else {
				$link = $string.".".$eid.".".$ext_name.".".$ext_id."_".$page.".html";
			}
		}
	} else {
		if($ext_id == "") {
			if($page == 0) {
				$link = "index.php?page=".$eid;
			} else {
				$link = "index.php?page=".$eid."&amp;start=".$page;
			}
		} else {
			if($page == 0) {
				$link = "index.php?page=".$eid."&amp;".$ext_name."=".$ext_id;
			} else {
				$link = "index.php?page=".$eid."&amp;start=".$page."&amp;".$ext_name."=".$ext_id;
			}
		}
	}
	$link = $sys_config_vars['url']."/".trim($link);
	return($link);
}

//**
// Input management

// replace special signs in html code
function replace_specialchars($string)
{
	$string = str_replace("�","&auml;",$string);
	$string = str_replace("�","&ouml;",$string);
	$string = str_replace("�","&uuml;",$string);
	$string = str_replace("�","&Auml;",$string);
	$string = str_replace("�","&Ouml;",$string);
	$string = str_replace("�","&Uuml;",$string);
	$string = str_replace("�","&szlig;",$string);
	$string = str_replace("@","&#64;",$string);
	$string = str_replace("'","&#039;",$string);
	
	return($string);
}

// convert text to html, if wysiwyg is not in use and for HTML mails
function text_2_html($string)
{
	$string = trim($string);
	/*$string = strip_tags($string,"<p><div><a><b><i><u><ul><ol><li><img><span><embed><object><param><iframe>");*/
	$string = stripslashes($string);
	$string = str_replace("\n","<br />",$string);
	$string = str_replace("'","&#039;",$string);
	$string = str_replace("@","&#64;",$string);
		
	return($string);
}

// convert html to text, if wysiwyg is not in use
function html_2_text($string)
{
	$string = trim($string);
	$string = str_replace("<br />","\n",$string);
	
	return($string);
}

// check, if username is still available
function available_username($db,$username,$userid=0)
{
	global $tbl_prefix;
	$db->query("SELECT username FROM ".$tbl_prefix."sys_user WHERE username = '".$username."' && uid <> '".$userid."' ORDER BY uid LIMIT 1");
	if($db->num_rows() == 1) {
		return(0);
	}
	return(1);
}

// check minimum quantity of strings ($length) and strict rules ($rule)
function check_string_rules($string,$length=3,$rule=0)
{
	if(strlen($string) < $length) {
		return(0);
	}
	// the default rule used for passwords is no. 1
	switch($rule)
	{
		case '1': // numbers and letters
			if(!preg_match("([0-9]{1,250})",$string) || !preg_match("([a-zA-Z������]{1,250})",$string)) {
				return(0);
			}
		break;
		
		case '2': // numbers, letters and specials signs
			if(!preg_match("([0-9]{1,250})",$string) || !preg_match("([a-zA-Z������]{1,250})",$string) || !preg_match("([\@\!\?\+\*\|\.\:\_\-]{1,250})",$string)) {
				return(0);
			}
		break;
		
		default: // no special rule
			// nothing will be checked
		break;
	}
	return(1);
}

// validate text-input fields
function validate_text($string)
{
	global $sys_config_vars,$sys_setting_vars;
	
	$string = trim($string);
	$string = strip_tags($string);
	$string = stripslashes($string);
	if($sys_setting_vars["htmlentities_off"] == 0) {
		// >> 0.5.1
		//$string = htmlentities($string,ENT_QUOTES);
		$string = htmlentities($string,ENT_QUOTES,$sys_config_vars['meta_charset']);
		// << 0.5.1
	} else {
		$string = str_replace("'","&#039;",$string);
		$string = str_replace("\"","&quot;",$string);
	}
	
	return($string);
}

// validate textbox-input fields
function validate_textbox($string)
{
	global $sys_config_vars;
	
	$string = trim($string);
	$string = strip_tags($string);
	$string = stripslashes($string);
	if($sys_setting_vars["htmlentities_off"] == 0) {
		// >> 0.5.1
		//$string = htmlentities($string,ENT_QUOTES);
		$string = htmlentities($string,ENT_QUOTES,$sys_config_vars['meta_charset']);
		// << 0.5.1
	} else {
		$string = str_replace("'","&#039;",$string);
	}
	$string = stripslashes($string);
	$string = str_replace("\n","<br />",$string);
	
	return($string);
}

// validate date-input fields and make timestamp
function validate_date($string)
{
	if(strlen($string) == 0) {
		return("");
	}
	if(!preg_match("/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/",$string)) {
		return(0);
	}
	$day = substr($string,0,2);
	$month = substr($string,3,2);
	$year = substr($string,-4);
	$hour = date("H",time());
	$minute = date("i",time());
	
	return(mktime($hour,$minute,0,$month,$day,$year));
}

// check for valid email
function email_is_valid($string)
{
	if(!preg_match("/([a-z0-9\_\-]{1,250})+@([a-z���0-9\_\-\.]{2,250})\.([a-z]{2,5})$/i",$string)) {
		return(0);
	}
	return(1);
}

// mark selected option in select fields; param 1 = value, param 2 = no of options
function mark_selected_value($value,$options=2)
{
	$array = array();
	switch($options)
	{
		case '3':
		break;
		
		default: // 2 options (e.g. Yes - No)
			if($value == 0) {
				$array['1'] = "selected";
				$array['2'] = "";
			} else {
				$array['1'] = "";
				$array['2'] = "selected";
			}
		break;
	}
	return($array);
}

// upload file to folder $folder
function upload_file($folder)
{
	$oldperms = fileperms($folder);
	@chmod($folder,$oldperms | 0222);
	if(!is_writeable($folder)) {
		return(0);
	} else {
		move_uploaded_file($_FILES["filename"]["tmp_name"],$folder.$_FILES["filename"]["name"]);
		@chmod($folder.$_FILES["filename"]["name"],0666);
	}
	@chmod($folder,$oldperms);
	return(1);
}

// upload files (tools) to folder $folder
function upload_files($folder)
{
	$oldperms = fileperms($folder);
	@chmod($folder,$oldperms | 0222);
	if(!is_writeable($folder)) {
		return(0);
	} else {
		$x = 0;
   	    while($x < 5)
	    {
			$x++;
			if($_FILES["file".$x]["size"] != 0) {
				move_uploaded_file($_FILES["file".$x]["tmp_name"],$folder.$_FILES["file".$x]["name"]);
				@chmod($folder.$_FILES["file".$x]["name"],0666);
			}
		}
	}
	@chmod($folder,$oldperms);
	return(1);
}

// delete file from folder $folder
function delete_file($file,$folder)
{
	if(file_exists($folder.$file) && strlen($file) != 0) {
		unlink($folder.$file);
	}
}

// translate 0,1 to Yes,No
function translate_yesno($value,$captionYes,$captionNo)
{
	if($value == 0) {
		return($captionNo);
	}
	if($value == 1) {
		return($captionYes);
	}
}

// check mandatory fields in forms; returns false if no value is found
function mandatory_field($value)
{
	if(strlen($value) == 0) {
		return(0);
	}
	return(1);
}

// checks parameter for integer value
function CheckInteger($string)
{
  if (!preg_match("/^\d+$/",$string))
  {
    return(FALSE); // $string includes other signs
  }
  return(TRUE); // $string is ok
}

// checks inputs or parameters for string value
function CheckString($string)
{
  if (!preg_match("=^[a-z����]+$=i",$string))
  {
    return(FALSE); // $string includes other signs
  }
  return(TRUE); // $string is ok
}

//**
// System Management

// header to url
function load_url($url)
{
	header("LOCATION:".$url);
	exit;
}

// show error document with message
function show_error_screen($title,$message) {
	$error_message = <<<EOIF
<html>
<head>
	<title>bloofoxCMS Error / Notice</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<style type="text/css">
	body {
		margin				: auto;
		text-align			: center;
		font-family         : sans-serif, arial, helvetica;
	}
	
	h1 {
		width				: 100%;
		color				: #ff0000;
		font:				: 16px bold;
	}
	
	p {
		width				: 100%;
		font-size			: 12px;
		line-height			: 16px;
	}
	</style>
</head>
<body>
<div align="center">
	<h1>$title</h1>
	<p>$message</p>
	<p>Powered by <a href="http://www.bloofox.com" target="_blank">bloofoxCMS</a></p>
</div>

</body>
</html>
EOIF;

	return $error_message;
}

// make html document type
function create_doctype($doc_type)
{
	switch($doc_type)
	{
		case 'HTML 4.01':
			$tmpl_doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">";
		break;
		
		case 'XHTML 1.0 Strict':
			$tmpl_doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n"
							."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
		break;
		
		default: // XHTML 1.0 Transitional
			$tmpl_doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"
							."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		break;
	}
	
	return($tmpl_doctype);
}

// make html header
function create_header($config_vars,$tmpl_vars,$explorer_vars,$sys_plugin_vars)
{
	switch($config_vars['meta_doctype'])
	{
		case 'HTML 4.01':
			$tmpl_head = ""
				."<title>".$config_vars['meta_title']." - ".trim($explorer_vars['name']." ".$sys_plugin_vars['title'])."</title>\n"
				."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$config_vars['meta_charset']."\">\n"
				."<meta http-equiv=\"Pragma\" content=\"no-cache\">\n"
				."<meta name=\"generator\" content=\"".get_current_version()."\">\n";
			// author
			if($config_vars['meta_author'] != "") {
				$tmpl_head .= "<meta name=\"author\" content=\"".$config_vars['meta_author']."\">\n";
			}
			// copyright
			if($config_vars['meta_copyright'] != "") {
				$tmpl_head .= "<meta name=\"copyright\" content=\"".$config_vars['meta_copyright']."\">\n";
			}
			// date
			$tmpl_head .= "<meta name=\"date\" content=\"".date("m/d/Y")."\">\n";
			// description
			if($explorer_vars['description'] != "" || $config_vars['meta_desc'] != "") {
				$tmpl_head .= "<meta name=\"description\" content=\"".trim($explorer_vars['description']." ".$config_vars['meta_desc'])."\">\n";
			}
			// keywords
			if($explorer_vars['keywords'] != "" && $config_vars['meta_keywords'] != "") {
				$tmpl_head .= "<meta name=\"keywords\" content=\"".$explorer_vars['keywords'].", ".$config_vars['meta_keywords']."\">\n";
			} else {
				if($explorer_vars['keywords'] != "") {
					$tmpl_head .= "<meta name=\"keywords\" content=\"".$explorer_vars['keywords']."\">\n";
				} else {
					if($config_vars['meta_keywords'] != "") {
						$tmpl_head .= "<meta name=\"keywords\" content=\"".$config_vars['meta_keywords']."\">\n";
					}
				}
			}
			// publisher (=author)
			if($config_vars['meta_author'] != "") {
				$tmpl_head .= "<meta name=\"publisher\" content=\"".$config_vars['meta_author']."\">\n";
			}
			// stylesheet, robots, title and language
			$tmpl_head .= "<meta name=\"revisit-after\" content=\"7 days\">\n"
				."<meta name=\"content-language\" content=\"".$config_vars['language']."\">\n"
				."<meta name=\"robots\" content=\"index,follow\">\n"
				."<meta name=\"title\" content=\"".$config_vars['meta_title']." - ".trim($explorer_vars['name']." ".$sys_plugin_vars['title'])."\">\n"
				."<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$tmpl_vars['name']."/".$tmpl_vars['css']."\">\n"
				."<link rel=\"icon\" href=\"media/images/favicon.ico\" type=\"image/x-icon\">\n"
				."<link rel=\"shortcut icon\" href=\"media/images/favicon.ico\" type=\"image/x-icon\">\n"
				.$config_vars['generator_text'];
			if($sys_plugin_vars['css'] != "") {
				$tmpl_head .= $sys_plugin_vars['css'];
			}
		break;
		
		default: // XHTML all versions
			$tmpl_head = ""
				."<title>".$config_vars['meta_title']." - ".trim($explorer_vars['name']." ".$sys_plugin_vars['title'])."</title>\n"
				."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$config_vars['meta_charset']."\" />\n"
				."<meta http-equiv=\"Pragma\" content=\"no-cache\" />\n"
				."<meta name=\"generator\" content=\"".get_current_version()."\" />\n";
			if($config_vars['meta_author'] != "") {
				$tmpl_head .= "<meta name=\"author\" content=\"".$config_vars['meta_author']."\" />\n";
			}
			if($config_vars['meta_copyright'] != "") {
				$tmpl_head .= "<meta name=\"copyright\" content=\"".$config_vars['meta_copyright']."\" />\n";
			}
			$tmpl_head .= "<meta name=\"date\" content=\"".date("m/d/Y")."\" />\n";
			if($explorer_vars['description'] != "" || $config_vars['meta_desc'] != "") {
				$tmpl_head .= "<meta name=\"description\" content=\"".trim($explorer_vars['description']." ".$config_vars['meta_desc'])."\" />\n";
			}
			if($explorer_vars['keywords'] != "" && $config_vars['meta_keywords'] != "") {
				$tmpl_head .= "<meta name=\"keywords\" content=\"".$explorer_vars['keywords'].", ".$config_vars['meta_keywords']."\" />\n";
			} else {
				if($explorer_vars['keywords'] != "") {
					$tmpl_head .= "<meta name=\"keywords\" content=\"".$explorer_vars['keywords']."\" />\n";
				} else {
					if($config_vars['meta_keywords'] != "") {
						$tmpl_head .= "<meta name=\"keywords\" content=\"".$config_vars['meta_keywords']."\" />\n";
					}
				}
			}
			if($config_vars['meta_author'] != "") {
				$tmpl_head .= "<meta name=\"publisher\" content=\"".$config_vars['meta_author']."\" />\n";
			}
			$tmpl_head .= "<meta name=\"revisit-after\" content=\"7 days\" />\n"
				."<meta name=\"content-language\" content=\"".$config_vars['language']."\" />\n"
				."<meta name=\"robots\" content=\"index,follow\" />\n"
				."<meta name=\"title\" content=\"".$config_vars['meta_title']." - ".trim($explorer_vars['name']." ".$sys_plugin_vars['title'])."\" />\n"
				."<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$tmpl_vars['name']."/".$tmpl_vars['css']."\" />\n"
				."<link rel=\"icon\" href=\"media/images/favicon.ico\" type=\"image/x-icon\" />\n"
				."<link rel=\"shortcut icon\" href=\"media/images/favicon.ico\" type=\"image/x-icon\" />\n"
				.$config_vars['generator_text'];
			if($sys_plugin_vars['css'] != "") {
				$tmpl_head .= $sys_plugin_vars['css'];
			}
		break;
	}
	
	return($tmpl_head);
}

// current file version
function get_current_version()
{
	return("bloofoxCMS 0.5.0");
}

// current database version
function get_database_version($db)
{
	global $tbl_prefix;
	
	$db->query("SELECT * FROM ".$tbl_prefix."sys_setting WHERE sid = '11' LIMIT 1");
	while($db->next_record()):
		$db_version = $db->f("setting_value");
	endwhile;
	
	return($db_version);
}

//**
// SESSION message management

// create confirm message
function CreateConfirmMessage($msg,$msgtext="")
{
	switch($msg)
	{
		case 2:
			session_name("sid");
			session_start();
			$_SESSION["message"] = $msgtext;
		break;
		
		case 1:
			$_SESSION["message"] = $msgtext;
		break;
		
		default:
			$_SESSION["message"] = "";
		break;
	}
}

// get confirm message
function GetConfirmMessage()
{
	if($_SESSION["message"] != "") {
		return($_SESSION["message"]);
	}
	
	return("");
}

?>