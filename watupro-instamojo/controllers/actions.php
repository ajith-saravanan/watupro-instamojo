<?php
// add your custom actions here. Don't forget to declare them by calling add_action() in watuprocustom_init()
class WatuPROMojoActions {
	// verify instamojo payment on template_redirect
	// if error wp_die
	// if success insert the payment and redirect
	static function verify_payment() {
		global $wpdb, $user_ID;
		if(empty($_GET['watupromojo'])) return true;
		
		$options = get_option('watupromojo_options');
		
		require WATUPROMOJO_PATH."/lib/instamojo/instamojo.php";
		
		$api = new Instamojo($options['api_key'], $options['token']);		
		//print_r($_GET);
		try {
        $response = $api->paymentDetail($_GET['payment_id']);
        // print_r($response);
        if($response['status'] != 'Credit') wp_die("Payment status not verified");
        
        if(empty($response['custom_fields'])) wp_die("Custom fields missing!");
       
        // now figure out the item we pay for and its price
        $item_id = $item_type = '';
        foreach($response['custom_fields'] as $key => $field) {
        	  if($field['label'] == 'item_id') $item_id = $field['value'];
        	  if($field['label'] == 'item_type') $item_type = $field['value'];
        }
        
        if(empty($item_id)) wp_die("Item ID is missing");
		  if(empty($item_type)) wp_die("Item type is missing");
		  
		  // select item to check amount etc		
			if($item_type == 'bundle') {
				$bundle = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watupro_bundles WHERE ID=%d", $item_id));
				$fee = $bundle->price;
				$target_url = $bundle->redirect_url;
			} 
			else {
				$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watupro_master WHERE ID=%d", $item_id));
				$fee = $exam->fee;
				if($exam->published_odd_url) $target_url = $exam->published_odd_url;
				else {
					$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} 
					WHERE (post_content LIKE '%[watupro ".$exam->ID."]%' OR post_content LIKE '%[wtpuc quiz_id=".$exam->ID."') 
					AND post_status='publish' AND post_title!=''
					ORDER BY post_date DESC");
					$target_url = get_permalink($post_id);
				}
			}
			
			// used coupon?
			$coupon_code = get_user_meta($user_ID, 'watupro_coupon', true);
			 
			if(!empty($coupon_code)) {
				$coupon = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watupro_coupons WHERE code=%s", trim($coupon_code)));
				if(!empty($coupon->ID) and ($coupon->num_uses == 0 or ($coupon->num_uses - $coupon->times_used) > 0)) {
					// apply to the price
					$fee = $fee - round($fee *  ($coupon->discount/ 100), 2);
				}	
			}	// end applying coupon	
			
			if($fee > $response['amount']) wp_die("Paid less: the cost is $fee while the amount paid is $response[amount]");
			
			$currency = get_option('watupro_currency');
			
			if($currency != $response['currency']) wp_die("Wrong currency. Paid in $response[currency] while the requested currency is $currency."); 
			
			// make sure payment ID is unique and insert the payment
        $payment_id_exists = $wpdb->get_var($wpdb->prepare("SELECT paycode FROM {$wpdb->prefix}watupro_payments 
		   WHERE paycode=%s", $_GET['payment_id']));		   		   
			if(empty($payment_id_exists)) {
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_payments SET 
				exam_id=%d, user_id=%s, date=CURDATE(), amount=%s, status='completed', paycode=%s, 
				method='paypal', bundle_id=%d", 
				@$exam->ID, $user_ID, $fee, $_GET['payment_id'], @$bundle->ID));
			}
			
			// figure out where to redirect				
			watupro_redirect($target_url);        
	   }
	   catch (Exception $e) {
	        wp_die('Error: ' . $e->getMessage());
	   }
	}
}