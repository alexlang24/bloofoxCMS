<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/mod_content.php -
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

// Set template block
$tpl->set_block("tmpl_content", "content", "content_handle");

// include plugin_wysiwyg_ckeditor3.6.4
if($admin_plugin[10001] == 1 && $admin_plugin[10000] == 0) {
	include_once("../plugins/wysiwyg_ckeditor3_6_4/install.php");
}

function get_structure($content_expl,$which=0,$selected_id=0)
{
	global $tbl_prefix,$sys_vars;
	// Init libraries
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
			
		$content_expl = get_structure($content_expl,$db->f("eid"),$selected_id);
	endwhile;
	
	return($content_expl);
}

include_once("include".$tmpl_set['inc']);

?>