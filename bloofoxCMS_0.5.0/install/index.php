<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - install/index.php -
//
// Copyrights (c) 2006-2013 Alexander Lang, Germany
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

// Set error reporting
error_reporting (E_ALL ^ E_NOTICE);

// Define constants
define ('SYS_WORK_DIR', getcwd());
define ('SYS_INDEX', 1);

// Load required libraries
include("../config.php");
require_once("../functions.php");
require_once("../system/class_template.php");

// Page handling
$page = $_GET['page'];
unset($content_php); // $content_php is set in file page_handler.php
include(SYS_WORK_DIR."/page_handler.php");

// build template
$tpl = new Template();
$tpl->set_unknowns("keep");

// get doctype
$tmpl_var_doctype = create_doctype(0);

// set templates
$basic_tmpl = array (
	"template"      => SYS_WORK_DIR."/templates/index.html", // Layout Template
	"content"       => SYS_WORK_DIR."/templates/".$content_html // Content Template
	);

$tpl->set_file($basic_tmpl);

include(SYS_WORK_DIR."/".$content_php);
			  
// load and parse template
$tpl->set_var(array(
	"doctype" => $tmpl_var_doctype,
	"title"   => $content_title,
	"page"    => $page // current page
	));

$tpl->parse ("template_handle", array("template"));
$tpl->p ("template_handle");
?>