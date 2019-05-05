<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_pagehandler.php -
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

class pagehandler {
	//**
	// variables
	var $var = 1;
	var $mode = "";
	var $page = "";
	var $action = "";
	var $path = "";
	
	var $menu = "menu.html"; // default menu template
	var $tmpl = "home.html"; // default template
	var $handler = "mod_home.php"; // default application file
	var $inc = ""; // default include file
	var $plugin = 0;
	
	//**
	// constructor
	function __construct($mode,$page,$action,$tmpl_path)
	{
		$this->mode = $mode;
		$this->page = $page;
		$this->action = $action;
		$this->path = $tmpl_path;
	}

	function pagehandler($mode,$page,$action,$tmpl_path)
	{
		self::__construct($mode,$page,$action,$tmpl_path);
	}
	
	//**
	// search templates and handler
	function set_tmpl_handling($db)
	{
		global $tbl_prefix,$sys_group_vars,$perm;
		
		switch($this->mode)
		{
			case 'plugins':
				$this->tmpl = "plugins_admin.html";
				if(isset($_POST['pluginID'])) {
					$_GET['pluginID'] = $_POST['pluginID'];
				}

				if(!isset($_GET['pluginID']) || !CheckInteger($_GET['pluginID'])) {
					load_url("index.php?mode=settings&page=plugins");
				}

				$db->query("SELECT admin_handler,install_path FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$_GET['pluginID']."' LIMIT 1");
				while($db->next_record()):
					$plugin_path = $db->f("install_path");
					$plugin_admin_handler = $db->f("admin_handler");
				endwhile;
				
				// interface to plugins admin templates
				if($plugin_admin_handler != "") {
					include("../plugins/".$plugin_path.$plugin_admin_handler);
				}
				$this->handler = "mod_plugins.php";
			break;
			
			case 'content':
				switch($this->page)
				{
					case 'media':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "mediacenter_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "mediacenter_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "mediacenter_delete.html";
							break;
							
							default:
								$this->tmpl = "mediacenter.html";
							break;
						}
						
						$this->inc = "inc_content_media.php";
					break;

					case 'pages':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "pages_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "pages_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "pages_delete.html";
							break;
							
							default:
								$this->tmpl = "pages.html";
							break;
						}
						
						$this->inc = "inc_content_pages.php";
					break;
					
					case 'articles':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "article_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "article_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "article_delete.html";
							break;
							
							default:
								$this->tmpl = "articles.html";
							break;
						}
						
						$this->inc = "inc_content_articles.php";
					break;
					
					case 'content':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "content_new.html";
							break;
						
							case 'edit':
								$this->tmpl = "content_edit.html";
							break;

							case 'del':
								$this->tmpl = "content_delete.html";
							break;
							
							default:
								$this->tmpl = "contents.html";
							break;
						}
						
						$this->inc = "inc_content_content.php";
					break;
					
