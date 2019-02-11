<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - plugins/module_captcha/class_captcha.php -
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

// translations
include("plugins/module_captcha/languages/".$sys_lang_vars['language']);

class captcha {

	// Init variables
	var $session_var = "";
	var $rand_string = "";
	var $img_width = 160; // pixel
	var $img_height = 60; // pixel
	
	// Constructor
	function captcha($var)
	{
		$this->session_var = $var;
		$this->create();
	}
	
	// Create random string
	function create()
	{
		mt_srand((double)microtime()*1000000);
		$signs = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789!?@";
		$rand_string = "";
		while(strlen($rand_string) < 6)
		{
			$rand_string .= substr($signs,(rand()%(strlen($signs))),1);
		}
		
		$this->rand_string = $rand_string;
		$_SESSION[$this->session_var] = $this->rand_string;
	}
	
	// Clear session variable
	function clear($var)
	{
		unset($_SESSION[$var]);
	}
	
	// Create Image
	function image()
	{
		header ("Content-type:image/jpeg");
		header ("Cache-control: no-cache, no-store");
		
		// create new empty image
		$new_image = @ImageCreate($this->img_width,$this->img_height);
		
		// load font type
		$font = imageloadfont('captcha.gdf'); //font type dimurph
		$background_color = ImageColorAllocate ($new_image, 255, 255, 255);

		// set captcha code
		$text_color = ImageColorAllocate ($new_image, 0, 0, 0);
		
		// random height of signs
		$sign1 = rand(5,25);
		$sign2 = rand(5,25);
		$sign3 = rand(5,25);
		$sign4 = rand(5,25);
		$sign5 = rand(5,25);
		$sign6 = rand(5,25);
		ImageString ($new_image, $font, 20, $sign1, substr($_SESSION[$_GET['var']],0,1), $text_color);
		ImageString ($new_image, $font, 40, $sign2, substr($_SESSION[$_GET['var']],1,1), $text_color);
		ImageString ($new_image, $font, 60, $sign3, substr($_SESSION[$_GET['var']],2,1), $text_color);
		ImageString ($new_image, $font, 80, $sign4, substr($_SESSION[$_GET['var']],3,1), $text_color);
		ImageString ($new_image, $font, 100, $sign5, substr($_SESSION[$_GET['var']],4,1), $text_color);
		ImageString ($new_image, $font, 120, $sign6, substr($_SESSION[$_GET['var']],5,1), $text_color);
		ImageJPEG ($new_image);

		imagedestroy ($new_image);
	}
	
	// Get Image Width for HTML
	function get_image_width()
	{
		return($this->img_width);
	}
	
	// Get Image Height for HTML
	function get_image_height()
	{
		return($this->img_height);
	}
	
	// Public: check code after input
	function check_code_input($error,$session_code)
	{
		$_POST['code'] = validate_text($_POST['code']);
		if(($session_code != $_POST['code'] || !mandatory_field($_POST['code'])) && $error == "") { 
			$error = "<p class='error'>".get_caption('captcha020','Please enter a valid code.')."</p>";
		}
		
		return($error);
	}
	
	// Public: create input field for code
	function get_input_field()
	{
		$input = "<input type='text' name='code' size='20' />";
		
		return($input);
	}
	
	// Public: create text & image for input help
	function get_input_label()
	{
		$input = get_caption("captcha010","Please enter the following code: <img src='templates/admincenter/images/icon-set16/info.png' border='0' width='16' height='16' alt='Case sensitive, no spaces' title='Case sensitive, no spaces' />");
	
		return($input);
	}
	
	// Public: create text & image for input help
	function get_captcha_image($var)
	{
		$image = "<img class='captcha' src='plugins/module_captcha/captcha.php?var=".$var."' border='0' alt='Code' width='".$this->get_image_width()."' height='".$this->get_image_height()."' />";
		
		return($image);
	}
}
?>