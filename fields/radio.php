<?php

/*
*  Fields API Radio Button Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_radio
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_radio') ) :

class fields_field_radio extends fields_field {
	
	
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
		$this->name = 'radio';
		$this->label = __("Radio Button",'fields');
		$this->category = 'choice';
		$this->defaults = array(
			'layout'			=> 'vertical',
			'choices'			=> array(),
			'default_value'		=> '',
			'other_choice'		=> 0,
			'save_other_choice'	=> 0,
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
		
		// vars
		$i = 0;
		$checked = false;
		
		
		// class
		$field['class'] .= ' fields-radio-list';
		$field['class'] .= ($field['layout'] == 'horizontal') ? ' fields-hl' : ' fields-bl';

		
		// e
		$e = '<ul ' . fields_esc_attr(array( 'class' => $field['class'] )) . '>';
		
		
		// other choice
		if( $field['other_choice'] ) {
			
			// vars
			$input = array(
				'type'		=> 'text',
				'name'		=> $field['name'],
				'value'		=> '',
				'disabled'	=> 'disabled'
			);
			
			
			// select other choice if value is not a valid choice
			if( !isset($field['choices'][ $field['value'] ]) ) {
				
				unset($input['disabled']);
				$input['value'] = $field['value'];
				$field['value'] = 'other';
				
			}
			
			
			$field['choices']['other'] = '</label><input type="text" ' . fields_esc_attr($input) . ' /><label>';
		
		}
		
		
		// require choices
		if( !empty($field['choices']) ) {
			
			// select first choice if value is not a valid choice
			if( !isset($field['choices'][ $field['value'] ]) ) {
				
				$field['value'] = key($field['choices']);
				
			}
			
			
			// foreach choices
			foreach( $field['choices'] as $value => $label ) {
				
				// increase counter
				$i++;
				
				
				// vars
				$atts = array(
					'type'	=> 'radio',
					'id'	=> $field['id'], 
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				if( strval($value) === strval($field['value']) ) {
					
					$atts['checked'] = 'checked';
					$checked = true;
					
				}
				
				if( isset($field['disabled']) && fields_in_array($value, $field['disabled']) ) {
				
					$atts['disabled'] = 'disabled';
					
				}
				
				
				// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
				if( $i > 1 ) {
				
					$atts['id'] .= '-' . $value;
					
				}
				
				$e .= '<li><label><input ' . fields_esc_attr( $atts ) . '/>' . $label . '</label></li>';
			}
		
		}
		

		$e .= '</ul>';
		
		echo $e;
		
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
		
		
		// choices
		fields_render_field_setting( $field, array(
			'label'			=> __('Choices','fields'),
			'instructions'	=> __('Enter each choice on a new line.','fields') . '<br /><br />' . __('For more control, you may specify both a value and label like this:','fields'). '<br /><br />' . __('red : Red','fields'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));
		
		
		// other_choice
		fields_render_field_setting( $field, array(
			'label'			=> __('Other','fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'other_choice',
			'message'		=> __("Add 'other' choice to allow for custom values", 'fields')
		));
		
		
		// save_other_choice
		fields_render_field_setting( $field, array(
			'label'			=> __('Save Other','fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'save_other_choice',
			'message'		=> __("Save 'other' values to the field's choices", 'fields')
		));
		
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Default Value','fields'),
			'instructions'	=> __('Appears when creating a new post','fields'),
			'type'			=> 'text',
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
	*  @todo	Fix bug where $field was found via json and has no ID
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// save_other_choice
		if( $field['save_other_choice'] ) {
			
			// value isn't in choices yet
			if( !isset($field['choices'][ $value ]) ) {
				
				// get ID if local
				if( !$field['ID'] ) {
					
					$field = fields_get_field( $field['key'], true );
					
				}
				
				
				// bail early if no ID
				if( !$field['ID'] ) {
					
					return $value;
					
				}
				
				
				// update $field
				$field['choices'][ $value ] = $value;
				
				
				// save
				fields_update_field( $field );
				
			}
			
		}		
		
		
		// return
		return $value;
	}
	
	
	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	5.2.9
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded from
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in te database
	*/
	
	function load_value( $value, $post_id, $field ) {
		
		// must be single value
		if( is_array($value) ) {
			
			$value = array_pop($value);
			
		}
		
		
		// return
		return $value;
		
	}
	
}

new fields_field_radio();

endif;

?>
