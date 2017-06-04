<?php

/*
*  FieldMaster oEmbed Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_oembed
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_oembed') ) :

class fieldmaster_field_oembed extends fieldmaster_field {
	
	
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
		$this->name = 'oembed';
		$this->label = __("oEmbed",'fields');
		$this->category = 'content';
		$this->defaults = array(
			'width'		=> '',
			'height'	=> '',
		);
		$this->default_values = array(
			'width' 	=> 640,
			'height'	=> 390
		);

		
		// extra
		add_action('wp_ajax_fields/fields/oembed/search',			array($this, 'ajax_search'));
		add_action('wp_ajax_nopriv_fields/fields/oembed/search',	array($this, 'ajax_search'));
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  wp_oembed_get
	*
	*  description
	*
	*  @type	function
	*  @date	24/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_oembed_get( $url = '', $width = 0, $height = 0 ) {
		
		// vars
		$embed = '';
		$res = array(
			'width'		=> $width,
			'height'	=> $height
		);
		
		
		// get emebed
		$embed = @wp_oembed_get( $url, $res );
		
		
		// return
		return $embed;
	}
	
	
	/*
	*  ajax_search
	*
	*  description
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_search() {
		
   		// options
   		$args = fields_parse_args( $_POST, array(
			's'			=> '',
			'nonce'		=> '',
			'width'		=> 0,
			'height'	=> 0,
		));
		
		
		// width and height
		if( !$args['width'] ) {
		
			$args['width'] = $this->default_values['width'];
			
		}
		
		if( !$args['height'] ) {
		
			$args['height'] = $this->default_values['height'];
			
		}
		
		
		// validate
		if( ! wp_verify_nonce($args['nonce'], 'fields_nonce') ) {
		
			die();
			
		}
		
		
		// get oembed
		echo $this->wp_oembed_get($args['s'], $args['width'], $args['height']);
		
		
		// die
		die();
			
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
		
		// default options
		foreach( $this->default_values as $k => $v ) {
		
			if( empty($field[ $k ]) ) {
			
				$field[ $k ] = $v;
				
			}
			
		}
		
		
		// atts
		$atts = array(
			'class'			=> 'fields-oembed',
			'data-width'	=> $field['width'],
			'data-height'	=> $field['height']
		);
		
		if( $field['value'] ) {
		
			$atts['class'] .= ' has-value';
			
		}
		
?>
<div <?php fields_esc_attr_e($atts) ?>>
	<div class="fields-hidden">
		<input type="hidden" data-name="value-input" name="<?php echo esc_attr($field['name']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
	</div>
	<div class="title fields-soh">
		
		<div class="title-value">
			<h4 data-name="value-title"><?php echo $field['value']; ?></h4>
		</div>
		
		<div class="title-search">
			
			<input data-name="search-input" type="text" placeholder="<?php _e("Enter URL", 'fields'); ?>" autocomplete="off" />
		</div>
		
		<a data-name="clear-button" href="#" class="fields-icon fields-icon-cancel grey fields-soh-target"></a>
		
	</div>
	<div class="canvas">
		
		<div class="canvas-loading">
			<i class="fields-loading"></i>
		</div>
		
		<div class="canvas-error">
			<p><strong><?php _e("Error", 'fields'); ?></strong>. <?php _e("No embed found for the given URL", 'fields'); ?></p>
		</div>
		
		<div class="canvas-media" data-name="value-embed">
			<?php if( !empty( $field['value'] ) ): ?>
				<?php echo $this->wp_oembed_get($field['value'], $field['width'], $field['height']); ?>
			<?php endif; ?>
		</div>
		
		<i class="fields-icon fields-icon-picture hide-if-value"></i>
		
	</div>
	
</div>
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
		
		// width
		fields_render_field_setting( $field, array(
			'label'			=> __('Embed Size','fields'),
			'type'			=> 'text',
			'name'			=> 'width',
			'prepend'		=> __('Width', 'fields'),
			'append'		=> 'px',
			'placeholder'	=> $this->default_values['width']
		));
		
		
		// height
		fields_render_field_setting( $field, array(
			'label'			=> __('Embed Size','fields'),
			'type'			=> 'text',
			'name'			=> 'height',
			'prepend'		=> __('Height', 'fields'),
			'append'		=> 'px',
			'placeholder'	=> $this->default_values['height'],
			'wrapper'		=> array(
				'data-append' => 'width'
			)
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
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
		
		// bail early if no value
		if( empty($value) ) {
			
			return $value;
		
		}
		
		
		// get oembed
		$value = $this->wp_oembed_get($value, $field['width'], $field['height']);
		
		
		// return
		return $value;
		
	}
	
}

new fieldmaster_field_oembed();

endif;

?>
