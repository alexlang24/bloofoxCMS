<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_plugins.php -
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

class plugins {
	//**
	// variables
	var $var = 1;
	var $plugin_dir = "../plugins/";
	var $install_file = "install.php";
	
	var $plugin_vars = array();
	var $plugin_query = array();
	var $plugin_table = array();
	
	//**
	// constructor
	function plugins()
	{
	}
	
	//**
	// install plugin
	function install($db,$plugin_name)
	{
		global $tbl_prefix,$ac;
		
		if($this->install_file_exist($plugin_name."/"))
		{
			$this->set_install_values($plugin_name."/");
			if($this->plugin_exist($db))
			{
				return($ac->show_error_message(get_caption('9370','Plugin (ID) already exists.')." ".$tbl_prefix."sys_plugin",$msg_class="error"));
			}
			
			// insert
			$db->query("INSERT INTO ".$tbl_prefix."sys_plugin (`pid`,`name`,`index_file`,`index_tmpl`,`tmpl_handler`,`admin_file`,`admin_handler`,`install_path`,`plugin_type`,`status`,`plugin_version`)
				VALUES('".$this->plugin_vars['pluginID']."','".$plugin_name."','".$this->plugin_vars['index_file']."','".$this->plugin_vars['index_tmpl']."','".$this->plugin_vars['tmpl_handler']."','".$this->plugin_vars['admin_file']."','".$this->plugin_vars['admin_handler']."','".$plugin_name."/','".$this->plugin_vars['plugin_type']."','1','".$this->plugin_vars['version']."')");
			
			// queries
			for($x=0; $x<count($this->plugin_query); $x++)
			{
				if($this->plugin_query[$x] != "") {
					$db->query($this->plugin_query[$x]);
				}
			}
			
			return("");
		}
		
		return($ac->show_error_message(get_caption('9500','install.php does not exist:')." /plugins/".$plugin_name,$msg_class="error"));
	}
	
	//**
	// uninstall plugin
	function uninstall($db,$plugin_name,$pid)
	{
		global $tbl_prefix,$ac;
		
		if($this->install_file_exist($plugin_name))
		{
			$this->set_install_values($plugin_name);
			
			if($this->plugin_vars['pluginID'] == $pid) {
				// queries
				for($x=0; $x<count($this->plugin_table); $x++)
				{
					if($this->plugin_table[$x] != "") {
						$db->query("DROP TABLE ".$tbl_prefix.$this->plugin_table[$x].";");
					}
				}
				
				$db->query("DELETE FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$pid."' LIMIT 1");
			}
			
			return("");
		}
		
		return($ac->show_error_message(get_caption('9500','install.php does not exist:')." /plugins/".$plugin_name,$msg_class="error"));
	}
	
	//**
	// check existing install.php
	function install_file_exist($plugin_name)
	{
		if(file_exists($this->plugin_dir.$plugin_name.$this->install_file)) {
			return(1);
		}
		return(0);
	}
	
	//**
	// read install.php
	function set_install_values($plugin_name)
	{
		global $tbl_prefix;
		
		require_once($this->plugin_dir.$plugin_name.$this->install_file);
		
		// read settings
		foreach($sys_plugin_vars as $key => $val)
		{
			$this->plugin_vars[$key] = $val;
		}
		
		// read queries
		for($x=0; $x<count($sys_plugin_sql); $x++)
		{
			$this->plugin_query[$x] = $sys_plugin_sql[$x];
		}
		
		// read table names
		for($x=0; $x<count($sys_plugin_table); $x++)
		{
			$this->plugin_table[$x] = $sys_plugin_table[$x];
		}
	}
	
	//**
	// check if plugin is already installed
	function plugin_exist($db)
	{
		global $tbl_prefix;
				
		$db->query("SELECT pid FROM ".$tbl_prefix."sys_plugin WHERE pid = '".$this->plugin_vars['pluginID']."' LIMIT 1");
		if($db->num_rows() == 0) {
			return(0);
		}
		return(1);
	}
}
?>