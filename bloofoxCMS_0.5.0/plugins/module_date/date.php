<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/module_date/date.php -
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

$date_vars['day'] = date("w");
$date_vars['month'] = date("n");

// Format: "j" = e.g. without leading null, "d" = e.g. with leading null
$date_vars['today'] = date("d");

// Format: "y" = e.g. 06, "Y" = e.g. 2006
$date_vars['year'] = date("Y");

// create arrays
$days = array(
	0 => get_caption('0613','Sunday'),
	1 => get_caption('0614','Monday'),
	2 => get_caption('0615','Tuesday'),
	3 => get_caption('0616','Wednesday'),
	4 => get_caption('0617','Thursday'),
	5 => get_caption('0618','Friday'),
	6 => get_caption('0619','Saturday'),
	);
	
$months = array(
	1=>get_caption('0601','January'),
	2=>get_caption('0602','February'),
	3=>get_caption('0603','March'),
	4=>get_caption('0604','April'),
	5=>get_caption('0605','May'),
	6=>get_caption('0606','June'),
	7=>get_caption('0607','July'),
	8=>get_caption('0608','August'),
	9=>get_caption('0609','September'),
	10=>get_caption('0610','October'),
	11=>get_caption('0611','November'),
	12=>get_caption('0612','December'));

// Create Date
$date_vars['format'] = "[WEEKDAY], [MONTH] [DAY] [YEAR]";

$date_vars['format'] = str_replace("[WEEKDAY]",$days[$date_vars['day']],$date_vars['format']);
$date_vars['format'] = str_replace("[DAY]",$date_vars['today'],$date_vars['format']);
$date_vars['format'] = str_replace("[MONTH]",$months[$date_vars['month']],$date_vars['format']);
$date_vars['format'] = str_replace("[YEAR]",$date_vars['year'],$date_vars['format']);

// Template Block-Setup
$tpl->set_block("plugin_module_date", "date", "date_handle");

$tpl->set_var(array(
	"current_date"    => $date_vars['format']
	));

$tpl->parse("date_handle", "date", true);
?>