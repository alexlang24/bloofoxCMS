<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/mod_menu.php -
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

for($i=1; $i<=23; $i++) {
	$sub_class[$i] = "class='subalways'";
}

for($j=1; $j<=6; $j++) {
	$menu_class[$j] = "class='always'";
}

switch($tmpl_set['handler'])
{
	case '/mod_plugins.php':
		$menu_class[3] = "class='current'";
		$sub_class[10] = "class='subcurrent'";
	break;
	
	case '/mod_content.php':
		$menu_class[2] = "class='current'";
		
		switch($page)
		{
			case 'media': $sub_class[5] = "class='subcurrent'"; break;
			case 'pages': $sub_class[3] = "class='subcurrent'"; break;
			case 'articles': $sub_class[4] = "class='subcurrent'"; break;
			case 'content': $sub_class[3] = "class='subcurrent'"; break;
			default: $sub_class[2] = "class='subcurrent'"; break;
		}
	break;

	case '/mod_settings.php':
		$menu_class[3] = "class='current'";
		
		switch($page)
		{
			case 'projects': $sub_class[7] = "class='subcurrent'"; break;
			case 'lang': $sub_class[8] = "class='subcurrent'"; break;
			case 'tmpl': $sub_class[9] = "class='subcurrent'"; break;
			case 'plugins': $sub_class[10] = "class='subcurrent'"; break;
			case 'charset': $sub_class[11] = "class='subcurrent'"; break;
			case 'editor': break;
			default: $sub_class[6] = "class='subcurrent'"; break;
		}
	break;
	
	case '/mod_user.php':
		$menu_class[4] = "class='current'";
		
		switch($page)
		{
			case 'groups': $sub_class[13] = "class='subcurrent'"; break;
			case 'permissions': $sub_class[14] = "class='subcurrent'"; break;
			case 'sessions': $sub_class[15] = "class='subcurrent'"; break;
			default: $sub_class[12] = "class='subcurrent'"; break;
		}
	break;
	
	case '/mod_tools.php':
		$menu_class[5] = "class='current'";
		switch($page)
		{
			case 'upload': $sub_class[17] = "class='subcurrent'"; break;
			case 'backup': $sub_class[18] = "class='subcurrent'"; break;
			case 'phpmyadmin': $sub_class[20] = "class='subcurrent'"; break;
			case 'update': $sub_class[23] = "class='subcurrent'"; break;
			case 'stats': $sub_class[1] = "class='subcurrent'";	break; 
			default: $sub_class[16] = "class='subcurrent'"; break;
		}
	break;
	
	default:
		switch($page)
		{
			case 'changepw':
				$menu_class[1] = "class='current'";
				$sub_class[22] = "class='subcurrent'"; 
			break;
			
			case 'myprofile':
				$menu_class[1] = "class='current'";
				$sub_class[21] = "class='subcurrent'"; 
			break;
			
			default:
				$menu_class[1] = "class='current'"; // Home
			break;
		}
	break;
}

if($sys_group_vars['content'] == 1) {
	$menu2_sub1 = "<li><a ".$sub_class[2]." href=\"index.php?mode=content&page=levels\">".get_caption('2010','Structure')."</a></li>";
	$menu2_sub2 = "<li><a ".$sub_class[3]." href=\"index.php?mode=content&page=pages\">".get_caption('2020','Pages')."</a></li>";
	$menu2_sub3 = "<li><a ".$sub_class[4]." href=\"index.php?mode=content&page=articles\">".get_caption('2030','Articles')."</a></li>";
	$menu2_sub4 = "<li><a ".$sub_class[5]." href=\"index.php?mode=content&page=media\">".get_caption('2040','Media')."</a></li>";
	$menu2_sub5 = "";
	$menu2_sub6 = "";
}

if($sys_group_vars['settings'] == 1) {
	$menu3_sub1 = "<li><a ".$sub_class[6]." href=\"index.php?mode=settings\">".get_caption('3010','Settings')."</a></li>";
	$menu3_sub2 = "<li><a ".$sub_class[7]." href=\"index.php?mode=settings&page=projects\">".get_caption('3020','Projects')."</a></li>";
	$menu3_sub3 = "<li><a ".$sub_class[8]." href=\"index.php?mode=settings&page=lang\">".get_caption('3030','Languages')."</a></li>";
	$menu3_sub4 = "<li><a ".$sub_class[9]." href=\"index.php?mode=settings&page=tmpl\">".get_caption('3040','Templates')."</a></li>";
	$menu3_sub5 = "<li><a ".$sub_class[10]." href=\"index.php?mode=settings&page=plugins\">".get_caption('3050','Plugins')."</a></li>";
	$menu3_sub6 = "<li><a ".$sub_class[11]." href=\"index.php?mode=settings&page=charset\">".get_caption('3060','Charset')."</a></li>";
}

if($sys_group_vars['permissions'] == 1) {
	$menu4_sub1 = "<li><a ".$sub_class[12]." href=\"index.php?mode=user\">".get_caption('4010','User')."</a></li>";
	$menu4_sub2 = "<li><a ".$sub_class[13]." href=\"index.php?mode=user&page=groups\">".get_caption('4020','User Groups')."</a></li>";
	$menu4_sub3 = "<li><a ".$sub_class[14]." href=\"index.php?mode=user&page=permissions\">".get_caption('4030','Permissions')."</a></li>";
	$menu4_sub4 = "<li><a ".$sub_class[15]." href=\"index.php?mode=user&page=sessions\">".get_caption('4040','Sessions')."</a></li>";
	$menu4_sub5 = "";
	$menu4_sub6 = "";
}

