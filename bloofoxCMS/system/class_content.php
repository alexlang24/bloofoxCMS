<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_content.php -
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

class content {

	// Init variables
	var $eid = 0;
	var $limit = 10; // Number of content entries per page
	
	var $title = "";
	var $action = "";
	var $user = "";
	var $pass = "";
	var $error = "";

	// Constructor
	function __construct()
	{
	}
	
	function content()
	{
	}
	
	// Set explorer id
	function set_eid($eid)
	{
		$this->eid = $eid;
	}
	
	// Set Login Vars
	function set_login_vars($title,$action,$user,$pass,$error)
	{
		$this->title = $title;
		$this->action = $action;
		$this->user = $user;
		$this->pass = $pass;
		$this->error = $error;
	}

	// build content
	function build_content($db,$login_required)
	{
		global $tbl_prefix, $tpl;
		
		switch($login_required)
		{
			case '1': // Login required
				// set template block
				$tpl->set_block("tmpl_content", "content", "content_handle");
				
				$tpl->set_var(array(
					"login_title"   => $this->title,
					"login_action"  => $this->action,
					"login_user"    => $this->user,
					"login_pass"    => $this->pass,
					"login_error"   => $this->error
					));
				
				// parse template
				$tpl->parse("content_handle", "content", true);
			break;
			
			default: // Standard Text Content
				// set template block
				$tpl->set_block("tmpl_content", "content", "content_handle");

				// get sys_contents
				$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$this->eid."' ORDER BY sorting");
				while($db->next_record()):
					$text = $this->format_text($db->f("text"));
					$tpl->set_var(array(
						"title"    => $db->f("title"),
						"text"     => $text
						));
					// parse template
					$tpl->parse("content_handle", "content", true);
				endwhile;
			break;
		}
	}
	
	// format text, return string
	function format_text($text)
	{
		$text = $text;
		
		return($text);
	}
	
	// create page navigation, return string
	function create_pages($db,$start,$sys_vars)
	{
		global $tbl_prefix,$sys_explorer_vars,$sys_config_vars;
		
		$sys_vars['limit'] = $this->limit;
		$sys_vars['start'] = 0;
		
		$sys_vars['start'] = $start * $sys_vars['limit'] - $sys_vars['limit'];
		if($sys_vars['start'] < 0) { $sys_vars['start'] = 0; }
		
		$db->query("SELECT cid FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$this->eid."' AND blocked = '0' ORDER BY sorting");
		$no_of_records = $db->num_rows();
		
		$factor = $no_of_records / $sys_vars['limit'];
		$factor = ceil($factor);
		
		$maxpage = 10;
		
		$part2 = $start+4;
		if($part2 <= $maxpage) {
		  $part2 = $maxpage;
		}
		
		$part1 = $start-5;
		if($part1 >= $factor - ($maxpage - 1)) {
		  $part1 = $factor - ($maxpage - 1);
		}
		
		if($factor > 1) {
			$sys_vars['pages'] = "<p><span class='bold'>".get_caption('2020','Pages').":</span> ";
			for($x=1; $x<=$factor; $x++) {
				if($factor <= $maxpage || ($factor >= $maxpage + 1 && $x >= $part1 && $x <= $part2)) {
					if($start == $x) {
						$sys_vars['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x)."'><span class='bold'>".$x."</span></a> ";
					} else {
						$sys_vars['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x)."'>".$x."</a> ";
					}
				}
			}
			$sys_vars['pages'] .= "</p>";
		}
		
		return($sys_vars);
	}
	
	// public: plugin_available, return yes,no
	function plugin_available($pid) {
		global $tbl_prefix;
		
		$db = new DB_Tpl();
		$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$pid."' LIMIT 1");
		while($db->next_record()):
			if($db->f("status") == 1) {
				return(1);
			}
		endwhile;
		
		return(0);
	}
	
	// public: get some page fields, used for Admincenter
	function get_page_vars($db,$eid=0)
	{
		global $tbl_prefix;
		
		$db->query("SELECT config_id,name,link_type,link_plugin,created_by FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$page_vars['config_id'] = $db->f("config_id");
			$page_vars['name'] = $db->f("name");
			$page_vars['link_type'] = $db->f("link_type");
			$page_vars['link_plugin'] = $db->f("link_plugin");
			$page_vars['created_by'] = $db->f("created_by");
		endwhile;
		
		return($page_vars);
	}
	
	// public: find first or last entry for next sorting number, used for Admincenter
	function get_sorting_number($db2,$insert="top",$eid=0)
	{
		global $tbl_prefix;
		$found = 0;
		
		if($insert == "top") {
			$db2->query("SELECT sorting FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$eid."' ORDER BY sorting LIMIT 1");
			while($db2->next_record()):
				$sorting = $db2->f("sorting") - 1;
				$found = 1;
			endwhile;
		}
		if($sorting <= 0 && $found == 1) {
			$insert = "bottom";
		}
		if($insert == "bottom") {
			$db2->query("SELECT sorting FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$eid."' ORDER BY sorting DESC LIMIT 1");
			while($db2->next_record()):
				$sorting = $db2->f("sorting") + 1;
				$found = 1;
			endwhile;
		}
		
		if($found == 0) {
			$sorting = 100000;
		}
		
		return($sorting);
	}
	
	// public: get a selection list from sys_media, used for Admincenter
	function get_media_selection($db2,$type=1,$name="images")
	{
		global $tbl_prefix;
		
		$images = "<select name='".$name."'><option>- ".get_caption('2500','Image')." -</option>";
		$db2->query("SELECT filename FROM ".$tbl_prefix."sys_media WHERE media_type = '".$type."'");
		while($db2->next_record()):
			$images .= "<option>".$db2->f("filename")."</option>";
		endwhile;
		$images .= "</select>";
		$images .= "\n<br /><a class='small' href='index.php?mode=content&page=media&action=new'>".get_caption('2470','Add Mediafile')."</a>";
		
		return($images);
	}
	
	// Return config_id from explorer_id
	function get_config_id_from_eid($db2,$eid)
	{
		global $tbl_prefix;
		
		$db2->query("SELECT config_id FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' LIMIT 1");
		while($db2->next_record()):
			$config_id = $db2->f("config_id");
		endwhile;
		
		return($config_id);
	}
}
?>