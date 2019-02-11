<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/guestbook_simple/gbook.php -
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

// check plugin ID
// guestbook must have "1000"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1000) {

	require_once(SYS_WORK_DIR.SYS_FOLDER."/class_admincenter.php");
	$ac = new admincenter();

	$sys_plugin_vars['css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"plugins/guestbook_simple/gbook.css\" />\n";
	
	// set template var
	$tpl->set_block("template_content", "content", "content_handle");

	// translations
	include("plugins/guestbook_simple/languages/".$sys_lang_vars['language']);
	
	$sys_print_vars['print'] = "";
	$curr_time = time();
	$db2 = new DB_Tpl();
	
	// set template block
	$error = "";
	if(isset($_POST['send'])) {
		// Block external postings
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		if(strpos($HTTP_REFERER,$_SERVER['SERVER_NAME']) == 0) {
			load_url("index.php");
		}
		// Validate input fields
		$_POST['name'] = validate_text($_POST['name']);
		$_POST['message'] = validate_textbox($_POST['message']);
		$_POST['email'] = validate_text($_POST['email']);
		$_POST['homepage'] = validate_text($_POST['homepage']);
		if($_POST['homepage'] != "") {
			if(strpos("http://",$_POST['homepage']) == 0) {
				$_POST['homepage'] = "http://".$_POST['homepage'];
			}
		}
		
		if(!mandatory_field($_POST['name']) && $error == "") { $error = $ac->show_error_message(get_caption('9160','You must enter a name.')); }
		if(!email_is_valid($_POST['email']) && $error == "") { $error = $ac->show_error_message(get_caption('9130','You must enter a valid e-mail.')); }
		if(!mandatory_field($_POST['message']) && $error == "") { $error = $ac->show_error_message(get_caption('gb_ErrorComment','You must enter a comment.')); }
		
		// Captcha
		if($content->plugin_available(20) == 1) {
			$captcha = new captcha('temp');
			$error = $captcha->check_code_input($error,$_SESSION["gbook"]);
		}
		
		if($error == "") {
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$timestamp = time();
			$db2->query("INSERT INTO ".$tbl_prefix."plugin_gbook_simple VALUES('','".$_GET['page']."','".$_POST['name']."','".$_POST['email']."','".$_POST['homepage']."','".$_POST['code']."','".$timestamp."','".$_POST['message']."','".$ip_address."')");
			load_url($_SERVER['REQUEST_URI']."#entries");
		}
	}
	
	//number of records per page
	$limit = 10;
	$start = $_GET['start'];
		
	$gb['start'] = $start * $limit - $limit;
	if($gb['start'] < 0) { $gb['start'] = 0; }
		
	$db2->query("SELECT cid FROM ".$tbl_prefix."plugin_gbook_simple ORDER BY cid DESC");
	$no_of_records = $db2->num_rows();
		
	$factor = $no_of_records / $limit;
	$factor = ceil($factor);
		
	if($factor >= 1) {
		$gb['pages'] = "<span class='bold'>".get_caption('2020','Pages').":</span> ";
		for($x=1; $x<=$factor; $x++) {
			if($start == $x) {
				$gb['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x)."'><span class='bold'>".$x."</span></a> ";
				if($x - 1 >= 1) {
					$gb['prev'] = "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x-1)."'>".get_caption('0300','Previous')."</a> ";
				}
				if($x + 1 <= $factor) {
					$gb['next'] = "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x+1)."'>".get_caption('0310','Next')."</a> ";
				}
			} else {
				$gb['pages'] .= "<a href='".create_url($sys_explorer_vars['eid'],$sys_explorer_vars['name'],$sys_config_vars['mod_rewrite'],'','',$x)."'>".$x."</a> ";
			}
		}
	}
	
	// create output
	$book = new DB_Tpl();
	$book->query("SELECT * FROM ".$tbl_prefix."plugin_gbook_simple ORDER BY cid DESC LIMIT ".$gb['start'].",".$limit);
	$no_of_records -= $gb['start'];
	
	$gb['output'] = "";
	while($book->next_record()){
		$gb['timestamp'] = $book->f("timestamp");
		$gb['name'] = $book->f("name");
		$gb['comment'] = $book->f("text");
		if($book->f("homepage") != "") {
			$gb['name'] = "<a href='".$book->f("homepage")."' title='".get_caption(0,'Homepage')."' target='_new'>".$book->f("name")."</a>";
		}
				
		$temp_gb['output'] = "
		<div class='guestbook'>
		<p>
			<label>#".$no_of_records." <strong>".$gb['name']."</strong> (".date($sys_lang_vars['datetime'],$gb['timestamp']).")</label><br />
			<label>
			".$gb['comment']."
			</label>
		</p>
		</div>\n";
	
		$gb['output'] = $gb['output'].$temp_gb['output'];
		$no_of_records -= 1;
	}
	
	// Captcha
	if($content->plugin_available(20) == 1) {
		$captcha = new captcha('temp');
		$captcha_label = $captcha->get_input_label();
		$captcha_image = $captcha->get_captcha_image('gbook');
		$captcha_input = $captcha->get_input_field();
	}
	
	$tpl->set_var(array(
		"gbook_title"         => "<h1>".$sys_explorer_vars['name']."</h1>",
		"gbook_action"        => str_replace("&","&amp;",$_SERVER['REQUEST_URI']),
		"gbook_sign"          => get_caption('gb_SignGuestbook'),
		"gbook_prev"          => $gb['prev'],
		"gbook_next"		  => $gb['next'],
		"gbook_pages"	      => $gb['pages'],
		"gbook_content"		  => $gb['output'],
		"gbook_error"         => $error,
		"gbook_name"          => get_caption('0110','Name'),
		"gbook_name_input"    => "<input class='text' type='text' name='name' size='30' maxlength='30' value='".$_POST['name']."' />",
		"gbook_email"         => get_caption('4200','E-Mail'),
		"gbook_email_input"   => "<input class='text' type='text' name='email' size='30' maxlength='250' value='".$_POST['email']."' />",
		"gbook_hp"            => get_caption(0,'Homepage'),
		"gbook_hp_input"      => "<input class='text' type='text' name='homepage' size='30' maxlength='250' value='".$_POST['homepage']."' />",
		"gbook_message"       => get_caption('gb_Comment','Comment'),
		"gbook_message_input" => "<textarea class='message' name='message' cols='40' rows='5'>".html_2_text($_POST['message'])."</textarea>",
		"gbook_captcha"       => $captcha_label,
		"gbook_captcha_image" => $captcha_image,
		"gbook_captcha_input" => $captcha_input,
		"gbook_submit"        => $ac->create_form_button("submit",get_caption('gb_SignGuestbook'))
		));

	// parse template
	$tpl->parse("content_handle", "content", true);
}	

?>