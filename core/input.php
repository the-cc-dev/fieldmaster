<?php 

class fields_input {
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		add_action('fields/save_post', 							array($this, 'save_post'), 10, 1);
		add_action('fields/input/admin_enqueue_scripts', 			array($this, 'admin_enqueue_scripts'), 10, 0);
		add_action('fields/input/admin_footer', 					array($this, 'admin_footer'), 10, 0);
		
		
		// ajax
		add_action( 'wp_ajax_fields/validate_save_post',			array($this, 'ajax_validate_save_post') );
		add_action( 'wp_ajax_nopriv_fields/validate_save_post',	array($this, 'ajax_validate_save_post') );
	}
	
	
	/*
	*  save_post
	*
	*  This function will save the $_POST data
	*
	*  @type	function
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_post( $post_id = 0 ) {
		
		// save $_POST data
		foreach( $_POST['fields'] as $k => $v ) {
			
			// get field
			$field = fields_get_field( $k );
			
			
			// update field
			if( $field ) {
				
				fields_update_value( $v, $post_id, $field );
				
			}
			
		}
	
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This function will enqueue all the required scripts / styles for FieldMaster
	*
	*  @type	action (fields/input/admin_enqueue_scripts)
	*  @date	6/10/13
	*  @since	5.0.0
	*
	*  @param	n/a	
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {

		// scripts
		wp_enqueue_script('fields-input');
		
		
		// styles
		wp_enqueue_style('fields-input');
		
	}
	

	/*
	*  admin_footer
	*
	*  description
	*
	*  @type	function
	*  @date	7/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_footer() {
		
		// vars
		$args = fields_get_setting('form_data');
		
		
		// global
		global $wp_version;
		
		
		// options
		$o = array(
			'post_id'		=> $args['post_id'],
			'nonce'			=> wp_create_nonce( 'fields_nonce' ),
			'admin_url'		=> admin_url(),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'ajax'			=> $args['ajax'],
			'validation'	=> $args['validation'],
			'wp_version'	=> $wp_version
		);
		
		
		// l10n
		$l10n = apply_filters( 'fields/input/admin_l10n', array(
			'unload'				=> __('The changes you made will be lost if you navigate away from this page','fields'),
			'expand_details' 		=> __('Expand Details','fields'),
			'collapse_details' 		=> __('Collapse Details','fields'),
			'validation_successful'	=> __('Validation successful', 'fields'),
			'validation_failed'		=> __('Validation failed', 'fields'),
			'validation_failed_1'	=> __('1 field requires attention', 'fields'),
			'validation_failed_2'	=> __('%d fields require attention', 'fields'),
			'restricted'			=> __('Restricted','fields')
		));
		
		
?>
<script type="text/javascript">
/* <![CDATA[ */
if( typeof fields !== 'undefined' ) {

	fields.o = <?php echo json_encode($o); ?>;
	fields.l10n = <?php echo json_encode($l10n); ?>;
	<?php do_action('fields/input/admin_footer_js'); ?>
	
	fields.do_action('prepare');
	
}
/* ]]> */
</script>
<?php
		
	}
	
	
	/*
	*  ajax_validate_save_post
	*
	*  This function will validate the $_POST data via AJAX
	*
	*  @type	function
	*  @date	27/10/2014
	*  @since	5.0.9
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function ajax_validate_save_post() {
		
		// bail early if _fieldsnonce is missing
		if( !isset($_POST['_fieldsnonce']) ) {
			
			wp_send_json_error();
			
		}
		
		
		// vars
		$json = array(
			'valid'		=> 1,
			'errors'	=> 0
		);
		
		
		// success
		if( fields_validate_save_post() ) {
			
			wp_send_json_success($json);
			
		}
		
		
		// update vars
		$json['valid'] = 0;
		$json['errors'] = fields_get_validation_errors();
		
		
		// return
		wp_send_json_success($json);
		
	}
	
}


// initialize
new fields_input();


/*
*  listener
*
*  This class will call all the neccessary actions during the page load for fields input to function
*
*  @type	class
*  @date	7/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

class fields_input_listener {
	
	function __construct() {
		
		// enqueue scripts
		do_action('fields/input/admin_enqueue_scripts');
		
		
		// vars
		$admin_head = 'admin_head';
		$admin_footer = 'admin_footer';
		
		
		// global
		global $pagenow;
		
		
		// determine action hooks
		if( $pagenow == 'customize.php' ) {
			
			$admin_head = 'customize_controls_print_scripts';
			$admin_footer = 'customize_controls_print_footer_scripts';
			
		} elseif( $pagenow == 'wp-login.php' ) { 
		
			$admin_head = 'login_head';
			$admin_footer = 'login_footer';
			
		} elseif( !is_admin() ) {
			
			$admin_head = 'wp_head';
			$admin_footer = 'wp_footer';
			
		}
		
		
		// add actions
		add_action($admin_head, 	array( $this, 'admin_head'), 20 );
		add_action($admin_footer, 	array( $this, 'admin_footer'), 20 );
		
	}
	
	function admin_head() {
		
		do_action('fields/input/admin_head');
	}
	
	function admin_footer() {
		
		do_action('fields/input/admin_footer');
	}
	
}


/*
*  fields_admin_init
*
*  This function is used to setup all actions / functionality for an admin page which will contain FieldMaster inputs
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function fields_enqueue_scripts() {
	
	// bail early if fields has already loaded
	if( fields_get_setting('enqueue_scripts') ) {
	
		return;
		
	}
	
	
	// update setting
	fields_update_setting('enqueue_scripts', 1);
	
	
	// add actions
	new fields_input_listener();
}


/*
*  fields_enqueue_uploader
*
*  This function will render a WP WYSIWYG and enqueue media
*
*  @type	function
*  @date	27/10/2014
*  @since	5.0.9
*
*  @param	n/a
*  @return	n/a
*/

