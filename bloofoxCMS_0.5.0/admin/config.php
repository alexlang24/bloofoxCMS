<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/config.php -
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

// Admincenter Default Template
$tmpl_vars['login'] = "../templates/admincenter/admin_login.html";
$tmpl_vars['name'] = "../templates/admincenter/admincenter.html";
$tmpl_vars['css'] = "../templates/admincenter/admincenter.css";
$tmpl_vars['path'] = "../templates/admincenter/";

// Folder Setup for Uploads
// needs slash at the end; templates are added automatically
$folder_setup[0] = "../media/images/";
$folder_setup[1] = "../media/files/";
$folder_setup[2] = "../languages/";

// Paths To Some Folders
// needs slash at the end; used for mediacenter, language and template settings
$path_to_image_folder = "../media/images/";
$path_to_profiles_folder = "../media/images/profiles/";
$path_to_files_folder = "../media/files/";
$path_to_lang_files = "../languages/";
$path_to_template_files = "../templates/";

// Level increase for page sorting
// usually no changes needed
$s_level_no = 10000;

// Limit for list entries
$s_limit = 20;

// Document Types
// if you extend the list you also need to extend the function create_doctype in file /functions.php
$doctype_setup[0] = "XHTML 1.0 Transitional";
$doctype_setup[1] = "HTML 4.01";
$doctype_setup[2] = "XHTML 1.0 Strict";

// HTML-Header
$tmpl_vars['header'] = "<title>bloofoxCMS Admincenter</title>\n"
	."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$sys_config_vars["meta_charset"]."\" />\n"
	."<meta name=\"AUTHOR\" content=\"bloofox, Alexander Lang\" />\n"
	."<meta name=\"COPYRIGHT\" content=\"bloofox.com, Alex Lang\" />\n"
	."<meta name=\"DATE\" content=\"".date("m/d/Y")."\" />\n"
	."<meta name=\"DESCRIPTION\" content=\"bloofoxCMS Admincenter is the backend for managing the contents of bloofoxCMS\" />\n"
	."<meta name=\"TITLE\" content=\"bloofoxCMS Admincenter\" />\n"
	."<link rel=\"stylesheet\" type=\"text/css\" href=\"{tmpl_css_path}\" />\n"
	."<link rel=\"icon\" href=\"../media/images/favicon.ico\" type=\"image/x-icon\" />\n"
	."<link rel=\"shortcut icon\" href=\"../media/images/favicon.ico\" type=\"image/x-icon\" />\n";

// HTML Footer-Line	
$tmpl_vars['footer'] = "<!--// Footer -->\n"
	."Powered by <a href=\"http://www.bloofox.com\" target=\"bloofox\">bloofoxCMS</a> &copy; 2012";
	
// Open HTML tags and available short tags for content creation without wysiwyg
$html_tags = "<br />HTML-Tags: &lt;div&gt;&lt;p&gt; &lt;b&gt;&lt;i&gt;&lt;u&gt; &lt;a&gt;&lt;img&gt; &lt;object&gt;&lt;param&gt;&lt;embed&gt; &lt;ul&gt;&lt;ol&gt;&lt;li&gt; &lt;span&gt; &lt;iframe&gt;";
?>