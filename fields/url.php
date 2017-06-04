<?php

/*
*  FieldMaster URL Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_url
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_url') ) :

class fieldmaster_field_url extends fieldmaster_field {
	
	
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
		$this->name = 'url';
		$this->label = __("Url",'fields');
		$this->defaults = array(
			'default_value'	=> '',
			'placeholder'	=> '',
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
		
		
		// populate atts
		$atts = array();
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// special atts
		foreach( array( 'readonly', 'disabled' ) as $k ) {
		
			if( !empty($field[ $k ]) ) {
			
				$atts[ $k ] = $k;
				
			}
			
		}
		
		
		// render
		$e .= '<div class="fields-input-wrap fields-url">';
		$e .= '<i class="fields-icon fields-icon-globe small"></i><input ' . fields_esc_attr( $atts ) . ' />';
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
		
		// bail early if empty		
		if( empty($value) ) {
				
			return $valid;
			
		}
		
		
		if( strpos($value, '://') !== false ) {
			
			// url
			
		} elseif( strpos($value, '//') === 0 ) {
			
			// protocol relative url
			
		} else {
			
			$valid = __('Value must be a valid URL', 'fields');
			
		}
		
		
		// return		
		return $valid;
		
	}
	
}

new fieldmaster_field_url();

endif;

?>