if($sys_group_vars['tools'] == 1) {
	$menu5_sub1 = "<li><a ".$sub_class[16]." href=\"index.php?mode=tools\">".get_caption('5010','Maintenance')."</a></li>";
	$menu5_sub2 = "<li><a ".$sub_class[17]." href=\"index.php?mode=tools&page=upload\">".get_caption('5020','Upload')."</a></li>";
	//$menu5_sub3 = "<li><a ".$sub_class[18]." href=\"index.php?mode=tools&page=backup\">".get_caption(0,'Backup')."</a></li>";
	//$menu5_sub4 = "<li><a ".$sub_class[23]." href=\"index.php?mode=tools&page=update\">".get_caption(0,'Update')."</a></li>";
	$menu5_sub5 = "<li><a ".$sub_class[20]." href=\"index.php?mode=tools&page=phpmyadmin\">".get_caption('5220','PhpMyAdmin')."</a></li>";
	$menu5_sub6 = "<li><a ".$sub_class[1]." href=\"index.php?mode=tools&page=stats\">".get_caption('5240','Statistics')."</a></li>";
}

//$menu6_sub1 = "<li><a ".$sub_class[21]." href=\"index.php?page=myprofile\">".get_caption('4630','My Profile')."</a></li>";
//$menu6_sub2 = "<li><a ".$sub_class[22]." href=\"index.php?page=changepw\">".get_caption('4650','Password')."</a></li>";
//$menu1_sub1 = "<li><a href='index.php?mode=logout' title='".get_caption('4650','Logout')."'>".get_caption('4650','Logout')." [ ".$_SESSION["username"]." ]</a></li>";

if(!empty($_SESSION["username"])) {
	$menu_username = get_caption('4060','Username').": ".$_SESSION["username"];
	$menu_myprofile = $ac->create_editprofile_icon("index.php?page=myprofile",get_caption('4630','My Profile'))." <a href='index.php?page=myprofile'>".get_caption('4630','My Profile')."</a>";
	$menu_changepwd = $ac->create_edit_icon("index.php?page=changepw",get_caption('4640','Change Password'))." <a href='index.php?page=changepw'>".get_caption('4640','Change Password')."</a>";
	$menu_logout = $ac->create_logout_icon("index.php?mode=logout",get_caption('4650','Logout'))." <a href='index.php?mode=logout'>".get_caption('4650','Logout')."</a>";
}

// Set template block
$tpl->set_block("tmpl_menu", "menu", "menu_handle");

// Set variables
$tpl->set_var(array(
	"menu_title"      => "<p class=\"logo\"><span class=\"small\">bloofoxCMS</span><br />AdminCenter</p>",
	"menu_username"   => $menu_username,
	"menu_logout"     => $menu_logout,
	"menu_myprofile"  => $menu_myprofile,
	"menu_changepwd"  => $menu_changepwd,
	"menu_closelayer" => get_caption('4660','Close [x]'),
	"menu_date"       => date($sys_vars['date']),
	"menu1"           => "<a ".$menu_class[1]." href=\"index.php\" title='".get_caption('1010','Home')."'>".get_caption('1010','Home')."</a>",
	//"menu1_sub1"      => $menu1_sub1,
	"menu1_sub2"      => $menu1_sub2,
	"menu1_sub3"      => $menu1_sub3,
	"menu1_sub4"      => $menu1_sub4,
	"menu1_sub5"      => $menu1_sub5,
	"menu1_sub6"      => $menu1_sub6,
	"menu2"           => "<a ".$menu_class[2]." href=\"index.php?mode=content\" title='".get_caption('2000','Contents')."'>".get_caption('2000','Contents')."</a>",
	"menu2_sub1"      => $menu2_sub1,
	"menu2_sub2"      => $menu2_sub2,
	"menu2_sub3"      => $menu2_sub3,
	"menu2_sub4"      => $menu2_sub4,
	"menu2_sub5"      => $menu2_sub5,
	"menu2_sub6"      => $menu2_sub6,
	"menu3"           => "<a ".$menu_class[3]." href=\"index.php?mode=settings\" title='".get_caption('3000','Administration')."'>".get_caption('3000','Administration')."</a>",
	"menu3_sub1"      => $menu3_sub1,
	"menu3_sub2"      => $menu3_sub2,
	"menu3_sub3"      => $menu3_sub3,
	"menu3_sub4"      => $menu3_sub4,
	"menu3_sub5"      => $menu3_sub5,
	"menu3_sub6"      => $menu3_sub6,
	"menu4"           => "<a ".$menu_class[4]." href=\"index.php?mode=user\" title='".get_caption('4000','Security')."'>".get_caption('4000','Security')."</a>",
	"menu4_sub1"      => $menu4_sub1,
	"menu4_sub2"      => $menu4_sub2,
	"menu4_sub3"      => $menu4_sub3,
	"menu4_sub4"      => $menu4_sub4,
	"menu4_sub5"      => $menu4_sub5,
	"menu4_sub6"      => $menu4_sub6,
	"menu5"           => "<a ".$menu_class[5]." href=\"index.php?mode=tools\" title='".get_caption('5000','Tools')."'>".get_caption('5000','Tools')."</a>",
	"menu5_sub1"      => $menu5_sub1,
	"menu5_sub2"      => $menu5_sub2,
	"menu5_sub3"      => $menu5_sub3,
	"menu5_sub4"      => $menu5_sub4,
	"menu5_sub5"      => $menu5_sub5,
	"menu5_sub6"      => $menu5_sub6
	/*
	"menu6"           => "<a ".$menu_class[6]." href=\"index.php?page=myprofile\" title='".get_caption('4620','My Account')."'>".get_caption('4620','My Account')."</a>",
	"menu6_sub1"      => $menu6_sub1,
	"menu6_sub2"      => $menu6_sub2
	*/
	));

// Parse template with variables
$tpl->parse("menu_handle", "menu", true);

?>