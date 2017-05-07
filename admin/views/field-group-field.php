<?php 

// global
global $post;


// extract args
extract( $args );


// add prefix
$field['prefix'] = "fields_fields[{$field['ID']}]";


// vars
$atts = array(
	'class' => "fields-field-object fields-field-object-{$field['type']}",
	'data-id'	=> $field['ID'],
	'data-key'	=> $field['key'],
	'data-type'	=> $field['type'],
);

$meta = array(
	'ID'			=> $field['ID'],
	'key'			=> $field['key'],
	'parent'		=> $field['parent'],
	'menu_order'	=> $field['menu_order'],
	'save'			=> '',
);


// replace
$atts['class'] = str_replace('_', '-', $atts['class']);

?>
<div <?php echo fields_esc_attr( $atts ); ?>>
	
	<div class="meta">
		<?php foreach( $meta as $k => $v ):
			
			fields_hidden_input(array( 'class' => "input-{$k}", 'name' => "{$field['prefix']}[{$k}]", 'value' => $v ));
				
		endforeach; ?>
	</div>
	
	<div class="handle">
		<ul class="fields-hl fields-tbody">
			<li class="li-field-order">
				<span class="fields-icon fields-icon-order"><?php echo ($i + 1); ?></span>
				<pre class="pre-field-key"><?php echo $field['key']; ?></pre>
			</li>
			<li class="li-field-label">
				<strong>
					<a class="edit-field" title="<?php _e("Edit field",'fields'); ?>" href="#"><?php echo $field['label']; ?></a>
					<?php if( $field['required'] ): ?><span class="fields-required">*</span><?php endif; ?>
				</strong>
				<div class="row-options">
					<a class="edit-field" title="<?php _e("Edit field",'fields'); ?>" href="#"><?php _e("Edit",'fields'); ?></a>
					<a class="duplicate-field" title="<?php _e("Duplicate field",'fields'); ?>" href="#"><?php _e("Duplicate",'fields'); ?></a>
					<a class="move-field" title="<?php _e("Move field to another group",'fields'); ?>" href="#"><?php _e("Move",'fields'); ?></a>
					<a class="delete-field" title="<?php _e("Delete field",'fields'); ?>" href="#"><?php _e("Delete",'fields'); ?></a>
				</div>
			</li>
			<li class="li-field-name"><?php echo $field['name']; ?></li>
			<li class="li-field-type">
				<?php if( fields_field_type_exists($field['type']) ): ?>
					<?php echo fields_get_field_type_label($field['type']); ?>
				<?php else: ?>
					<b><?php _e('Error', 'fields'); ?></b> <?php _e('Field type does not exist', 'fields'); ?>
				<?php endif; ?>
			</li>	
		</ul>
	</div>
	
	<div class="settings">			
		<table class="fields-table">
			<tbody>
				<?php 
		
				// label
				fields_render_field_wrap(array(
					'label'			=> __('Field Label','fields'),
					'instructions'	=> __('This is the name which will appear on the EDIT page','fields'),
					'required'		=> 1,
					'type'			=> 'text',
					'name'			=> 'label',
					'prefix'		=> $field['prefix'],
					'value'			=> $field['label'],
					'class'			=> 'field-label'
				), 'tr');
				
				
				// name
				fields_render_field_wrap(array(
					'label'			=> __('Field Name','fields'),
					'instructions'	=> __('Single word, no spaces. Underscores and dashes allowed','fields'),
					'required'		=> 1,
					'type'			=> 'text',
					'name'			=> 'name',
					'prefix'		=> $field['prefix'],
					'value'			=> $field['name'],
					'class'			=> 'field-name'
				), 'tr');
				
				
				// type
				fields_render_field_wrap(array(
					'label'			=> __('Field Type','fields'),
					'instructions'	=> '',
					'required'		=> 1,
					'type'			=> 'select',
					'name'			=> 'type',
					'prefix'		=> $field['prefix'],
					'value'			=> $field['type'],
					'choices' 		=> fields_get_field_types(),
					'class'			=> 'field-type'
				), 'tr');
				
				
				// instructions
				fields_render_field_wrap(array(
					'label'			=> __('Instructions','fields'),
					'instructions'	=> __('Instructions for authors. Shown when submitting data','fields'),
					'type'			=> 'textarea',
					'name'			=> 'instructions',
					'prefix'		=> $field['prefix'],
					'value'			=> $field['instructions'],
					'rows'			=> 5
				), 'tr');
				
				
				// required
				fields_render_field_wrap(array(
					'label'			=> __('Required?','fields'),
					'instructions'	=> '',
					'type'			=> 'radio',
					'name'			=> 'required',
					'prefix'		=> $field['prefix'],
					'value'			=> $field['required'],
					'choices'		=> array(
						1				=> __("Yes",'fields'),
						0				=> __("No",'fields'),
					),
					'layout'		=> 'horizontal',
					'class'			=> 'field-required'
				), 'tr');
				
				
				// type specific settings
				do_action("fields/render_field_settings/type={$field['type']}", $field);
				
				
				// 3rd party settings
				do_action('fields/render_field_settings', $field);
				
				
				// conditional logic
				fields_get_view('field-group-field-conditional-logic', array( 'field' => $field ));
				
				
				// wrapper
				fields_render_field_wrap(array(
					'label'			=> __('Wrapper Attributes','fields'),
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'width',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['width'],
					'prepend'		=> __('width', 'fields'),
					'append'		=> '%',
					'wrapper'		=> array(
						'data-name' => 'wrapper'
					)
				), 'tr');
				
				fields_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'class',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['class'],
					'prepend'		=> __('class', 'fields'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				fields_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'id',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['id'],
					'prepend'		=> __('id', 'fields'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				?>
				<tr class="fields-field fields-field-save">
					<td class="fields-label"></td>
					<td class="fields-input">
						<ul class="fields-hl">
							<li>
								<a class="edit-field fields-button grey" title="<?php _e("Close Field",'fields'); ?>" href="#"><?php _e("Close Field",'fields'); ?></a>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>
