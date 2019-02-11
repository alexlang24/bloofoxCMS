<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_permissions.php -
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

class permissions {

	var $backend_user = 0; // Access to Admincenter
	var $current_time = 0; // Current time for statistics
	var $current_user = 0; // Current user id
	var $group_vars = array();
	
	// Constructor
	function permissions()
	{
		$this->current_time = time();
	}
	
	// Public: Check, if current user is logged in
	function check_session()
	{
		global $tbl_prefix,$sys_setting_vars;
		
		session_name("sid");
		session_start;
		if(isset($_SESSION["username"]) && isset($_SESSION["password"])) {
			$db = new DB_Tpl();
			$session_id = session_id();
			if($sys_setting_vars['login_protection'] == 1) {
				$db->query("SELECT sid FROM ".$tbl_prefix."sys_session WHERE type = 0 && session_id = '".$session_id."'");
				if($db->num_rows() == 0) {
					return(0);
				}
			}
			return(1);
		}
		return(0);
	}
	
	// Public: Make login session
	function login($db,$user,$pass)
	{
		global $tbl_prefix,$sys_setting_vars;
		$user = strtolower($user);
		$pass = md5($pass);
		// login by username
		$db->query("SELECT uid,username,password,groups,curr_login,login_page FROM ".$tbl_prefix."sys_user WHERE username = '".$user."' && password = '".$pass."' && blocked = '0' && deleted = '0' && status = '1' ORDER BY username");
		$total = $db->num_rows();
		// login by email
		if($total == 0) {
			$user2 = $this->get_username($db,$this->get_userid_by_email($db,$user));
			$db->query("SELECT uid,username,password,groups,curr_login,login_page FROM ".$tbl_prefix."sys_user WHERE username = '".$user2."' && password = '".$pass."' && blocked = '0' && deleted = '0' && status = '1' ORDER BY username");
			$total = $db->num_rows();
		}
		
		if($total == 1) {
			while($db->next_record()):
				session_name("sid");
				session_start();
				$_SESSION["uid"] = $db->f("uid");
				$_SESSION["username"] = $db->f("username");
				$_SESSION["password"] = $db->f("password");
				$_SESSION["usergroups"] = $db->f("groups");
				$_SESSION["login_page"] = $db->f("login_page");
				$this->current_user = $db->f("uid");
				$new_last_login = $db->f("curr_login");
			endwhile;
			
			$this->set_online_status($db,1);
			$db->query( "UPDATE ".$tbl_prefix."sys_user SET last_login = '".$new_last_login."' WHERE uid = '".$_SESSION["uid"]."' ORDER BY uid LIMIT 1" );
			$db->query( "UPDATE ".$tbl_prefix."sys_user SET curr_login = '".$this->current_time."' WHERE uid = '".$_SESSION["uid"]."' ORDER BY uid LIMIT 1" );
			
			$this->get_group_vars($db,$_SESSION["usergroups"]);
			if($sys_setting_vars['login_protection'] == 1) {
				$this->archive_session($db);
				$this->log_session($db,0,1,$user,$_SESSION["uid"]);
			}
			return(1);
		}
		
		if($sys_setting_vars['login_protection'] == 1) {
			$this->log_session($db,0,0,$user);
			if($this->count_session($db) >= 5) {
				// check groups
				$db->query("SELECT uid,groups FROM ".$tbl_prefix."sys_user WHERE username = '".$user."' && blocked = '0' && deleted = '0' && status = '1' ORDER BY username LIMIT 1");
				while($db->next_record()):
					$groups = $db->f("groups");
				endwhile;
				$this->get_group_vars($db,$groups);
				if($this->group_vars['demo'] == 0) {
					// send mail to user's email
					$this->send_unblock_mail($db);
					// block active user because too many logins failed
					$db->query("UPDATE ".$tbl_prefix."sys_user SET blocked = '1' WHERE uid = '".$this->current_user."' AND status = '1' LIMIT 1");
				}
			}
		}
		return(0);
	}
	
	// Public: set current user
	function set_current_user($uid)
	{
		$this->current_user = $uid;
	}
	
	// Private: Set online status True OR False
	function set_online_status($db,$new_status) {
		global $tbl_prefix;
		$db->query( "UPDATE ".$tbl_prefix."sys_user SET online_status = '".$new_status."' WHERE uid = '".$this->current_user."' ORDER BY uid LIMIT 1" );
	}
	
