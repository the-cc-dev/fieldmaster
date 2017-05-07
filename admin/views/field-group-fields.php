<?php 

// vars
// Note: $args is always passed to this view from above
$fields = array();
$layout = false;
$parent = 0;


// use fields if passed in
extract( $args );


// add clone
$fields[] = fields_get_valid_field(array(
	'ID'		=> 'fieldscloneindex',
	'key'		=> 'fieldscloneindex',
	'label'		=> __('New Field','fields'),
	'name'		=> 'new_field',
	'type'		=> 'text',
	'parent'	=> $parent
));


?>
<div class="fields-field-list-wrap">
	
	<ul class="fields-hl fields-thead">
		<li class="li-field-order"><?php _e('Order','fields'); ?></li>
		<li class="li-field-label"><?php _e('Label','fields'); ?></li>
		<li class="li-field-name"><?php _e('Name','fields'); ?></li>
		<li class="li-field-type"><?php _e('Type','fields'); ?></li>
	</ul>
	
	<div class="fields-field-list<?php if( $layout ){ echo " layout-{$layout}"; } ?>">
		
		<?php foreach( $fields as $i => $field ): ?>
			
			<?php fields_get_view('field-group-field', array( 'field' => $field, 'i' => $i )); ?>
			
		<?php endforeach; ?>
		
		<div class="no-fields-message" <?php if(count($fields) > 1){ echo 'style="display:none;"'; } ?>>
			<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'fields'); ?>
		</div>
		
	</div>
	
	<ul class="fields-hl fields-tfoot">
		<li class="comic-sans">
			<i class="fields-icon fields-icon-arrow-combo"></i><?php _e('Drag and drop to reorder','fields'); ?>
		</li>
		<li class="fields-fr">
			<a href="#" class="fields-button blue add-field"><?php _e('+ Add Field','fields'); ?></a>
		</li>
	</ul>

</div>
