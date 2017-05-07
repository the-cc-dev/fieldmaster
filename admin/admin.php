<?php 

class fields_admin {
	
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
		add_action('admin_menu', 			array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts',	array($this, 'admin_enqueue_scripts'), 0);
		add_action('admin_notices', 		array($this, 'admin_notices'));
		
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
		
		
		// vars
		$slug = 'edit.php?post_type=fields-field-group';
		$cap = fields_get_setting('capability');
		
		
		// add parent
		add_menu_page(__("Fields API",'fields'), __("Fields API",'fields'), $cap, $slug, false, 'dashicons-welcome-widgets-menus', '80.025');
		
		
		// add children
		add_submenu_page($slug, __('Field Groups','fields'), __('Field Groups','fields'), $cap, $slug );
		add_submenu_page($slug, __('Add New','fields'), __('Add New','fields'), $cap, 'post-new.php?post_type=fields-field-group' );
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This function will add the already registered css
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		wp_enqueue_style( 'fields-global' );
		
	}
	
	
	/*
	*  admin_notices
	*
	*  This function will render any admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_notices() {
		
		// vars
		$admin_notices = fields_get_admin_notices();
		
		
		// bail early if no notices
		if( empty($admin_notices) ) {
			
			return;
			
		}
		
		
		foreach( $admin_notices as $notice ) {
			
			$open = '';
			$close = '';
				
			if( $notice['wrap'] ) {
				
				$open = "<{$notice['wrap']}>";
				$close = "</{$notice['wrap']}>";
				
			}
				
			?>
			<div class="notice is-dismissible <?php echo $notice['class']; ?>"><?php echo $open . $notice['text'] . $close; ?></div>
			<?php
				
		}
		
	}
	
}


// initialize
new fields_admin();

?>
