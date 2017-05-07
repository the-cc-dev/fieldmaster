<?php

/*
*  Fields API Password Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_password
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_password') ) :

class fields_field_password extends fields_field {
	
	
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
		$this->name = 'password';
		$this->label = __("Password",'fields');
		$this->defaults = array(
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
		$o = array( 'type', 'id', 'class', 'name', 'value', 'placeholder' );
		$e = '';
		
		
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
	}
	
}

new fields_field_password();

endif;

?>
