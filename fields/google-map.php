<?php

/*
*  Fields API Google Map Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_google_map
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_google_map') ) :

class fields_field_google_map extends fields_field {
	
	
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
		$this->name = 'google_map';
		$this->label = __("Google Map",'fields');
		$this->category = 'jquery';
		$this->defaults = array(
			'height'		=> '',
			'center_lat'	=> '',
			'center_lng'	=> '',
			'zoom'			=> ''
		);
		$this->default_values = array(
			'height'		=> '400',
			'center_lat'	=> '-37.81411',
			'center_lng'	=> '144.96328',
			'zoom'			=> '14'
		);
		$this->l10n = array(
			'locating'			=> __("Locating",'fields'),
			'browser_support'	=> __("Sorry, this browser does not support geolocation",'fields'),
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
		
		// validate value
		if( empty($field['value']) ) {
			
			$field['value'] = array();
			
		}
		
		
		// value
		$field['value'] = fields_parse_args($field['value'], array(
			'address'	=> '',
			'lat'		=> '',
			'lng'		=> ''
		));
		
		
		// default options
		foreach( $this->default_values as $k => $v ) {
		
			if( empty($field[ $k ]) ) {
			
				$field[ $k ] = $v;
				
			}
				
		}
		
		
		// vars
		$atts = array(
			'id'			=> $field['id'],
			'class'			=> "fields-google-map {$field['class']}",
			'data-id'		=> $field['id'] . '-' . uniqid(), 
			'data-lat'		=> $field['center_lat'],
			'data-lng'		=> $field['center_lng'],
			'data-zoom'		=> $field['zoom'],
		);
		
		
		// has value
		if( $field['value']['address'] ) {
		
			$atts['class'] .= ' -value';
			
		}
		
?>
<div <?php fields_esc_attr_e($atts); ?>>
	
	<div class="fields-hidden">
		<?php foreach( $field['value'] as $k => $v ): ?>
			<input type="hidden" class="input-<?php echo $k; ?>" name="<?php echo esc_attr($field['name']); ?>[<?php echo $k; ?>]" value="<?php echo esc_attr( $v ); ?>" />
		<?php endforeach; ?>
	</div>
	
	<div class="title fields-soh">
		
		<div class="actions fields-soh-target">
			<a href="#" data-name="search" class="fields-icon fields-icon-search grey" title="<?php _e("Search", 'fields'); ?>"></a>
			<a href="#" data-name="clear" class="fields-icon fields-icon-cancel grey" title="<?php _e("Clear location", 'fields'); ?>"></a>
			<a href="#" data-name="locate" class="fields-icon fields-icon-location grey" title="<?php _e("Find current location", 'fields'); ?>"></a>
		</div>
		
		<input class="search" type="text" placeholder="<?php _e("Search for address...",'fields'); ?>" value="<?php echo $field['value']['address']; ?>" />
		<i class="fields-loading"></i>
				
	</div>
	
	<div class="canvas" style="height: <?php echo $field['height']; ?>px"></div>
	
</div>
<?php
		
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
		
		// center_lat
		fields_render_field_setting( $field, array(
			'label'			=> __('Center','fields'),
			'instructions'	=> __('Center the initial map','fields'),
			'type'			=> 'text',
			'name'			=> 'center_lat',
			'prepend'		=> 'lat',
			'placeholder'	=> $this->default_values['center_lat']
		));
		
		
		// center_lng
		fields_render_field_setting( $field, array(
			'label'			=> __('Center','fields'),
			'instructions'	=> __('Center the initial map','fields'),
			'type'			=> 'text',
			'name'			=> 'center_lng',
			'prepend'		=> 'lng',
			'placeholder'	=> $this->default_values['center_lng'],
			'wrapper'		=> array(
				'data-append' => 'center_lat'
			)
		));
		
		
		// zoom
		fields_render_field_setting( $field, array(
			'label'			=> __('Zoom','fields'),
			'instructions'	=> __('Set the initial zoom level','fields'),
			'type'			=> 'text',
			'name'			=> 'zoom',
			'placeholder'	=> $this->default_values['zoom']
		));
		
		
		// allow_null
		fields_render_field_setting( $field, array(
			'label'			=> __('Height','fields'),
			'instructions'	=> __('Customise the map height','fields'),
			'type'			=> 'text',
			'name'			=> 'height',
			'append'		=> 'px',
			'placeholder'	=> $this->default_values['height']
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
		
		// bail early if not required
		if( ! $field['required'] ) {
			
			return $valid;
			
		}
		
		
		if( empty($value) || empty($value['lat']) || empty($value['lng']) ) {
			
			return false;
			
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
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
	
		if( empty($value) || empty($value['lat']) || empty($value['lng']) ) {
			
			return false;
			
		}
		
		
		// return
		return $value;
	}
}

new fields_field_google_map();

endif;

?>
