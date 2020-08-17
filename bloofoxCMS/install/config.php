<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/config.php -
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

// NOTHING NEED TO BE CHANGED HERE!

// Database Version
$db_version = "bloofoxCMS 0.5.2";

// default language is English

$_GET['lang'] = strip_tags($_GET['lang']);
$_GET['lang'] = trim($_GET['lang']);
switch($_GET['lang'])
{
	case 'de':
		$_SESSION['lang'] = "de";
	break;
	
	case 'en':
		$_SESSION['lang'] = "en";
	break;
}

switch($_SESSION['lang'])
{
	case 'de':
		include("languages/deutsch.php");
	break;
	
	default:
		include("languages/english.php");
	break;
}

// system tables
$strTablelist = $tbl_prefix."sys_charset<br />"
	.$tbl_prefix."sys_config<br />"
	.$tbl_prefix."sys_content<br />"
	.$tbl_prefix."sys_explorer<br />"
	.$tbl_prefix."sys_lang<br />"
	.$tbl_prefix."sys_media<br />"
	.$tbl_prefix."sys_permission<br />"
	.$tbl_prefix."sys_plugin<br />"
	.$tbl_prefix."sys_profile<br />"
	.$tbl_prefix."sys_session<br />"
	.$tbl_prefix."sys_setting<br />"
	.$tbl_prefix."sys_template<br />"
	.$tbl_prefix."sys_user<br />"
	.$tbl_prefix."sys_usergroup<br />";
?>