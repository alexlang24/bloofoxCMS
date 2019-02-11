<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/contact_form_simple/tmpl_handler.php -
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

$contact_vars['get'] = $_GET['sent'];
switch($contact_vars['get'])
{
	case '1': // contact message sent
		$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path")."contact_sent.html";
	break;
		
	default: // contact form
		$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path")."contact.html";
	break;
}
?>