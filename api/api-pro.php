<?php 

/*
*  fields_pro_get_view
*
*  This function will load in a file from the 'admin/views' folder and allow variables to be passed through
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$view_name (string)
*  @param	$args (array)
*  @return	n/a
*/

function fields_pro_get_view( $view_name = '', $args = array() ) {
	
	// vars
	$path = fields_get_path("pro/admin/views/{$view_name}.php");
	
	
	if( file_exists($path) ) {
		
		include( $path );
		
	}
	
}


/*
*  fields_pro_get_remote_url
*
*  description
*
*  @type	function
*  @date	16/01/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fields_pro_get_remote_url( $action = '', $args = array() ) {
	
	// defaults
	$args['a'] = $action;
	$args['p'] = 'pro';
	
	
	// vars
	$url = "http://connect.advancedcustomfields.com/index.php?" . build_query($args);
	
	
	// return
	return $url;
	
}


/*
*  fields_pro_get_remote_response
*
*  description
*
*  @type	function
*  @date	16/01/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fields_pro_get_remote_response( $action = '', $post = array() ) {
	
	// vars
	$url = fields_pro_get_remote_url( $action );
	
	
	// connect
	$request = wp_remote_post( $url, array(
		'body' => $post
	));
	
	
	// return body
    if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
    	
        return $request['body'];
    
    }
    
    
    // return
    return 0;
    
}


/*
*  fields_pro_is_update_available
*
*  This function will return true if an update is available
*
*  @type	function
*  @date	14/05/2014
*  @since	5.0.0
*
*  @param	n/a
*  @return	(boolean)
*/

function fields_pro_is_update_available() {
	
	// vars
	$info = fields_pro_get_remote_info();
	$version = fields_get_setting('version');
	 
	
	// return false if no info
	if( empty($info['version']) ) {
		
		return false;
		
	}
	
    
    // return false if the external version is '<=' the current version
	if( version_compare($info['version'], $version, '<=') ) {
		
    	return false;
    
    }
    
	
	// return
	return true;
	
}


/*
*  fields_pro_get_remote_info
*
*  This function will return remote plugin data
*
*  @type	function
*  @date	16/01/2014
*  @since	5.0.0
*
*  @param	n/a
*  @return	(mixed)
*/

function fields_pro_get_remote_info() {
	
	// clear transient if force check is enabled
	if( !empty($_GET['force-check']) ) {
		
		// only allow transient to be deleted once per page load
		if( empty($_GET['fields-ignore-force-check']) ) {
			
			delete_transient( 'fields_pro_get_remote_info' );
			
		}
		
		
		// update $_GET
		$_GET['fields-ignore-force-check'] = true;
		
	}
	
	
	// get transient
	$transient = get_transient( 'fields_pro_get_remote_info' );

	if( $transient !== false ) {
	
		return $transient;
	
	}

	
	// vars
	$info = fields_pro_get_remote_response('get-info');
	$timeout = 12 * HOUR_IN_SECONDS;
	
	
    // decode
    if( !empty($info) ) {
    	
		$info = json_decode($info, true);
		
		// fake info version
        //$info['version'] = '6.0.0';
        
    } else {
	    
	    $info = 0; // allow transient to be returned, but empty to validate
	    $timeout = 2 * HOUR_IN_SECONDS;
	    
    }
        
        
	// update transient
	set_transient('fields_pro_get_remote_info', $info, $timeout );
	
	
	// return
	return $info;
}


function fields_pro_is_license_active() {
	
	// vars
	$data = fields_pro_get_license( true );
	$url = home_url();
	
	if( !empty($data['url']) && !empty($data['key']) && $data['url'] == $url ) {
		
		return true;
		
	}
	
	
	return false;
	
}

function fields_pro_get_license( $all = false ) {
	
	// get option
	$data = get_option('fields_pro_license');
	
	
	// decode
	$data = base64_decode($data);
	
	
	// attempt deserialize
	if( is_serialized( $data ) )
	{
		$data = maybe_unserialize($data);
		
		// $all
		if( !$all )
		{
			$data = $data['key'];
		}
		
		return $data;
	}
	
	
	// return
	return false;
}



function fields_pro_update_license( $license ) {
	
	$save = array(
		'key'	=> $license,
		'url'	=> home_url()
	);
	
	
	$save = maybe_serialize($save);
	$save = base64_encode($save);
	
	
	return update_option('fields_pro_license', $save);
	
}

?>
