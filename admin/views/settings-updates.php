<?php 

// extract
extract($args);


// vars
$active = $license ? true : false;
$nonce = $active ? 'deactivate_pro_licence' : 'activate_pro_licence';
$input = $active ? 'password' : 'text';
$button = $active ? __('Deactivate License', 'fields') : __('Activate License', 'fields');
$readonly = $active ? 1 : 0;

?>
<div class="wrap fields-settings-wrap">
	
	<h2><?php _e('Updates', 'fields'); ?></h2>
	
	<div class="fields-box">
		<div class="title">
			<h3><?php echo fields_get_setting('name'); ?> <?php _e('License','fields') ?></h3>
		</div>
		<div class="inner">
			<p><?php _e("To unlock updates, please enter your license key below. If you don't have a licence key, please see",'fields'); ?> <a href="http://www.advancedcustomfields.com/pro" target="_blank"><?php _e('details & pricing', 'fields'); ?></a></p>
			<form action="" method="post">
			<div class="fields-hidden">
				<input type="hidden" name="_fieldsnonce" value="<?php echo wp_create_nonce( $nonce ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label for="fields-field-fields_pro_licence"><?php _e('License Key', 'fields'); ?></label>
                    	</th>
						<td>
							<?php 
							
							// render field
							fields_render_field(array(
								'type'		=> $input,
								'name'		=> 'fields_pro_licence',
								'value'		=> str_repeat('*', strlen($license)),
								'readonly'	=> $readonly
							));
							
							?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" value="<?php echo $button; ?>" class="fields-button blue">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
            
		</div>
		
	</div>
	
	<div class="fields-box">
		<div class="title">
			<h3><?php _e('Update Information', 'fields'); ?></h3>
		</div>
		<div class="inner">
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Current Version', 'fields'); ?></label>
                    	</th>
						<td>
							<?php echo $current_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Latest Version', 'fields'); ?></label>
                    	</th>
						<td>
							<?php echo $remote_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Update Available', 'fields'); ?></label>
                    	</th>
						<td>
							<?php if( $update_available ): ?>
								
								<span style="margin-right: 5px;"><?php _e('Yes', 'fields'); ?></span>
								
								<?php if( $active ): ?>
									<a class="fields-button blue" href="<?php echo admin_url('plugins.php?s=Advanced+Custom+Fields+Pro'); ?>"><?php _e('Update Plugin', 'fields'); ?></a>
								<?php else: ?>
									<a class="fields-button" disabled="disabled" href="#"><?php _e('Please enter your license key above to unlock updates', 'fields'); ?></a>
								<?php endif; ?>
								
							<?php else: ?>
								
								<span style="margin-right: 5px;"><?php _e('No', 'fields'); ?></span>
								<a class="fields-button" href="<?php echo add_query_arg('force-check', 1); ?>"><?php _e('Check Again', 'fields'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php if( $changelog ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Changelog', 'fields'); ?></label>
                    	</th>
						<td>
							<?php echo $changelog; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php if( $upgrade_notice ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Upgrade Notice', 'fields'); ?></label>
                    	</th>
						<td>
							<?php echo $upgrade_notice; ?>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			</form>
            
		</div>
		
		
	</div>
	
</div>
<style type="text/css">
	#fields_pro_licence {
		width: 75%;
	}
</style>
