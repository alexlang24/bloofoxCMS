<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_admincenter.php -
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

class admincenter {
	//**
	// variables
	var $var = 1;
	var $insert_path = 0;
	var $current_page = 0;
	var $factor = 0;
	var $default_icon_path = "";
	
	//**
	// constructor
	function admincenter($frontend=0)
	{
		$_GET['cid'] = $this->clean_int_values($_GET['cid']);
		$_GET['eid'] = $this->clean_int_values($_GET['eid']);
		$_GET['mid'] = $this->clean_int_values($_GET['mid']);
		$_GET['lid'] = $this->clean_int_values($_GET['lid']);
		$_GET['tid'] = $this->clean_int_values($_GET['tid']);
		$_GET['pid'] = $this->clean_int_values($_GET['pid']);
		$_GET['gid'] = $this->clean_int_values($_GET['gid']);
		$_GET['sid'] = $this->clean_int_values($_GET['sid']);
		$_GET['userid'] = $this->clean_int_values($_GET['userid']);
		$_GET['start'] = $this->clean_int_values($_GET['start']);
		$_GET['page_id'] = $this->clean_int_values($_GET['page_id']);
		
		$this->default_icon_path = "../templates/admincenter/images/icon-set16/";
		if($frontend == 1) {
			$this->default_icon_path = "templates/admincenter/images/icon-set16/";
		}
	}
	
	//**
	// check and clean integer values in GET parameters
	function clean_int_values($value)
	{
		if(!CheckInteger($value)) {
			return(0);
		}
		return($value);
	}
	
	//**
	// creates action links to backend sites
	function create_link($url,$id,$title,$class,$caption,$target=0)
	{
		if(empty($url) || empty($id)) {
			return("<a href='#' title='".$title."' class='".$class."'>".$caption."</a> ");
		}
		
		if($target == 0) {
			return("<a href='".$url.$id."' title='".$title."' class='".$class."'>".$caption."</a> ");
		} else {
			return("<a href='".$url.$id."' title='".$title."' class='".$class."' target='_new'>".$caption."</a> ");
		}
	}
	
	//**
	// return username from user_id
	function get_username($user_id)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$db->query("SELECT username FROM ".$tbl_prefix."sys_user WHERE uid = '".$user_id."' ORDER BY uid");
		while($db->next_record()):
			$username = $db->f("username");
		endwhile;
		
