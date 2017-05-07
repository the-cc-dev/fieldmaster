<?php

/*
*  Fields API Email Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_email
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_email') ) :

class fields_field_email extends fields_field {
	
	
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
		$this->name = 'email';
		$this->label = __("Email",'fields');
		$this->defaults = array(
			'default_value'	=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
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

	}	
	
}

new fields_field_email();

endif;

?>