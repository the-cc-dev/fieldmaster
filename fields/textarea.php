<?php

/*
*  Fields API Text Area Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_textarea
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_textarea') ) :

class fields_field_textarea extends fields_field {
	
	
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
		$this->name = 'textarea';
		$this->label = __("Text Area",'fields');
		$this->defaults = array(
			'default_value'	=> '',
			'new_lines'		=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'readonly'		=> 0,
			'disabled'		=> 0,
			'rows'			=> ''
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
		$o = array( 'id', 'class', 'name', 'placeholder', 'rows' );
		$s = array( 'readonly', 'disabled' );
		$e = '';
		
		
		// maxlength
		if( $field['maxlength'] !== '' ) {
		
			$o[] = 'maxlength';
			
		}
		
		
		// rows
		if( empty($field['rows']) ) {
		
			$field['rows'] = 8;
			
		}
		
		
		// populate atts
		$atts = array();
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// special atts
		foreach( $s as $k ) {
		
			if( $field[ $k ] ) {
			
				$atts[ $k ] = $k;
				
			}
			
		}
		

		$e .= '<textarea ' . fields_esc_attr( $atts ) . ' >';
		$e .= esc_textarea( $field['value'] );
		$e .= '</textarea>';
		
		
		// return
		echo $e;
		
	}
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field_settings( $field ) {
		
		// Fields API4 migration
		if( empty($field['ID']) ) {
			
			$field['new_lines'] = 'wpautop';
			
		}
		
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Default Value','fields'),
			'instructions'	=> __('Appears when creating a new post','fields'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));
		
		
		// placeholder
		fields_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','fields'),
			'instructions'	=> __('Appears within the input','fields'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// maxlength
		fields_render_field_setting( $field, array(
			'label'			=> __('Character Limit','fields'),
			'instructions'	=> __('Leave blank for no limit','fields'),
			'type'			=> 'number',
			'name'			=> 'maxlength',
		));
		
		
		// rows
		fields_render_field_setting( $field, array(
			'label'			=> __('Rows','fields'),
			'instructions'	=> __('Sets the textarea height','fields'),
			'type'			=> 'number',
			'name'			=> 'rows',
			'placeholder'	=> 8
		));
		
		
		// formatting
		fields_render_field_setting( $field, array(
			'label'			=> __('New Lines','fields'),
			'instructions'	=> __('Controls how new lines are rendered','fields'),
			'type'			=> 'select',
			'name'			=> 'new_lines',
			'choices'		=> array(
				'wpautop'		=> __("Automatically add paragraphs",'fields'),
				'br'			=> __("Automatically add &lt;br&gt;",'fields'),
				''				=> __("No Formatting",'fields')
			)
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value or not for template
		if( empty($value) || !is_string($value) ) {
			
			return $value;
		
		}
				
		
		// new lines
		if( $field['new_lines'] == 'wpautop' ) {
			
			$value = wpautop($value);
			
		} elseif( $field['new_lines'] == 'br' ) {
			
			$value = nl2br($value);
			
		}
		
		
		// return
		return $value;
	}
	
}

new fields_field_textarea();

endif;

?>