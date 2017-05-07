<?php

/*
*  Fields API Comment Form Class
*
*  All the logic for adding fields to comments
*
*  @class 		fields_form_comment
*  @package		Fields API
*  @subpackage	Forms
*/

if( ! class_exists('fields_form_comment') ) :

class fields_form_comment {
	
	
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
		add_action( 'admin_enqueue_scripts',			array( $this, 'admin_enqueue_scripts' ) );
		
		
		// render
		add_action( 'comment_form_logged_in_after',		array( $this, 'add_comment') );
		add_action( 'comment_form_after_fields',		array( $this, 'add_comment') );

		
		// save
		add_action( 'edit_comment', 					array( $this, 'save_comment' ), 10, 1 );
		add_action( 'comment_post', 					array( $this, 'save_comment' ), 10, 1 );
		
	}
	
	
	/*
	*  validate_page
	*
	*  This function will check if the current page is for a post/page edit form
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	(boolean)
	*/
	
	function validate_page() {
		
		// global
		global $pagenow;
		
		
		// validate page
		if( $pagenow == 'comment.php' ) {
			
			return true;
			
		}
		
		
		// return
		return false;		
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
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		// validate page
		if( ! $this->validate_page() ) {
		
			return;
			
		}
		
		
		// load fields scripts
		fields_enqueue_scripts();
		
		
		// actions
		add_action('admin_footer',				array($this, 'admin_footer'), 10, 1);
		add_action('add_meta_boxes_comment', 	array($this, 'edit_comment'), 10, 1);

	}
	
	
	/*
	*  edit_comment
	*
	*  This function is run on the admin comment.php page and will render the Fields API fields within custom metaboxes to look native
	*
	*  @type	function
	*  @date	19/10/13
	*  @since	5.0.0
	*
	*  @param	$comment (object)
	*  @return	n/a
	*/
	
	function edit_comment( $comment ) {
		
		// vars
		$post_id = "comment_{$comment->comment_ID}";


		// get field groups
		$field_groups = fields_get_field_groups(array(
			'comment' => $comment->comment_ID
		));
		
		
		// render
		if( !empty($field_groups) ) {
		
			// render post data
			fields_form_data(array( 
				'post_id'	=> $post_id, 
				'nonce'		=> 'comment' 
			));
			
			
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = fields_get_fields( $field_group );
				
				?>
				<div id="fields-<?php echo $field_group['ID']; ?>" class="stuffbox editcomment">
					<h3><?php echo $field_group['title']; ?></h3>
					<div class="inside">
						<table class="form-table">
							<tbody>
								<?php fields_render_fields( $post_id, $fields, 'tr', 'field' ); ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				
			}
		
		}
		
	}
	
	
	/*
	*  add_comment
	*
	*  This function will add fields to the front end comment form
	*
	*  @type	function
	*  @date	19/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function add_comment() {
		
		// vars
		$post_id = "comment_0";

		
		// get field groups
		$field_groups = fields_get_field_groups(array(
			'comment' => 'new'
		));
		
		
		if( !empty($field_groups) ) {
			
			// render post data
			fields_form_data(array( 
				'post_id'	=> $post_id, 
				'nonce'		=> 'comment' 
			));
			
			
			foreach( $field_groups as $field_group ) {
				
				$fields = fields_get_fields( $field_group );
				
				?>
				<table class="form-table">
					<tbody>
						<?php fields_render_fields( $post_id, $fields, 'tr', 'field' ); ?>
					</tbody>
				</table>
				<?php
				
			}
		
		}
		
	}
	
	
	/*
	*  save_comment
	*
	*  This function will save the comment data
	*
	*  @type	function
	*  @date	19/10/13
	*  @since	5.0.0
	*
	*  @param	comment_id (int)
	*  @return	n/a
	*/
	
	function save_comment( $comment_id ) {
		
		// bail early if not valid nonce
		if( ! fields_verify_nonce('comment') ) {
		
			return $comment_id;
			
		}
		
	    
	    // validate and save
	    if( fields_validate_save_post(true) ) {
	    
			fields_save_post( "comment_{$comment_id}" );	
			
		}
				
	}
	
	
	/*
	*  admin_footer
	*
	*  description
	*
	*  @type	function
	*  @date	27/03/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_footer() {
		
?>
<script type="text/javascript">
(function($) {
	
	// vars
	var $spinner = $('#publishing-action .spinner');
	
	
	// create spinner if not exists (may exist in future WP versions)
	if( !$spinner.exists() ) {
		
		// create spinner
		$spinner = $('<span class="spinner"></span>');
		
		
		// append
		$('#publishing-action').prepend( $spinner );
		
	}
	
})(jQuery);	
</script>
<?php
		
	}
			
}

new fields_form_comment();

endif;

?>
