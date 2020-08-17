<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/navigation_submenu_default/submenu.php -
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
//
// You should have received a copy of the GNU General Public License
// along with bloofoxCMS; if not, please contact the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//*****************************************************************//

$curr_time = time();
$db2 = new DB_Tpl();

// Set Template Block for "submenu"
$tpl->set_block("plugin_navigation_submenu_default", "submenu_default", "submenu_default_handle");

// get the route of eids from current page up to level 2
function get_level_eids($level_eids,$which)
{
	global $tbl_prefix,$sys_explorer_vars,$sys_config_vars;
	
	// Init libraries
	$db = new DB_Tpl();
	$curr_time = time();
	$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$which."' && config_id = '".$sys_explorer_vars['config_id']."' && blocked = '0' && startdate < '".$curr_time."' && (enddate > '".$curr_time."' || enddate = '') ORDER BY preid,sorting");
	while($db->next_record()):
		$level_eids[$db->f("level")] = $db->f("eid");
		if($db->f("level") > 1) {
			$level_eids = get_level_eids($level_eids,$db->f("preid"));
		}
	endwhile;
	
	return($level_eids);
}
	
$level_eids = get_level_eids($level_eids,$sys_explorer_vars['eid']);
	
// Select Records
function set_entries($level_eids,$which,$total=0)
{
	global $tpl,$tbl_prefix,$sys_explorer_vars,$sys_config_vars;
	
	// Init libraries
	$db = new DB_Tpl();
	$curr_time = time();
	
	// Get Records
	$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE preid = '".$which."' && blocked = '0' && invisible = '0' && startdate < '".$curr_time."' && (enddate > '".$curr_time."' || enddate = '') ORDER BY sorting");
	while($db->next_record()):
		$menu_vars['target'] = "";
		if($db->f("link_target") != "") {
			$menu_vars['target'] = "target='".$db->f("link_target")."'";
		}
		$menu_vars['active'] = "";
		if($sys_explorer_vars['eid'] == $db->f("eid")) {
			$menu_vars['active'] = "class='active'";
		}
	
		if($db->f("link_type") == 1) {
			$menu_vars['name'] = "<a ".$menu_vars['active']." href='".$db->f("link_url")."' ".$menu_vars['target']." title='".$db->f("name")."'>".$db->f("name")."</a>";
		} else {
			$menu_vars['name'] = "<a ".$menu_vars['active']." href='".create_url($db->f("eid"),$db->f("name"),$sys_config_vars['mod_rewrite'])."' ".$menu_vars['target']." title='".$db->f("name")."'>".$db->f("name")."</a>";
		}
		
		if($db->f("level") > 3) {
			$menu_vars['width'] = ($db->f("level") - 3) * 10;
			$menu_vars['style'] = "style='margin-left: ".$menu_vars['width']."px'";
		}

		// Set Variables
		$tpl->set_var(array(
			"submenu_title"  => "<p class='bold'>".get_caption('2000','Contents')."</p>",
			"submenu_name"   => $menu_vars['name'],
			"submenu_style"  => $menu_vars['style']
			));
		// Parse Template
		$tpl->parse("submenu_default_handle", "submenu_default", true);
		
		$total++;
		// level check
		if($level_eids[$db->f("level")] == $db->f("eid")) {
			$total = set_entries($level_eids,$db->f("eid"),$total);
		}
	endwhile;		
	
	return($total);
}

if($login_required == 0) {
	$menu_vars['total'] = set_entries($level_eids,$menu_vars['level2eid']);
}

if($login_required == 1 || $menu_vars['total'] == 0)
{
	$tpl->set_var(array(
		"submenu_title"  => "",
		"submenu_name"   => "",
		"submenu_style"  => ""
		));
	$tpl->parse("submenu_default_handle", "submenu_default", true);
}
?>