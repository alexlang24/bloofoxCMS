<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/step3.php -
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

if(!file_exists("../system/class_mysql.php")) {
	load_url("index.php?page=1");
}

if(!file_exists("../media/txt/install.txt")) {
	$error = "<p style='color: red;'>".$strErrorReport."</p>";
}

$tpl->set_block("content", "step3", "step3_handle");

$tpl->set_var(array(
	"step3_text"          => "<p>$strCongratulations<br />$strNotice</p>",
	"step3_error"         => $error,
	"step3_userdata"      => "<p class='bold'>$strAdminLogin</p>",
	"step3_user"          => "<br />$strUser",
	"step3_pw"            => "<br />$strPw",
	"step3_next_step"     => "<p><a href='../admin/index.php'>$strNextAdmincenter</a></p>"
	));

$tpl->parse("step3_handle", "step3", true);
?>