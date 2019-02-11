<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/contact_form_simple/contact.php -
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

// check plugin ID
// Contact must have "1001"
if($login_required == 0 && $sys_explorer_vars['link_plugin'] == 1001) {

	$sys_print_vars['print'] = "";
	$sys_plugin_vars['css'] = "<link rel='stylesheet' type='text/css' href='plugins/contact_form_simple/contact.css' />\n";
	
	// translations
	include("plugins/contact_form_simple/languages/".$sys_lang_vars['language']);

	// set template block
	$tpl->set_block("template_content", "content", "content_handle");
	
	switch($contact_vars['get'])
	{
		case '1': // contact message sent
			$tpl->set_var(array(
				"contact_title"       => "<h1>".$sys_explorer_vars['name']."</h1>",
				"contact_message"     => get_caption('form_070','Thank you for your message! The message was successfully sent.')
				));
		break;
		
		default: // contact form
		$error = "";
		if(isset($_POST['send'])) {
			// Block external postings
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			if(strpos($HTTP_REFERER,$_SERVER['SERVER_NAME']) == 0) {
				load_url("index.php");
			}
			// Validate input fields
			$_POST['name'] = validate_text($_POST['name']);
			$_POST['phone'] = validate_text($_POST['phone']);
			$_POST['message'] = validate_text($_POST['message']);
			$_POST['email'] = validate_text($_POST['email']);
			
			if(!mandatory_field($_POST['name'])) { $error = "<p class='error'>".get_caption('9160','You must enter a name.')."</p>"; }
			if(!email_is_valid($_POST['email']) && $error == "") { $error = "<p class='error'>".get_caption('9130','You must enter a valid e-mail.')."</p>"; }
			if(!mandatory_field($_POST['message']) && $error == "") { $error = "<p class='error'>".get_caption('form_060','You must enter a message.')."</p>"; }
			// Captcha
			if($content->plugin_available(20) == 1) {
				$captcha = new captcha('temp');
				$error = $captcha->check_code_input($error,$_SESSION["contact"]);
			}
			
			if($error == "") {
				$contact_vars['msg'] = "";
				$contact_vars['msg'] .= "Server-URL: ".$sys_config_vars['url']."\n\n";
				$contact_vars['msg'] .= get_caption('0110','Name').": ".$_POST['name']."\n\n";
				$contact_vars['msg'] .= get_caption('form_030','Phone').": ".$_POST['phone']."\n\n";
				$contact_vars['msg'] .= get_caption('form_040','Message').": ".$_POST['message']."\n\n";
				$contact_vars['msg'] .= get_caption('4200','E-Mail').": ".$_POST['email']."\n\n";
				$contact_vars['msg'] .= "Code: ".$_POST['code']."\n\n";
				$contact_vars['msg'] .= get_caption('3410','Version').": ".get_current_version()."\n\n";
				
				// Parameter: recipient, sender, subject, body, mode (1=html,0=plain)
				send_mail($sys_config_vars['mail'],$_POST['email'],get_caption('form_010','Web Form'),$contact_vars['msg'],$sys_setting_vars['html_mails']);
				
				load_url("index.php?page=".$sys_explorer_vars['eid']."&sent=1");
			}
		}
		
		// Captcha
		if($content->plugin_available(20) == 1) {
			$captcha = new captcha('temp');
			$captcha_label = $captcha->get_input_label();
			$captcha_image = $captcha->get_captcha_image('contact');
			$captcha_input = $captcha->get_input_field();
		}
		
		$tpl->set_var(array(
			"contact_title"         => "<h1>".$sys_explorer_vars['name']."</h1>",
			"contact_action"        => $_SERVER['REQUEST_URI'],
			"contact_error"         => $error,
			"contact_name"          => get_caption('0110','Name'),
			"contact_name_input"    => "<input class='text' type='text' name='name' size='30' maxlength='30' value='".$_POST['name']."' />",
			"contact_phone"         => get_caption('form_030','Phone'),
			"contact_phone_input"   => "<input class='text' type='text' name='phone' size='30' maxlength='30' value='".$_POST['phone']."' />",
			"contact_email"         => get_caption('4200','E-Mail'),
			"contact_email_input"   => "<input class='email' type='text' name='email' size='50' maxlength='250' value='".$_POST['email']."' />",
			"contact_message"       => get_caption('form_040','Message'),
			"contact_message_input" => "<textarea class='message' name='message' cols='30' rows='5'>".$_POST['message']."</textarea>",
			"contact_captcha"       => $captcha_label,
			"contact_captcha_image" => $captcha_image,
			"contact_captcha_input" => $captcha_input,
			"contact_submit"        => "<input type='submit' name='send' value='".get_caption('form_050','Submit Message')."' />"
			));
		break;
	}
	
	// parse template
	$tpl->parse("content_handle", "content", true);
}
?>