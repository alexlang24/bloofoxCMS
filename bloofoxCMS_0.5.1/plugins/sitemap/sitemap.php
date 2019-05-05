<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/sitemap/sitemap.php -
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
// Sitemap must have "1004"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1004) {
	
	// set template block
	$tpl->set_block("template_content", "content", "content_handle");

	function get_entries($content,$which=0)
	{
		global $tbl_prefix,$sys_explorer_vars,$sys_config_vars;
		
		// Init libraries
		$db = new DB_Tpl();
		$curr_time = time();
	 	$db->query("SELECT * FROM ".$tbl_prefix."sys_explorer WHERE preid = '".$which."' && config_id = '".$sys_explorer_vars['config_id']."' && blocked = '0' && startdate < '".$curr_time."' && (enddate > '".$curr_time."' || enddate = '') ORDER BY preid,sorting");
		while($db->next_record()):
			$indent = $db->f("level") * 10 - 10;
			
			$sitemap_vars['break'] = "";
			$sitemap_vars['bold'] = "";
			if($db->f("level") == 2) {
				$sitemap_vars['break'] = "<br />\n";
				$sitemap_vars['bold'] = "class='bold'";
			}
			
			switch($db->f("link_type"))
			{
				case '1': // External Url
					$sitemap_vars['hyperlink'] = $db->f("link_url");
					$sitemap_vars['target'] = "";
					if($db->f("link_target") != "") {
						$sitemap_vars['target'] = "target='".$db->f("link_target")."'";
					}
				break;
				
				case '2': // Shortcut
					$sitemap_vars['hyperlink'] = create_url($db->f("link_eid"),$db->f("name"),$sys_config_vars['mod_rewrite']);
					$sitemap_vars['target'] = "";
				break;
				
				default:
					$sitemap_vars['hyperlink'] = create_url($db->f("eid"),$db->f("name"),$sys_config_vars['mod_rewrite']);
					$sitemap_vars['target'] = "";
				break;
			}
		
			if($db->f("level") > 1) {
				$content .= $sitemap_vars['break']."<p style='text-indent: ".$indent."px;' ".$sitemap_vars['bold']."><a href='".$sitemap_vars['hyperlink']."' ".$sitemap_vars['target'].">".$db->f("name")."</a></p>\n";
			}
			$content = get_entries($content,$db->f("eid"));
		endwhile;
		
		return($content);
	}

	$tpl->set_var(array(
		"title"   => "<h1>".$sys_explorer_vars['name']."</h1>",
		"sitemap" => get_entries($content_expl)
		));
		
	// parse template
	$tpl->parse("content_handle", "content", true);
}
?>