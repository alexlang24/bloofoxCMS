<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_filter.php -
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

class filter {
	//**
	// variables
	var $var = 1;
	
	//**
	// constructor
	function __construct()
	{
	}
	
	//**
	// return filter form for contents
	function create_filter_content($db)
	{
		global $tbl_prefix;
		global $ac;
		
		$filter_option1 = "<option value=''>".get_caption('0710','- Show all projects -')."</option>";
		$db->query("SELECT cid,name FROM ".$tbl_prefix."sys_config ORDER BY cid");
		while($db->next_record()):
			if($_SESSION["filter_content"] == $db->f("cid")) {
				$filter_option1 .= "<option value='".$db->f("cid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$filter_option1 .= "<option value='".$db->f("cid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		$filter_form = "<form method='post'>"
			."<table><tr>"
			."<td><select name='filter_content'>".$filter_option1."</select></td>"
			."<td>".$ac->create_form_button("submit",get_caption('0700','Set'))."</td>"
			."</tr></table>"
			."</form>";
		
		return($filter_form);
	}
	
	//**
	// return filter form for mediacenter
	function create_filter_media($db)
	{
		global $ac;
		$filter_option = "<option value=''>".get_caption('0720','- Show all types -')."</option>";
		if($_SESSION["filter_media"] == "0") {
			$filter_option .= "<option value='0' selected='selected'>".get_caption('File')."</option>";
		} else {
			$filter_option .= "<option value='0'>".get_caption('File')."</option>";
		}
		if($_SESSION["filter_media"] == "1") {
			$filter_option .= "<option value='1' selected='selected'>".get_caption('Image')."</option>";
		} else {
			$filter_option .= "<option value='1'>".get_caption('Image')."</option>";
		}
		
		$filter_form = "<form method='post'>"
			."<table><tr>"
			."<td><select name='filter_media'>".$filter_option."</select></td>"
			."<td>".$ac->create_form_button("submit",get_caption('0700','Set'))."</td>"
			."</tr></table>"
			."</form>";
		
		return($filter_form);
	}
	
	//**
	// return filter form for users
	function create_filter_user($db)
	{
		global $ac;
		$filter_option = "<option value=''>".get_caption('0740','- Show all user - ')."</option>";
		if($_SESSION["filter_user"] == "1") {
			$filter_option .= "<option value='1' selected='selected'>".get_caption('0260','Active')."</option>";
		} else {
			$filter_option .= "<option value='1'>".get_caption('0260','Active')."</option>";
		}
		if($_SESSION["filter_user"] == "0") {
			$filter_option .= "<option value='0' selected='selected'>".get_caption('0270','Inactive')."</option>";
		} else {
			$filter_option .= "<option value='0'>".get_caption('0270','Inactive')."</option>";
		}
		
		$filter_form = "<form method='post'>"
			."<table><tr>"
			."<td><select name='filter_user'>".$filter_option."</select></td>"
			."<td>".$ac->create_form_button("submit",get_caption('0700','Set'))."</td>"
			."</tr></table>"
			."</form>";
		
		return($filter_form);
	}
}
?>