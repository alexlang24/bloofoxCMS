<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_pages.php -
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

class pages {

	// Init variables
	var $sys_time = 0;
	var $sys_explorer_vars = array();

	// Constructor
	function __construct()
	{
		$this->sys_time = time();
	}
	
	function pages()
	{
		self::__construct();
	}

	// Get correct explorer entry from database
	function get_explorer_vars($db,$sys_config_vars,$sys_explorer_id)
	{
		global $tbl_prefix;
		
		// check if requested page is used for an user login
		$db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE login_page = '".$sys_explorer_id."' ORDER BY uid");
		$user_result = $db->num_rows();

		if($user_result > 0 && $_SESSION["login_page"] == $sys_explorer_id) {
			// select page for user login
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$sys_explorer_id."' && blocked = '0' && startdate < '".$this->sys_time."' && (enddate > '".$this->sys_time."' || enddate = '') ORDER BY eid LIMIT 1");
			$q = $db->num_rows();
		} else {
			// select requested page
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$sys_explorer_id."' && blocked = '0' && startdate < '".$this->sys_time."' && (enddate > '".$this->sys_time."' || enddate = '') ORDER BY eid LIMIT 1");
			$q = $db->num_rows();
		}
		
		if($q > 0) {
			while($db->next_record()):
				$eid = $db->f("eid");
			endwhile;
			
			$this->set_explorer($db,$eid);
		}
		
		// select root page, if explorer page couldn't be found
		if($q == 0) {
			$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$sys_config_vars['root_id']."' ORDER BY eid LIMIT 1");
			$q = $db->num_rows();

			if($q == 0) {
				die("Error: Couldn't find root page! Class: class_pages.php");
			}
			
			while($db->next_record()):
				$eid = $db->f("eid");
			endwhile;
			
			$this->set_explorer($db,$eid);
		}
		
		return($this->sys_explorer_vars);
	}
	
	// Set sys_explorer_vars-Array
	function set_explorer($db,$eid)
	{
		global $tbl_prefix;
		$explorer_vars = array();
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		
		while($db->next_record()):
			$explorer_vars["eid"] = $db->f("eid");
			$explorer_vars["level"] = $db->f("level");
			$explorer_vars["preid"] = $db->f("preid");
			$explorer_vars["sorting"] = $db->f("sorting");
			$explorer_vars["config_id"] = $db->f("config_id");
			$explorer_vars["name"] = $db->f("name");
			$explorer_vars["link_type"] = $db->f("link_type");
			$explorer_vars["link_target"] = $db->f("link_target");
			$explorer_vars["link_url"] = $db->f("link_url");
			$explorer_vars["link_eid"] = $db->f("link_eid");
			$explorer_vars["link_plugin"] = $db->f("link_plugin");
			$explorer_vars["link_param"] = $db->f("link_param");
			$explorer_vars["groups"] = $db->f("groups");
			$explorer_vars["keywords"] = $db->f("keywords");
			$explorer_vars["template_id"] = $db->f("template_id");
			$explorer_vars["description"] = $db->f("description");
			$explorer_vars["title"] = $db->f("title");
		endwhile;
		
		$this->sys_explorer_vars = $explorer_vars;
	}

	// select explorer entries for selection
	function get_structure($content_expl,$which=0,$selected_id=0)
	{
		global $tbl_prefix;
		$db = new DB_Tpl();
		
		if(isset($_SESSION["filter_content"])) {
			$db->query("SELECT eid,name,level FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_SESSION["filter_content"]."' && preid = '".$which."' ORDER BY preid,sorting");
		} else {
			$db->query("SELECT eid,name,level FROM ".$tbl_prefix."sys_explorer WHERE preid = '".$which."' ORDER BY preid,sorting");
		}
		while($db->next_record()):
			$indent = $db->f("level") * 10 - 10;
			if($selected_id == $db->f("eid")) {
				$content_expl .= "<option value='".$db->f("eid")."' selected='selected' style='margin-left: ".$indent."px; font-weight: bold;'>".$db->f("name")."</option>";
			} else {
				$content_expl .= "<option value='".$db->f("eid")."' style='margin-left: ".$indent."px;'>".$db->f("name")."</option>";
			}
				
			$content_expl = $this->get_structure($content_expl,$db->f("eid"),$selected_id);
		endwhile;
		
		return($content_expl);
	}
}
?>