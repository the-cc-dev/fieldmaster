<?php

/*
*  Fields API Number Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_number
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_number') ) :

class fields_field_number extends fields_field {
	
	
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
		$this->name = 'number';
		$this->label = __("Number",'fields');
		$this->defaults = array(
			'default_value'	=> '',
			'min'			=> '',
			'max'			=> '',
			'step'			=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
			'readonly'		=> 0,
			'disabled'		=> 0,
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$o = array( 'type', 'id', 'class', 'min', 'max', 'step', 'name', 'value', 'placeholder' );
		$e = '';
		
		
		// step
		if( !$field['step'] ) {
		
			$field['step'] = 'any';
			
		}
		
		
		// prepend
		if( $field['prepend'] !== "" ) {
		
			$field['class'] .= ' fields-is-prepended';
			$e .= '<div class="fields-input-prepend">' . $field['prepend'] . '</div>';
			
		}
		
		
		// append
		if( $field['append'] !== "" ) {
		
			$field['class'] .= ' fields-is-appended';
			$e .= '<div class="fields-input-append">' . $field['append'] . '</div>';
			
		}
		
		
		// populate atts
		$atts = array();
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// special atts
		foreach( array( 'readonly', 'disabled' ) as $k ) {
		
			if( $field[ $k ] ) {
			
				$atts[ $k ] = $k;
				
			}
			
		}
		
		
		// render
		$e .= '<div class="fields-input-wrap">';
		$e .= '<input ' . fields_esc_attr( $atts ) . ' />';
		$e .= '</div>';
		
		
		// return
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
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Default Value','fields'),
			'instructions'	=> __('Appears when creating a new post','fields'),
			'type'			=> 'text',
			'name'			=> 'default_value',
		));
		
		
		// placeholder
		fields_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','fields'),
			'instructions'	=> __('Appears within the input','fields'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// prepend
		fields_render_field_setting( $field, array(
			'label'			=> __('Prepend','fields'),
			'instructions'	=> __('Appears before the input','fields'),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		fields_render_field_setting( $field, array(
			'label'			=> __('Append','fields'),
			'instructions'	=> __('Appears after the input','fields'),
			'type'			=> 'text',
			'name'			=> 'append',
		));
		
		
		// min
		fields_render_field_setting( $field, array(
			'label'			=> __('Minimum Value','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
		));
		
		
		// max
		fields_render_field_setting( $field, array(
			'label'			=> __('Maximum Value','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
		));
		
		
		// max
		fields_render_field_setting( $field, array(
			'label'			=> __('Step Size','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'step',
		));
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// remove ','
		if( fields_str_exists(',', $value) ) {
			
			$value = str_replace(',', '', $value);
			
		}
				
		
		// if value is not numeric...
		if( !is_numeric($value) ) {
			
			// allow blank to be saved
			if( !empty($value) ) {
				
				$valid = __('Value must be a number', 'fields');
				
			}
			
			
			// return early
			return $valid;
			
		}
		
		
		// convert
		$value = floatval($value);
		
		
		// min
		if( is_numeric($field['min']) && $value < floatval($field['min'])) {
			
			$valid = sprintf(__('Value must be equal to or higher than %d', 'fields'), $field['min'] );
			
		}
		
		
		// max
		if( is_numeric($field['max']) && $value > floatval($field['max']) ) {
			
			$valid = sprintf(__('Value must be equal to or lower than %d', 'fields'), $field['max'] );
			
		}
		
		
		// return		
		return $valid;
		
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
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the $post_id of which the value will be saved
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// no formatting needed for empty value
		if( empty($value) ) {
			
			return $value;
			
		}
		
		
		// remove ','
		if( fields_str_exists(',', $value) ) {
			
			$value = str_replace(',', '', $value);
			
		}
		
		
		// return
		return $value;
	}
	
	
}

new fields_field_number();

endif;

?>
