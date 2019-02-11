<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/navigation_bottom_menu/admin.php -
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

if($_GET['page'] == "del" && isset($_GET['bid']) && $sys_group_vars['demo'] == 0) {
	$db->query("DELETE FROM ".$tbl_prefix."plugin_bottom_menu WHERE bid = '".$_GET['bid']."'");
	load_url("index.php?mode=plugins&pluginID=12");
}

if(isset($_POST['add']) && isset($_POST['eid']) && $sys_group_vars['demo'] == 0) {
	$_POST['sorting'] = validate_text($_POST['sorting']);
	$db->query("INSERT INTO ".$tbl_prefix."plugin_bottom_menu VALUES('','".$_POST['cid']."','".$_POST['eid']."','".$_POST['sorting']."')");
	load_url("index.php?mode=plugins&pluginID=12");
}

$bm_vars['explorer'] = "";

//get all entries
function get_structure($content_expl,$which=0)
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
		$content_expl .= "<option value='".$db->f("eid")."' style='margin-left: ".$indent."px;'>".$db->f("name")."</option>";
			
		$content_expl = get_structure($content_expl,$db->f("eid"));
	endwhile;
	
	return($content_expl);
}
$bm_vars['explorer'] = get_structure($bm_vars['explorer']);

$db->query("SELECT cid,name FROM ".$tbl_prefix."sys_config ORDER BY cid");
while($db->next_record()):
	$bm_vars['config'] .= "<option value='".$db->f("cid")."'>".$db->f("name")."</option>";
endwhile;

$db->query("SELECT *,".$tbl_prefix."sys_explorer.name AS `page_name` FROM ".$tbl_prefix."plugin_bottom_menu, ".$tbl_prefix."sys_explorer, ".$tbl_prefix."sys_config
	WHERE ".$tbl_prefix."plugin_bottom_menu.explorer_id = ".$tbl_prefix."sys_explorer.eid && ".$tbl_prefix."sys_config.cid = ".$tbl_prefix."plugin_bottom_menu.config_id
	ORDER BY ".$tbl_prefix."plugin_bottom_menu.config_id,".$tbl_prefix."plugin_bottom_menu.sort ASC");
$bm_vars['rows'] = $db->num_rows();
		
while($db->next_record()):
	$tpl->set_var(array(
		"plugins_title"     => "<h1>Plugin: ".$plugin_name."</h1>",
		"plugins_menu"      => "",
		"bm_add_action"     => "index.php?mode=plugins&pluginID=12",
		"bm_add_cid"        => "<select name='cid'>".$bm_vars['config']."</select>",
		"bm_add_select"     => "<select name='eid'>".$bm_vars['explorer']."</select>",
		"bm_add_sorting"    => "<input type='text' name='sorting' size='5' maxlength='10' />",
		"bm_add_submit"     => "<input type='submit' name='add' value='".get_caption('2050','Add Page')."' />",
		"bm_str_config"     => get_caption('3020','Projects'),
		"bm_config_id"      => $db->f("name")." (".$db->f("config_id").")",
		"bm_str_eid"        => get_caption('2020','Pages'),
		"bm_explorer_id"    => $db->f("page_name")." (".$db->f("eid").")",
		"bm_str_sorting"    => get_caption('2460','Sorting'),
		"bm_sorting"        => $db->f("sort"),
		"bm_str_action"     => get_caption('0100','Action'),
		"bm_action"         => $ac->create_delete_icon("index.php?mode=plugins&pluginID=12&page=del&bid=".$db->f("bid"),get_caption('0211','Delete'))
		));

	// Parse template with variables
	$tpl->parse("plugins_handle", "plugins", true);
endwhile;
		
if($bm_vars['rows'] == 0) {
	$tpl->set_var(array(
		"plugins_title"     => "<h1>Plugin: ".$plugin_name."</h1>",
		"plugins_menu"      => "",
		"bm_add_action"     => "index.php?mode=plugins&pluginID=12",
		"bm_add_cid"        => "<select name='cid'>".$bm_vars['config']."</select>",
		"bm_add_select"     => "<select name='eid'>".$bm_vars['explorer']."</select>",
		"bm_add_sorting"    => "<input type='text' name='sorting' size='5' maxlength='10' />",
		"bm_add_submit"     => "<input type='submit' name='add' value='".get_caption('2050','Add Page')."' />",
		"bm_str_config"     => get_caption('3020','Projects'),
		"bm_config_id"      => "",
		"bm_str_eid"        => get_caption('2000','Contents'),
		"bm_explorer_id"    => "",
		"bm_str_sorting"    => get_caption('2460','Sorting'),
		"bm_sorting"        => "",
		"bm_str_action"     => get_caption('0100','Action'),
		"bm_action"         => ""
		));

	// Parse template with variables
	$tpl->parse("plugins_handle", "plugins", true);
}
?>