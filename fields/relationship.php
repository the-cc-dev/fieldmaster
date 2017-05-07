<?php

/*
*  Fields API Relationship Field Class
*
*  All the logic for this field type
*
*  @class 		fields_field_relationship
*  @extends		fields_field
*  @package		Fields API
*  @subpackage	Fields
*/

if( ! class_exists('fields_field_relationship') ) :

class fields_field_relationship extends fields_field {
	
	
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
		$this->name = 'relationship';
		$this->label = __("Relationship",'fields');
		$this->category = 'relational';
		$this->defaults = array(
			'post_type'			=> array(),
			'taxonomy'			=> array(),
			'min' 				=> 0,
			'max' 				=> 0,
			'filters'			=> array('search', 'post_type', 'taxonomy'),
			'elements' 			=> array(),
			'return_format'		=> 'object'
		);
		$this->l10n = array(
			'min'		=> __("Minimum values reached ( {min} values )",'fields'),
			'max'		=> __("Maximum values reached ( {max} values )",'fields'),
			'loading'	=> __('Loading','fields'),
			'empty'		=> __('No matches found','fields'),
		);
		
		
		// extra
		add_action('wp_ajax_fields/fields/relationship/query',			array($this, 'ajax_query'));
		add_action('wp_ajax_nopriv_fields/fields/relationship/query',	array($this, 'ajax_query'));
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  get_choices
	*
	*  This function will return an array of data formatted for use in a select2 AJAX response
	*
	*  @type	function
	*  @date	15/10/2014
	*  @since	5.0.9
	*
	*  @param	$options (array)
	*  @return	(array)
	*/
	
	function get_choices( $options = array() ) {
		
   		// defaults
   		$options = fields_parse_args($options, array(
			'post_id'			=> 0,
			's'					=> '',
			'post_type'			=> '',
			'taxonomy'			=> '',
			'lang'				=> false,
			'field_key'			=> '',
			'paged'				=> 1
		));
		
		
		// vars
   		$r = array();
   		$args = array();
   		
   		
   		// paged
   		$args['posts_per_page'] = 20;
   		$args['paged'] = $options['paged'];
   		
		
		// load field
		$field = fields_get_field( $options['field_key'] );
		
		if( !$field ) {
		
			return false;
			
		}
		
		
		// update $args
		if( !empty($options['post_type']) ) {
			
			$args['post_type'] = fields_get_array( $options['post_type'] );
		
		} elseif( !empty($field['post_type']) ) {
		
			$args['post_type'] = fields_get_array( $field['post_type'] );
			
		} else {
			
			$args['post_type'] = fields_get_post_types();
		}
		
		
		// update taxonomy
		$taxonomies = array();
		
		if( !empty($options['taxonomy']) ) {
			
			$term = fields_decode_taxonomy_term($options['taxonomy']);
			
			// append to $args
			$args['tax_query'] = array(
				
				array(
					'taxonomy'	=> $term['taxonomy'],
					'field'		=> 'slug',
					'terms'		=> $term['term'],
				)
				
			);
			
			
		} elseif( !empty($field['taxonomy']) ) {
			
			$taxonomies = fields_decode_taxonomy_terms( $field['taxonomy'] );
			
			// append to $args
			$args['tax_query'] = array();
			
			
			// now create the tax queries
			foreach( $taxonomies as $taxonomy => $terms ) {
			
				$args['tax_query'][] = array(
					'taxonomy'	=> $taxonomy,
					'field'		=> 'slug',
					'terms'		=> $terms,
				);
				
			}
			
		}	
		
		
		// search
		if( $options['s'] ) {
		
			$args['s'] = $options['s'];
			
		}
		
		
		// filters
		$args = apply_filters('fields/fields/relationship/query', $args, $field, $options['post_id']);
		$args = apply_filters('fields/fields/relationship/query/name=' . $field['name'], $args, $field, $options['post_id'] );
		$args = apply_filters('fields/fields/relationship/query/key=' . $field['key'], $args, $field, $options['post_id'] );
		
		
		// get posts grouped by post type
		$groups = fields_get_grouped_posts( $args );
		
		if( !empty($groups) ) {
			
			foreach( array_keys($groups) as $group_title ) {
				
				// vars
				$posts = fields_extract_var( $groups, $group_title );
				$titles = array();
				
				
				// data
				$data = array(
					'text'		=> $group_title,
					'children'	=> array()
				);
				
				
				foreach( array_keys($posts) as $post_id ) {
					
					// override data
					$posts[ $post_id ] = $this->get_post_title( $posts[ $post_id ], $field, $options['post_id'] );
					
				};
				
				
				// order by search
				if( !empty($args['s']) ) {
					
					$posts = fields_order_by_search( $posts, $args['s'] );
					
				}
				
				
				// append to $data
				foreach( array_keys($posts) as $post_id ) {
					
					$data['children'][] = array(
						'id'	=> $post_id,
						'text'	=> $posts[ $post_id ]
					);
					
				}
				
				
				// append to $r
				$r[] = $data;
				
			}
			
			
			// add as optgroup or results
			if( count($args['post_type']) == 1 ) {
				
				$r = $r[0]['children'];
				
			}
			
		}
		
		
		// return
		return $r;
			
	}
	
	
	/*
	*  ajax_query
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
	
	function ajax_query() {
		
		// validate
		if( !fields_verify_ajax() ) {
		
			die();
			
		}
		
		
		// get posts
		$posts = $this->get_choices( $_POST );
		
		
		// validate
		if( !$posts ) {
			
			die();
			
		}
		
		
		// return JSON
		echo json_encode( $posts );
		die();
			
	}
	
	
	/*
	*  get_post_title
	*
	*  This function returns the HTML for a result
	*
	*  @type	function
	*  @date	1/11/2013
	*  @since	5.0.0
	*
	*  @param	$post (object)
	*  @param	$field (array)
	*  @param	$post_id (int) the post_id to which this value is saved to
	*  @return	(string)
	*/
	
