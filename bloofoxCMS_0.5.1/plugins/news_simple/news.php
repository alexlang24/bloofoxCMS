<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/news_simple/text_news.php -
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
// text_news must have "1006"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1006) {
	
	// init db connection
	$db2 = new DB_Tpl();
	
	// create page handling
	$sys_vars = $content->create_pages($db2,$_GET['start'],$sys_vars);
	
	// set template block
	$tpl->set_block("template_content", "content", "content_handle");

	// get sys_contents
	$db2->query("SELECT * FROM ".$tbl_prefix."sys_content WHERE explorer_id = '".$content->eid."' AND blocked = '0' ORDER BY sorting LIMIT ".$sys_vars['start'].",".$content->limit."");
	$no_of_records = $db2->num_rows();
	
	while($db2->next_record()):
		$text = $content->format_text($db2->f("text"));
		
		if($db2->f("created_at") != "") {
			$content_created = date($sys_vars['date'],$db2->f("created_at"));
		} else {
			$content_created = "";
		}
		
		$tpl->set_var(array(
			"title"    => "<a name='".$db2->f("cid")."'></a>".$db2->f("title"),
			"text"     => $text,
			"created"  => $content_created
		));
	
		// parse template
		$tpl->parse("content_handle", "content", true);
	endwhile;
	
	// if no contents were found show this content
	if($no_of_records == 0) {
		$tpl->set_var(array(
			"title"    => get_caption('C010','Notice'),
			"text"     => get_caption("C020","Content (Articles) not found for this page!"),
			"created"  => ""
		));
	
		// parse template
		$tpl->parse("content_handle", "content", true);
	}
}
?>