	// Private: make session entry
	function log_session($db,$type,$status,$user,$uid=0)
	{
		global $tbl_prefix;
		
		if($uid == 0) {
			$uid = $this->get_userid($db,$user);
		}
		
		// if no valid username found then exit logging
		if($uid == 0) {
			return;
		}
		
		$date = date("Y-m-d",$this->current_time);
		$time = date("H:i:s",$this->current_time);
		$timestamp = $this->current_time;
		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = session_id();
		
		$db->query("INSERT INTO ".$tbl_prefix."sys_session VALUES ('','".$type."','".$uid."','".$date."','".$time."','".$status."','".$timestamp."','".$ip."','".$session_id."');");
	}
	
	// Private: archive session entries (set type to 1)
	function archive_session($db)
	{
		global $tbl_prefix;
		$session_id = session_id();
		$db->query("UPDATE ".$tbl_prefix."sys_session SET type = 1 WHERE uid = '".$this->current_user."' && type = 0 && session_id = '".$session_id."'");
	}
	
	// Private: count failed sessions after logging current failed session
	function count_session($db)
	{
		global $tbl_prefix;
		
		$uid = $this->current_user;
		$db->query("SELECT sid FROM ".$tbl_prefix."sys_session WHERE type = 0 && status = 0 && uid = '".$uid."'");
		$total = $db->num_rows();
		
		return($total);
	}
	
	// Public: count failed sessions since last login
	function count_failed_session($db,$last_login)
	{
		global $tbl_prefix;
		
		$uid = $_SESSION["uid"];
		$db->query("SELECT sid FROM ".$tbl_prefix."sys_session WHERE type = 1 && status = 0 && uid = '".$uid."' && timestamp > '".$last_login."'");
		$total = $db->num_rows();
		
		return($total);
	}
	
	// Private: get userid from username
	function get_userid($db,$user)
	{
		global $tbl_prefix;
		
		$db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE username = '".$user."' LIMIT 1");
		while($db->next_record()):
			$uid = $db->f("uid");
		endwhile;
		$this->current_user = $uid;
		
		return($uid);
	}
	
	// Private: get userid from username
	function get_username($db,$uid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT username FROM ".$tbl_prefix."sys_user WHERE uid = '".$uid."' LIMIT 1");
		while($db->next_record()):
			return($db->f("username"));
		endwhile;
		
		return(0);
	}
	
	// Public: Destroy login session
	function logout($db,$url)
	{
		if($_GET['login'] == "logout") {
			global $tbl_prefix;
			$this->current_user = $_SESSION['uid'];
			$this->archive_session($db);
			
			session_destroy();
			$this->set_online_status($db,0);
			CreateConfirmMessage(2,get_caption('9255','Logout successful!'));
			
			load_url($url);
		}
	}
	
	// Public: Check, if user is backenduser
	function backend_access()
	{
		if($this->group_vars['backend'] == 1) {
			$this->backend_user = 1;
		}
		
		return($this->backend_user);
	}
	
	// Public: Load sys_vars array from Session var
	function get_vars_from_session($sys_vars)
	{
		$sys_vars['login_status'] = 1;
		$sys_vars['username'] = $_SESSION["username"];
		$sys_vars['uid'] = $_SESSION["uid"];
		$sys_vars['login_page'] = $_SESSION["login_page"];
		$sys_vars['usertext'] = get_caption("A090","You are logged in, ");
		$sys_vars['logout_url'] = "<a href='index.php?login=logout' title='".get_caption('A060','Logout')."'>".get_caption('A060','Logout')."</a>";
		$sys_vars['logout'] = "<p class='logout'>".$sys_vars['logout_url']."</p>";
		
		if($this->backend_access() == 1) {
			$sys_vars['admin_url'] = "<a href='admin/index.php' title='".get_caption("A110","Admincenter")."'>".get_caption("A110","Admincenter")."</a>";
		}
		
		return($sys_vars);
	}
	
	// Public: Get last login
	function get_last_login($db)
	{
		global $tbl_prefix;
		$uid = $_SESSION['uid'];
		
		$db->query( "SELECT last_login FROM ".$tbl_prefix."sys_user WHERE uid = '".$uid."' ORDER BY uid LIMIT 1" );
		while($db->next_record()):
			return($db->f("last_login"));
		endwhile;
		
		return(0);
	}
	
