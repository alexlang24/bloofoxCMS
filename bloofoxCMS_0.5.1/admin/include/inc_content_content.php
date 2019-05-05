<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_content_content.php -
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

// load and init content class
require_once(SYS_FOLDER."/class_content.php");
$contents = new content();

// get explorer_id from cid
if(isset($_GET['cid'])) {
	$db->query("SELECT explorer_id FROM ".$tbl_prefix."sys_content WHERE cid = '".$_GET['cid']."' LIMIT 1");
	while($db->next_record()):
		$_GET['eid'] = $db->f("explorer_id");
	endwhile;
}

// convert post var to get var
if(isset($_POST['eid'])) {
	$_GET['eid'] = $_POST['eid'];
}

// header back when NOT link_type "standard" or "plugin"
$page_vars = $contents->get_page_vars($db,$_GET['eid']);

if($page_vars['link_type'] != 0 && $page_vars['link_type'] != 3) {
	load_url("index.php?mode=content&page=pages");
}

switch($action)
{
	case 'new':
		load_url("index.php?mode=content&page=content&page_id=".$_GET['eid']);
	break;

	case 'edit': // edit content entry
		load_url("index.php?mode=content&page=content&page_id=".$_GET['eid']);
	break;
			
	case 'del':
		load_url("index.php?mode=content&page=content&page_id=".$_GET['eid']);
	break;
			
	default: // Article list
		// Move actions
		if(isset($_GET['action']) && (($page_vars['link_type'] == 0 && $sys_rights['content_default']['write'] == 1) || ($page_vars['link_type'] == 3 && $sys_rights['content_plugins']['write'] == 1))) {
			if(!CheckInteger($_GET['cid'])) {
				unset($_GET['action']);
			}
			switch($_GET['action'])
			{
				case 'move_up':
					// get cid of prev entry
					$db->query("SELECT cid,sorting FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$_GET['eid']."' ORDER BY sorting DESC");
					while($db->next_record()):
						if($prev == 1) {
							$prev_cid = $db->f("cid");
							$prev_sorting = $db->f("sorting");
							$prev = 0;
						}
						if($db->f("cid") == $_GET['cid']) {
							$curr_sorting = $db->f("sorting");
							$prev = 1;
						}
					endwhile;
					// change sorting
					if($prev_cid != 0) {
						$db->query("UPDATE ".$tbl_prefix."sys_content SET sorting = ".$curr_sorting." WHERE cid = '".$prev_cid."' LIMIT 1");
						$db->query("UPDATE ".$tbl_prefix."sys_content SET sorting = ".$prev_sorting." WHERE cid = '".$_GET['cid']."' LIMIT 1");
					}
				break;
		
				case 'move_down':
					// get cid of next entry
					$db->query("SELECT cid,sorting FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$_GET['eid']."' ORDER BY sorting");
					while($db->next_record()):
						if($next == 1) {
							$next_cid = $db->f("cid");
							$next_sorting = $db->f("sorting");
							$next = 0;
						}
						if($db->f("cid") == $_GET['cid']) {
							$curr_sorting = $db->f("sorting");
							$next = 1;
						}
					endwhile;
					// change sorting
					if($next_cid != 0) {
						$db->query("UPDATE ".$tbl_prefix."sys_content SET sorting = ".$curr_sorting." WHERE cid = '".$next_cid."' LIMIT 1");
						$db->query("UPDATE ".$tbl_prefix."sys_content SET sorting = ".$next_sorting." WHERE cid = '".$_GET['cid']."' LIMIT 1");
					}
				break;
			}
			load_url("index.php?mode=content&page=content&eid=".$_GET['eid']);
		}
		
		// Create Table
		$content_list .= "<table class='list'>"
			."<tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('','ID')."</p></td>"
			."<td width='200'><p class='bold'>".get_caption('2400','Title')."</p></td>"
			."<td><p class='bold'>".get_caption('2170','Blocked')."</p></td>"
			."<td><p class='bold'>".get_caption('2320','Created by')."</p></td>"
			."<td><p class='bold'>".get_caption('2330','Created at')."</p></td>"
			."<td><p class='bold'>".get_caption('2340','Changed by')."</p></td>"
			."<td><p class='bold'>".get_caption('2350','Changed at')."</p></td>"
			."<td width='100'><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
			
		// settings
		$start = $_GET['start'];
		$limit = $s_limit; // default: 20
		
		$db->query("SELECT cid FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$_GET['eid']."'");
		$total = $db->num_rows();
		
		// create page handling
		$page_html_code = $ac->create_page_handling($total,$limit,"index.php?mode=content&page=content&start=",$start);
		$start = $ac->get_page_start($start,$limit);
		
		// Select Record
		$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$_GET['eid']."' ORDER BY sorting LIMIT ".$start.",".$limit."");
		
		while($db->next_record()):
			$cid = $db->f("cid");
			$sorting = $db->f("sorting") / 2;
			//$content_type = $db->f("content_type");
			
			$created_at = "";
			if($db->f("created_at") != "") {
				$created_at = date($sys_vars['datetime'],$db->f("created_at"));
			}
			
			$changed_at = "";
			if($db->f("changed_at") != "") {
				$changed_at = date($sys_vars['datetime'],$db->f("changed_at"));
			}
			
			$content_list .= "<tr class='bg_color2'>"
				."<td>".$db->f("cid")."</td>"
				."<td><a href='index.php?mode=content&page=articles&action=edit&cid=".$cid."&page_id=".$_GET['eid']."' title='".substr(strip_tags($db->f("text")),0,200)."'>".$db->f("title")."</a></td>"
				."<td>".translate_yesno($db->f("blocked"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>".$db->f("created_by")."</td>"
				."<td>".$created_at."</td>"
				."<td>".$db->f("changed_by")."</td>"
				."<td>".$changed_at."</td>"
				."<td>"
				.$ac->create_edit_icon("index.php?mode=content&page=articles&action=edit&cid=".$cid."&page_id=".$_GET['eid'],get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=content&page=articles&action=del&cid=".$cid."&page_id=".$_GET['eid'],get_caption('0211','Delete'))
				.$ac->create_move_action("index.php?mode=content&page=content&action=move_up&cid=".$cid,get_caption('2271','Move Up'),3)
				.$ac->create_move_action("index.php?mode=content&page=content&action=move_down&cid=".$cid,get_caption('2281','Move Down'),4)
				.$ac->create_preview_icon("../index.php?page=".$db->f("explorer_id"),get_caption('0421','Preview'))
				."</td>";
		endwhile;
		
		// Close Table
		$content_list .= "</table>";
		
		// Set variables
		$tpl->set_var(array(
			"content_title"       => "<h2>".get_caption('2000','Contents')." / ".get_caption('2020','Pages')." / ".get_caption('2030','Articles')."</h2>",
			"confirm_message"     => $ac->show_ok_message(GetConfirmMessage()),
			"content_new"         => $ac->create_add_icon("index.php?mode=content&page=articles&action=new&page_id=".$_GET['eid'],get_caption('2360','Add Article')),
			"content_list"        => $content_list,
			"page_handling"       => $page_html_code,
			"content_filter"      => $page_vars['name']
			));

		// Parse template with variables
		$tpl->parse("content_handle", "content", true);

	break;
}
?>