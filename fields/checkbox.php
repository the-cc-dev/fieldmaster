<?php

/*
*  Fields API Checkbox Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_checkbox
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_checkbox') ) :

class fields_field_checkbox extends fields_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->name = 'checkbox';
		$this->label = __("Checkbox",'fields');
		$this->category = 'choice';
		$this->defaults = array(
			'layout'		=> 'vertical',
			'choices'		=> array(),
			'default_value'	=> '',
			'toggle'		=> 0
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// decode value (convert to array)
		$field['value'] = fields_get_array($field['value'], false);
		
		
		// hiden input
		fields_hidden_input(array(
			'type'	=> 'hidden',
			'name'	=> $field['name'],
		));
		
		
		// vars
		$i = 0;
		$li = '';
		$all_checked = true;
		
		
		// checkbox saves an array
		$field['name'] .= '[]';
		
		
		// foreach choices
		if( !empty($field['choices']) ) {
			
			foreach( $field['choices'] as $value => $label ) {
				
				// increase counter
				$i++;
				
				
				// vars
				$atts = array(
					'type'	=> 'checkbox',
					'id'	=> $field['id'], 
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				// is choice selected?
				if( in_array($value, $field['value']) ) {
					
					$atts['checked'] = 'checked';
					
				} else {
					
					$all_checked = false;
					
				}
				
				
				if( isset($field['disabled']) && fields_in_array($value, $field['disabled']) ) {
				
					$atts['disabled'] = 'disabled';
					
				}
				
				
				// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
				if( $i > 1 ) {
				
					$atts['id'] .= '-' . $value;
					
				}
				
				
				// append HTML
				$li .= '<li><label><input ' . fields_esc_attr( $atts ) . '/>' . $label . '</label></li>';
				
			}
			
			
			// toggle all
			if( $field['toggle'] ) {
				
				// vars
				$label = __("Toggle All", 'fields');
				$atts = array(
					'type'	=> 'checkbox',
					'class'	=> 'fields-checkbox-toggle'
				);
				
				
				// custom label
				if( is_string($field['toggle']) ) {
					
					$label = $field['toggle'];
					
				}
				
				
				// checked
				if( $all_checked ) {
					
					$atts['checked'] = 'checked';
					
				}
				
				
				// append HTML
				$li = '<li><label><input ' . fields_esc_attr( $atts ) . '/>' . $label . '</label></li>' . $li;
					
			}
		
		}
		
		
		// class
		$field['class'] .= ' fields-checkbox-list';
		$field['class'] .= ($field['layout'] == 'horizontal') ? ' fields-hl' : ' fields-bl';

		
		// return
		echo '<ul ' . fields_esc_attr(array( 'class' => $field['class'] )) . '>' . $li . '</ul>';
		
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// encode choices (convert from array)
		$field['choices'] = fields_encode_choices($field['choices']);
		$field['default_value'] = fields_encode_choices($field['default_value']);
				
		
		// choices
		fields_render_field_setting( $field, array(
			'label'			=> __('Choices','fields'),
			'instructions'	=> __('Enter each choice on a new line.','fields') . '<br /><br />' . __('For more control, you may specify both a value and label like this:','fields'). '<br /><br />' . __('red : Red','fields'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));	
		
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Default Value','fields'),
			'instructions'	=> __('Enter each default value on a new line','fields'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));
		
		
		// layout
		fields_render_field_setting( $field, array(
			'label'			=> __('Layout','fields'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal', 
			'choices'		=> array(
				'vertical'		=> __("Vertical",'fields'), 
				'horizontal'	=> __("Horizontal",'fields')
			)
		));
		
		
		// layout
		fields_render_field_setting( $field, array(
			'label'			=> __('Toggle','fields'),
			'instructions'	=> __('Prepend an extra checkbox to toggle all choices','fields'),
			'type'			=> 'radio',
			'name'			=> 'toggle',
			'layout'		=> 'horizontal', 
			'choices'		=> array(
				1				=> __("Yes",'fields'),
				0				=> __("No",'fields'),
			)
		));
		
		
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = fields)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field ) {
		
		// decode choices (convert to array)
		$field['choices'] = fields_decode_choices($field['choices']);
		$field['default_value'] = fields_decode_choices($field['default_value']);
		
		
		// return
		return $field;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// validate
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// array
		if( is_array($value) ) {
			
			// save value as strings, so we can clearly search for them in SQL LIKE statements
			$value = array_map('strval', $value);
			
		}
		
		
		// return
		return $value;
	}
	
}

new fields_field_checkbox();

endif;

?>
