<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_content_levels.php -
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

if(isset($_GET['action']) && $sys_group_vars['demo'] == 0 && $sys_rights['content_levels']['write'] == 1) {
	// init structure class
	$tree = new structure($s_level_no);
		
	// save changes in database
	if(!CheckInteger($_GET['eid'])) {
		unset($_GET['action']);
	}
	switch($_GET['action'])
	{
		case 'move_left':
			$tree->move_left($db,$_GET['eid']);
		break;
		
		case 'move_right':
			$tree->move_right($db,$_GET['eid']);
		break;
		
		case 'move_up':
			$tree->move_up($db,$_GET['eid']);
		break;
		
		case 'move_down':
			$tree->move_down($db,$_GET['eid']);
		break;
	}
	// header url
	load_url("index.php?mode=content&page=levels");
}

// init filter class
$filters = new filter();

// Filter
if(isset($_POST["send"])) {
	if($_POST["filter_content"] == "") {
		unset($_SESSION["filter_content"]);
	} else {
		$_SESSION["filter_content"] = $_POST["filter_content"];
	}
	load_url("index.php?mode=content&page=levels");
}

$content_filter = $filters->create_filter_content($db);


// Create explorer overview
// Headline
$content_expl = "<table class='list'><tr class='bg_color3'>"
	."<td width=\"80\"><p class='bold'>".get_caption('','')."</p></td>"
	."<td><p class='bold'>".get_caption('','ID')."</p></td>"
	."<td><p class='bold'>".get_caption('0110','Name')."</p></td>"
	."<td><p class='bold'>".get_caption('2140','Type')."</p></td>"
	."<td><p class='bold'>".get_caption('2450','Previous ID')."</p></td>"
	."<td><p class='bold'>".get_caption('2460','Sorting')."</p></td>"
	."<td width=\"80\"><p class='bold'>".get_caption('0100','Action')."</p></td>"
	."</tr>";

function get_entries($content_expl,$which=0)
{
	global $tbl_prefix,$sys_vars,$expl_structure,$ac;
	// Init libraries
	$db = new DB_Tpl();
	
	if(isset($_SESSION["filter_content"])) {
		$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$_SESSION["filter_content"]."' && preid = '".$which."' && blocked = '0' ORDER BY preid,sorting");
	} else {
		$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE preid = '".$which."' && blocked = '0' ORDER BY preid,sorting");
	}
	while($db->next_record()):
		switch($db->f("link_type"))
		{
			case '3': $link_type = get_caption('2130','Plugin'); break;
			case '2': $link_type = get_caption('2120','Shortcut'); break;
			case '1': $link_type = get_caption('2110','URL'); break;
			default: $link_type = get_caption('2100','Standard'); break;
		}

		$indent = $db->f("level") * 10 - 10;
		$bold = "";
		if($db->f("level") == 1) {
			$bold = " class='bold'";
		}
		$content_expl .= "<tr class='bg_color2'>"
			."<td>"
			.$ac->create_move_action("index.php?mode=content&page=levels&action=move_left&eid=".$db->f("eid"),get_caption('2251','Move Left'),1)
			.$ac->create_move_action("index.php?mode=content&page=levels&action=move_right&eid=".$db->f("eid"),get_caption('2261','Move Right'),2)
			.$ac->create_move_action("index.php?mode=content&page=levels&action=move_up&eid=".$db->f("eid"),get_caption('2271','Move Up'),3)
			.$ac->create_move_action("index.php?mode=content&page=levels&action=move_down&eid=".$db->f("eid"),get_caption('2281','Move Down'),4)
			."</td>"
			."<td>".$db->f("eid")."</td>"
			."<td><p style='text-indent: ".$indent."px;'><span".$bold.">".$db->f("name")."</span></p></td>"
			."<td>".$link_type."</td>"
			."<td>".$db->f("preid")."</td>"
			."<td>".$db->f("sorting")."</td>"
			."<td>"
			.$ac->create_edit_icon("index.php?mode=content&page=pages&action=edit&eid=".$db->f("eid"),get_caption('0201','Edit'))
			.$ac->create_delete_icon("index.php?mode=content&page=pages&action=del&eid=".$db->f("eid"),get_caption('0211','Delete'))
			.$ac->create_editcontent_icon("index.php?mode=content&page=content&eid=".$db->f("eid"),get_caption('0221','Articles'))
			.$ac->create_preview_icon("../index.php?page=".$db->f("eid"),get_caption('0421','Preview'))
			."</td>"
			."</tr>";
		$content_expl = get_entries($content_expl,$db->f("eid"));
	endwhile;
	
	return($content_expl);
}

$content_expl = get_entries($content_expl);
$content_expl .= "</table>";

// Set variables
$tpl->set_var(array(
	"content_title"    => "<h2>".get_caption('2000','Contents')." / ".get_caption('2010','Structure')."</h2>",
	"confirm_message"  => $ac->show_ok_message(GetConfirmMessage()),
	"expl_overview"    => $content_expl,
	"expl_filter"      => $content_filter,
	"expl_new"         => $ac->create_add_icon("index.php?mode=content&page=pages&action=new",get_caption('2050','Add Page'))
	));

// Parse template with variables
$tpl->parse("content_handle", "content", true);
?>