<style type="text/css">
.watupromojo-form label {
	min-width: 180px;
	display: block;
	float: left;
}
</style>

<div class="wrap">
	<h1><?php _e('Instamojo Settings', 'watupromojo');?></h1>
	
	<form method="post">
		<div class="inside watupromojo-form">		
			<h2><?php _e('Account Settings', 'watupromojo');?> </h2>
			
			<p><label><?php _e('API Key', 'watupromojo')?></label> <input type="text" name="api_key" value="<?php echo stripslashes(@$options['api_key']);?>" size="40"></p>
			<p><label><?php _e('Auth Token', 'watupromojo')?></label> <input type="text" name="token" value="<?php echo stripslashes(@$options['token']);?>" size="40"></p>
		
			<h2><?php _e('Link Attributes', 'watupromojo');?> </h2>
			<p><?php  _e('You need to create one payment link in your Instamojo account which will serve to handle your WatuPRO quiz and bundle buttons. The link must have two custom fields: item_id, and item_type. You need to enter the <b>field identifiers (not field names!)</b> in this form too.', 'watupromojo');?></p>
			<p><label><?php  _e('Link URL:', 'watupromojo')?></label> <input type="text" name="link" value="<?php echo @$options['link']?>" size="50"></p>		
			<p><label><?php  _e('Item ID field identifier:', 'watupromojo')?></label> <input type="text" name="field_item_id" value="<?php echo @$options['field_item_id']?>"></p>
			<p><label><?php  _e('Item Type field identifier:', 'watupromojo')?></label> <input type="text" name="field_item_type" value="<?php echo @$options['field_item_type']?>"></p>
			<p><label><?php _e('Choose text of the button', 'watupromojo')?></label> <input type="text" name="button_text" value="<?php echo stripslashes(@$options['button_text']);?>"></p>
			
			<p style="color:red;"><b><?php _e('You must enter the following URL in the "Custom Redirection URL" field in the "Advanced Settings" for your payment link in Instamojo:', 'watupromojo')?></b><br>
			<?php _e('Copy this:', 'watupromojo')?> <input type="text" size="60" value="<?php echo site_url('?watupromojo=1');?>" onclick="this.select();" readonly="readonly"></p>
			
			<p><input type="submit" name="ok" value="<?php _e('Save Settings', 'watupromojo')?>"></p>
		</div>
	</form>
</div>