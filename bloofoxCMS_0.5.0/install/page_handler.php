<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/status.php -
//
// Copyrights (c) 2006 Alexander Lang, Germany
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

// Include config.php with language layer
require("config.php");

// handle pages
switch($page)
{
	case '0':
		$content_title = $strDBError;
		$content_html = "dberror.html";
		$content_php = "dberror.php";
	break;
	
	case '3':
		$content_title = $strStep3;
		$content_html = "step3.html";
		$content_php = "step3.php";
	break;
	
	case '2':
		$content_title = $strStep2;
		$content_html = "step2.html";
		$content_php = "step2.php";
	break;
	
	case '1':
		$content_title = $strStep1;
		$content_html = "step1.html";
		$content_php = "step1.php";
	break;
	
	default:
		$content_title = $strWelcomeToSetup;
		$content_html = "home.html";
		$content_php = "home.php";
	break;
}
?>