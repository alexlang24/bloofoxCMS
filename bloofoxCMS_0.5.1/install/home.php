<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/home.php -
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

$tpl->set_block("content", "home", "home_handle");

$OK = "<p style='color: green;'>OK</p>";
$ERROR = "<p style='color: red;'>Error</p>";

if(is_dir("../admin")) { $dir_admin = $OK; } else { $dir_admin = $ERROR; }
if(is_dir("../languages")) { $dir_lang = $OK; } else { $dir_lang = $ERROR; }
if(is_dir("../media")) { $dir_media = $OK; } else { $dir_media = $ERROR; }
$dir_files = $ERROR;
if(is_dir("../media/files")) {
	if(is_writeable("../media/files")) { $dir_files = $OK; } else { $dir_error_files = "<p style='color: red;'>/media/files ".$strErrorDir."</p>"; }
}
$dir_images = $ERROR;
if(is_dir("../media/images")) {
	if(is_writeable("../media/images")) { $dir_images = $OK; } else { $dir_error_images = "<p style='color: red;'>/media/images ".$strErrorDir."</p>"; }
}
$dir_txt = $ERROR;
if(is_dir("../media/txt")) {
	if(is_writeable("../media/txt")) { $dir_txt = $OK; } else { $dir_error_txt = "<p style='color: red;'>/media/txt ".$strErrorDir."</p>"; }
}
if(is_dir("../plugins")) { $dir_plugins = $OK; } else { $dir_plugins = $ERROR; }
$dir_system = $ERROR;
if(is_dir("../system")) {
	if(is_writeable("../system")) { $dir_system = $OK; } else { $dir_error_system = "<p style='color: red;'>/system ".$strErrorDir."</p>"; }
}
if(is_dir("../templates")) { $dir_tmpl = $OK; } else { $dir_tmpl = $ERROR; }

$tpl->set_var(array(
	"home_dir_title"          => "<p class='bold'>$strDir</p>",
	"home_dir_admin"          => "/admin",
	"home_dir_admin_status"   => $dir_admin,
	"home_dir_languages"        => "/languages",
	"home_dir_languages_status" => $dir_lang,
	"home_dir_media"          => "/media",
	"home_dir_media_status"   => $dir_media,
	"home_dir_files"          => "/media/files",
	"home_dir_files_status"   => $dir_files,
	"home_dir_images"         => "/media/images",
	"home_dir_images_status"  => $dir_images,
	"home_dir_txt"            => "/media/txt",
	"home_dir_txt_status"     => $dir_txt,
	"home_dir_plugins"        => "/plugins",
	"home_dir_plugins_status" => $dir_plugins,
	"home_dir_system"         => "/system",
	"home_dir_system_status"  => $dir_system,
	"home_dir_templates"        => "/templates",
	"home_dir_templates_status" => $dir_tmpl,
	"home_dir_errors"         => $dir_error_files.$dir_error_images.$dir_error_txt.$dir_error_system,
	"home_text"               => "<p>".$strWelcomeText."</p>",
	"home_next_step"          => "<form action='index.php?page=1' method='post'><input type='submit' value='".$strNextStep1."' /></form>"
	));

$tpl->parse("home_handle", "home", true);
?>