		return($username);
	}
	
	//**
	// return language name from lang_id
	function get_lang_name($lang_id)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$db->query("SELECT name FROM ".$tbl_prefix."sys_lang WHERE lid = '".$lang_id."' ORDER BY lid");
		while($db->next_record()):
			$lang_name = $db->f("name");
		endwhile;
		
		return($lang_name);
	}
	
	//**
	// return template name from tmpl_id
	function get_tmpl_name($tmpl_id)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$db->query("SELECT name FROM ".$tbl_prefix."sys_template WHERE tid = '".$tmpl_id."' ORDER BY tid");
		while($db->next_record()):
			$tmpl_name = $db->f("name");
		endwhile;
		
		return($tmpl_name);
	}
	
	//**
	// return user groups
	function get_sys_usergroups($groups,$action)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$sys_usergroups = "";
		$sys_counter = 0;
		$db->query("SELECT name FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			$sys_counter += 1;
			$group_checked = 0;
			
			if($action == "edit") {
				for($i=0;$i<count($groups);$i++)
				{
					if($groups[$i] == $db->f("name")) {
						$group_checked = 1;
					}
				}
			} else {
				if($_POST[$sys_counter] == true) {
					$group_checked = 1;
				}
			}
			
			if($group_checked) {
				$sys_usergroups .= "<input type='checkbox' name='".$sys_counter."' value='".$db->f("name")."' checked='checked' /> ".$db->f("name")."<br />";
			} else {
				$sys_usergroups .= "<input type='checkbox' name='".$sys_counter."' value='".$db->f("name")."' /> ".$db->f("name")."<br />";
			}
		endwhile;
		
		return($sys_usergroups);
	}
	
	//**
	// return  templates
	function get_sys_templates($current)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();

		$sys_tmpl = "";
		$db->query("SELECT tid,name FROM ".$tbl_prefix."sys_template WHERE be = '0' ORDER BY tid");
		while($db->next_record()):
			if($current == $db->f("tid")) {
				$sys_tmpl .= "<option value='".$db->f("tid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_tmpl .= "<option value='".$db->f("tid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		return($sys_tmpl);
	}
	
	//**
	// return languages
	function get_sys_languages($current)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$sys_lang = "";
		$db->query("SELECT lid,name FROM ".$tbl_prefix."sys_lang ORDER BY lid");
		while($db->next_record()):
			if($current == $db->f("lid")) {
				$sys_lang .= "<option value='".$db->f("lid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_lang .= "<option value='".$db->f("lid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		return($sys_lang);
	}
	
	//**
	// return charsets
	function get_sys_charset($current)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$sys_charset = "";
		$db->query("SELECT * FROM ".$tbl_prefix."sys_charset ORDER BY name");
		while($db->next_record()):
			if($current == $db->f("name")) {
				$sys_charset .= "<option value='".$db->f("name")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_charset .= "<option value='".$db->f("name")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		return($sys_charset);
	}
	
	//**
	// return doctypes
	function get_doctypes($current)
	{
		global $doctype_setup;
		
		$doctypes = "";
		for($i=0; $i<count($doctype_setup); $i++) {
			if($current == $doctype_setup[$i]) {
				$doctypes .= "<option value='".$doctype_setup[$i]."' selected='selected'>".$doctype_setup[$i]."</option>";
			} else {
				$doctypes .= "<option value='".$doctype_setup[$i]."'>".$doctype_setup[$i]."</option>";
			}
		}
		
		return($doctypes);
	}
	
	//**
	// return group names for insert
	function get_group_names_for_insert()
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$groups = "";
		$sys_counter = 0;
		$db->query("SELECT name FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			$sys_counter += 1;
			if($_POST[$sys_counter]) {
				$groups .= $db->f("name").",";
			}
		endwhile;
		$groups = substr($groups,0,-1);
		
		return($groups);
	}
	
	//**
	// return user groups for selection
	function get_sys_groups($current)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		$sys_groups = "";
		$db->query("SELECT gid,name FROM ".$tbl_prefix."sys_usergroup ORDER BY gid");
		while($db->next_record()):
			if($current == $db->f("gid")) {
				$sys_groups .= "<option value='".$db->f("gid")."' selected='selected'>".$db->f("name")."</option>";
			} else {
				$sys_groups .= "<option value='".$db->f("gid")."'>".$db->f("name")."</option>";
			}
		endwhile;
		
		return($sys_groups);
	}
	
	//**
	// get files of a folder and make a list
	function get_files($folder,$selected,$format="")
	{
		$dir = opendir($folder);
		while($file = readdir($dir))
		{
			// only files in $format
			if($format != "") {
				$strpos = strpos($file,$format);
			} else {
				if(strpos($file,".") != 0) {
					$strpos = 0;
				} else {
					$strpos = 1;
				}
			}
			if($file != "." && $file != ".." && $strpos != 0)
			{
				if($this->insert_path == 1) {
					$file = $folder.$file;
				}
				if($selected == $file) {
					$dir_list .= "<option value='".$file."' selected='selected'>".$file."</option>";
				} else {
					$dir_list .= "<option value='".$file."'>".$file."</option>";
				}
			}
		}
		closedir($dir);
		return($dir_list);
	}
	
	//**
	// return folders
	function get_folders($current)
	{
		global $folder_setup;
		
		$folders = "";
		for($i=0; $i<count($folder_setup); $i++) {
			if($current == $folder_setup[$i]) {
				$folders .= "<option value='".$folder_setup[$i]."' selected='selected'>".$folder_setup[$i]."</option>";
			} else {
				$folders .= "<option value='".$folder_setup[$i]."'>".$folder_setup[$i]."</option>";
			}
		}
		
		// add template folders automatically
		$this->insert_path = 1;
		$folders .= $this->get_files("../templates/",$current,"");
		
		// add image folders automatically (folders in dir /media/images)
		$folders .= $this->get_files("../media/images/",$current,"");
		
		$this->insert_path = 0;
		return($folders);
	}
	
	//**
	// Get update information if relevant
	function get_update_message()
	{
		$version['current'] = get_current_version();
		$version['current'] = explode(" ",$version['current']);
		$version['current'] = $version['current'][1];
		
		if(file_exists("../update/index.php")) {
			$update_link = "<a href='../update'>".get_caption('5250','Update')."</a>";
		}
		$version['check'] =	"<p class='info'>".get_caption('9240','Update is available. It is recommended to update to the latest version.')." ".$update_link."</p>";
		
		return($version);
	}
	
	//**
	// return language file for admincenter
	function get_admin_language_file($db)
	{
		global $tbl_prefix;
		
		if(isset($_SESSION['uid'])) {
			$db->query("SELECT be_lang FROM ".$tbl_prefix."sys_profile WHERE user_id = '".$_SESSION['uid']."' ORDER BY user_id LIMIT 1");
			while($db->next_record()):
				$lid = $db->f("be_lang");
			endwhile;
			
			$db->query("SELECT filename FROM ".$tbl_prefix."sys_lang WHERE lid = '".$lid."' ORDER BY lid LIMIT 1");
			while($db->next_record()):
				$filename = $db->f("filename");
			endwhile;
			
			if(mandatory_field($filename)) {
				return($filename);
			}
		}
		
		$db->query("SELECT filename FROM ".$tbl_prefix."sys_lang ORDER BY lid LIMIT 1");
		while($db->next_record()):
			$filename = $db->f("filename");
		endwhile;
		
		return($filename);
	}
	
	//**
	// validate post variables for projects
	function validate_post_vars_project()
	{
		$_POST['name'] = validate_text($_POST['name']);
		$_POST['meta_title'] = validate_text($_POST['meta_title']);
		$_POST['meta_desc'] = validate_text($_POST['meta_desc']);
		$_POST['meta_keywords'] = validate_text($_POST['meta_keywords']);
		$_POST['meta_author'] = validate_text($_POST['meta_author']);
		$_POST['meta_copyright'] = validate_text($_POST['meta_copyright']);
		$_POST['mail'] = validate_text($_POST['mail']);
	}
	
	//**
	// form management
	function create_form_button($btn_type="submit",$btn_caption="Submit",$css_class="btn",$btn_name="send") {
		$btn_html_code = "<input class='".$css_class."' type='".$btn_type."' name='".$btn_name."' value='".$btn_caption."' />";
		return($btn_html_code);
	}
	
	function load_cancel_action($btn_name,$url) {
		if(isset($btn_name)) {
			load_url($url);
		}
	}
	
	//**
	// icon management
	function create_add_icon($url,$title,$icon_path="add.png",$link_class="edit") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_edit_icon($url,$title,$icon_path="edit.png",$link_class="edit") {
	$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_delete_icon($url,$title,$icon_path="delete.png",$link_class="delete") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_preview_icon($url,$title,$icon_path="preview.png",$link_class="edit") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_editcontent_icon($url,$title,$icon_path="article_edit.png",$link_class="edit") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_logout_icon($url,$title,$icon_path="logout.png",$link_class="delete") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_editprofile_icon($url,$title,$icon_path="profile_edit.png",$link_class="edit") {
		$icon_path = $this->default_icon_path.$icon_path;
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	function create_legend_icon($which) {
		if($which == 1) { 
			$icon_path=$this->default_icon_path."add.png";
		}
		if($which == 2) { 
			$icon_path=$this->default_icon_path."edit.png";
		}
		if($which == 3) { 
			$icon_path=$this->default_icon_path."delete.png";
		}
		if($which == 4) { 
			$icon_path=$this->default_icon_path."article_edit.png";
		}
		if($which == 5) { 
			$icon_path=$this->default_icon_path."preview.png";
		}
		if($which == 6) { 
			$icon_path=$this->default_icon_path."profile_edit.png";
		}
		if($which == 7) { 
			$icon_path=$this->default_icon_path."logout.png";
		}

		$icon_html_code = "<img src='".$icon_path."' alt='".$which."' border='0' width='16' height='16' /> ";
		return($icon_html_code);
	}
	
	function create_move_action($url,$title,$icon,$link_class="edit") {
		if($icon == 1) {
			$icon_path=$this->default_icon_path."move_left.png";
		}
		if($icon == 2) {
			$icon_path=$this->default_icon_path."move_right.png";
		}
		if($icon == 3) {
			$icon_path=$this->default_icon_path."move_up.png";
		}
		if($icon == 4) {
			$icon_path=$this->default_icon_path."move_down.png";
		}
		
		$icon_html_code = "<a class='".$link_class."' href='".$url."' title='".$title."'><img src='".$icon_path."' alt='".$title."' border='0' width='16' height='16' /></a> ";
		return($icon_html_code);
	}
	
	//**
	// message management
	function show_error_message($caption,$msg_class="error") {
		if($caption == "") {
			return("");
		}
		$icon_path = $this->default_icon_path."error.png";
		return("<p class='".$msg_class."'><img src='".$icon_path."' alt='error.png' border='0' width='16' height='16' /> ".$caption."</p>");
	}
	
	function show_ok_message($caption,$msg_class="ok") {
		if($caption == "") {
			return("");
		}
		$icon_path = $this->default_icon_path."ok.png";
		return("<p class='".$msg_class."'><img src='".$icon_path."' alt='ok.png' border='0' width='16' height='16' /> ".$caption."</p>");
	}
	
	function show_info_message($caption,$msg_class="info") {
		if($caption == "") {
			return("");
		}
		$icon_path = $this->default_icon_path."info.png";
		return("<p class='".$msg_class."'><img src='".$icon_path."' alt='info.png' border='0' width='16' height='16' /> ".$caption."</p>");
	}
	
	function show_question_message($caption,$msg_class="info") {
		if($caption == "") {
			return("");
		}
		$icon_path = $this->default_icon_path."question.png";
		return("<p class='".$msg_class."'><img src='".$icon_path."' alt='question.png' border='0' width='16' height='16' /> ".$caption."</p>");
	}
	
	// show demo error
	function show_demo_error($demo_mode=0) {
		if($demo_mode == 1) {
			return($this->show_info_message(get_caption('9220','You are logged in with a demo user.')));
		}
		return('');
	}
	
	//**
	// page handling
	function create_page_handling($total,$limit=20,$url,$start=1) {
		if($start == 0) { $start = 1; }
		
		$this->factor = $total / $limit;
		$this->factor = ceil($this->factor);
		
		$maxpage = 10;
		
		$part2 = $start+4;
		if($part2 <= $maxpage) {
		  $part2 = $maxpage;
		}
		
		$part1 = $start-5;
		if($part1 >= $this->factor - ($maxpage - 1)) {
		  $part1 = $this->factor - ($maxpage - 1);
		}
		
		if($this->factor > 1) {
			$page_html_code = "";
			for($x=1; $x<=$this->factor; $x++) {
				if($this->factor <= $maxpage || ($this->factor >= $maxpage + 1 && $x >= $part1 && $x <= $part2)) {
					if($start == $x) {
						$page_html_code .= "<a href='".$url.$x."'><span class='bold'>".$x."</span></a> ";
						$this->current_page = $x;
					} else {
						$page_html_code .= "<a href='".$url.$x."'>".$x."</a> ";
					}
				}
			}
		}
		
		$page_html_code = $this->set_page_next($page_html_code,$url);
		
		return($page_html_code);
	}
	
	function get_page_start($start,$limit=20) {
		$start = $start * $limit - $limit;
		if($start < 0) { 
			$start = 0;
		}
		
		return($start);
	}
	
	private function set_page_next($page_html_code,$url) {
		if($this->current_page == 0) {
			$this->current_page = 1;
		}
		
		if($this->current_page > 1) {
			$prev = $this->current_page - 1;
			$new_page_html_code = "<a href='".$url."1"."'>".get_caption('0301','|< First')."</a> <a href='".$url.$prev."'>".get_caption('0300','Previous')."</a>";
		} else {
			$new_page_html_code = get_caption('0301','|< First')." ".get_caption('0300','Previous');
		}

		$new_page_html_code = $new_page_html_code." ".$page_html_code;
		
		if($this->current_page < $this->factor) {
			$next = $this->current_page + 1;
			$new_page_html_code = $new_page_html_code." "."<a href='".$url.$next."'>".get_caption('0310','Next')."</a> <a href='".$url.$this->factor."'>".get_caption('0311','Last >|')."</a>";
		} else {
			$new_page_html_code = $new_page_html_code." ".get_caption('0310','Next')." ".get_caption('0311','Last >|');
		}
		
		$new_page_html_code = "<div class='page_handling'>".get_caption('2390','Page')." ".$this->current_page." ".get_caption('0315','of')." ".$this->factor.": ".$new_page_html_code."</div>";
		
		return($new_page_html_code);
	}
}
?>