	// Private: send mail to user to unblock account
	function send_unblock_mail($db)
	{
		global $tbl_prefix,$sys_config_vars;
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_user WHERE uid = '".$this->current_user."' LIMIT 1");
		while($db->next_record()):
			$username = $db->f("username");
			$user_key = $db->f("key");
			$blocked = $db->f("blocked");
			$status = $db->f("status");
		endwhile;
		
		if($blocked == 1 || $status == 0) {
			return;
		}
		
		$db->query("SELECT email FROM ".$tbl_prefix."sys_profile WHERE user_id = '".$this->current_user."' ORDER BY user_id LIMIT 1");
		while($db->next_record()):
			$msg = get_caption('1020','Hello')." ".$username."! \n"
				.get_caption('A080','Your account has been blocked after too many failed logins. Click the link below to unblock your account:\n')
				."\n"
				.$sys_config_vars['url']."/index.php?key=".$user_key."\n"
				."\n"
				.$sys_config_vars['company_name'];
			if($db->f("email") != "") {
				send_mail($db->f("email"),$sys_config_vars['mail'],get_caption('A070','Unblock your account'),$msg);
			}
		endwhile;
	}
	
	// Get current user
	function get_user_profile($db,$uid)
	{
		global $tbl_prefix;
		$sys_user_vars = array();
		
		$db->query("SELECT * FROM ".$tbl_prefix."sys_profile WHERE user_id = '".$uid."' ORDER BY user_id LIMIT 1");
		while($db->next_record()):
			$sys_user_vars["user_id"] = $db->f("user_id");
			$sys_user_vars["firstname"] = $db->f("firstname");
			$sys_user_vars["lastname"] = $db->f("lastname");
			$sys_user_vars["address1"] = $db->f("address1");
			$sys_user_vars["address2"] = $db->f("address2");
			$sys_user_vars["city"] = $db->f("city");
			$sys_user_vars["zip_code"] = $db->f("zip_code");
			$sys_user_vars["email"] = $db->f("email");
			$sys_user_vars["birthday"] = $db->f("birthday");
			$sys_user_vars["gender"] = $db->f("gender");
			$sys_user_vars["picture"] = $db->f("picture");
			$sys_user_vars["be_lang"] = $db->f("be_lang");
			$sys_user_vars["be_tmpl"] = $db->f("be_tmpl");
			$sys_user_vars['show_email'] = $db->f("show_email");
		endwhile;
		
		return($sys_user_vars);
	}
	
	// Private: Get user_id by email
	function get_userid_by_email($db,$email)
	{
		global $tbl_prefix;
				
		$db->query("SELECT user_id FROM ".$tbl_prefix."sys_profile WHERE email = '".$email."' ORDER BY user_id LIMIT 1");
		while($db->next_record()):
			return($db->f("user_id"));
		endwhile;
		
		return(0);
	}
	
	// Get current password from user
	function get_current_password($db)
	{
		global $tbl_prefix;
		
		$db->query("SELECT password FROM ".$tbl_prefix."sys_user WHERE uid = '".$_SESSION['uid']."' ORDER BY uid LIMIT 1");
		while($db->next_record()):
			$password = $db->f("password");
		endwhile;
		
		return($password);
	}
	
	// Unblock user account by mail
	function unblock_user_account($db) {
		$user_key = validate_text($_GET['key']);
		if(isset($user_key) && $user_key != "") {
			global $tbl_prefix;
			$db->query("UPDATE ".$tbl_prefix."sys_user SET `blocked` = '0' WHERE `key` = '".$user_key."' && blocked = '1'");
			$db->query("SELECT uid FROM ".$tbl_prefix."sys_user WHERE `key` = '".$user_key."' LIMIT 1");
			while($db->next_record()):
				$this->set_current_user($db->f("uid"));
			endwhile;
			
			$this->archive_session($db);
		}
	}
	
	// Get group(s) for current user
	function get_group_vars($db,$groups)
	{
		global $tbl_prefix;
		$local_group_vars = array();
		$local_group_vars['backend'] = 0;
		$local_group_vars['content'] = 0;
		$local_group_vars['settings'] = 0;
		$local_group_vars['configure'] = 0;
		$local_group_vars['permissions'] = 0;
		$local_group_vars['tools'] = 0;
		$local_group_vars['demo'] = 0;
		
		$groups = explode(",",$groups);
		for($i=0; $i<count($groups); $i++)
		{
			$db->query("SELECT * FROM ".$tbl_prefix."sys_usergroup WHERE name = '".$groups[$i]."' LIMIT 1");
			while($db->next_record()):
				if($db->f("backend") == 1) {
					$local_group_vars['backend'] = 1;
				}
				if($db->f("content") == 1) {
					$local_group_vars['content'] = 1;
				}
				if($db->f("settings") == 1) {
					$local_group_vars['settings'] = 1;
				}
				if($db->f("configure") == 1) {
					$local_group_vars['configure'] = 1;
				}
				if($db->f("permissions") == 1) {
					$local_group_vars['permissions'] = 1;
				}
				if($db->f("tools") == 1) {
					$local_group_vars['tools'] = 1;
				}
				if($db->f("demo") == 1) {
					$local_group_vars['demo'] = 1;
				}
			endwhile;
		}
		
		$this->group_vars = $local_group_vars;
		return($this->group_vars);
	}
	