function fields_enqueue_uploader() {
	
	// bail early if doing ajax
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		
		return;
		
	}
	
	
	// bail early if fields has already loaded
	if( fields_get_setting('enqueue_uploader') ) {
	
		return;
		
	}
	
	
	// update setting
	fields_update_setting('enqueue_uploader', 1);
	
	
	// enqueue media if user can upload
	if( current_user_can( 'upload_files' ) ) {
		
		wp_enqueue_media();
		
	}
	
	
	// create dummy editor
	?><div class="fields-hidden"><?php wp_editor( '', 'fields_content' ); ?></div><?php
	
}


/*
*  fields_form_data
*
*  description
*
*  @type	function
*  @date	15/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fields_form_data( $args = array() ) {
	
	// make sure scripts and styles have been included
	// case: front end bbPress edit user
	fields_enqueue_scripts();
	
	
	// defaults
	$args = fields_parse_args($args, array(
		'post_id'		=> 0,		// ID of current post
		'nonce'			=> 'post',	// nonce used for $_POST validation
		'validation'	=> 1,		// runs AJAX validation
		'ajax'			=> 0,		// fetches new field groups via AJAX
	));
	
	
	// save form_data for later actions
	fields_update_setting('form_data', $args);
	
	
	// enqueue uploader if page allows AJAX fields to appear
	if( $args['ajax'] ) {
		
		add_action('admin_footer', 'fields_enqueue_uploader', 1);
		
	}
	
	?>
	<div id="fields-form-data" class="fields-hidden">
		<input type="hidden" name="_fieldsnonce" value="<?php echo wp_create_nonce( $args['nonce'] ); ?>" />
		<input type="hidden" name="_fieldschanged" value="0" />
		<?php do_action('fields/input/form_data', $args); ?>
	</div>
	<?php
}


/*
*  fields_save_post
*
*  description
*
*  @type	function
*  @date	8/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fields_save_post( $post_id = 0 ) {
	
	// bail early if no fields values
	if( empty($_POST['fields']) ) {
		
		return false;
		
	}
	
	
	// hook for 3rd party customization
	do_action('fields/save_post', $post_id);
	
	
	// return
	return true;

}


/*
*  fields_validate_save_post
*
*  This function is run to validate post data
*
*  @type	function
*  @date	25/11/2013
*  @since	5.0.0
*
*  @param	$show_errors (boolean) if true, errors will be shown via a wo_die screen
*  @return	(boolean)
*/

