<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/dberror.php -
//
// Copyrights (c) 2006-2008 Alexander Lang, Germany
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
//
// You should have received a copy of the GNU General Public License
// along with bloofoxCMS; if not, please contact the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

$tpl->set_block("content", "dberror", "dberror_handle");

$tpl->set_var(array(
	"var_action"        => "javascript:history.back(1);",
	"var_error"         => $strDBErrorText,
	"var_submit"     => "<input type='submit' name='send' value='".$strBack."' />"
	));

$tpl->parse("dberror_handle", "dberror", true);
?>