	// Get permissions for groups
	function get_permission($db,$groups)
	{
		global $tbl_prefix;
		$local_rights_vars = array();
		$local_rights_vars['projects']['write'] = 1;
		$local_rights_vars['projects']['delete'] = 1;
		if($this->group_vars['settings'] == 1) {
			$local_rights_vars['set_lang']['write'] = 1;
			$local_rights_vars['set_lang']['delete'] = 1;
			$local_rights_vars['set_tmpl']['write'] = 1;
			$local_rights_vars['set_tmpl']['delete'] = 1;
			$local_rights_vars['set_plugins']['write'] = 1;
			$local_rights_vars['set_plugins']['delete'] = 1;
			$local_rights_vars['set_charsets']['write'] = 1;
			$local_rights_vars['set_charsets']['delete'] = 1;
			$local_rights_vars['set_general']['write'] = 1;
			$local_rights_vars['set_general']['delete'] = 1;
		}
		$local_rights_vars['content_pages']['write'] = 1;
		$local_rights_vars['content_pages']['delete'] = 1;
		$local_rights_vars['content_default']['write'] = 1;
		$local_rights_vars['content_default']['delete'] = 1;
		$local_rights_vars['content_plugins']['write'] = 1;
		$local_rights_vars['content_plugins']['delete'] = 1;
		$local_rights_vars['content_media']['write'] = 1;
		$local_rights_vars['content_media']['delete'] = 1;
		$local_rights_vars['content_levels']['write'] = 1;
		$local_rights_vars['content_levels']['delete'] = 1;
		$local_rights_vars['user']['write'] = 1;
		$local_rights_vars['user']['delete'] = 1;
		$local_rights_vars['user_groups']['write'] = 1;
		$local_rights_vars['user_groups']['delete'] = 1;
		$local_rights_vars['user_permissions']['write'] = 1;
		$local_rights_vars['user_permissions']['delete'] = 1;
		$local_rights_vars['user_sessions']['write'] = 1;
		$local_rights_vars['user_sessions']['delete'] = 1;
		$local_rights_vars['tools']['write'] = 1;
		$local_rights_vars['tools']['delete'] = 1;
		
		$groups = explode(",",$groups);
		for($i=0; $i<count($groups); $i++)
		{
			$db->query("SELECT gid FROM ".$tbl_prefix."sys_usergroup WHERE name = '".$groups[$i]."' LIMIT 1");
			while($db->next_record()):
				$groups[$i] = $db->f("gid");
			endwhile;
		}
		
		for($i=0; $i<count($groups); $i++)
		{
			$db->query("SELECT * FROM ".$tbl_prefix."sys_permission WHERE group_id = '".$groups[$i]."' ORDER BY group_id");
			while($db->next_record()):
				if($db->f("page") == "projects") {
					$local_rights_vars['projects']['write'] = $db->f("object_w");
					$local_rights_vars['projects']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "set_lang") {
					$local_rights_vars['set_lang']['write'] = $db->f("object_w");
					$local_rights_vars['set_lang']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "set_tmpl") {
					$local_rights_vars['set_tmpl']['write'] = $db->f("object_w");
					$local_rights_vars['set_tmpl']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "set_plugins") {
					$local_rights_vars['set_plugins']['write'] = $db->f("object_w");
					$local_rights_vars['set_plugins']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "set_charsets") {
					$local_rights_vars['set_charsets']['write'] = $db->f("object_w");
					$local_rights_vars['set_charsets']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "set_general") {
					$local_rights_vars['set_general']['write'] = $db->f("object_w");
					$local_rights_vars['set_general']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "content_pages") {
					$local_rights_vars['content_pages']['write'] = $db->f("object_w");
					$local_rights_vars['content_pages']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "content_default") {
					$local_rights_vars['content_default']['write'] = $db->f("object_w");
					$local_rights_vars['content_default']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "content_plugins") {
					$local_rights_vars['content_plugins']['write'] = $db->f("object_w");
					$local_rights_vars['content_plugins']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "content_media") {
					$local_rights_vars['content_media']['write'] = $db->f("object_w");
					$local_rights_vars['content_media']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "content_levels") {
					$local_rights_vars['content_levels']['write'] = $db->f("object_w");
					$local_rights_vars['content_levels']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "user") {
					$local_rights_vars['user']['write'] = $db->f("object_w");
					$local_rights_vars['user']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "user_groups") {
					$local_rights_vars['user_groups']['write'] = $db->f("object_w");
					$local_rights_vars['user_groups']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "user_permissions") {
					$local_rights_vars['user_permissions']['write'] = $db->f("object_w");
					$local_rights_vars['user_permissions']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "user_sessions") {
					$local_rights_vars['user_sessions']['write'] = $db->f("object_w");
					$local_rights_vars['user_sessions']['delete'] = $db->f("object_d");
				}
				if($db->f("page") == "tools") {
					$local_rights_vars['tools']['write'] = $db->f("object_w");
					$local_rights_vars['tools']['delete'] = $db->f("object_d");
				}
			endwhile;
		}
		
		return($local_rights_vars);
	}
	
