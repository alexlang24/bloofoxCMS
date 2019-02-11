<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/article_list/list.php -
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

// check plugin ID
// text_list must have "1005"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1005) {
	
	// init db connection
	$db2 = new DB_Tpl();
	$db3 = new DB_Tpl();
	
	// set template block
	$tpl->set_block("template_content", "content", "content_handle");

	// get sys_contents
	$db2->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$cont->eid."' AND blocked = '0' ORDER BY sorting LIMIT 1");
	$no_of_records = $db2->num_rows();
	
	while($db2->next_record()):
		$text = $cont->format_text($db2->f("text"));
		
		$tpl->set_var(array(
			"title"    => $db2->f("title"),
			"text"     => $text
		));
	endwhile;
	
	if($no_of_records == 0) {
		$tpl->set_var(array(
			"title"    => "",
			"text"     => ""
		));
	}
		
	// list view
	$curr_time = time();
	$db2->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$sys_explorer_vars['config_id']."' "
		."&& link_type = 0 && preid = ".$sys_explorer_vars['eid']." && invisible = '0' && blocked = '0' "
		."&& startdate < '".$curr_time."' && (enddate > '".$curr_time."' || enddate = '') ORDER BY preid,sorting");
	$no_of_records = $db2->num_rows();
	
	while($db2->next_record()):
		$db3->query("SELECT text FROM ".$tbl_prefix."sys_content WHERE explorer_id = ".$db2->f("eid")." ORDER BY sorting LIMIT 1");
		while($db3->next_record()):
			$list_text = $db3->f("text");
			$list_text = validate_text($list_text);
			$list_text = str_replace("&amp;","&",$list_text);
			$list_text = substr($list_text,0,250)."...";
		endwhile;
		$tpl->set_var(array(
			"list_title"    => "<a href='".create_url($db2->f("eid"),$db2->f("name"),$sys_config_vars['mod_rewrite'])."' title='".$db2->f("name")."'>".$db2->f("name")."</a>",
			"list_text"     => $list_text
		));
		
		// parse template
		$tpl->parse("content_handle", "content", true);
	endwhile;
	
	// if no contents were found show this content
	if($no_of_records == 0) {
		$tpl->set_var(array(
			"title"         => get_caption('C010','Notice'),
			"text"          => get_caption("C020","Content (Articles) not found for this page!"),
			"list_title"    => "",
			"list_text"     => ""
		));
	
		// parse template
		$tpl->parse("content_handle", "content", true);
	}
}
?>