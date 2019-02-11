<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/module_searchbox/searchbox.php -
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

// translations
include("plugins/module_searchbox/languages/".$sys_lang_vars['language']);

// Find result page in explorer
$db2 = new DB_Tpl();
$db2->query("SELECT eid,name FROM ".$tbl_prefix."sys_explorer WHERE config_id = '".$sys_explorer_vars['config_id']."' && link_type = '3' && link_plugin = '1003' LIMIT 1");
while($db2->next_record()):
	$search_vars['search_site_url'] = create_url($db2->f("eid"),$db2->f("name"),$sys_config_vars['mod_rewrite']);
endwhile;

// Template Block-Setup
$tpl->set_block("plugin_module_searchbox", "searchbox", "searchbox_handle");

$tpl->set_var(array(
	"search_title"     => get_caption('E030','Search'),
	"search_action"    => $search_vars['search_site_url'],
	"search"           => validate_text($_POST['search']),
	"search_submit"    => "<input type='submit' value='".get_caption('E010','Search')."' />"
));
			 
$tpl->parse("searchbox_handle", "searchbox", true);
?>