<?php

/*
*  Fields API Tab Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_tab
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_tab') ) :

class fields_field_tab extends fields_field {
	
	
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
		$this->name = 'tab';
		$this->label = __("Tab",'fields');
		$this->category = 'layout';
		$this->defaults = array(
			'value'		=> false, // prevents fields_render_fields() from attempting to load value
			'placement'	=> 'top',
			'endpoint'	=> 0 // added in 5.2.8
		);
		
		
		// do not delete!
    	parent::__construct();
	}
	
	
	/*
	*  prepare_field
	*
	*  description
	*
	*  @type	function
	*  @date	9/07/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
/*
	function prepare_field( $field ) {
		
		// append class
		if( $field['endpoint'] ) {
			
			$field['wrapper']['class'] .= ' fields-field-tab-endpoint';
			
		}
		
		
		// return
		return $field;
		
	}
*/
	
	
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
		$atts = array(
			'class'				=> 'fields-tab',
			'data-placement'	=> $field['placement'],
			'data-endpoint'		=> $field['endpoint']
		);
		
		?>
		<div <?php fields_esc_attr_e( $atts ); ?>><?php echo $field['label']; ?></div>
		<?php
		
		
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
		
		// message
		$message = '';
		$message .= '<span class="fields-error-message"><p>' . __("The tab field will display incorrectly when added to a Table style repeater field or flexible content field layout", 'fields') . '</p></span>';
		$message .= '<p>' . __( 'Use "Tab Fields" to better organize your edit screen by grouping fields together.', 'fields') . '</p>';
		$message .= '<p>' . __( 'All fields following this "tab field" (or until another "tab field" is defined) will be grouped together using this field\'s label as the tab heading.','fields') . '</p>';
		
		// default_value
		fields_render_field_setting( $field, array(
			'label'			=> __('Instructions','fields'),
			'instructions'	=> '',
			'type'			=> 'message',
			'message'		=> $message,
			'new_lines'		=> ''
		));
		
		
		// preview_size
		fields_render_field_setting( $field, array(
			'label'			=> __('Placement','fields'),
			'type'			=> 'select',
			'name'			=> 'placement',
			'choices' 		=> array(
				'top'			=>	__("Top aligned",'fields'),
				'left'			=>	__("Left Aligned",'fields'),
			)
		));
		
		
		// endpoint
		fields_render_field_setting( $field, array(
			'label'			=> __('End-point','fields'),
			'instructions'	=> __('Use this field as an end-point and start a new group of tabs','fields'),
			'type'			=> 'radio',
			'name'			=> 'endpoint',
			'choices'		=> array(
				1				=> __("Yes",'fields'),
				0				=> __("No",'fields'),
			),
			'layout'	=>	'horizontal',
		));
				
	}
	
}

new fields_field_tab();

endif;

?>
