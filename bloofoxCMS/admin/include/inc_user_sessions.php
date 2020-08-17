<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - admin/inc_user_sessions.php -
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

switch($action)
{
	case 'kill':
		// Create sessions view
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0 && $sys_rights['user_sessions']['delete'] == 1) {
			CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
			$db->query("UPDATE ".$tbl_prefix."sys_session SET type = 1 WHERE sid = '".$_POST['sid']."' LIMIT 1");
			load_url("index.php?mode=user&page=sessions");
		}
		
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=user&page=sessions");
		
		// select record
		if(isset($_POST['sid'])) {
			$_GET['sid'] = $_POST['sid'];
		}
		$db->query("SELECT * FROM ".$tbl_prefix."sys_session WHERE sid = '".$_GET['sid']."' ORDER BY sid LIMIT 1");
		while($db->next_record()):
			$sid = $db->f("sid");
		endwhile;
		
		// Set variables
		$tpl->set_var(array(
			"user_title"       => "<h2>".get_caption('4000','Security')." / ".get_caption('4560','Kill Session')."</h2>",
			"user_action"      => "index.php?mode=user&page=sessions&action=kill",
			"user_question"    => $ac->show_question_message(get_caption('4610','Do you like to quit this session?')),
			"user_name"        => "<p class='bold'>".$sid."</p>",
			"user_sid"         => "<input type='hidden' name='sid' value='".$sid."' />",
			"user_button_send" => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
	break;

	default:
		// Create sessions view
		
		// Headline
		$user_sessions .= "<table class='list'><tr class='bg_color3'>"
			."<td><p class='bold'>".get_caption('0380','Type')."</p></td>"
			."<td><p class='bold'>".get_caption('4060','Username')."</p></td>"
			."<td><p class='bold'>".get_caption('0510','Date')."</p></td>"
			."<td><p class='bold'>".get_caption('0520','Time')."</p></td>"
			."<td><p class='bold'>".get_caption('0250','Status')."</p></td>"
			."<td><p class='bold'>".get_caption('0530','Timestamp')."</p></td>"
			."<td><p class='bold'>".get_caption('4600','IP')."</p></td>"
			."<td><p class='bold'>".get_caption('4570','MySession')."</p></td>"
			."<td><p class='bold'>".get_caption('0100','Action')."</p></td>"
			."</tr>";
		
		// settings
		$start = $_GET['start'];
		$limit = $s_limit; // default 20
		
		// count records
		$db->query("SELECT sid FROM ".$tbl_prefix."sys_session");
		$total = $db->num_rows();
		
		// create page handling
		$page_html_code = $ac->create_page_handling($total,$limit,"index.php?mode=user&page=sessions&start=",$start);
		$start = $ac->get_page_start($start,$limit);
		
		// Lines
		$db->query("SELECT * FROM ".$tbl_prefix."sys_user INNER JOIN ".$tbl_prefix."sys_session ON ".$tbl_prefix."sys_session.uid = ".$tbl_prefix."sys_user.uid ORDER BY ".$tbl_prefix."sys_session.sid DESC LIMIT ".$start.",".$limit);
		while($db->next_record()):
			$own_session = 0;
			if($_SESSION['uid'] == $db->f("uid") && $db->f("type") == 0 && session_id() == $db->f("session_id")) {
				$own_session = 1;
			}
			if($db->f("type") == 0) {
				$type = get_caption('0360','Open');
			} else {
				$type = get_caption('0370','Closed');
			}
			if($db->f("status") == 0) {
				$status = get_caption('4580','Login Failed');
			} else {
				$status = get_caption('4590','Login OK');
			}
			if($sys_group_vars['demo'] == 0) {
				$show_ip = $db->f("ip");
			} else {
				$show_ip = "********";
			}
			
			$user_sessions .= "<tr class='bg_color2'>"
				."<td>".$type."</td>"
				."<td>".$db->f("username")."</td>"
				."<td>".$db->f("date")."</td>"
				."<td>".$db->f("time")."</td>"
				."<td>".$status."</td>"
				."<td>".$db->f("timestamp")."</td>"
				."<td>".$show_ip."</td>"
				."<td>".translate_yesno($own_session,get_caption('0130','Yes'),get_caption('0140','No'))."</td>"
				."<td>";
			if($db->f("type") == 0) {
				$user_sessions .= $ac->create_logout_icon("index.php?mode=user&page=sessions&action=kill&sid=".$db->f("sid"),get_caption('4560','Quit Session'));
			}	
			$user_sessions .= "</td>"
				."</tr>";
		endwhile;
		$user_sessions .= "</table>";
		
		// Set variables
		$tpl->set_var(array(
			"user_title"       => "<h2>".get_caption('4000','Security')." / ".get_caption('4040','Sessions')."</h2>",
			"confirm_message"  => $ac->show_ok_message(GetConfirmMessage()),
			"user_sessions"    => $user_sessions,
			"page_handling"    => $page_html_code
			));
	break;
}
?>