	// group comparison
	function compare_groups($content_groups)
	{
		if($content_groups == "") {
			return(1);
		}
		
		$group_found = 0;
		$content_groups = explode(",",$content_groups);
		for($i=0; $i<count($content_groups); $i++)
		{
			if(eregi($content_groups[$i],$_SESSION["usergroups"]) && $group_found == 0) {
				$group_found = 1;
			}
		}
		
		return($group_found);
	}
	
	// rename group when changing group names in Admincenter
	function rename_group($db2,$group_name_old,$group_name_new)
	{
		global $tbl_prefix;
		// rename group in all users
		$i = 0;
		
		$db2->query("SELECT uid, groups FROM ".$tbl_prefix."sys_user");
		while($db2->next_record()):
			if(eregi($group_name_old, $db2->f("groups"))) {
				$found_user[$i] = $db2->f("uid");
				$user_groups_new[$i] = str_replace($group_name_old,$group_name_new,$db2->f("groups"));
				$i += 1;
			}
		endwhile;
		
		for($i=0; $i<count($found_user); $i++) {
			$db2->query("UPDATE ".$tbl_prefix."sys_user SET groups = '".$user_groups_new[$i]."' WHERE uid = '".$found_user[$i]."' LIMIT 1");
		}
		
		// rename group in all explorer entries
		$i = 0;
		
		$db2->query("SELECT eid, groups FROM ".$tbl_prefix."sys_explorer");
		while($db2->next_record()):
			if(eregi($group_name_old, $db2->f("groups"))) {
				$found_user[$i] = $db2->f("eid");
				$user_groups_new[$i] = str_replace($group_name_old,$group_name_new,$db2->f("groups"));
				$i += 1;
			}
		endwhile;
		
		for($i=0; $i<count($found_user); $i++) {
			$db2->query("UPDATE ".$tbl_prefix."sys_explorer SET groups = '".$user_groups_new[$i]."' WHERE eid = '".$found_user[$i]."' LIMIT 1");
		}
		
		// rename group in all websites
		$i = 0;
		
		$db2->query("SELECT cid, user_groups FROM ".$tbl_prefix."sys_config");
		while($db2->next_record()):
			if(eregi($group_name_old, $db2->f("user_groups"))) {
				$found_user[$i] = $db2->f("cid");
				$user_groups_new[$i] = str_replace($group_name_old,$group_name_new,$db2->f("user_groups"));
				$i += 1;
			}
		endwhile;
		
		for($i=0; $i<count($found_user); $i++) {
			$db2->query("UPDATE ".$tbl_prefix."sys_config SET user_groups = '".$user_groups_new[$i]."' WHERE cid = '".$found_user[$i]."' LIMIT 1");
		}
	}
	
	// check existing group when trying to delete a group in Admincenter
	function delete_group($db2,$group_name)
	{
		global $tbl_prefix;
		
		$db2->query("SELECT uid AS id FROM ".$tbl_prefix."sys_user WHERE groups LIKE '%".$group_name."%' "
			."UNION SELECT eid AS id FROM ".$tbl_prefix."sys_explorer WHERE groups LIKE '%".$group_name."%' "
			."UNION SELECT cid AS id FROM ".$tbl_prefix."sys_config WHERE user_groups LIKE '%".$group_name."%'");
		
		if($db2->num_rows() >= 1) {
			return(0);
		} else {
			return(1);
		}
	}
}
?>