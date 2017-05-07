<?php 

class fields_json {
	
	function __construct() {
		
		// update setting
		fields_update_setting('save_json', get_stylesheet_directory() . '/fields-json');
		fields_append_setting('load_json', get_stylesheet_directory() . '/fields-json');
		
		
		// actions
		add_action('fields/update_field_group',		array($this, 'update_field_group'), 10, 5);
		add_action('fields/duplicate_field_group',		array($this, 'update_field_group'), 10, 5);
		add_action('fields/untrash_field_group',		array($this, 'update_field_group'), 10, 5);
		add_action('fields/trash_field_group',			array($this, 'delete_field_group'), 10, 5);
		add_action('fields/delete_field_group',		array($this, 'delete_field_group'), 10, 5);
		add_action('fields/include_fields', 			array($this, 'include_fields'), 10, 5);
		
	}
	
	
	/*
	*  update_field_group
	*
	*  This function is hooked into the fields/update_field_group action and will save all field group data to a .json file 
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function update_field_group( $field_group ) {
		
		// validate
		if( !fields_get_setting('json') ) {
		
			return;
			
		}
		
		
		// get fields
		$field_group['fields'] = fields_get_fields( $field_group );
		
		
		// save file
		fields_write_json_field_group( $field_group );
			
	}
	
	
	/*
	*  delete_field_group
	*
	*  This function will remove the field group .json file
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function delete_field_group( $field_group ) {
		
		// validate
		if( !fields_get_setting('json') ) {
		
			return;
			
		}
		
		
		fields_delete_json_field_group( $field_group['key'] );
		
	}
		
	
	/*
	*  include_fields
	*
	*  This function will include any JSON files found in the active theme
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$version (int)
	*  @return	n/a
	*/
	
	function include_fields() {
		
		// validate
		if( !fields_get_setting('json') ) {
		
			return;
			
		}
		
		
		// vars
		$paths = fields_get_setting('load_json');
		
		
		// loop through and add to cache
		foreach( $paths as $path ) {
			
			// remove trailing slash
			$path = untrailingslashit( $path );
		
		
			// check that path exists
			if( !file_exists( $path ) ) {
			
				continue;
				
			}
			
			
			$dir = opendir( $path );
	    
		    while(false !== ( $file = readdir($dir)) ) {
		    
		    	// only json files
		    	if( strpos($file, '.json') === false ) {
		    	
			    	continue;
			    	
		    	}
		    	
		    	
		    	// read json
		    	$json = file_get_contents("{$path}/{$file}");
		    	
		    	
		    	// validate json
		    	if( empty($json) ) {
			    	
			    	continue;
			    	
		    	}
		    	
		    	
		    	// decode
		    	$json = json_decode($json, true);
		    	
		    	
		    	// add local
		    	$json['local'] = 'json';
		    	
		    	
		    	// add field group
		    	fields_add_local_field_group( $json );
		        
		    }
		    
		}
		
	}
	
}

new fields_json();


/*
*  fields_write_json_field_group
*
*  This function will save a field group to a json file within the current theme
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$field_group (array)
*  @return	(boolean)
*/

function fields_write_json_field_group( $field_group ) {
	
	// vars
	$path = fields_get_setting('save_json');
	$file = $field_group['key'] . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if dir does not exist
	if( !is_writable($path) ) {
	
		return false;
		
	}
	
	
	// extract field group ID
	$id = fields_extract_var( $field_group, 'ID' );
	
	
	// add modified time
	$field_group['modified'] = get_post_modified_time('U', true, $id, true);
	
	
	// prepare fields
	$field_group['fields'] = fields_prepare_fields_for_export( $field_group['fields'] );
		
		
	// write file
	$f = fopen("{$path}/{$file}", 'w');
	fwrite($f, fields_json_encode( $field_group ));
	fclose($f);
	
	
	// return
	return true;
	
}


/*
*  fields_delete_json_field_group
*
*  This function will delete a json field group file
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$key (string)
*  @return	(boolean)
*/

function fields_delete_json_field_group( $key ) {
	
	// vars
	$path = fields_get_setting('save_json');
	$file = $key . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if file does not exist
	if( !is_readable("{$path}/{$file}") ) {
	
		return false;
		
	}
	
		
	// remove file
	unlink("{$path}/{$file}");
	
	
	// return
	return true;
	
}


?>
