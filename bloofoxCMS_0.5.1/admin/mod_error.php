<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/mod_error.php -
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
$tpl->set_block("tmpl_content", "error", "error_handle");

// default values
$error_title = "<h2>".get_caption('3020','Projects')."</h2>";
$error_text = $ac->show_error_message(get_caption('9260','You have not the permissions to view this page.'));

switch($mode)
{
	case 'content':
		$error_title = "<h2>".get_caption('2000','Contents')."</h2>";
	break;
	
	case 'settings':
		$error_title = "<h2>".get_caption('3000','Administration')."</h2>";
	break;
	
	case 'user':
		$error_title = "<h2>".get_caption('4000','Security')."</h2>";
	break;
	
	case 'tools':
		$error_title = "<h2>".get_caption('5000','Tools')."</h2>";
	break;
}

// Set variables
$tpl->set_var(array(
	"error_title"    => $error_title,
	"error_text"     => $error_text
	));

// Parse template with variables
$tpl->parse("error_handle", "error", true);
?>