function fields_validate_save_post( $show_errors = false ) {
	
	// validate required fields
	if( !empty($_POST['fields']) ) {
		
		$keys = array_keys($_POST['fields']);
		
		// loop through and save $_POST data
		foreach( $keys as $key ) {
			
			// get field
			$field = fields_get_field( $key );
			
			
			// validate
			fields_validate_value( $_POST['fields'][ $key ], $field, "fields[{$key}]" );
			
		}
		// foreach($fields as $key => $value)
	}
	// if($fields)
	
	
	// hook for 3rd party customization
	do_action('fields/validate_save_post');
	
	
	// check errors
	if( $errors = fields_get_validation_errors() ) {
		
		if( $show_errors ) {
			
			$message = '<h2>Validation failed</h2><ul>';
			
			foreach( $errors as $error ) {
				
				$message .= '<li>' . $error['message'] . '</li>';
				
			}
			
			$message .= '</ul>';
			
			wp_die( $message, 'Validation failed' );
			
		}
		
		return false;
		
	}
	
	
	// return
	return true;
}


/*
*  fields_validate_value
*
*  This function will validate a value for a field
*
*  @type	function
*  @date	27/10/2014
*  @since	5.0.9
*
*  @param	$value (mixed)
*  @param	$field (array)
*  @param	$input (string) name attribute of DOM elmenet
*  @return	(boolean)
*/

function fields_validate_value( $value, $field, $input ) {
	
	// vars
	$valid = true;
	$message = sprintf( __( '%s value is required', 'fields' ), $field['label'] );
	
	
	// valid
	if( $field['required'] ) {
		
		// valid is set to false if the value is empty, but allow 0 as a valid value
		if( empty($value) && !is_numeric($value) ) {
			
			$valid = false;
			
		}
		
	}
	
	
	// filter for 3rd party customization
	$valid = apply_filters( "fields/validate_value", $valid, $value, $field, $input );
	$valid = apply_filters( "fields/validate_value/type={$field['type']}", $valid, $value, $field, $input );
	$valid = apply_filters( "fields/validate_value/name={$field['name']}", $valid, $value, $field, $input );
	$valid = apply_filters( "fields/validate_value/key={$field['key']}", $valid, $value, $field, $input );
	
	
	// allow $valid to be a custom error message
	if( !empty($valid) && is_string($valid) ) {
		
		$message = $valid;
		$valid = false;
		
	}
	
	
	if( !$valid ) {
		
		fields_add_validation_error( $input, $message );
		return false;
		
	}
	
	
	// return
	return true;
	
}


/*
*  fields_add_validation_error
*
*  This function will add an error message for a field
*
*  @type	function
*  @date	25/11/2013
*  @since	5.0.0
*
*  @param	$input (string) name attribute of DOM elmenet
*  @param	$message (string) error message
*  @return	$post_id (int)
*/

function fields_add_validation_error( $input, $message = '' ) {
	
	// instantiate array if empty
	if( empty($GLOBALS['fields_validation_errors']) ) {
		
		$GLOBALS['fields_validation_errors'] = array();
		
	}
	
	
	// add to array
	$GLOBALS['fields_validation_errors'][] = array(
		'input'		=> $input,
		'message'	=> $message
	);
	
}


/*
*  fields_add_validation_error
*
*  This function will return any validation errors
*
*  @type	function
*  @date	25/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array|boolean)
*/

function fields_get_validation_errors() {
	
	if( empty($GLOBALS['fields_validation_errors']) ) {
		
		return false;
		
	}
	
	return $GLOBALS['fields_validation_errors'];
	
}

?>
