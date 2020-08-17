<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/navigation_bottom_menu/install.php -
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

// Version
$sys_plugin_vars['version'] = "2.0";

// Parameter
$sys_plugin_vars['pluginID'] = 12;
$sys_plugin_vars['index_file'] = "bottom_menu.php";
$sys_plugin_vars['index_tmpl'] = "bottom_menu.html";
$sys_plugin_vars['tmpl_handler'] = "";
$sys_plugin_vars['admin_file'] = "admin.php";
$sys_plugin_vars['admin_handler'] = "admin_handler.php";
$sys_plugin_vars['plugin_type'] = 0;

// Queries
$sys_plugin_sql[0] = "CREATE TABLE `".$tbl_prefix."plugin_bottom_menu` ("
	."`bid` int(10) unsigned NOT NULL auto_increment,"
	."`config_id` int(10) NOT NULL,"
	."`explorer_id` int(10) NOT NULL,"
	."`sort` int(10) unsigned NOT NULL default '0',"
	."PRIMARY KEY  (`bid`)"
	.") ENGINE=MyISAM AUTO_INCREMENT=1 ;";
	
$sys_plugin_table[0] = "plugin_bottom_menu";
?>