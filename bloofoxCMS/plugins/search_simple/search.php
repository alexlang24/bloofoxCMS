<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/search_simple/search.php -
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
// Search must have "1003"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1003) {

	$sys_print_vars['print'] = "";
	if(isset($_GET['search'])) {
		$_POST['search'] = $_GET['search'];
	}
	
	// translations
	include("plugins/search_simple/languages/".$sys_lang_vars['language']);

	
	// set template block
	$tpl->set_block("template_content", "content", "content_handle");
	
	$search_vars['title'] = "<h1>".$sys_explorer_vars['name']."</h1>";
	$search_vars['searchbox'] = "<form action='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'])."' method='post'>"
		."<table border='0'>"
		."<tr><td>"
		."<input type='text' name='search' size='50' value='".validate_text($_POST['search'])."' />"
		."<input type='submit' value='".get_caption('E010','Search')."' />"
		."</td></tr>"
		."</table><br />"
		."</form>";
	$search_vars['result'] = "";
	
	$search = validate_text($_POST['search']);
	$search = str_replace("%","",$search);
	
	if(isset($_POST['search']) && mandatory_field($search)) {
		$db2 = new DB_Tpl();
		
		$search_vars['title'] = "<h1>".get_caption('E020','Search Result')."</h1>";
		$search_vars['string'] = $search;
		$search = explode(" ",$search);
		$search_vars['found'] = 0;
		$search_vars['count'] = 0;
		$search_vars['page'] = 0;
		$search_cid = array();
		
		//number of records per page
		$limit = 10;
		$start = $_GET['start'];
	
		for($x=0; $x<count($search); $x++)
		{
			$db2->query("SELECT ".$tbl_prefix."sys_content.cid,".$tbl_prefix."sys_content.explorer_id,".$tbl_prefix."sys_content.title,".$tbl_prefix."sys_content.text FROM ".$tbl_prefix."sys_content"
				." INNER JOIN ".$tbl_prefix."sys_explorer ON ".$tbl_prefix."sys_explorer.eid = ".$tbl_prefix."sys_content.explorer_id AND ".$tbl_prefix."sys_explorer.config_id = '".$sys_explorer_vars['config_id']."'"
				." WHERE ".$tbl_prefix."sys_explorer.groups = '' && ".$tbl_prefix."sys_explorer.blocked = '0' && ".$tbl_prefix."sys_content.blocked = '0' && (".$tbl_prefix."sys_content.title LIKE '%".$search[$x]."%' || ".$tbl_prefix."sys_content.text LIKE '%".$search[$x]."%')"
				." ORDER BY ".$tbl_prefix."sys_content.cid");
		
			while($db2->next_record()):
				$text = validate_text($db2->f("text"));
				$text = str_replace("&amp;","&",$text);
				$text = substr($text,0,250);
				
				if(array_search($db2->f("cid"),$search_cid) == false) {
					$search_vars['found'] = 1;
					$search_cid[$db2->f("cid")] = $db2->f("cid");
					$search_vars['count'] += 1;
					if($search_vars['count'] > $search_vars['page'] * $limit) {
						$search_vars['page'] += 1;
					}
					$search_vars['result'][$search_vars['page']] .= "<p><span class='bold'><a href='".create_url($db2->f("explorer_id"),$db2->f("title"),$sys_config_vars['mod_rewrite'])."'>".$db2->f("title")."</a></span><br />"
						.$text."<br /><span class='search'>".create_url($db2->f("explorer_id"),$db2->f("title"),$sys_config_vars['mod_rewrite'])."</span></p><br />\n";
				}
			endwhile;
		}
		
		if($search_vars['found'] == 0) {
			$search_vars['result'][1] .= "<p>".get_caption('E040','The search resulted no hits.')."</p>";
		}
		
		$factor = $search_vars['count'] / $limit;
		$factor = ceil($factor);
			
		if($factor >= 1) {
			$search_vars['pages'] = "<span class='bold'>".get_caption('2020','Pages').":</span> ";
			if($sys_config_vars['mod_rewrite'] == 1) {
				$q = "?";
			} else {
				$q = "&amp;";
			}
			for($x=1; $x<=$factor; $x++) {
				if($start == $x) {
					$search_vars['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x).$q."search=".$search_vars['string']."'><span class='bold'>".$x."</span></a> ";
					if($x - 1 >= 1) {
						$search_vars['prev'] = "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x-1).$q."search=".$search_vars['string']."'>".get_caption('0300','Previous')."</a> - ";
					}
					if($x + 1 <= $factor) {
						$search_vars['next'] = "- <a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x+1).$q."search=".$search_vars['string']."'>".get_caption('0310','Next')."</a> ";
					}
				} else {
					$search_vars['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x).$q."search=".$search_vars['string']."'>".$x."</a> ";
				}
			}
		}
	}
	
	$tpl->set_var(array(
		"title"      => $search_vars['title'],
		"searchbox"  => $search_vars['searchbox'],
		"result"     => $search_vars['result'][$start],
		"pages"      => $search_vars['pages'],
		"next"       => $search_vars['next'],
		"prev"       => $search_vars['prev']
		));
			
	// parse template
	$tpl->parse("content_handle", "content", true);
}
?>