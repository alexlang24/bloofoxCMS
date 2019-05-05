<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/register_form_simple/tmpl_handler.php -
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

$reg_vars['get'] = $_GET['register'];
switch($reg_vars['get'])
{
	case '2': // register finished
		$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path")."register_done.html";
	break;
	
	case '3': // register confirmed, account activated
		$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path")."register_confirm.html";
	break;
		
	default: // register new account
		$sys_tmpl_vars['content'] = "/plugins/".$db->f("install_path")."register_account.html";
	break;
}
?>