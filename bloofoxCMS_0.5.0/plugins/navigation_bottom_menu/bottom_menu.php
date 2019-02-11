<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/navigation_bottom_menu/bottom_menu.php -
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

// set template block
$tpl->set_block("plugin_navigation_bottom_menu", "bottom_menu", "bottom_menu_handle");


// init db class
$db2 = new DB_Tpl();

$db2->query( "SELECT ".$tbl_prefix."sys_explorer.eid, ".$tbl_prefix."sys_explorer.name, ".$tbl_prefix."sys_explorer.link_target, ".$tbl_prefix."sys_explorer.link_url, ".$tbl_prefix."sys_explorer.link_type  
	FROM ".$tbl_prefix."plugin_bottom_menu, ".$tbl_prefix."sys_explorer 
	WHERE ".$tbl_prefix."sys_explorer.eid = ".$tbl_prefix."plugin_bottom_menu.explorer_id && ".$tbl_prefix."plugin_bottom_menu.config_id = ".$sys_config_vars["cid"]."
	ORDER BY ".$tbl_prefix."plugin_bottom_menu.sort ASC" );
$bm_vars['rows'] = $db2->num_rows();

if($bm_vars['rows'] == 0) {
	$tpl->set_var(array(
		"bm_link"      => ""
	));
	// parse template
	$tpl->parse("bottom_menu_handle", "bottom_menu", true);
}

while($db2->next_record()):
	$bm_vars['target'] = "";
	if($db2->f("link_target") != "") {
		$bm_vars['target'] = "target='".$db2->f("link_target")."'";
	}
	
	if($db2->f("link_type") == 1) {
		$bm_vars['url'] = $db2->f("link_url");
	} else {
		$bm_vars['url'] = create_url($db2->f("eid"),$db2->f("name"),$sys_config_vars['mod_rewrite']);
	}
	
	$tpl->set_var(array(
		"bm_link"      => "<li><a href='".$bm_vars['url']."' title='".$db2->f("name")."' ".$bm_vars['target'].">".$db2->f("name")."</a></li>"
	));
	// parse template
	$tpl->parse("bottom_menu_handle", "bottom_menu", true);
endwhile;
?>