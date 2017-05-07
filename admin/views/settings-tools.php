<?php 

// vars
$field = array(
	'label'		=> __('Select Field Groups', 'fields'),
	'type'		=> 'checkbox',
	'name'		=> 'fields_export_keys',
	'prefix'	=> false,
	'value'		=> false,
	'toggle'	=> true,
	'choices'	=> array(),
);

$field_groups = fields_get_field_groups();


// populate choices
if( $field_groups ) {
	
	foreach( $field_groups as $field_group ) {
		
		$field['choices'][ $field_group['key'] ] = $field_group['title'];
		
	}
	
}

?>
<div class="wrap fields-settings-wrap">
	
	<h2><?php _e('Tools', 'fields'); ?></h2>
	
	<div class="fields-box" id="fields-export-field-groups">
		<div class="title">
			<h3><?php _e('Export Field Groups', 'fields'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the field groups you would like to export and then select your export method. Use the download button to export to a .json file which you can then import to another Fields API installation. Use the generate button to export to PHP code which you can place in your theme.', 'fields'); ?></p>
			
			<form method="post" action="">
			<div class="fields-hidden">
				<input type="hidden" name="_fieldsnonce" value="<?php echo wp_create_nonce( 'export' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
	                <?php fields_render_field_wrap( $field, 'tr' ); ?>
					<tr>
						<th></th>
						<td>
							<input type="submit" name="download" class="fields-button blue" value="<?php _e('Download export file', 'fields'); ?>" />
							<input type="submit" name="generate" class="fields-button blue" value="<?php _e('Generate export code', 'fields'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
            
		</div>
	</div>

	
	<div class="fields-box">
		<div class="title">
			<h3><?php _e('Import Field Groups', 'fields'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the Fields API JSON file you would like to import. When you click the import button below, Fields API will import the field groups.', 'fields'); ?></p>
			
			<form method="post" action="" enctype="multipart/form-data">
			<div class="fields-hidden">
				<input type="hidden" name="_fieldsnonce" value="<?php echo wp_create_nonce( 'import' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Select File', 'fields'); ?></label>
                    	</th>
						<td>
							<input type="file" name="fields_import_file">
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" class="fields-button blue" value="<?php _e('Import', 'fields'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			
		</div>
		
		
	</div>
	
</div>
