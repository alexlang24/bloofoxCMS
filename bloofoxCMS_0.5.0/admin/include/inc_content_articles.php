<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_content_articles.php -
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

// init filter class
$filters = new filter();

// check source
$eid = $_POST['eid'];
if(isset($_GET['page_id']) && $_GET['page_id'] > 0) {
	$page_source_url = "pages";
	$eid = $_GET['page_id'];
	$page_id = $eid;
} else {
	$page_source_url = "articles";
	$page_id = 0;
}

switch($action)
{
	case 'new':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0) {
			// get needed page field values
			$page_vars = $contents->get_page_vars($db,$_POST['eid']);
			
			// check permissions for page link_type "standard" or "plugin"
			if(($page_vars['link_type'] == 0 && $sys_rights['content_default']['write'] == 1) || ($page_vars['link_type'] == 3 && $sys_rights['content_plugins']['write'] == 1)) {
				// format input fields
				$_POST['title'] = validate_text($_POST['title']);
				$_POST['text'] = replace_specialchars($_POST['text']);
				$_POST['startdate'] = validate_date($_POST['startdate']);
				$_POST['enddate'] = validate_date($_POST['enddate']);
				
				if($_POST['startdate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9430','The starting date is invalid. Please consider the format.')); $_POST['startdate'] = ""; }
				if($_POST['enddate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9440','The ending date is invalid. Please consider the format.')); $_POST['enddate'] = ""; }

				if($admin_plugin[10000] == 0) {
					$_POST['text'] = text_2_html($_POST['text']);
				}
				
				if($error == "") {
					CreateConfirmMessage(1,get_caption("0400","New entry was successfully added."));
					$sorting = $contents->get_sorting_number($db,$_POST['insert'],$_POST['eid']);
					$config_id = $contents->get_config_id_from_eid($db,$_POST['eid']);
					
					$created_by = $_SESSION["username"];
					$created_at = time();
					
					$db->query("INSERT INTO ".$tbl_prefix."sys_content VALUES ('','".$_POST['eid']."','".$sorting."','".$config_id."','','".$_POST['title']."','".$_POST['text']."','".$_POST['blocked']."','".$created_by."','".$created_at."','','','".$_POST['startdate']."','".$_POST['enddate']."')");
					
					// header url
					if($page_source_url == "pages") {
						load_url("index.php?mode=content&page=content&eid=".$_POST['eid']);
					} else {
						load_url("index.php?mode=content&page=articles");
					}
				}
			} else {
				$error = $ac->show_error_message(get_caption('9510','You can add articles only for pages on type "Standard" and "Plugin".'));
			}
		}
			
		// include SPAW wysiwyg-editor for field text
		if($admin_plugin[10000] == 1) {
			include_once("../plugins/wysiwyg_spaw2/spaw.inc.php");
			$sw = new SpawEditor('text',
				stripslashes($_POST['text']),
				$sys_vars['token'], // language
				'', // toolbar mode
				'', // theme
				$sys_setting_vars["textbox_width"].'px', // width
				$sys_setting_vars["textbox_height"].'px' // height
				);
			$textarea = $sw->getHTML();
			$html_tags = "";
		} else {
			$_POST['text'] = html_2_text($_POST['text']);
			$textarea = "<textarea name='text' cols='60' rows='10'>".$_POST['text']."</textarea>";
		}
		
		// hide HTML-Tags with wysiwyg
		if($admin_plugin[10001] == 1) {
			$html_tags = "";
		}
		
		$content_articles = get_structure($content_articles,0,$eid);
		
		$blocked = mark_selected_value($_POST['blocked']);
		
		if(!empty($_POST['startdate'])) {
			$_POST['startdate'] = date("d.m.Y",$_POST['startdate']);
		}
		if(!empty($_POST['enddate'])) {
			$_POST['enddate'] = date("d.m.Y",$_POST['enddate']);
		}

		// Set variables
		$tpl->set_var(array(
			"content_title"           => "<h2>".get_caption('2000','Contents')." / ".get_caption('2360','Add Article')."</h2>",
			"tab_general"             => get_caption('0170','General'),
			"tab_options"             => get_caption('0180','Options'),
			"content_action"          => "index.php?mode=content&page=articles&action=new&page_id=".$page_id,
			"content_error"           => $error,
			"content_insert"          => "<input type='radio' name='insert' value='top' checked='checked' /> ".get_caption('2420','Top')." <input type='radio' name='insert' value='bottom' /> ".get_caption('2430','Bottom'),
			"content_info"            => "",
			"content_page"            => get_caption('2390','Page'),
			"content_page_input"      => "<select name='eid'>".$content_articles."</select>",
			//"content_type"          => "Art",
			//"content_type_input"    => "<select name='content_type'>".$sys_content_type."</select>",
			"content_header"          => get_caption('2400','Title'),
			"content_header_input"    => "<input type='text' name='title' value='".$_POST['title']."' size='60' maxlength='250' />",
			"content_text"            => get_caption('2410','Text'),
			"content_text_input"      => $textarea.$html_tags,
			"content_blocked"         => get_caption('2170','Blocked'),
			"content_blocked_input"   => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
			"content_startdate"       => get_caption('2200','Starting Date'),
			"content_startdate_input" => "<input type='text' name='startdate' size='10' maxlength='10' value='".$_POST['startdate']."' /> ".get_caption('0500','DD.MM.YYYY'),
			"content_enddate"         => get_caption('2210','Ending Date'),
			"content_enddate_input"   => "<input type='text' name='enddate' size='10' maxlength='10' value='".$_POST['enddate']."' /> ".get_caption('0500','DD.MM.YYYY'),
			"content_button_send"     => $ac->create_form_button("submit",get_caption('2360','Add Article'))
			));
			// Parse template with variables
		$tpl->parse("content_handle", "content", true);
	break;
	
	case 'edit':
		// default editing
		if((isset($_POST['send']) || isset($_POST['sendclose'])) && $sys_group_vars['demo'] == 0) {
			// get needed page field values
			$page_vars = $contents->get_page_vars($db,$_POST['eid']);
			
			// check permissions for page link_type "standard" or "plugin"
			if(($page_vars['link_type'] == 0 && $sys_rights['content_default']['write'] == 1) || ($page_vars['link_type'] == 3 && $sys_rights['content_plugins']['write'] == 1)) {
				$cid = $_POST['cid'];
				// format input fields
				$_POST['title'] = validate_text($_POST['title']);
				$_POST['text'] = replace_specialchars($_POST['text']);
				if($admin_plugin[10000] == 0) {
					$_POST['text'] = text_2_html($_POST['text']);
				}

				if(!empty($_POST['startdate'])) {
					$_POST['startdate'] = validate_date($_POST['startdate']);
				}
				if(!empty($_POST['enddate'])) {
					$_POST['enddate'] = validate_date($_POST['enddate']);
				}
				
				if($_POST['startdate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9430','The starting date is invalid. Please consider the format.')); }
				if($_POST['enddate'] == "0" && $error == "") { $error = $ac->show_error_message(get_caption('9440','The ending date is invalid. Please consider the format.')); }

				// save changes in database
				//$db2->query("UPDATE ".$tbl_prefix."sys_content SET content_type = '".$_POST['content_type']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				$db2->query("UPDATE ".$tbl_prefix."sys_content SET blocked = '".$_POST['blocked']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				
				if($_POST['eid'] != $_POST['old_eid']) {
					$db2->query("UPDATE ".$tbl_prefix."sys_content SET explorer_id = '".$_POST['eid']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
					$sorting = $contents->get_sorting_number($db2,"bottom",$_POST['eid']);
					$db2->query("UPDATE ".$tbl_prefix."sys_content SET sorting = '".$sorting."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
					$config_id = $contents->get_config_id_from_eid($db2,$_POST['eid']);
					$db2->query("UPDATE ".$tbl_prefix."sys_content SET config_id = '".$config_id."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				}
				
				$text_hash = md5($_POST['title'].$_POST['text']);
				if($_POST['text_hash'] != $text_hash) {
					$db2->query("UPDATE ".$tbl_prefix."sys_content SET title = '".$_POST['title']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
					$db2->query("UPDATE ".$tbl_prefix."sys_content SET text = '".$_POST['text']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				}
				// log change
				$db2->query("UPDATE ".$tbl_prefix."sys_content SET changed_by = '".$_SESSION['username']."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				$changed_at = time();
				$db2->query("UPDATE ".$tbl_prefix."sys_content SET changed_at = '".$changed_at."' WHERE cid = '".$cid."' ORDER BY cid LIMIT 1");
				
				if(strlen($_POST['startdate']) == 0 || $_POST['startdate'] > 0) {
					$db->query("UPDATE ".$tbl_prefix."sys_content SET startdate = '".$_POST['startdate']."' WHERE cid = '".$cid."' LIMIT 1");
				}
				if(strlen($_POST['enddate']) == 0 || $_POST['enddate'] > 0) {
					$db->query("UPDATE ".$tbl_prefix."sys_content SET enddate = '".$_POST['enddate']."' WHERE cid = '".$cid."' LIMIT 1");
				}
				
				// header url
				if($error == "") {
					CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
					if(isset($_POST['sendclose'])) {
						if($page_source_url == "pages") {
							load_url("index.php?mode=content&page=content&eid=".$_POST['eid']);
						} else {				
							load_url("index.php?mode=content&page=articles");
						}
					} else {
						load_url("index.php?mode=content&page=articles&action=edit&cid=".$cid."&page_id=".$page_id);
					}
				}
			} else {
				$error = $ac->show_error_message(get_caption('9520','You can edit articles only for pages on type "Standard" and "Plugin".'));
			}
		}
		
		// Select Record
		$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		
		while($db->next_record()):
			$cid = $db->f("cid");
			$eid = $db->f("explorer_id");
			//$content_type = $db->f("content_type");
			$title = $db->f("title");
			$text = $db->f("text");
			$blocked = mark_selected_value($db->f("blocked"));
			$text_hash = md5($title.$text);
			$created_by = $db->f("created_by");
			// content protection per user
			if($sys_setting_vars['user_content_only'] == 1 && $sys_rights['set_general']['write'] == 0) {
				if($created_by != $_SESSION['username']) {
					if($page_source_url == "pages") {
						load_url("index.php?mode=content&page=content&eid=".$eid);
					} else {
						load_url("index.php?mode=content&page=articles");
					}
				}
			}
			
			// include SPAW wysiwyg-editor for field text
			if($admin_plugin[10000] == 1) {
				include_once("../plugins/wysiwyg_spaw2/spaw.inc.php");
				$sw = new SpawEditor('text',
					stripslashes($text),
					$sys_vars['token'], // language
					'', // toolbar mode
					'', // theme
					$sys_setting_vars["textbox_width"].'px', // width
					$sys_setting_vars["textbox_height"].'px' // height
					);
				$textarea = $sw->getHTML();
				$html_tags = "";
			} else {
				$text = html_2_text($text);
				$textarea = "<textarea name='text' cols='60' rows='10'>".$text."</textarea>";
			}
			
			// hide HTML-Tags with wysiwyg
			if($admin_plugin[10001] == 1) {
				$html_tags = "";
			}
			
			$content_created_at = "";
			if($db->f("created_at") != "") {
				$content_created_at = date($sys_vars['datetime'],$db->f("created_at"));
			}
			$content_changed_at = "";
			if($db->f("changed_at") != "") {
				$content_changed_at = date($sys_vars['datetime'],$db->f("changed_at"));
			}
			
			$content_info = get_caption('2320','Created by').": ".$db->f("created_by")."<br />"
				.get_caption('2330','Created at').": ".$content_created_at."<br />"
				.get_caption('2340','Changed by').": ".$db->f("changed_by")."<br />"
				.get_caption('2350','Changed at').": ".$content_changed_at;
			
			$content_articles = get_structure($content_articles,0,$eid);
			
			$startdate = $db->f("startdate");
			$enddate = $db->f("enddate");
			
			if(!empty($startdate)) {
				$startdate = date("d.m.Y",$startdate);
			}
			if(!empty($enddate)) {
				$enddate = date("d.m.Y",$enddate);
			}
			
			// Set variables
			$tpl->set_var(array(
				"content_title"           => "<h2>".get_caption('2000','Contents')." / ".get_caption('2370','Edit Article')."</h2>",
				"tab_general"             => get_caption('0170','General'),
				"tab_options"             => get_caption('0180','Options'),
				"content_action"          => "index.php?mode=content&page=articles&action=edit&cid=".$cid."&page_id=".$page_id,
				"content_error"           => $error,
				"confirm_message"         => $ac->show_ok_message(GetConfirmMessage()),
				"content_info"            => $content_info,
				"content_page"            => get_caption('2390','Page'),
				"content_page_input"      => "<select name='eid'>".$content_articles."</select><input type='hidden' name='old_eid' value='".$eid."' />",
				//"content_type"          => "Art",
				//"content_type_input"    => "<select name='content_type'>".$sys_content_type."</select>",
				"content_header"          => get_caption('2400','Title'),
				"content_header_input"    => "<input type='text' name='title' value='".$title."' size='60' maxlength='250' />",
				"content_text"            => get_caption('2410','Text'),
				"content_text_input"      => $textarea.$html_tags,
				"content_blocked"         => get_caption('2170','Blocked'),
				"content_blocked_input"   => "<select name='blocked'><option value='0' ".$blocked['1'].">".get_caption('0140','No')."</option><option value='1' ".$blocked['2'].">".get_caption('0130','Yes')."</option></select>",
				"content_hash"            => "<input type='hidden' name='text_hash' value='".$text_hash."' />",
				"content_cid"             => "<input type='hidden' name='cid' value='".$cid."' />",
				"content_startdate"       => get_caption('2200','Starting Date'),
				"content_startdate_input" => "<input type='text' name='startdate' size='10' maxlength='10' value='".$startdate."' /> ".get_caption('0500','DD.MM.YYYY'),
				"content_enddate"         => get_caption('2210','Ending Date'),
				"content_enddate_input"   => "<input type='text' name='enddate' size='10' maxlength='10' value='".$enddate."' /> ".get_caption('0500','DD.MM.YYYY'),
				"content_button_send"     => $ac->create_form_button("submit",get_caption('0120','Save'))." ".$ac->create_form_button("submit",get_caption('0121','Save & Close'),"btn","sendclose"),
				"content_button_reset"    => $ac->create_form_button("reset",get_caption('0125','Reset'))
				));
			// Parse template with variables
			$tpl->parse("content_handle", "content", true);
		endwhile;
	break;
	
	case 'del':
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0) {
			// get needed page field values
			$page_vars = $contents->get_page_vars($db,$_POST['eid']);
			
			// check permissions for page link_type "standard" or "plugin"
			if(($page_vars['link_type'] == 0 && $sys_rights['content_default']['delete'] == 1) || ($page_vars['link_type'] == 3 && $sys_rights['content_plugins']['delete'] == 1)) {
				$db->query("DELETE FROM ".$tbl_prefix."sys_content WHERE cid = '".$_POST['cid']."' LIMIT 1");
				CreateConfirmMessage(1,get_caption('0410','Entry was successfully deleted.'));
				if($page_source_url == "pages") {
					load_url("index.php?mode=content&page=content&eid=".$_POST['eid']);
				} else {
					load_url("index.php?mode=content&page=articles");
				}
			}
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=content&page=articles");
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		$db->query("SELECT cid,title,explorer_id,created_by FROM ".$tbl_prefix."sys_content WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$title = $db->f("title");
			$eid = $db->f("explorer_id");
			$created_by = $db->f("created_by");
		endwhile;
		
		// content protection per user
		if($sys_setting_vars['user_content_only'] == 1 && $sys_rights['set_general']['write'] == 0) {
			if($created_by != $_SESSION['username']) {
				if($page_source_url == "pages") {
					load_url("index.php?mode=content&page=content&eid=".$eid);
				} else {			
					load_url("index.php?mode=content&page=articles");
				}
			}
		}
			
		// Set variables
		$tpl->set_var(array(
			"content_title"       => "<h2>".get_caption('2000','Contents')." / ".get_caption('2380','Delete Article')."</h2>",
			"content_action"      => "index.php?mode=content&page=articles&action=del&page_id=".$page_id,
			"content_question"    => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"content_name"        => "<p class='bold'>".$title."</p>",
			"content_cid"         => "<input type='hidden' name='cid' value='".$cid."' /><input type='hidden' name='eid' value='".$eid."' />",
			"content_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
		$tpl->parse("content_handle", "content", true);
	break;

	default: // List user articles and articles by others
		// Filter
		if(isset($_POST["send"])) {
			if($_POST["filter_content"] == "") {
				unset($_SESSION["filter_content"]);
			} else {
				$_SESSION["filter_content"] = $_POST["filter_content"];
			}
			load_url("index.php?mode=content&page=articles");
		}
		
		$content_filter = $filters->create_filter_content($db);

		// Article list
		$content_all .= "<table class='list'>"
			."<tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('','ID')."</p></td>"
			."<td width='200'><p class='bold'>".get_caption('2400','Title')."</p></td>"
			."<td><p class='bold'>".get_caption('2170','Blocked')."</p></td>"
			."<td><p class='bold'>".get_caption('2320','Created by')."</p></td>"
			."<td><p class='bold'>".get_caption('2330','Created at')."</p></td>"
			."<td><p class='bold'>".get_caption('2340','Changed by')."</p></td>"
			."<td><p class='bold'>".get_caption('2350','Changed at')."</p></td>"
			."<td width='80'><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
		
		// settings
		$start = $_GET['start'];
		$limit = $s_limit; // default: 20
		
		if(isset($_SESSION["filter_content"])) {
			if($sys_setting_vars['user_content_only'] == "1" && $sys_rights['set_general']['write'] == 0) {
				$db->query("SELECT cid FROM ".$tbl_prefix."sys_content WHERE created_by = '".$_SESSION['username']."' AND config_id = '".$_SESSION["filter_content"]."'");
			} else {
				$db->query("SELECT cid FROM ".$tbl_prefix."sys_content WHERE config_id = '".$_SESSION["filter_content"]."'");
			}
		} else {
			if($sys_setting_vars['user_content_only'] == "1" && $sys_rights['set_general']['write'] == 0) {
				$db->query("SELECT cid FROM ".$tbl_prefix."sys_content WHERE created_by = '".$_SESSION['username']."'");
			} else {
				$db->query("SELECT cid FROM ".$tbl_prefix."sys_content");
			}
		}
		$total = $db->num_rows();
		
		// create page handling
		$page_html_code = $ac->create_page_handling($total,$limit,"index.php?mode=content&page=articles&start=",$start);
		$start = $ac->get_page_start($start,$limit);
		
		// Lines: filter on user if not admin rights
		if(isset($_SESSION["filter_content"])) {
			if($sys_setting_vars['user_content_only'] == "1" && $sys_rights['set_general']['write'] == 0) {
				$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE created_by = '".$_SESSION['username']."' AND config_id = '".$_SESSION["filter_content"]."' ORDER BY title LIMIT ".$start.",".$limit."");
			} else {
				$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE config_id = '".$_SESSION["filter_content"]."' ORDER BY title LIMIT ".$start.",".$limit."");
			}
		} else {
			if($sys_setting_vars['user_content_only'] == "1" && $sys_rights['set_general']['write'] == 0) {
				$db->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE created_by = '".$_SESSION['username']."' ORDER BY title LIMIT ".$start.",".$limit."");
			} else {
				$db->query("SELECT * FROM ".$tbl_prefix."sys_content ORDER BY title LIMIT ".$start.",".$limit."");
			}
		}
		
		while($db->next_record()):
			//$page_vars = $contents->get_page_vars($db2,$db->f("explorer_id"));
			//$contents->get_config_id_from_eid($db2,$db->f("explorer_id"));
			$content_all .= "<tr class='bg_color2'>";
			$content_all .= "<td>".$db->f("cid")."</td>";
			$content_all .= "<td><a href='index.php?mode=content&page=articles&action=edit&cid=".$db->f("cid")."' title='".substr(strip_tags($db->f("text")),0,200)."'>".$db->f("title")."</a></td>";
			$content_all .= "<td>".translate_yesno($db->f("blocked"),get_caption('0130','Yes'),get_caption('0140','No'))."</td>";
			$content_all .= "<td>".$db->f("created_by")."</td>";
			
			if($db->f("created_at") == "") { 
				$content_all .= "<td></td>";
			} else {
				$content_all .= "<td>".date($sys_vars['datetime'],$db->f("created_at"))."</td>";
			}
			
			$content_all .= "<td>".$db->f("changed_by")."</td>";
			
			if($db->f("changed_at") == "") { 
				$content_all .= "<td></td>";
			} else {
				$content_all .= "<td>".date($sys_vars['datetime'],$db->f("changed_at"))."</td>";
			}
			$content_all .= "<td>"
				.$ac->create_edit_icon("index.php?mode=content&page=articles&action=edit&cid=".$db->f("cid"),get_caption('0201','Edit'))
				.$ac->create_delete_icon("index.php?mode=content&page=articles&action=del&cid=".$db->f("cid"),get_caption('0211','Delete'))
				.$ac->create_preview_icon("../index.php?page=".$db->f("explorer_id"),get_caption('0421','Preview'))
				."</td>";
			$content_all .= "</tr>";
		endwhile;
		$content_all .= "</table>";
		
		// Set variables
		$tpl->set_var(array(
			"content_title"      => "<h2>".get_caption('2000','Contents')." / ".get_caption('2030','Articles')."</h2>",
			"confirm_message"    => $ac->show_ok_message(GetConfirmMessage()),
			"content_new"        => $ac->create_add_icon("index.php?mode=content&page=articles&action=new",get_caption('2360','Add Article')),
			"content_list"       => $content_all,
			"content_filter"     => $content_filter,
			"page_handling"      => $page_html_code
			));
		
		// Parse template with variables
		$tpl->parse("content_handle", "content", true);
	break;
}
?>