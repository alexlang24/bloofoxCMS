<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/navigation_menu_default/menu.php -
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

$curr_time = time();
$db2 = new DB_Tpl();

// get level 2 entry of selected entry to mark active link
function menu2_get_level2_eid($db2,$preid)
{
	global $tbl_prefix;
	
	$db2->query("SELECT eid,preid,level FROM ".$tbl_prefix."sys_explorer WHERE eid = ".$preid." ORDER BY preid,sorting LIMIT 1");
	while($db2->next_record()):
		$eid = $db2->f("eid");
		$preid = $db2->f("preid");
		$level = $db2->f("level");
	endwhile;
	
	if($level == 2) {
		return($eid);
	} else {
		if(isset($level)) {
			$eid = menu2_get_level2_eid($db2,$preid);
		}
	}
	
	return($eid);
}

$menu_vars['level2eid'] = menu2_get_level2_eid($db2,$sys_explorer_vars['preid']);
if(empty($menu_vars['level2eid'])) {
	$menu_vars['level2eid'] = $sys_explorer_vars['eid'];
}

// Set Template Block for "menu"
$tpl->set_block("plugin_navigation_menu_default", "menu_default", "menu_default_handle");

// Select Records
$db2->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$sys_explorer_vars['config_id']."' && level = 2 && blocked = '0' && invisible = '0' && startdate < '".$curr_time."' && (enddate > '".$curr_time."' || enddate = '') ORDER BY sorting");

if($db2->num_rows() == 0)
{
	$tpl->set_var(array(
		"menu_name"   => ""
		));
	$tpl->parse("menu_default_handle", "menu_default", true);
}

while($db2->next_record()):
	$menu_vars['target'] = "";
	if($db2->f("link_target") != "") {
		$menu_vars['target'] = "target='".$db2->f("link_target")."'";
	}
	$menu_vars['active'] = "";
	if($sys_explorer_vars['eid'] == $db2->f("eid") || $menu_vars['level2eid'] == $db2->f("eid")) {
		$menu_vars['active'] = "class='active'";
	}
		
	if($db2->f("link_type") == 1) {
		$menu_vars['name'] = "<a ".$menu_vars['active']." href='".$db2->f("link_url")."' ".$menu_vars['target']." title='".$db2->f("name")."'>".$db2->f("name")."</a>";
	} else {
		$menu_vars['name'] = "<a ".$menu_vars['active']." href='".create_url($db2->f("eid"),$db2->f("name"),$sys_config_vars['mod_rewrite'])."' ".$menu_vars['target']." title='".$db2->f("name")."'>".$db2->f("name")."</a>";
	}
	// Set Variables
	$tpl->set_var(array(
		"menu_name"   => $menu_vars['name']
		));
	// Parse Template
	$tpl->parse("menu_default_handle", "menu_default", true);
endwhile;
?>