<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - update/index.php -
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

// Set error reporting
error_reporting (E_ALL ^ E_NOTICE);

// Define constants
define ('SYS_WORK_DIR', getcwd());
define ('SYS_INDEX', 1);

// check sql parameter
if(isset($_GET['sql']) || isset($_POST['sql'])) {
	die("Access denied! Forbidden SQL query.");
}

// Setup Update files
$update_files[0] = "update_0.3.0-0.3.1.php";
$update_files[1] = "update_0.3.1-0.3.2.php";
$update_files[2] = "update_0.3.2-0.3.3.php";
$update_files[3] = "update_0.3.3-0.3.4.php";
$update_files[4] = "update_0.3.4-0.3.5.php";
$update_files[5] = "update_0.3.5-0.4.0.php";
$update_files[6] = "update_0.4.0-0.4.1.php";
$update_files[7] = "update_0.4.1-0.5.0.php";

// Load required libraries
include("../config.php");
include("../functions.php");
require_once("../system/class_mysql.php");

// create doctype
echo create_doctype(0);
echo "<html>\n";
echo "<head><title>bloofoxCMS Update Center</title></head>\n";
echo "<body>\n";
echo "<h1>bloofoxCMS Update Center</h1>\n";

// Handle update files
if(isset($_GET['page']) && CheckInteger($_GET['page'])) {
	$page = $_GET['page'];
	$upd_var['update_file'] = SYS_WORK_DIR."/".$update_files[$page];
	if(file_exists($upd_var['update_file'])) {
		include_once($upd_var['update_file']);
	}
}
	
// List of update files
echo "<table border='1'>\n";
echo "<tr><td>Source Version</td><td>Target Version</td><td>Action</td></tr>\n";

for($i=0; $i<count($update_files); $i++)
{
	// get source version from file
	$upd_var['source_version'] = substr($update_files[$i],strpos($update_files[$i],"_")+1,strpos($update_files[$i],"-") - strpos($update_files[$i],"_") - 1);
	// get target version from file
	$upd_var['update_version'] = substr($update_files[$i],strpos($update_files[$i],"-")+1,strpos($update_files[$i],".php") - strpos($update_files[$i],"-") - 1);
	
	echo "<tr>\n";
	echo "<td>".$upd_var['source_version']."</td>\n";
	echo "<td>".$upd_var['update_version']."</td>\n";
	echo "<td><a href='index.php?page=".$i."' title='".get_caption('Update')."'>".get_caption('Update')."</a></td>\n";
	echo "</tr>\n";
}

echo "</table>\n";

// Close HTML file
echo "</body>\n";
echo "</html>\n";
?>