	function get_post_title( $post, $field, $post_id = 0 ) {
		
		// get post_id
		if( !$post_id ) {
			
			$post_id = fields_get_setting('form_data/post_id', get_the_ID());
			
		}
		
		
		// vars
		$title = fields_get_post_title( $post );
		
		
		// elements
		if( !empty($field['elements']) ) {
			
			if( in_array('featured_image', $field['elements']) ) {
				
				$image = '';
				
				if( $post->post_type == 'attachment' ) {
					
					$image = wp_get_attachment_image( $post->ID, array(17, 17) );
					
				} else {
					
					$image = get_the_post_thumbnail( $post->ID, array(17, 17) );
					
				}
				
				
				$title = '<div class="thumbnail">' . $image . '</div>' . $title;
			}
			
		}
		
		
		// filters
		$title = apply_filters('fields/fields/relationship/result', $title, $post, $field, $post_id);
		$title = apply_filters('fields/fields/relationship/result/name=' . $field['_name'], $title, $post, $field, $post_id);
		$title = apply_filters('fields/fields/relationship/result/key=' . $field['key'], $title, $post, $field, $post_id);
		
		
		// return
		return $title;
		
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
		
		// vars
		$values = array();
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> "fields-relationship {$field['class']}",
			'data-min'			=> $field['min'],
			'data-max'			=> $field['max'],
			'data-s'			=> '',
			'data-post_type'	=> '',
			'data-taxonomy'		=> '',
			'data-paged'		=> 1,
		);
		
		
		// Lang
		if( defined('ICL_LANGUAGE_CODE') ) {
		
			$atts['data-lang'] = ICL_LANGUAGE_CODE;
			
		}
		
		
		// data types
		$field['post_type'] = fields_get_array( $field['post_type'] );
		$field['taxonomy'] = fields_get_array( $field['taxonomy'] );
		
		
		// width for select filters
		$width = array(
			'search'	=> 0,
			'post_type'	=> 0,
			'taxonomy'	=> 0
		);
		
		if( !empty($field['filters']) ) {
			
			$width = array(
				'search'	=> 50,
				'post_type'	=> 25,
				'taxonomy'	=> 25
			);
			
			foreach( array_keys($width) as $k ) {
				
				if( ! in_array($k, $field['filters']) ) {
				
					$width[ $k ] = 0;
					
				}
				
			}
			
			
			// search
			if( $width['search'] == 0 ) {
			
				$width['post_type'] = ( $width['post_type'] == 0 ) ? 0 : 50;
				$width['taxonomy'] = ( $width['taxonomy'] == 0 ) ? 0 : 50;
				
			}
			
			// post_type
			if( $width['post_type'] == 0 ) {
			
				$width['taxonomy'] = ( $width['taxonomy'] == 0 ) ? 0 : 50;
				
			}
			
			
			// taxonomy
			if( $width['taxonomy'] == 0 ) {
			
				$width['post_type'] = ( $width['post_type'] == 0 ) ? 0 : 50;
				
			}
			
			
			// search
			if( $width['post_type'] == 0 && $width['taxonomy'] == 0 ) {
			
				$width['search'] = ( $width['search'] == 0 ) ? 0 : 100;
				
			}
		}
		
		
		// post type filter
		$post_types = array();
		
