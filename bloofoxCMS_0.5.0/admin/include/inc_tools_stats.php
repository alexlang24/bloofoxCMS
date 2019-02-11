<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_home_stats.php -
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

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

// General Statistics
$result1 = $db->query("SELECT cid FROM ".$tbl_prefix."sys_config");
$total1 = $db->num_rows();

$result2 = $db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer");
$total2 = $db->num_rows();

$result3 = $db->query("SELECT cid FROM ".$tbl_prefix."sys_content");
$total3 = $db->num_rows();

$result4 = $db->query("SELECT lid FROM ".$tbl_prefix."sys_lang");
$total4 = $db->num_rows();

$result5 = $db->query("SELECT tid FROM ".$tbl_prefix."sys_template");
$total5 = $db->num_rows();

$result6 = $db->query("SELECT pid FROM ".$tbl_prefix."sys_plugin");
$total6 = $db->num_rows();

$result7 = $db->query("SELECT cid FROM ".$tbl_prefix."sys_charset");
$total7 = $db->num_rows();

$total_all = $total1 + $total2 + $total3 + $total4 + $total5 + $total6 + $total7;

$home_stats['general'] = "<p class='bold'>".get_caption('0170','General')."</p>";
$home_stats['general'] .= "<table border='0' class='list'>";
// 1
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('3020','Projects')."</td>";
$home_stats['general'] .= "<td>".$total1."</td>";
if($total_all != 0) {
	$total1 = round($total1 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total1."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 2
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('2020','Pages')."</td>";
$home_stats['general'] .= "<td>".$total2."</td>";
if($total_all != 0) {
	$total2 = round($total2 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total2."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 3
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('2030','Articles')."</td>";
$home_stats['general'] .= "<td>".$total3."</td>";
if($total_all != 0) {
	$total3 = round($total3 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total3."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 4
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('3030','Languages')."</td>";
$home_stats['general'] .= "<td>".$total4."</td>";
if($total_all != 0) {
	$total4 = round($total4 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total4."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 5
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('3040','Templates')."</td>";
$home_stats['general'] .= "<td>".$total5."</td>";
if($total_all != 0) {
	$total5 = round($total5 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total5."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 6
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('3050','Plugins')."</td>";
$home_stats['general'] .= "<td>".$total6."</td>";
if($total_all != 0) {
	$total6 = round($total6 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total6."' height='10' /></td>";
$home_stats['general'] .= "</tr>";
// 7
$home_stats['general'] .= "<tr class='bg_color2'>";
$home_stats['general'] .= "<td>".get_caption('3060','Charsets')."</td>";
$home_stats['general'] .= "<td>".$total7."</td>";
if($total_all != 0) {
	$total7 = round($total7 / $total_all * 100);
}
$home_stats['general'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total7."' height='10' /></td>";
$home_stats['general'] .= "</tr>";

$home_stats['general'] .= "</table>";

// User Statistics
$result1 = $db->query("SELECT pid FROM ".$tbl_prefix."sys_profile WHERE gender = '0'");
$total1 = $db->num_rows();

$result2 = $db->query("SELECT pid FROM ".$tbl_prefix."sys_profile WHERE gender = '1'");
$total2 = $db->num_rows();

$result3 = $db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE deleted = '1'");
$total3 = $db->num_rows();

$result4 = $db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE blocked = '1'");
$total4 = $db->num_rows();

$result5 = $db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE status = '1'");
$total5 = $db->num_rows();

$result6 = $db->query("SELECT uid FROM ".$tbl_prefix."sys_user");
$total6 = $db->num_rows();

$result7 = $db->query("SELECT gid FROM ".$tbl_prefix."sys_usergroup");
$total7 = $db->num_rows();

$total_all = $total1 + $total2 + $total3 + $total4 + $total5 + $total6 + $total7;

$home_stats['user'] = "<p class='bold'>".get_caption('4010','Users')."</p>";
$home_stats['user'] .= "<table border='0' class='list'>";
// 1
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('4260','Male')."</td>";
$home_stats['user'] .= "<td>".$total1."</td>";
if($total_all != 0) {
	$total1 = round($total1 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total1."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 2
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('4270','Female')."</td>";
$home_stats['user'] .= "<td>".$total2."</td>";
if($total_all != 0) {
	$total2 = round($total2 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total2."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 3
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('0240','Deleted')."</td>";
$home_stats['user'] .= "<td>".$total3."</td>";
if($total_all != 0) {
	$total3 = round($total3 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total3."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 4
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('0230','Blocked')."</td>";
$home_stats['user'] .= "<td>".$total4."</td>";
if($total_all != 0) {
	$total4 = round($total4 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total4."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 5
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('0260','Active')."</td>";
$home_stats['user'] .= "<td>".$total5."</td>";
if($total_all != 0) {
	$total5 = round($total5 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total5."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 6
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('0320','Total')."</td>";
$home_stats['user'] .= "<td>".$total6."</td>";
if($total_all != 0) {
	$total6 = round($total6 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total6."' height='10' /></td>";
$home_stats['user'] .= "</tr>";
// 7
$home_stats['user'] .= "<tr class='bg_color2'>";
$home_stats['user'] .= "<td>".get_caption('4020','User Groups')."</td>";
$home_stats['user'] .= "<td>".$total7."</td>";
if($total_all != 0) {
	$total7 = round($total7 / $total_all * 100);
}
$home_stats['user'] .= "<td><img src='../templates/admincenter/images/stats.gif' width='".$total7."' height='10' /></td>";
$home_stats['user'] .= "</tr>";

$home_stats['user'] .= "</table>";

// Set variables
$tpl->set_var(array(
	"home_title"         => "<h2>".get_caption('5000','Tools')." / ".get_caption('5240','Statistics')."</h2>",
	"home_stats_general" => "<p>".$home_stats['general']."</p>",
	"home_stats_user"    => "<p>".$home_stats['user']."</p>"
	));
?>