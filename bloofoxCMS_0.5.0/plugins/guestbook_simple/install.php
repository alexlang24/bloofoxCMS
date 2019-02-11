<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/guestbook_simple/install.php -
//
// Copyrights (c) 2006-21012 Alexander Lang, Germany
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
$sys_plugin_vars['pluginID'] = 1000;
$sys_plugin_vars['index_file'] = "gbook.php";
$sys_plugin_vars['index_tmpl'] = "gbook.html";
$sys_plugin_vars['tmpl_handler'] = "";
$sys_plugin_vars['admin_file'] = "admin.php";
$sys_plugin_vars['admin_handler'] = "admin_handler.php";
$sys_plugin_vars['plugin_type'] = 1;

// Queries
$sys_plugin_sql[0] = "CREATE TABLE `".$tbl_prefix."plugin_gbook_simple` ("
	."`cid` int(10) unsigned NOT NULL auto_increment,"
	."`eid` int(11) NOT NULL,"
	."`name` varchar(50) NOT NULL,"
	."`email` varchar(50) NOT NULL,"
	."`homepage` varchar(250) NOT NULL,"
	."`code` varchar(30) NOT NULL,"
	."`timestamp` varchar(20) NOT NULL,"
	."`text` longtext NOT NULL,"
	."`ip_address` varchar(80) NOT NULL,"
	."PRIMARY KEY (`cid`)"
	.") ENGINE=MyISAM AUTO_INCREMENT=1 ;";

$sys_plugin_table[0] = "plugin_gbook_simple";
?>