		if( $width['post_type'] ) {
			
			if( !empty($field['post_type']) ) {
			
				$post_types = $field['post_type'];
	
	
			} else {
				
				$post_types = fields_get_post_types();
				
			}
			
			$post_types = fields_get_pretty_post_types($post_types);
			
		}
		
		
		// taxonomy filter
		$taxonomies = array();
		$term_groups = array();
		
		if( $width['taxonomy'] ) {
			
			// taxonomies
			if( !empty($field['taxonomy']) ) {
				
				// get the field's terms
				$term_groups = fields_get_array( $field['taxonomy'] );
				$term_groups = fields_decode_taxonomy_terms( $term_groups );
				
				
				// update taxonomies
				$taxonomies = array_keys($term_groups);
			
			} elseif( !empty($field['post_type']) ) {
				
				// loop over post types and find connected taxonomies
				foreach( $field['post_type'] as $post_type ) {
					
					$post_taxonomies = get_object_taxonomies( $post_type );
					
					// bail early if no taxonomies
					if( empty($post_taxonomies) ) {
						
						continue;
						
					}
						
					foreach( $post_taxonomies as $post_taxonomy ) {
						
						if( !in_array($post_taxonomy, $taxonomies) ) {
							
							$taxonomies[] = $post_taxonomy;
							
						}
						
					}
								
				}
				
			} else {
				
				$taxonomies = fields_get_taxonomies();
				
			}
			
			
			// terms
			$term_groups = fields_get_taxonomy_terms( $taxonomies );
			
			
			// update $term_groups with specific terms
			if( !empty($field['taxonomy']) ) {
				
				foreach( array_keys($term_groups) as $taxonomy ) {
					
					foreach( array_keys($term_groups[ $taxonomy ]) as $term ) {
						
						if( ! in_array($term, $field['taxonomy']) ) {
							
							unset($term_groups[ $taxonomy ][ $term ]);
							
						}
						
					}
					
				}
				
			}
			
		}
		// end taxonomy filter
			
		?>
<div <?php fields_esc_attr_e($atts); ?>>
	
	<div class="fields-hidden">
		<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
	</div>
	
	<?php if( $width['search'] || $width['post_type'] || $width['taxonomy'] ): ?>
	<div class="filters">
		
		<ul class="fields-hl">
		
			<?php if( $width['search'] ): ?>
			<li style="width:<?php echo $width['search']; ?>%;">
				<div class="inner">
				<input class="filter" data-filter="s" placeholder="<?php _e("Search...",'fields'); ?>" type="text" />
				</div>
			</li>
			<?php endif; ?>
			
			<?php if( $width['post_type'] ): ?>
			<li style="width:<?php echo $width['post_type']; ?>%;">
				<div class="inner">
				<select class="filter" data-filter="post_type">
					<option value=""><?php _e('Select post type','fields'); ?></option>
					<?php foreach( $post_types as $k => $v ): ?>
						<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select>
				</div>
			</li>
			<?php endif; ?>
			
			<?php if( $width['taxonomy'] ): ?>
			<li style="width:<?php echo $width['taxonomy']; ?>%;">
				<div class="inner">
				<select class="filter" data-filter="taxonomy">
					<option value=""><?php _e('Select taxonomy','fields'); ?></option>
					<?php foreach( $term_groups as $k_opt => $v_opt ): ?>
						<optgroup label="<?php echo $k_opt; ?>">
							<?php foreach( $v_opt as $k => $v ): ?>
								<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
				</div>
			</li>
			<?php endif; ?>
		</ul>
		
	</div>
	<?php endif; ?>
	
	<div class="selection fields-cf">
	
		<div class="choices">
		
			<ul class="fields-bl list"></ul>
			
		</div>
		
		<div class="values">
		
			<ul class="fields-bl list">
			
				<?php if( !empty($field['value']) ): 
					
