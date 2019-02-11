<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/mod_plugins.php -
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

// Set template block
$tpl->set_block("tmpl_content", "plugins", "plugins_handle");

if(isset($_POST['pluginID'])) {
	$_GET['pluginID'] = $_POST['pluginID'];
}

if(!isset($_GET['pluginID'])) {
	load_url("index.php?mode=settings&page=plugins");
}

$db->query("SELECT * FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$_GET['pluginID']."' LIMIT 1");
while($db->next_record()):
	$plugin_name = $db->f("name");
	$plugin_path = $db->f("install_path");
	$plugin_admin_file = $db->f("admin_file");
endwhile;

if($plugin_admin_file != "") {
	include("../plugins/".$plugin_path.$plugin_admin_file);
} else {
	$tpl->set_var(array(
	"plugins_title"   => "<h1>Plugin: ".$plugin_name."</h1>",
	"plugins_content" => "" 
	));
	// Parse template with variables
	$tpl->parse("plugins_handle", "plugins", true);
}
?>