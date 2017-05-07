<?php 

class fields_settings_tools {
	
	var $view = 'settings-tools',
		$data = array();
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
	
		// actions
		add_action('admin_menu', array($this, 'admin_menu'));
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will add the Fields API menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// bail early if no show_admin
		if( !fields_get_setting('show_admin') ) {
			
			return;
			
		}
		
		
		// add page
		$page = add_submenu_page('edit.php?post_type=fields-field-group', __('Tools','fields'), __('Tools','fields'), fields_get_setting('capability'),'fields-settings-tools', array($this,'html') );
		
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		
	}
	
	
	/*
	*  load
	*
	*  This function will look at the $_POST data and run any functions if needed
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load() {
		
		// all export pages should not load local fields
		fields_disable_local();
		
		
		// run import / export
		if( fields_verify_nonce('import') ) {
			
			$this->import();
		
		} elseif( fields_verify_nonce('export') ) {
			
			if( isset($_POST['generate']) ) {
				
				$this->generate();
			
			} else {
				
				$this->export();
			
			}
		
		}
		
		
		// load fields scripts
		fields_enqueue_scripts();
		
	}
	
	
	/*
	*  html
	*
	*  This function will render the view
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html() {
		
		// load view
		fields_get_view($this->view, $this->data);
		
	}
	
	
	/*
	*  export
	*
	*  This function will export field groups to a .json file
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function export() {
		
		// vars
		$json = $this->get_json();
		
		
		// validate
		if( $json === false ) {
			
			fields_add_admin_notice( __("No field groups selected", 'fields') , 'error');
			return;
		
		}
		
		
		// set headers
		$file_name = 'fields-export-' . date('Y-m-d') . '.json';
		
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( "Content-Type: application/json; charset=utf-8" );
		
		echo fields_json_encode( $json );
		die;
		
	}
	
	
	/*
	*  import
	*
	*  This function will import a .json file of field groups
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function import() {
		
		// validate
		if( empty($_FILES['fields_import_file']) ) {
			
			fields_add_admin_notice( __("No file selected", 'fields') , 'error');
			return;
		
		}
		
		
		// vars
		$file = $_FILES['fields_import_file'];
		
		
		// validate error
		if( $file['error'] ) {
			
			fields_add_admin_notice(__('Error uploading file. Please try again', 'fields'), 'error');
			return;
		
		}
		
		
		// validate type
		if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
		
			fields_add_admin_notice(__('Incorrect file type', 'fields'), 'error');
			return;
			
		}
		
		
		// read file
		$json = file_get_contents( $file['tmp_name'] );
		
		
		// decode json
		$json = json_decode($json, true);
		
		
		// validate json
    	if( empty($json) ) {
    	
    		fields_add_admin_notice(__('Import file empty', 'fields'), 'error');
	    	return;
    	
    	}
    	
    	
    	// if importing an auto-json, wrap field group in array
    	if( isset($json['key']) ) {
	    	
	    	$json = array( $json );
	    	
    	}
    	
    	
    	// vars
    	$added = array();
    	$ignored = array();
    	$ref = array();
    	$order = array();
    	
    	foreach( $json as $field_group ) {
    		
	    	// check if field group exists
	    	if( fields_get_field_group($field_group['key'], true) ) {
	    		
	    		// append to ignored
	    		$ignored[] = $field_group['title'];
	    		continue;
	    	
	    	}
	    	
	    	
	    	// remove fields
			$fields = fields_extract_var($field_group, 'fields');
			
			
			// format fields
			$fields = fields_prepare_fields_for_import( $fields );
			
			
			// save field group
			$field_group = fields_update_field_group( $field_group );
			
			
			// add to ref
			$ref[ $field_group['key'] ] = $field_group['ID'];
			
			
			// add to order
			$order[ $field_group['ID'] ] = 0;
			
			
			// add fields
			foreach( $fields as $field ) {
				
				// add parent
				if( empty($field['parent']) ) {
					
					$field['parent'] = $field_group['ID'];
					
				} elseif( isset($ref[ $field['parent'] ]) ) {
					
					$field['parent'] = $ref[ $field['parent'] ];
						
				}
				
				
				// add field menu_order
				if( !isset($order[ $field['parent'] ]) ) {
					
					$order[ $field['parent'] ] = 0;
					
				}
				
				$field['menu_order'] = $order[ $field['parent'] ];
				$order[ $field['parent'] ]++;
				
				
				// save field
				$field = fields_update_field( $field );
				
				
				// add to ref
				$ref[ $field['key'] ] = $field['ID'];
				
			}
			
			// append to added
			$added[] = '<a href="' . admin_url("post.php?post={$field_group['ID']}&action=edit") . '" target="_blank">' . $field_group['title'] . '</a>';
			
    	}
    	
    	
    	// messages
    	if( !empty($added) ) {
    		
    		$message = __('<b>Success</b>. Import tool added %s field groups: %s', 'fields');
    		$message = sprintf( $message, count($added), implode(', ', $added) );
    		
	    	fields_add_admin_notice( $message );
    	
    	}
    	
    	if( !empty($ignored) ) {
    		
    		$message = __('<b>Warning</b>. Import tool detected %s field groups already exist and have been ignored: %s', 'fields');
    		$message = sprintf( $message, count($ignored), implode(', ', $ignored) );
    		
	    	fields_add_admin_notice( $message, 'error' );
    	
    	}
    	
		
	}
	
	
	/*
	*  generate
	*
	*  This function will generate PHP code to include in your theme
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function generate() {
		
		// vars
		$json = $this->get_json();
		
		
		// validate
		if( $json === false ) {
			
			fields_add_admin_notice( __("No field groups selected", 'fields') , 'error');
			return;
		
		}
		
				
		// update view
		$this->view = 'settings-tools-export';
		$this->data['field_groups'] = $json;
		
	}
	
	
	/*
	*  get_json
	*
	*  This function will return the JSON data for given $_POST args
	*
	*  @type	function
	*  @date	3/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function get_json() {
		
		// validate
		if( empty($_POST['fields_export_keys']) ) {
			
			return false;
				
		}
		
		
		// vars
		$json = array();
		
		
		// construct JSON
		foreach( $_POST['fields_export_keys'] as $key ) {
			
			// load field group
			$field_group = fields_get_field_group( $key );
			
			
			// validate field group
			if( empty($field_group) ) {
				
				continue;
			
			}
			
			
			// load fields
			$field_group['fields'] = fields_get_fields( $field_group );
	
	
			// prepare fields
			$field_group['fields'] = fields_prepare_fields_for_export( $field_group['fields'] );
			
			
			// extract field group ID
			$id = fields_extract_var( $field_group, 'ID' );
			
			
			// add to json array
			$json[] = $field_group;
			
		}
		
		
		// return
		return $json;
		
	}
	
}


// initialize
new fields_settings_tools();

?>