					// get posts
					$posts = fields_get_posts(array(
						'post__in' => $field['value'],
						'post_type'	=> $field['post_type']
					));
					
					
					// set choices
					if( !empty($posts) ):
						
						foreach( array_keys($posts) as $i ):
							
							// vars
							$post = fields_extract_var( $posts, $i );
							
							
							?><li>
								<input type="hidden" name="<?php echo $field['name']; ?>[]" value="<?php echo $post->ID; ?>" />
								<span data-id="<?php echo $post->ID; ?>" class="fields-rel-item">
									<?php echo $this->get_post_title( $post, $field ); ?>
									<a href="#" class="fields-icon fields-icon-minus small dark" data-name="remove_item"></a>
								</span>
							</li><?php
							
						endforeach;
						
					endif;
				
				endif; ?>
				
			</ul>
			
			
			
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
		
		// vars
		$field['min'] = empty($field['min']) ? '' : $field['min'];
		$field['max'] = empty($field['max']) ? '' : $field['max'];
		
		
		// post_type
		fields_render_field_setting( $field, array(
			'label'			=> __('Filter by Post Type','fields'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'post_type',
			'choices'		=> fields_get_pretty_post_types(),
			'multiple'		=> 1,
			'ui'			=> 1,
			'allow_null'	=> 1,
			'placeholder'	=> __("All post types",'fields'),
		));
		
		
		// taxonomy
		fields_render_field_setting( $field, array(
			'label'			=> __('Filter by Taxonomy','fields'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'taxonomy',
			'choices'		=> fields_get_taxonomy_terms(),
			'multiple'		=> 1,
			'ui'			=> 1,
			'allow_null'	=> 1,
			'placeholder'	=> __("All taxonomies",'fields'),
		));
		
		
		// filters
		fields_render_field_setting( $field, array(
			'label'			=> __('Filters','fields'),
			'instructions'	=> '',
			'type'			=> 'checkbox',
			'name'			=> 'filters',
			'choices'		=> array(
				'search'		=> __("Search",'fields'),
				'post_type'		=> __("Post Type",'fields'),
				'taxonomy'		=> __("Taxonomy",'fields'),
			),
		));
		
		
		// filters
		fields_render_field_setting( $field, array(
			'label'			=> __('Elements','fields'),
			'instructions'	=> __('Selected elements will be displayed in each result','fields'),
			'type'			=> 'checkbox',
			'name'			=> 'elements',
			'choices'		=> array(
				'featured_image'	=> __("Featured Image",'fields'),
			),
		));
		
		
		// min
		fields_render_field_setting( $field, array(
			'label'			=> __('Minimum posts','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
		));
		
		
		// max
		fields_render_field_setting( $field, array(
			'label'			=> __('Maximum posts','fields'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
		));
		
		
		
		
		// return_format
		fields_render_field_setting( $field, array(
			'label'			=> __('Return Format','fields'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'choices'		=> array(
				'object'		=> __("Post Object",'fields'),
				'id'			=> __("Post ID",'fields'),
			),
			'layout'	=>	'horizontal',
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
		
		
		// force value to array
		$value = fields_get_array( $value );
		
		
		// convert to int
		$value = array_map('intval', $value);
		
		
		// load posts if needed
		if( $field['return_format'] == 'object' ) {
			
			// get posts
			$value = fields_get_posts(array(
				'post__in' => $value,
				'post_type'	=> $field['post_type']
			));
			
		}
		
		
		// return
		return $value;
		
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
		
		// default
		if( empty($value) || !is_array($value) ) {
		
			$value = array();
			
		}
		
		
		// min
		if( count($value) < $field['min'] ) {
		
			$valid = _n( '%s requires at least %s selection', '%s requires at least %s selections', $field['min'], 'fields' );
			$valid = sprintf( $valid, $field['label'], $field['min'] );
			
		}
		
		
		// return		
		return $valid;
		
	}
		
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// validate
		if( empty($value) ) {
			
			return $value;
			
		}
		
		
		// force value to array
		$value = fields_get_array( $value );
		
					
		// array
		foreach( $value as $k => $v ){
		
			// object?
			if( is_object($v) && isset($v->ID) ) {
			
				$value[ $k ] = $v->ID;
				
			}
			
		}
		
		
		// save value as strings, so we can clearly search for them in SQL LIKE statements
		$value = array_map('strval', $value);
		
	
		// return
		return $value;
		
	}
		
}

new fields_field_relationship();

endif;

?>
