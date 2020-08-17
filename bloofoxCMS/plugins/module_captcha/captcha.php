<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/modul_captcha/captcha.php -
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

// Make or find session
session_name("sid");
session_start();

// Block external usage
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
if(strpos($HTTP_REFERER,$_SERVER['SERVER_NAME']) == 0) {
	die("Forget It!");
}

define ('SYS_INDEX', 1);
if(empty($_GET['var'])) { exit; }

include("class_captcha.php");
$captcha = new captcha($_GET['var']);
$captcha->image();
?>