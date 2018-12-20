<?php
// add your custom shortcode handling here
// don't forget that shortcodes must be declared by calling add_shortcode() in wautprocustom_init()
class WatuPROMojoShortcodes {
	static function button($atts) {
		global $wpdb, $user_ID, $user_email;
		
		$options = get_option('watupromojo_options');		
		 
		 // let's construct the URL
		 $url = $options['link'] . "?embed=form&data_email=" . $user_email . "&data_amount=" . $atts['amount'] 
		 	. "&data_readonly=data_amount&data_" . $options['field_item_id'] . "=" . $atts['item_id'] 
		 	. "&data_" . $options['field_item_type'] . "=" . $atts['item_type'] 
		 	. "&data_hidden=data_" . $options['field_item_id'] . "&data_hidden=data_" . $options['field_item_type'];
		 return '<a href="'.$url.'" class="watupromojo-button">'.stripslashes($options['button_text']).'</a>';	  
	}
}