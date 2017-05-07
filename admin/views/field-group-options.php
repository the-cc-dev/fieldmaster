<?php 

// active
fields_render_field_wrap(array(
	'label'			=> __('Status','fields'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'active',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['active'],
	'choices' 		=> array(
		1				=>	__("Active",'fields'),
		0				=>	__("Disabled",'fields'),
	)
));


// style
fields_render_field_wrap(array(
	'label'			=> __('Style','fields'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'style',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['style'],
	'choices' 		=> array(
		'default'			=>	__("Standard (WP metabox)",'fields'),
		'seamless'			=>	__("Seamless (no metabox)",'fields'),
	)
));


// position
fields_render_field_wrap(array(
	'label'			=> __('Position','fields'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'position',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['position'],
	'choices' 		=> array(
		'fields_after_title'	=> __("High (after title)",'fields'),
		'normal'			=> __("Normal (after content)",'fields'),
		'side' 				=> __("Side",'fields'),
	),
	'default_value'	=> 'normal'
));


// label_placement
fields_render_field_wrap(array(
	'label'			=> __('Label placement','fields'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'label_placement',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['label_placement'],
	'choices' 		=> array(
		'top'			=>	__("Top aligned",'fields'),
		'left'			=>	__("Left Aligned",'fields'),
	)
));


// instruction_placement
fields_render_field_wrap(array(
	'label'			=> __('Instruction placement','fields'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'instruction_placement',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['instruction_placement'],
	'choices' 		=> array(
		'label'		=>	__("Below labels",'fields'),
		'field'		=>	__("Below fields",'fields'),
	)
));


// menu_order
fields_render_field_wrap(array(
	'label'			=> __('Order No.','fields'),
	'instructions'	=> __('Field groups with a lower order will appear first','fields'),
	'type'			=> 'number',
	'name'			=> 'menu_order',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['menu_order'],
));


// description
fields_render_field_wrap(array(
	'label'			=> __('Description','fields'),
	'instructions'	=> __('Shown in field group list','fields'),
	'type'			=> 'text',
	'name'			=> 'description',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['description'],
));


// hide on screen
fields_render_field_wrap(array(
	'label'			=> __('Hide on screen','fields'),
	'instructions'	=> __('<b>Select</b> items to <b>hide</b> them from the edit screen.','fields') . '<br /><br />' . __("If multiple field groups appear on an edit screen, the first field group's options will be used (the one with the lowest order number)",'fields'),
	'type'			=> 'checkbox',
	'name'			=> 'hide_on_screen',
	'prefix'		=> 'fields_field_group',
	'value'			=> $field_group['hide_on_screen'],
	'toggle'		=> true,
	'choices' => array(
		'permalink'			=>	__("Permalink", 'fields'),
		'the_content'		=>	__("Content Editor",'fields'),
		'excerpt'			=>	__("Excerpt", 'fields'),
		'custom_fields'		=>	__("Fields API", 'fields'),
		'discussion'		=>	__("Discussion", 'fields'),
		'comments'			=>	__("Comments", 'fields'),
		'revisions'			=>	__("Revisions", 'fields'),
		'slug'				=>	__("Slug", 'fields'),
		'author'			=>	__("Author", 'fields'),
		'format'			=>	__("Format", 'fields'),
		'page_attributes'	=>	__("Page Attributes", 'fields'),
		'featured_image'	=>	__("Featured Image", 'fields'),
		'categories'		=>	__("Categories", 'fields'),
		'tags'				=>	__("Tags", 'fields'),
		'send-trackbacks'	=>	__("Send Trackbacks", 'fields'),
	)
));


// 3rd party settings
do_action('fields/render_field_group_settings', $field_group);
		
?>
<div class="fields-hidden">
	<input type="hidden" name="fields_field_group[key]" value="<?php echo $field_group['key']; ?>" />
</div>
<script type="text/javascript">
if( typeof fields !== 'undefined' ) {
		
	fields.postbox.render({
		'id': 'fields-field-group-options',
		'label': 'left'
	});	

}
</script>