					default:
						$this->tmpl = "content_levels.html";
						$this->inc = "inc_content_levels.php";
					break;
				}
				$this->handler = "mod_content.php";
				if($sys_group_vars['content'] == 0) {
					$this->tmpl = "error.html";
					$this->handler = "mod_error.php";
				}
			break;
			
			case 'settings':
				switch($this->page)
				{
					case 'charset':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "charset_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "charset_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "charset_delete.html";
							break;
							
							default:
								$this->tmpl = "charset.html";
							break;
						}
						
						$this->inc = "inc_settings_charset.php";
					break;
				
					case 'editor':
						$this->tmpl = "editor.html";
						$this->inc = "inc_settings_editor.php";
					break;
					
					case 'plugins':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "plugins_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "plugins_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "plugins_delete.html";
							break;
							
							default:
								$this->tmpl = "plugins.html";
							break;
						}
						
						$this->inc = "inc_settings_plugins.php";
					break;
					
					case 'tmpl':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "templates_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "templates_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "templates_delete.html";
							break;
							
							default:
								$this->tmpl = "templates.html";
							break;
						}
						
						$this->inc = "inc_settings_tmpl.php";
					break;
					
					case 'lang':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "lang_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "lang_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "lang_delete.html";
							break;
							
							default:
								$this->tmpl = "lang.html";
							break;
						}
						
						$this->inc = "inc_settings_lang.php";
					break;
					
					case 'projects':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "project_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "project_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "project_delete.html";
							break;
							
							default:
								$this->tmpl = "projects.html";
							break;
						}
						
						$this->inc = "inc_settings_projects.php";
					break;
					
					default: // general
						$this->tmpl = "settings_general.html";
						$this->inc = "inc_settings_general.php";
					break;
				}
				$this->handler = "mod_settings.php";
				if($sys_group_vars['settings'] == 0) {	
					$this->tmpl = "error.html";
					$this->handler = "mod_error.php";
				}
			break;
			
			case 'user':
				switch($this->page)
				{
					case 'sessions':
						switch($this->action)
						{
							case 'kill':
								$this->tmpl = "user_sessions_delete.html";
							break;
							
							default:
								$this->tmpl = "user_sessions.html";
							break;
						}
						
						$this->inc = "inc_user_sessions.php";
					break;
					
					case 'permissions':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "user_permissions_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "user_permissions_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "user_permissions_delete.html";
							break;
							
							default:
								$this->tmpl = "user_permissions.html";
							break;
						}
						
						$this->inc = "inc_user_permissions.php";
					break;
					
					case 'groups':
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "user_groups_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "user_groups_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "user_groups_delete.html";
							break;
							
							default:
								$this->tmpl = "user_groups.html";
							break;
						}
						
						$this->inc = "inc_user_groups.php";
					break;
					
					default: // users
						switch($this->action)
						{
							case 'new':
								$this->tmpl = "user_new.html";
							break;
							
							case 'edit':
								$this->tmpl = "user_edit.html";
							break;
							
							case 'del':
								$this->tmpl = "user_delete.html";
							break;
							
							case 'profile':
								$this->tmpl = "user_profile.html";
							break;
							
							default:
								$this->tmpl = "user.html";
							break;
						}
						
						$this->inc = "inc_user_user.php";
					break;
				}
				$this->handler = "mod_user.php";
				if($sys_group_vars['permissions'] == 0) {
					$this->tmpl = "error.html";
					$this->handler = "mod_error.php";
				}
			break;
			
			case 'tools':
				switch($this->page)
				{
					case 'upload':
						switch($this->action)
						{
							default:
								$this->tmpl = "tools_upload.html";
							break;
						}
						
						$this->inc = "inc_tools_upload.php";
					break;

					case 'backup':
						switch($this->action)
						{
							default:
								$this->tmpl = "tools_backup.html";
							break;
						}
						
						$this->inc = "inc_tools_backup.php";
					break;
					
					case 'phpmyadmin':
						switch($this->action)
						{
							default:
								$this->tmpl = "tools_phpmyadmin.html";
							break;
						}
						
						$this->inc = "inc_tools_phpmyadmin.php";
					break;
					
					case 'stats':
						$this->tmpl = "tools_stats.html";
						$this->inc = "inc_tools_stats.php";
					break;
					
					default: // maintenance
						switch($this->action)
						{
							case 'optimize':
								$this->tmpl = "tools_optimize.html";
							break;
							
							case 'inactive_user':
								$this->tmpl = "tools_inactiveuser.html";
							break;
							
							case 'deleted_user':
								$this->tmpl = "tools_deleteduser.html";
							break;
							
							case 'sessions':
								$this->tmpl = "tools_sessions.html";
							break;
							
							default: // overview
								$this->tmpl = "tools.html";
							break;
						}
						
						$this->inc = "inc_tools_tools.php";
					break;
				}
				$this->handler = "mod_tools.php";
				if($sys_group_vars['tools'] == 0) {	
					$this->tmpl = "error.html";
					$this->handler = "mod_error.php";
				}
			break;
			
			case 'logout':
				$_GET['login'] = "logout";
				$perm->logout($db,"index.php");
			break;
			
			case 'login':
				$this->menu = "menu_empty.html";
				$this->tmpl = "login.html";
				$this->handler = "mod_login.php";
			break;

			default:
				switch($this->page)
				{
					case 'changepw':
						$this->tmpl = "changepw.html";
						$this->inc = "inc_home_changepw.php";
					break;
					
					case 'profiles':
						$this->tmpl = "profiles.html";
						$this->inc = "inc_home_profiles.php";
					break;
					
					case 'myprofile':
						$this->tmpl = "myprofile.html";
						$this->inc = "inc_home_myprofile.php";
					break;
					
					default: // home
						$this->tmpl = "home.html";
						$this->inc = "inc_home_home.php";
					break;
				}
				$this->handler = "mod_home.php";
			break;
		}
	}
	
	//**
	// get tmpl parameter
	function get_tmpl_parameter()
	{
		$tmpl_set = array();
		$tmpl_set['menu'] = $this->path.$this->menu;
		if($this->plugin == 1) {
			$tmpl_set['mode'] = $this->tmpl;
		} else {
			$tmpl_set['mode'] = $this->path.$this->tmpl;
		}
		$tmpl_set['handler'] = "/".$this->handler;
		$tmpl_set['inc'] = "/".$this->inc;
		
		return($tmpl_set);
	}
}
?>