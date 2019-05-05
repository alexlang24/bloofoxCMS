<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/module_location_bar/location_bar.php -
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

// translations
include("plugins/module_location_bar/languages/".$sys_lang_vars['language']);

// get path to current page
function get_location($eid,$content="")
{
	global $tbl_prefix,$sys_config_vars;
	
	// if: Print Root Page
	$is_root_page = 0;
	
	if($sys_config_vars['root_id'] == $eid) {
		$is_root_page = 1;
	}
	
	
	if($is_root_page == 0) {	
		// Init libraries
		$db = new DB_Tpl();
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."'");
		while($db->next_record()): 
			if(empty($content)) {
				$content = "<a href='".create_url($db->f("eid"),$db->f("name"),$sys_config_vars['mod_rewrite'])."' title='".$db->f("name")."'>".$db->f("name")."</a>";
			} else {
				$content = "<a href='".create_url($db->f("eid"),$db->f("name"),$sys_config_vars['mod_rewrite'])."' title='".$db->f("name")."'>".$db->f("name")."</a> &gt;&gt; ".$content;
			}
			if($db->f("preid") != 0) {
				$content = get_location($db->f("preid"),$content);
			}
		endwhile;
	}
	
	return($content);
}

// Template Block-Setup
$tpl->set_block("plugin_module_location_bar", "location", "location_handle");

$tpl->set_var(array(
	"location_value"    => get_caption('F010','You are here:')." ".get_location($sys_explorer_vars['eid'])
	));

$tpl->parse("location_handle", "location", true);
?>