<?php

/*
*  FieldMaster Admin Field Groups Class
*
*  All the logic for editing a list of field groups
*
*  @class 		fields_admin_field_groups
*  @package		FieldMaster
*  @subpackage	Admin
*/

if( ! class_exists('fields_admin_field_groups') ) :

class fields_admin_field_groups {
	
	// vars
	var $url = 'edit.php?post_type=fields-field-group',
		$sync = array();
		
	
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
		add_action('current_screen',		array($this, 'current_screen'));
		add_action('trashed_post',			array($this, 'trashed_post'));
		add_action('untrashed_post',		array($this, 'untrashed_post'));
		add_action('deleted_post',			array($this, 'deleted_post'));
		
	}
	
	
	/*
	*  current_screen
	*
	*  This function is fired when loading the admin page before HTML has been rendered.
	*
	*  @type	action (current_screen)
	*  @date	21/07/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function current_screen() {
		
		// validate screen
		if( !fields_is_screen('edit-fields-field-group') ) {
		
			return;
			
		}
		

		// customize post_status
		global $wp_post_statuses;
		
		
		// modify publish post status
		$wp_post_statuses['publish']->label_count = _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'fields' );
		
		
		// reorder trash to end
		$wp_post_statuses['trash'] = fields_extract_var( $wp_post_statuses, 'trash' );

		
		// check stuff
		$this->check_duplicate();
		$this->check_sync();
		
		
		// actions
		add_action('admin_enqueue_scripts',							array($this, 'admin_enqueue_scripts'));
		add_action('admin_footer',									array($this, 'admin_footer'));
		
		
		// columns
		add_filter('manage_edit-fields-field-group_columns',			array($this, 'field_group_columns'), 10, 1);
		add_action('manage_fields-field-group_posts_custom_column',	array($this, 'field_group_columns_html'), 10, 2);
		
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
		
		wp_enqueue_script('fields-input');
		
	}
	
	
	/*
	*  check_duplicate
	*
	*  This function will check for any $_GET data to duplicate
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function check_duplicate() {
		
		// message
		if( $ids = fields_maybe_get($_GET, 'fieldsduplicatecomplete') ) {
			
			// explode
			$ids = explode(',', $ids);
			$total = count($ids);
			
			if( $total == 1 ) {
				
				fields_add_admin_notice( sprintf(__('Field group duplicated. %s', 'fields'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );
				
			} else {
				
				fields_add_admin_notice( sprintf(_n( '%s field group duplicated.', '%s field groups duplicated.', $total, 'fields' ), $total) );
				
			}
			
		}
		
		
		// import field group
		if( $id = fields_maybe_get($_GET, 'fieldsduplicate') ) {
			
			// validate
			check_admin_referer('bulk-posts');
			
			
			// duplicate
			$field_group = fields_duplicate_field_group( $id );
			
			
			// redirect
			wp_redirect( admin_url( $this->url . '&fieldsduplicatecomplete=' . $field_group['ID'] ) );
			exit;
			
		} elseif( fields_maybe_get($_GET, 'action2') === 'fieldsduplicate' ) {
		
			// validate
			check_admin_referer('bulk-posts');
				
			
			// get ids
			$ids = fields_maybe_get($_GET, 'post');
			
			if( !empty($ids) ) {
				
				// vars
				$new_ids = array();
				
				foreach( $ids as $id ) {
					
					// duplicate
					$field_group = fields_duplicate_field_group( $id );
					
					
					// increase counter
					$new_ids[] = $field_group['ID'];
					
				}
				
				
				// redirect
				wp_redirect( admin_url( $this->url . '&fieldsduplicatecomplete=' . implode(',', $new_ids)) );
				exit;
			}
		
		}
		
	}
	
	
	/*
	*  check_sync
	*
	*  This function will check for any $_GET data to sync
	*
	*  @type	function
	*  @date	9/12/2014
	*  @since	5.1.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function check_sync() {
		
		// message
		if( $ids = fields_maybe_get($_GET, 'fieldssynccomplete') ) {
			
			// explode
			$ids = explode(',', $ids);
			$total = count($ids);
			
			if( $total == 1 ) {
				
				fields_add_admin_notice( sprintf(__('Field group synchronised. %s', 'fields'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );
				
			} else {
				
				fields_add_admin_notice( sprintf(_n( '%s field group synchronised.', '%s field groups synchronised.', $total, 'fields' ), $total) );
				
			}
			
		}
		
		
		// vars
		$groups = fields_get_field_groups();
		
		
		// bail early if no field groups
		if( empty($groups) ) {
			
			return;
			
		}
		
		
		// find JSON field groups which have not yet been imported
		foreach( $groups as $group ) {
			
			// vars
			$local = fields_maybe_get($group, 'local', false);
			$modified = fields_maybe_get($group, 'modified', 0);
			$private = fields_maybe_get($group, 'private', false);
			
			
			// ignore DB / PHP / private field groups
			if( $local !== 'json' || $private ) {
				
				// do nothing
				
			} elseif( !$group['ID'] ) {
				
				$this->sync[ $group['key'] ] = $group;
				
			} elseif( $modified && $modified > get_post_modified_time('U', true, $group['ID'], true) ) {
				
				$this->sync[ $group['key'] ]  = $group;
				
			}
						
		}
		
		
		// bail if no sync needed
		if( empty($this->sync) ) {
			
			return;
			
		}
	
		
		// import field group
		if( $key = fields_maybe_get($_GET, 'fieldssync') ) {
			
			// disable JSON
			// - this prevents a new JSON file being created and causing a 'change' to theme files - solves git anoyance
			fields_update_setting('json', false);
			
			
			// validate
			check_admin_referer('bulk-posts');
			
			
			// append fields
			if( fields_have_local_fields( $key ) ) {
				
				$this->sync[ $key ]['fields'] = fields_get_local_fields( $key );
				
			}
			
			
			// import
			$field_group = fields_import_field_group( $this->sync[ $key ] );
			
			
			// redirect
			wp_redirect( admin_url( $this->url . '&fieldssynccomplete=' . $field_group['ID'] ) );
			exit;
			
		} elseif( fields_maybe_get($_GET, 'action2') === 'fieldssync' ) {
			
			// validate
			check_admin_referer('bulk-posts');
				
			
			// get ids
			$keys = fields_maybe_get($_GET, 'post');
			
			if( !empty($keys) ) {
				
				// disable JSON
				// - this prevents a new JSON file being created and causing a 'change' to theme files - solves git anoyance
				fields_update_setting('json', false);
				
				// vars
				$new_ids = array();
				
				foreach( $keys as $key ) {
					
					// append fields
					if( fields_have_local_fields( $key ) ) {
						
						$this->sync[ $key ]['fields'] = fields_get_local_fields( $key );
						
					}
					
					
					// import
					$field_group = fields_import_field_group( $this->sync[ $key ] );
										
					
					// append
					$new_ids[] = $field_group['ID'];
					
				}
				
				
				// redirect
				wp_redirect( admin_url( $this->url . '&fieldssynccomplete=' . implode(',', $new_ids)) );
				exit;
				
			}
		
		}
		
		
		// filters
		add_filter('views_edit-fields-field-group', array($this, 'list_table_views'));
		
	}
	
	
	/*
	*  list_table_views
	*
	*  This function will add an extra link for JSON in the field group list table
	*
	*  @type	function
	*  @date	3/12/2014
	*  @since	5.1.5
	*
	*  @param	$views (array)
	*  @return	$views
	*/
	
	function list_table_views( $views ) {
		
		// vars
		$class = '';
		$total = count($this->sync);
		
		// active
		if( fields_maybe_get($_GET, 'post_status') === 'sync' ) {
			
			// actions
			add_action('admin_footer', array($this, 'sync_admin_footer'), 5);
			
			
			// set active class
			$class = ' class="current"';
			
			
			// global
			global $wp_list_table;
			
			
			// update pagination
			$wp_list_table->set_pagination_args( array(
				'total_items' => $total,
				'total_pages' => 1,
				'per_page' => $total
			));
			
		}
		
		
		// add view
		$views['json'] = '<a' . $class . ' href="' . admin_url($this->url . '&post_status=sync') . '">' . __('Sync available', 'fields') . ' <span class="count">(' . $total . ')</span></a>';
		
		
		// return
		return $views;
		
	}
	
	
	/*
	*  trashed_post
	*
	*  This function is run when a post object is sent to the trash
	*
	*  @type	action (trashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function trashed_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'fields-field-group' ) {
		
			return;
		
		}
		
		
		// trash field group
		fields_trash_field_group( $post_id );
		
	}
	
	
	/*
	*  untrashed_post
	*
	*  This function is run when a post object is restored from the trash
	*
	*  @type	action (untrashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function untrashed_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'fields-field-group' ) {
		
			return;
			
		}
		
		
		// trash field group
		fields_untrash_field_group( $post_id );
		
	}
	
	
	/*
	*  deleted_post
	*
	*  This function is run when a post object is deleted from the trash
	*
	*  @type	action (deleted_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function deleted_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'fields-field-group' ) {
		
			return;
			
		}
		
		
		// trash field group
		fields_delete_field_group( $post_id );
		
	}
	
	
	/*
	*  field_group_columns
	*
	*  This function will customize the columns for the field group table
	*
	*  @type	filter (manage_edit-fields-field-group_columns)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$columns (array)
	*  @return	$columns (array)
	*/
	
	function field_group_columns( $columns ) {
		
		return array(
			'cb'	 				=> '<input type="checkbox" />',
			'title' 				=> __('Title', 'fields'),
			'fields-fg-description'	=> __('Description', 'fields'),
			'fields-fg-status' 		=> '<i class="fields-icon fields-icon-dot-3 small fields-js-tooltip" title="' . __('Status', 'fields') . '"></i>',
			'fields-fg-count' 			=> __('Fields', 'fields'),
			
		);
		
	}
	
	
	/*
	*  field_group_columns_html
	*
	*  This function will render the HTML for each table cell
	*
	*  @type	action (manage_fields-field-group_posts_custom_column)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$column (string)
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function field_group_columns_html( $column, $post_id ) {
		
		// vars
		$field_group = fields_get_field_group( $post_id );
		
		
		// render
		$this->render_column( $column, $field_group );
	    
	}
	
	function render_column( $column, $field_group ) {
		
		// description
		if( $column == 'fields-fg-description' ) {
			
			if( $field_group['description'] ) {
				
				echo '<span class="fields-description">' . $field_group['description'] . '</span>';
				
			}
        
        // status
	    } elseif( $column == 'fields-fg-status' ) {
			
			if( isset($this->sync[ $field_group['key'] ]) ) {
				
				echo '<i class="fields-icon fields-icon-sync grey small fields-js-tooltip" title="' . __('Sync available', 'fields') .'"></i> ';
				
			}
			
			if( $field_group['active'] ) {
				
				//echo '<i class="fields-icon fields-icon-check small fields-js-tooltip" title="' . __('Active', 'fields') .'"></i> ';
				
			} else {
				
				echo '<i class="fields-icon fields-icon-minus yellow small fields-js-tooltip" title="' . __('Disabled', 'fields') . '"></i> ';
				
			}
	    
        // fields
	    } elseif( $column == 'fields-fg-count' ) {
			
			echo fields_get_field_count( $field_group );
        
        }
		
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
		// vars
		$www = 'http://www.advancedcustomfields.com/resources/';
		
?><script type="text/html" id="tmpl-fields-column-2">
<div class="fields-column-2">
	<div class="fields-box">
		<div class="inner">
			<h2><?php echo fields_get_setting('name'); ?> <?php echo fields_get_setting('version'); ?></h2>

			<h3><?php _e("Changelog",'fields'); ?></h3>
			<p><?php _e("See what's new in",'fields'); ?> <a href="<?php echo admin_url('edit.php?post_type=fields-field-group&page=fields-settings-info&tab=changelog'); ?>"><?php _e("version",'fields'); ?> <?php echo fields_get_setting('version'); ?></a>
			
			<h3><?php _e("Resources",'fields'); ?></h3>
			<ul>
				<li><a href="<?php echo $www; ?>#getting-started" target="_blank"><?php _e("Getting Started",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#updates" target="_blank"><?php _e("Updates",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#field-types" target="_blank"><?php _e("Field Types",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#functions" target="_blank"><?php _e("Functions",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#actions" target="_blank"><?php _e("Actions",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#filters" target="_blank"><?php _e("Filters",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#how-to" target="_blank"><?php _e("'How to' guides",'fields'); ?></a></li>
				<li><a href="<?php echo $www; ?>#tutorials" target="_blank"><?php _e("Tutorials",'fields'); ?></a></li>
			</ul>
		</div>
		<div class="footer footer-blue">
			<ul class="fields-hl">
				<li><?php _e("Created by",'fields'); ?> Elliot Condon</li>
			</ul>
		</div>
	</div>
</div>
<div class="fields-clear"></div>
</script>
<script type="text/javascript">
(function($){
	
	// wrap
	$('#wpbody .wrap').attr('id', 'fields-field-group-wrap');
	
	
	// wrap form
	$('#posts-filter').wrap('<div class="fields-columns-2" />');
	
	
	// add column main
	$('#posts-filter').addClass('fields-column-1');
	
	
	// add column side
	$('#posts-filter').after( $('#tmpl-fields-column-2').html() );
	
	
	// modify row actions
	$('#the-list tr').each(function(){
		
		// vars
		var $tr = $(this),
			id = $tr.attr('id'),
			description = $tr.find('.column-fields-fg-description').html();
		
		
		// replace Quick Edit with Duplicate (sync page has no id attribute)
		if( id ) {
			
			// vars
			var post_id	= id.replace('post-', '');
			
			
			// create el
			var $span = $('<span class="fields-duplicate-field-group"><a title="<?php _e('Duplicate this item', 'fields'); ?>" href="<?php echo admin_url($this->url . '&fieldsduplicate='); ?>' + post_id + '&_wpnonce=<?php echo wp_create_nonce('bulk-posts'); ?>"><?php _e('Duplicate', 'fields'); ?></a> | </span>');
			
			
			// replace
			$tr.find('.column-title .row-actions .inline').replaceWith( $span );
			
		}
		
		
		// add description to title
		$tr.find('.column-title .row-title').after( description );
		
	});
	
	
	// modify bulk actions
	$('#bulk-action-selector-bottom option[value="edit"]').attr('value','fieldsduplicate').text('<?php _e( 'Duplicate', 'fields' ); ?>');
	
	
	// clean up table
	$('#adv-settings label[for="fields-fg-description-hide"]').remove();
	
	
	// mobile compatibility
	var status = $('.fields-icon-dot-3').first().attr('title');
	$('td.column-fields-fg-status').attr('data-colname', status);
	
})(jQuery);
</script>
<?php
		
	}
	
	
	/*
	*  sync_admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function sync_admin_footer() {
		
		// vars
		$i = -1;
		$columns = array(
			'fields-fg-description',
			'fields-fg-status',
			'fields-fg-count'
		);
		
?>
<script type="text/html" id="tmpl-fields-json-tbody">
<?php foreach( $this->sync as $field_group ): $i++; ?>
	<tr <?php if($i%2 == 0): ?>class="alternate"<?php endif; ?>>
		<th class="check-column" scope="row">
			<label for="cb-select-<?php echo $field_group['key']; ?>" class="screen-reader-text"><?php printf( __( 'Select %s', 'fields' ), $field_group['title'] ); ?></label>
			<input type="checkbox" value="<?php echo $field_group['key']; ?>" name="post[]" id="cb-select-<?php echo $field_group['key']; ?>">
		</th>
		<td class="post-title page-title column-title">
			<strong>
				<span class="row-title"><?php echo $field_group['title']; ?></span><span class="fields-description"><?php echo $field_group['key']; ?>.json</span>
			</strong>
			<div class="row-actions">
				<span class="import"><a title="<?php echo esc_attr( __('Synchronise field group', 'fields') ); ?>" href="<?php echo admin_url($this->url . '&post_status=sync&fieldssync=' . $field_group['key'] . '&_wpnonce=' . wp_create_nonce('bulk-posts')); ?>"><?php _e( 'Sync', 'fields' ); ?></a></span>
			</div>
		</td>
		<?php foreach( $columns as $column ): ?>
			<td class="column-<?php echo $column; ?>"><?php $this->render_column( $column, $field_group ); ?></td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</script>
<script type="text/javascript">
(function($){
	
	// update table HTML
	$('#the-list').html( $('#tmpl-fields-json-tbody').html() );
	
	
	// modify bulk actions
	$('#bulk-action-selector-bottom option[value="edit"]').attr('value','fieldssync').text('<?php _e('Sync', 'fields'); ?>');
	$('#bulk-action-selector-bottom option[value="trash"]').remove();
		
})(jQuery);
</script>
<?php
		
	}
			
}

new fields_admin_field_groups();

endif;

?>
