<?php

/*
*  FieldMaster Widget Form Class
*
*  All the logic for adding fields to widgets
*
*  @class 		fields_form_widget
*  @package		FieldMaster
*  @subpackage	Forms
*/

if( ! class_exists('fields_form_widget') ) :

class fields_form_widget {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// actions
		add_action('admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('in_widget_form', 			array($this, 'edit_widget'), 10, 3);
		
		
		// filters
		add_filter('widget_update_callback', 	array($this, 'widget_update_callback'), 10, 4);
		
		
		// ajax
		add_action('wp_ajax_update-widget', 	array($this, 'ajax_update_widget'), 0, 1);
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_enqueue_scripts() {
		
		// validate screen
		if( fields_is_screen('widgets') || fields_is_screen('customize') ) {
		
			// valid
			
		} else {
			
			return;
			
		}
		
		
		// load fields scripts
		fields_enqueue_scripts();
		
		
		// actions
		add_action('fields/input/admin_footer', array($this, 'admin_footer'), 1);

	}
	
	
	/*
	*  edit_widget
	*
	*  This function will render the fields for a widget form
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	$widget (object)
	*  @param	$return (null)
	*  @param	$instance (object)
	*  @return	$post_id (int)
	*/
	function edit_widget( $widget, $return, $instance ) {
		
		// vars
		$post_id = 0;
		
		
		// get id
		if( $widget->number !== '__i__' ) {
		
			$post_id = "widget_{$widget->id}";
			
		}
		
		
		// get field groups
		$field_groups = fields_get_field_groups(array(
			'widget' => $widget->id_base
		));
		
		
		// render
		if( !empty($field_groups) ) {
			
			// render post data
			fields_form_data(array( 
				'post_id'	=> $post_id, 
				'nonce'		=> 'widget' 
			));
			
			
			foreach( $field_groups as $field_group ) {
				
				$fields = fields_get_fields( $field_group );
				
				fields_render_fields( $post_id, $fields, 'div', 'field' );
				
			}
			
			if( $widget->updated ): ?>
			<script type="text/javascript">
			(function($) {
				
				fields.do_action('append', $('[id^="widget"][id$="<?php echo $widget->id; ?>"]') );
				
			})(jQuery);	
			</script>
			<?php endif;
		
		}
		
	}
	
	
	/*
	*  save_widget
	*
	*  This function will hook in before 'widget_update_callback' and save values to bypass customizer validation issues
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function ajax_update_widget() {
		
		// remove default save filter
		remove_filter('widget_update_callback', array($this, 'widget_update_callback'), 10, 4);
		
		
		// bail early if no nonce
		if( !fields_verify_nonce('widget') ) {
		
			return;
			
		}
		
		
		// vars
		$id = fields_maybe_get($_POST, 'widget-id');
		
	    
	    // save data
	    if( $id && fields_validate_save_post() ) {
	    	
			fields_save_post( "widget_{$id}" );		
		
		}
		
	}

	
	
	/*
	*  widget_update_callback
	*
	*  This function will hook into the widget update filter and save FieldMaster data
	*
	*  @type	function
	*  @date	27/05/2015
	*  @since	5.2.3
	*
	*  @param	$instance (array) widget settings
	*  @param	$new_instance (array) widget settings
	*  @param	$old_instance (array) widget settings
	*  @param	$widget (object) widget info
	*  @return	$instance
	*/
	
	function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {
		
		// bail early if no nonce
		if( !fields_verify_nonce('widget') ) {
		
			return $instance;
			
		}
		
		
	    // save
	    if( fields_validate_save_post() ) {
	    	
			fields_save_post( "widget_{$widget->id}" );		
		
		}
		
		
		// return
		return $instance;
		
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will add some custom HTML to the footer of the edit page
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
?>
<script type="text/javascript">
(function($) {
	
	 fields.add_filter('get_fields', function( $fields ){
	 	
	 	// widgets
	 	$fields = $fields.not('#available-widgets .fields-field');
	 	
	 	
	 	// customizer
	 	$fields = $fields.not('.widget-tpl .fields-field');
	 	
	 	
	 	// return
	 	return $fields;
	 	
    });
	
	
	$('#widgets-right').on('click', '.widget-control-save', function( e ){
		
		// vars
		var $form = $(this).closest('form');
		
		
		// bail early if not active
		if( !fields.validation.active ) {
		
			return true;
			
		}
		
		
		// ignore validation (only ignore once)
		if( fields.validation.ignore ) {
		
			fields.validation.ignore = 0;
			return true;
			
		}
		
		
		// bail early if this form does not contain FieldMaster data
		if( !$form.find('#fields-form-data').exists() ) {
		
			return true;
		
		}

		
		// stop WP JS validation
		e.stopImmediatePropagation();
		
		
		// store submit trigger so it will be clicked if validation is passed
		fields.validation.$trigger = $(this);
		
		
		// run validation
		fields.validation.fetch( $form );
		
		
		// stop all other click events on this input
		return false;
		
	});
	
	
	$(document).on('click', '.widget-top', function(){
		
		var $el = $(this).parent().children('.widget-inside');
		
		setTimeout(function(){
			
			fields.get_fields('', $el).each(function(){
				
				fields.do_action('show_field', $(this));	
				
			});
			
		}, 250);
				
	});
	
	$(document).on('widget-added', function( e, $widget ){
		
		fields.do_action('append', $widget );
		
	});
	
	$(document).on('widget-saved widget-updated', function( e, $widget ){
		
		// unlock form
		fields.validation.toggle( $widget, 'unlock' );
		
		
		// submit
		fields.do_action('submit', $widget );
		
	});
	
	<?php if( fields_is_screen('customize') ): ?>
	
	// customizer saves widget on any input change, so unload is not needed
	fields.unload.active = 0;

	<?php endif; ?>
		
})(jQuery);	
</script>
<?php
		
	}
	
}

new fields_form_widget();

endif;

?>
