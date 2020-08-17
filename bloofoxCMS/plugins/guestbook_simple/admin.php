<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/guestbook_simple/admin.php -
//
// Copyrights (c) 2008-2012 Alexander Lang, Germany
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

switch($page)
{
	case "del":
		$ac->load_cancel_action($_POST['cancel'],"index.php?mode=plugins&pluginID=1000");
		
		if(isset($_POST['send']) && $sys_group_vars['demo'] == 0) {
			if(CheckInteger($_POST['cid'])) {
				$db->query("DELETE FROM ".$tbl_prefix."plugin_gbook_simple WHERE cid = '".$_POST['cid']."'");
			}
			
			load_url("index.php?mode=plugins&pluginID=1000");
		}
		
		// select record
		if(isset($_POST['cid'])) {
			$_GET['cid'] = $_POST['cid'];
		}
		
		if(!CheckInteger($_GET['cid'])) {
			load_url("index.php?mode=plugins&pluginID=1000");
		}
		
		$db->query("SELECT cid,text FROM ".$tbl_prefix."plugin_gbook_simple WHERE cid = '".$_GET['cid']."' ORDER BY cid LIMIT 1");
		while($db->next_record()):
			$cid = $db->f("cid");
			$text = $db->f("text");
		endwhile;
		
		$tpl->set_var(array(
			"plugins_title"   => "<h1>Plugin: ".$plugin_name."</h1>",
			"plugins_content" => $plugin_content,
			"gb_action"       => "index.php?mode=plugins&pluginID=1000&page=del",
			"gb_question"     => $ac->show_question_message(get_caption('0290','Do you like to delete this entry?')),
			"gb_text"         => $text,
			"gb_cid"          => "<input type='hidden' name='cid' value='".$cid."' />",
			"gb_submit"       => $ac->create_form_button("submit",get_caption('0130','Yes'))." ".$ac->create_form_button("submit",get_caption('0126','Cancel'),"btn","cancel")
			));
		
		// Parse template with variables
		$tpl->parse("plugins_handle", "plugins", true);
	break;
	
	default: // Plugin
		$db->query("SELECT * FROM ".$tbl_prefix."plugin_gbook_simple ORDER BY cid DESC");
		$gb_vars["rows"] = $db->num_rows();
		
		while($db->next_record()):
			$tpl->set_var(array(
				"plugins_title"   => "<h1>Plugin: ".$plugin_name."</h1>",
				"plugins_content" => $plugin_content,
				"gb_str_date"     => get_caption("0510","Date"),
				"gb_val_date"     => date($sys_vars["datetime"],$db->f("timestamp")),
				"gb_str_name"     => get_caption("0110","Name"),
				"gb_val_name"     => $db->f("name"),
				"gb_str_email"    => get_caption("4200","E-Mail"),
				"gb_val_email"    => $db->f("email"),
				"gb_str_ip"       => get_caption(0,"IP"),
				"gb_val_ip"       => $db->f("ip_address"),
				"gb_str_action"   => get_caption("0100","Action"),
				"gb_val_action"   => $ac->create_delete_icon("index.php?mode=plugins&pluginID=1000&page=del&cid=".$db->f("cid"),get_caption('0211','Delete'))
			));
			
			// Parse template with variables
			$tpl->parse("plugins_handle", "plugins", true);
		endwhile;
		
		if($gb_vars["rows"] == 0) {
			$tpl->set_var(array(
				"plugins_title"   => "<h1>Plugin: ".$plugin_name."</h1>",
				"plugins_content" => $plugin_content,
				"gb_str_date"     => get_caption("0510","Date"),
				"gb_val_date"     => "",
				"gb_str_name"     => get_caption("0110","Name"),
				"gb_val_name"     => "",
				"gb_str_email"    => get_caption("4200","E-Mail"),
				"gb_val_email"    => "",
				"gb_str_ip"       => get_caption(0,"IP"),
				"gb_val_ip"       => "",
				"gb_str_action"   => get_caption("0100","Action"),
				"gb_val_action"   => ""
			));
			
			// Parse template with variables
			$tpl->parse("plugins_handle", "plugins", true);
		}
	break;
}
?>