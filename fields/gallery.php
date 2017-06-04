<?php

/*
*  FieldMaster Gallery Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_gallery
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_gallery') ) :

class fieldmaster_field_gallery extends fieldmaster_field {
	
	
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
		$this->name = 'gallery';
		$this->label = __("Gallery",'fields');
		$this->category = 'content';
		$this->defaults = array(
			'preview_size'	=> 'thumbnail',
			'library'		=> 'all',
			'min'			=> 0,
			'max'			=> 0,
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> ''
		);
		$this->l10n = array(
			'select'		=> __("Add Image to Gallery",'fields'),
			'edit'			=> __("Edit Image",'fields'),
			'update'		=> __("Update Image",'fields'),
			'uploadedTo'	=> __("uploaded to this post",'fields'),
			'max'			=> __("Maximum selection reached",'fields')
		);
		
		
		// actions
		add_action('wp_ajax_fields/fields/gallery/get_attachment',				array($this, 'ajax_get_attachment'));
		add_action('wp_ajax_nopriv_fields/fields/gallery/get_attachment',		array($this, 'ajax_get_attachment'));
		
		add_action('wp_ajax_fields/fields/gallery/update_attachment',			array($this, 'ajax_update_attachment'));
		add_action('wp_ajax_nopriv_fields/fields/gallery/update_attachment',	array($this, 'ajax_update_attachment'));
		
		add_action('wp_ajax_fields/fields/gallery/get_sort_order',				array($this, 'ajax_get_sort_order'));
		add_action('wp_ajax_nopriv_fields/fields/gallery/get_sort_order',		array($this, 'ajax_get_sort_order'));
		
		
		
		// do not delete!
    	parent::__construct();
	}
	
	
	/*
	*  ajax_get_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_attachment() {
	
		// options
   		$options = fields_parse_args( $_POST, array(
			'post_id'		=>	0,
			'id'			=>	0,
			'field_key'		=>	'',
			'nonce'			=>	'',
		));
   		
		
		// validate
		if( ! wp_verify_nonce($options['nonce'], 'fields_nonce') ) {
			
			die();
			
		}
		
		if( empty($options['id']) ) {
		
			die();
			
		}
		
		
		// load field
		$field = fields_get_field( $options['field_key'] );
		
		if( !$field ) {
		
			die();
			
		}
		
		
		// render
		$this->render_attachment( $options['id'], $field );
		die;
		
	}
	
	
	/*
	*  ajax_update_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_update_attachment() {
		
		// validate nonce
		if( !wp_verify_nonce($_POST['nonce'], 'fields_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// bail early if no attachments
		if( empty($_POST['attachments']) ) {
		
			wp_send_json_error();
			
		}
		
		
		// loop over attachments
		foreach( $_POST['attachments'] as $id => $changes ) {
			
			if ( !current_user_can( 'edit_post', $id ) )
				wp_send_json_error();
				
			$post = get_post( $id, ARRAY_A );
		
			if ( 'attachment' != $post['post_type'] )
				wp_send_json_error();
		
			if ( isset( $changes['title'] ) )
				$post['post_title'] = $changes['title'];
		
			if ( isset( $changes['caption'] ) )
				$post['post_excerpt'] = $changes['caption'];
		
			if ( isset( $changes['description'] ) )
				$post['post_content'] = $changes['description'];
		
			if ( isset( $changes['alt'] ) ) {
				$alt = wp_unslash( $changes['alt'] );
				if ( $alt != get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
					$alt = wp_strip_all_tags( $alt, true );
					update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
				}
			}
			
			
			// save post
			wp_update_post( $post );
			
			
			/** This filter is documented in wp-admin/includes/media.php */
			// - seems off to run this filter AFTER the update_post function, but there is a reason
			// - when placed BEFORE, an empty post_title will be populated by WP
			// - this filter will still allow 3rd party to save extra image data!
			$post = apply_filters( 'attachment_fields_to_save', $post, $changes );
			
			
			// save meta
			fields_save_post( $id );
						
		}
		
		
		// return
		wp_send_json_success();
			
	}
	
	
	/*
	*  ajax_get_sort_order
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_sort_order() {
		
		// vars
		$r = array();
		$order = 'DESC';
   		$args = fields_parse_args( $_POST, array(
			'ids'			=>	0,
			'sort'			=>	'date',
			'field_key'		=>	'',
			'nonce'			=>	'',
		));
		
		
		// validate
		if( ! wp_verify_nonce($args['nonce'], 'fields_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// reverse
		if( $args['sort'] == 'reverse' ) {
		
			$ids = array_reverse($args['ids']);
			
			wp_send_json_success($ids);
			
		}
		
		
		if( $args['sort'] == 'title' ) {
			
			$order = 'ASC';
			
		}
		
		
		// find attachments (DISTINCT POSTS)
		$ids = get_posts(array(
			'post_type'		=> 'attachment',
			'numberposts'	=> -1,
			'post_status'	=> 'any',
			'post__in'		=> $args['ids'],
			'order'			=> $order,
			'orderby'		=> $args['sort'],
			'fields'		=> 'ids'		
		));
		
		
		// success
		if( !empty($ids) ) {
		
			wp_send_json_success($ids);
			
		}
		
		
		// failure
		wp_send_json_error();
		
	}
	
	
	/*
	*  render_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function render_attachment( $id = 0, $field ) {
		
		// vars
		$attachment = wp_prepare_attachment_for_js( $id );
		$thumb = '';
		$prefix = "attachments[{$id}]";
		$compat = get_compat_media_markup( $id );
		$dimentions = '';
		
		
		// thumb
		if( isset($attachment['thumb']['src']) ) {
			
			// video
			$thumb = $attachment['thumb']['src'];
			
		} elseif( isset($attachment['sizes']['thumbnail']['url']) ) {
			
			// image
			$thumb = $attachment['sizes']['thumbnail']['url'];
			
		} elseif( $attachment['type'] === 'image' ) {
			
			// svg
			$thumb = $attachment['url'];
			
		} else {
			
			// fallback (perhaps attachment does not exist)
			$thumb = $attachment['icon'];
				
		}
		
		
		
		// dimentions
		if( $attachment['type'] === 'audio' ) {
			
			$dimentions = __('Length', 'fields') . ': ' . $attachment['fileLength'];
			
		} elseif( !empty($attachment['width']) ) {
			
			$dimentions = $attachment['width'] . ' x ' . $attachment['height'];
			
		}
		
		if( $attachment['filesizeHumanReadable'] ) {
			
			$dimentions .=  ' (' . $attachment['filesizeHumanReadable'] . ')';
			
		}
		
		?>
		<div class="fields-gallery-side-info fields-cf">
			<img src="<?php echo $thumb; ?>" alt="<?php echo $attachment['alt']; ?>" />
			<p class="filename"><strong><?php echo $attachment['filename']; ?></strong></p>
			<p class="uploaded"><?php echo $attachment['dateFormatted']; ?></p>
			<p class="dimensions"><?php echo $dimentions; ?></p>
			<p class="actions"><a href="#" class="edit-attachment" data-id="<?php echo $id; ?>"><?php _e('Edit', 'fields'); ?></a> <a href="#" class="remove-attachment" data-id="<?php echo $id; ?>"><?php _e('Remove', 'fields'); ?></a></p>
		</div>
		<table class="form-table">
			<tbody>
				<?php 
				
				fields_render_field_wrap(array(
					//'key'		=> "{$field['key']}-title",
					'name'		=> 'title',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> 'Title',
					'value'		=> $attachment['title']
				), 'tr');
				
				fields_render_field_wrap(array(
					//'key'		=> "{$field['key']}-caption",
					'name'		=> 'caption',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> 'Caption',
					'value'		=> $attachment['caption']
				), 'tr');
				
				fields_render_field_wrap(array(
					//'key'		=> "{$field['key']}-alt",
					'name'		=> 'alt',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> 'Alt Text',
					'value'		=> $attachment['alt']
				), 'tr');
				
				fields_render_field_wrap(array(
					//'key'		=> "{$field['key']}-description",
					'name'		=> 'description',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> 'Description',
					'value'		=> $attachment['description']
				), 'tr');
				
				?>
			</tbody>
		</table>
		<?php echo $compat['item']; ?>
		
		<?php
		
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
		
		// enqueue
		fields_enqueue_uploader();
		
		
		// vars
		$posts = array();
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> "fields-gallery {$field['class']}",
			'data-preview_size'	=> $field['preview_size'],
			'data-library'		=> $field['library'],
			'data-min'			=> $field['min'],
			'data-max'			=> $field['max'],
			'data-mime_types'	=> $field['mime_types'],
		);
		
		
		// set gallery height
		$height = fields_get_user_setting('gallery_height', 400);
		$height = max( $height, 200 ); // minimum height is 200
		$atts['style'] = "height:{$height}px";
		
		
		// load posts
		if( !empty($field['value']) ) {
			
			$posts = fields_get_posts(array(
				'post_type'	=> 'attachment',
				'post__in'	=> $field['value']
			));
			
		}
		
		
		?>
<div <?php fields_esc_attr_e($atts); ?>>
	
	<div class="fields-hidden">
		<input type="hidden" <?php fields_esc_attr_e(array( 'name' => $field['name'], 'value' => '', 'data-name' => 'ids' )); ?> />
	</div>
	
	<div class="fields-gallery-main">
		
		<div class="fields-gallery-attachments">
			
			<?php if( !empty($posts) ): ?>
			
				<?php foreach( $posts as $post ): 
					
					// vars
					$type = fields_maybe_get(explode('/', $post->post_mime_type), 0);
					$thumb_id = $post->ID;
					$thumb_url = '';
					$thumb_class = 'fields-gallery-attachment fields-soh';
					$filename = wp_basename($post->guid);
					
					
					// thumb
					if( $type === 'image' || $type === 'audio' || $type === 'video' ) {
						
						// change $thumb_id
						if( $type === 'audio' || $type === 'video' ) {
							
							$thumb_id = get_post_thumbnail_id( $post->ID );
							
						}
						
						
						// get attachment
						if( $thumb_id ) {
							
							$thumb_url = wp_get_attachment_image_src( $thumb_id, $field['preview_size'] );
							$thumb_url = fields_maybe_get( $thumb_url, 0 );
						
						}
						
					}
					
					
					// fallback
					if( !$thumb_url ) {
						
						$thumb_url = wp_mime_type_icon( $post->ID );
						$thumb_class .= ' is-mime-icon';
						
					}
					
					?>
					<div class="<?php echo $thumb_class; ?>" data-id="<?php echo $post->ID; ?>">
						<input type="hidden" name="<?php echo $field['name']; ?>[]" value="<?php echo $post->ID; ?>" />
						<div class="margin" title="<?php echo $filename; ?>">
							<div class="thumbnail">
								<img src="<?php echo $thumb_url; ?>"/>
							</div>
							<?php if( $type !== 'image' ): ?>
							<div class="filename"><?php echo fields_get_truncated($filename, 18); ?></div>
							<?php endif; ?>
						</div>
						<div class="actions fields-soh-target">
							<a class="fields-icon fields-icon-cancel dark remove-attachment" data-id="<?php echo $post->ID; ?>" href="#"></a>
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php endif; ?>
			
			
		</div>
		
		<div class="fields-gallery-toolbar">
			
			<ul class="fields-hl">
				<li>
					<a href="#" class="fields-button blue add-attachment"><?php _e('Add to gallery', 'fields'); ?></a>
				</li>
				<li class="fields-fr">
					<select class="bulk-actions">
						<option value=""><?php _e('Bulk actions', 'fields'); ?></option>
						<option value="date"><?php _e('Sort by date uploaded', 'fields'); ?></option>
						<option value="modified"><?php _e('Sort by date modified', 'fields'); ?></option>
						<option value="title"><?php _e('Sort by title', 'fields'); ?></option>
						<option value="reverse"><?php _e('Reverse current order', 'fields'); ?></option>
					</select>
				</li>
			</ul>
			
		</div>
		
	</div>
	
	<div class="fields-gallery-side">
	<div class="fields-gallery-side-inner">
			
		<div class="fields-gallery-side-data"></div>
						
		<div class="fields-gallery-toolbar">
			
			<ul class="fields-hl">
				<li>
					<a href="#" class="fields-button close-sidebar"><?php _e('Close', 'fields'); ?></a>
				</li>
				<li class="fields-fr">
					<a class="fields-button blue update-attachment"><?php _e('Update', 'fields'); ?></a>
				</li>
			</ul>
			
		</div>
		
	</div>	
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
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// clear numeric settings
		$clear = array(
			'min',
			'max',
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) {
				
				$field[$k] = '';
				
			}
			
		}
		
		
		// min
		fields_render_field_setting( $field, array(
			'label'			=> __('Minimum Selection','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min'
		));
		
		
		// max
		fields_render_field_setting( $field, array(
			'label'			=> __('Maximum Selection','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max'
		));
		
		
		// preview_size
		fields_render_field_setting( $field, array(
			'label'			=> __('Preview Size','fields'),
			'instructions'	=> __('Shown when entering data','fields'),
			'type'			=> 'select',
			'name'			=> 'preview_size',
			'choices'		=> fields_get_image_sizes()
		));
		
		
		// library
		fields_render_field_setting( $field, array(
			'label'			=> __('Library','fields'),
			'instructions'	=> __('Limit the media library choice','fields'),
			'type'			=> 'radio',
			'name'			=> 'library',
			'layout'		=> 'horizontal',
			'choices' 		=> array(
				'all'			=> __('All', 'fields'),
				'uploadedTo'	=> __('Uploaded to post', 'fields')
			)
		));
		
		
		// min
		fields_render_field_setting( $field, array(
			'label'			=> __('Minimum','fields'),
			'instructions'	=> __('Restrict which images can be uploaded','fields'),
			'type'			=> 'text',
			'name'			=> 'min_width',
			'prepend'		=> __('Width', 'fields'),
			'append'		=> 'px',
		));
		
		fields_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_height',
			'prepend'		=> __('Height', 'fields'),
			'append'		=> 'px',
			'wrapper'		=> array(
				'data-append' => 'min_width'
			)
		));
		
		fields_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('File size', 'fields'),
			'append'		=> 'MB',
			'wrapper'		=> array(
				'data-append' => 'min_width'
			)
		));	
		
		
		// max
		fields_render_field_setting( $field, array(
			'label'			=> __('Maximum','fields'),
			'instructions'	=> __('Restrict which images can be uploaded','fields'),
			'type'			=> 'text',
			'name'			=> 'max_width',
			'prepend'		=> __('Width', 'fields'),
			'append'		=> 'px',
		));
		
		fields_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_height',
			'prepend'		=> __('Height', 'fields'),
			'append'		=> 'px',
			'wrapper'		=> array(
				'data-append' => 'max_width'
			)
		));
		
		fields_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('File size', 'fields'),
			'append'		=> 'MB',
			'wrapper'		=> array(
				'data-append' => 'max_width'
			)
		));	
		
		
		// allowed type
		fields_render_field_setting( $field, array(
			'label'			=> __('Allowed file types','fields'),
			'instructions'	=> __('Comma separated list. Leave blank for all types','fields'),
			'type'			=> 'text',
			'name'			=> 'mime_types',
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
			
			// return false as $value may be '' (from DB) which doesn't make much sense
			return false;
		
		}
		
		
		// get posts
		$posts = fields_get_posts(array(
			'post_type'	=> 'attachment',
			'post__in'	=> $value,
		));
		
		
		
		// update value to include $post
		foreach( array_keys($posts) as $i ) {
			
			$posts[ $i ] = fields_get_attachment( $posts[ $i ] );
			
		}
				
		
		// return
		return $posts;
		
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
		
		if( empty($value) || !is_array($value) ) {
		
			$value = array();
			
		}
		
		
		if( count($value) < $field['min'] ) {
		
			$valid = _n( '%s requires at least %s selection', '%s requires at least %s selections', $field['min'], 'fields' );
			$valid = sprintf( $valid, $field['label'], $field['min'] );
			
		}
		
				
		return $valid;
		
	}

	
}

new fieldmaster_field_gallery();

endif;

?>
