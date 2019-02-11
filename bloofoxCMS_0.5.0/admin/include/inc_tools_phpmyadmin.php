<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_tools_phpmyadmin.php -
//
// Copyrights (c) 2006 - 2012 Alexander Lang, Germany
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

switch($action)
{
	default:
		$phpMyAdmin = $sys_setting_vars['phpmyadmin_url'];
		if(empty($phpMyAdmin)) {
			$tools_var['content'] = $ac->show_info_message(get_caption('5200',"A hyperlink to PhpMyAdmin is not configured in Admincenter / Administration / Settings."));
		} else {
			$tools_var['content'] = "<p><a href='".$phpMyAdmin."' target='_blank'>".get_caption('5210',"Open PhpMyAdmin...")."</a></p>";
		}
		// Set variables
		$tpl->set_var(array(
			"tools_title"    => "<h2>".get_caption('5000','Tools')." / ".get_caption('5220','PhpMyAdmin')."</h2>",
			"tools_content"  => $tools_var['content']
			));
	break;
}
?>