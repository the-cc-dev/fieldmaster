<?php

/*
*  Fields API Message Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_message
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_message') ) :

class fields_field_message extends fields_field {
	
	
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
		$this->name = 'message';
		$this->label = __("Message",'fields');
		$this->category = 'layout';
		$this->defaults = array(
			'value'			=> false, // prevents fields_render_fields() from attempting to load value
			'message'		=> '',
			'esc_html'		=> 0,
			'new_lines'		=> 'wpautop',
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
		$m = $field['message'];
		
		
		// wptexturize (improves "quotes")
		$m = wptexturize( $m );
		
		
		// esc_html
		if( $field['esc_html'] ) {
			
			$m = esc_html( $m );
			
		}
		
		
		// new lines
		if( $field['new_lines'] == 'wpautop' ) {
			
			$m = wpautop($m);
			
		} elseif( $field['new_lines'] == 'br' ) {
			
			$m = nl2br($m);
			
		}
		
		
		// return
		echo $m;
		
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
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Message','fields'),
			'instructions'	=> '',
			'type'			=> 'textarea',
			'name'			=> 'message',
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
		
		
		// HTML
		fields_render_field_setting( $field, array(
			'label'			=> __('Escape HTML','fields'),
			'instructions'	=> __('Allow HTML markup to display as visible text instead of rendering','fields'),
			'type'			=> 'radio',
			'name'			=> 'esc_html',
			'choices'		=> array(
				1				=> __("Yes",'fields'),
				0				=> __("No",'fields'),
			),
			'layout'	=>	'horizontal',
		));
		
	}
	
}

new fields_field_message();

endif;

?>
