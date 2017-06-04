<?php
/*
Plugin Name: FieldMaster
Plugin URI: https://goldhat.ca
Description: FieldMaster for create field interfaces and data storage in WordPress.
Version: 6.0.0
Author: GoldHat Group
Author URI: https://goldhat.ca
Copyright: GoldHat Group, Elliot Condon
Text Domain: fieldmaster
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fields') ) :

class fieldsAPI {

	// vars
	var $settings;


	/*
	*  __construct
	*
	*  A dummy constructor to ensure FieldMaster is only initialized once
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct() {

		/* Do nothing here */

	}


	/*
	*  initialize
	*
	*  The real constructor to initialize FieldMaster
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function initialize() {

		// vars
		$this->settings = array(

			// basic
			'name'				=> __('FieldMaster', 'fields'),
			'version'			=> '6.0.0',

			// urls
			'basename'			=> plugin_basename( __FILE__ ),
			'path'					=> plugin_dir_path( __FILE__ ),
			'dir'						=> plugin_dir_url( __FILE__ ),

			// options
			'show_admin'		=> true,
			'show_updates'	=> true,
			'stripslashes'	=> false,
			'local'				  => true,
			'json'				  => true,
			'save_json'			=> '',
			'load_json'			=> array(),
			'default_language'	=> '',
			'current_language'	=> '',
			'capability'		=> 'manage_options',
			'uploader'			=> 'wp',
			'autoload'			=> false
		);

		// include helpers
		include_once('api/api-helpers.php');

		// api
		fields_include('api/api-value.php');
		fields_include('api/api-field.php');
		fields_include('api/api-field-group.php');
		fields_include('api/api-template.php');
		fields_include('api/api-pro.php');
		fields_include('api/api-options-page.php');

		// core
		fields_include('core/ajax.php');
		fields_include('core/field.php');
		fields_include('core/input.php');
		fields_include('core/json.php');
		fields_include('core/local.php');
		fields_include('core/location.php');
		fields_include('core/media.php');
		fields_include('core/revisions.php');
		fields_include('core/compatibility.php');
		fields_include('core/third_party.php');


		// forms
		fields_include('forms/attachment.php');
		fields_include('forms/comment.php');
		fields_include('forms/post.php');
		fields_include('forms/taxonomy.php');
		fields_include('forms/user.php');
		fields_include('forms/widget.php');


		// admin
		if( is_admin() ) {

			fields_include('admin/admin.php');
			fields_include('admin/field-group.php');
			fields_include('admin/field-groups.php');
			fields_include('admin/update.php');
			fields_include('admin/settings-tools.php');
			fields_include('admin/settings-info.php');
			fields_include('admin/options-page.php');
			fields_include('admin/settings-updates.php');

		}

		// fields
		fields_include('fields/text.php');
		fields_include('fields/textarea.php');
		fields_include('fields/number.php');
		fields_include('fields/email.php');
		fields_include('fields/url.php');
		fields_include('fields/password.php');
		fields_include('fields/wysiwyg.php');
		fields_include('fields/oembed.php');
		fields_include('fields/image.php');
		fields_include('fields/file.php');
		fields_include('fields/select.php');
		fields_include('fields/checkbox.php');
		fields_include('fields/radio.php');
		fields_include('fields/true_false.php');
		fields_include('fields/post_object.php');
		fields_include('fields/page_link.php');
		fields_include('fields/relationship.php');
		fields_include('fields/taxonomy.php');
		fields_include('fields/user.php');
		fields_include('fields/google-map.php');
		fields_include('fields/date_picker.php');
		fields_include('fields/color_picker.php');
		fields_include('fields/message.php');
		fields_include('fields/tab.php');
		fields_include('fields/repeater.php');
		fields_include('fields/flexible-content.php');
		fields_include('fields/gallery.php');

		// actions
		add_action('init',			array($this, 'wp_init'), 5);
		add_filter('posts_where',	array($this, 'wp_posts_where'), 10, 2 );
		add_action('fields/input/admin_enqueue_scripts',			array($this, 'input_admin_enqueue_scripts'));
		add_action('fields/field_group/admin_enqueue_scripts',		array($this, 'field_group_admin_enqueue_scripts'));
		add_action('fields/field_group/admin_l10n',				array($this, 'field_group_admin_l10n'));

	}


	/*
	*  wp_init
	*
	*  This function will run on the WP init action and setup many things
	*
	*  @type	action (init)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function wp_init() {

		// vars
		$cap = fields_get_setting('capability');
		$version = fields_get_setting('version');
		$lang = get_locale();
		$scripts = array();
		$styles = array();
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// redeclare dir - allow another plugin to modify dir (maybe force SSL)
		fields_update_setting('dir', plugin_dir_url( __FILE__ ));

		// set text domain
		load_textdomain( 'fields', fields_get_path( 'lang/fields-' . get_locale() . '.mo' ) );

		// register post type 'fields-field-group'
		register_post_type('fields-field-group', array(
			'labels'			=> array(
			    'name'					=> __( 'Field Groups', 'fields' ),
				'singular_name'			=> __( 'Field Group', 'fields' ),
			    'add_new'				=> __( 'Add New' , 'fields' ),
			    'add_new_item'			=> __( 'Add New Field Group' , 'fields' ),
			    'edit_item'				=> __( 'Edit Field Group' , 'fields' ),
			    'new_item'				=> __( 'New Field Group' , 'fields' ),
			    'view_item'				=> __( 'View Field Group', 'fields' ),
			    'search_items'			=> __( 'Search Field Groups', 'fields' ),
			    'not_found'				=> __( 'No Field Groups found', 'fields' ),
			    'not_found_in_trash'	=> __( 'No Field Groups found in Trash', 'fields' ),
			),
			'public'			=> false,
			'show_ui'			=> true,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));


		// register post type 'fields-field'
		register_post_type('fields-field', array(
			'labels'			=> array(
			    'name'					=> __( 'Fields', 'fields' ),
				'singular_name'			=> __( 'Field', 'fields' ),
			    'add_new'				=> __( 'Add New' , 'fields' ),
			    'add_new_item'			=> __( 'Add New Field' , 'fields' ),
			    'edit_item'				=> __( 'Edit Field' , 'fields' ),
			    'new_item'				=> __( 'New Field' , 'fields' ),
			    'view_item'				=> __( 'View Field', 'fields' ),
			    'search_items'			=> __( 'Search Fields', 'fields' ),
			    'not_found'				=> __( 'No Fields found', 'fields' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'fields' ),
			),
			'public'			=> false,
			'show_ui'			=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));


		// register post status
		register_post_status('fields-disabled', array(
			'label'                     => __( 'Disabled', 'fields' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', 'fields' ),
		));


		// append scripts
		$scripts['select2'] = array(
			'src'	=> fields_get_dir("assets/inc/select2/select2{$min}.js"),
			'deps'	=> array('jquery')
		);

		$scripts['fields-input'] = array(
			'src'	=> fields_get_dir("assets/js/fields-input{$min}.js"),
			'deps'	=> array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-sortable',
				'jquery-ui-resizable',
				'jquery-ui-datepicker',
				'wp-color-picker',
				'select2'
			)
		);

		$scripts['fields-field-group'] = array(
			'src'	=> fields_get_dir("assets/js/fields-field-group{$min}.js"),
			'deps'	=> array('fields-input')
		);


		// select2-l10n
		if( $lang ) {

			// vars
			$lang = str_replace('_', '-', $lang);
			$lang_code = substr($lang, 0, 2);
			$src = '';


			// attempt 1
			if( file_exists(fields_get_path("assets/inc/select2/select2_locale_{$lang_code}.js")) ) {

				$src = fields_get_dir("assets/inc/select2/select2_locale_{$lang_code}.js");

			} elseif( file_exists(fields_get_path("assets/inc/select2/select2_locale_{$lang}.js")) ) {

				$src = fields_get_dir("assets/inc/select2/select2_locale_{$lang}.js");

			}


			// only append if file exists
			if( $src ) {

				// append script
				$scripts['select2-l10n'] = array(
					'src'	=> $src,
					'deps'	=> array('select2')
				);


				// append dep
				$scripts['fields-input']['deps'][] = 'select2-l10n';

			}

		}


		// register scripts
		foreach( $scripts as $handle => $script ) {

			wp_register_script( $handle, $script['src'], $script['deps'], $version );

		}


		// append styles
		$styles['select2'] = array(
			'src'		=> fields_get_dir('assets/inc/select2/select2.css'),
			'deps'		=> false
		);

		$styles['fields-datepicker'] = array(
			'src'		=> fields_get_dir('assets/inc/datepicker/jquery-ui-1.10.4.custom.min.css'),
			'deps'		=> false
		);

		$styles['fields-global'] = array(
			'src'		=> fields_get_dir('assets/css/fields-global.css'),
			'deps'		=> false
		);

		$styles['fields-input'] = array(
			'src'		=> fields_get_dir('assets/css/fields-input.css'),
			'deps'		=> array('fields-global', 'wp-color-picker', 'select2', 'fields-datepicker')
		);

		$styles['fields-field-group'] = array(
			'src'		=> fields_get_dir('assets/css/fields-field-group.css'),
			'deps'		=> array('fields-input')
		);


		// register styles
		foreach( $styles as $handle => $style ) {

			wp_register_style( $handle, $style['src'], $style['deps'], $version );

		}

		// min
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';


		// register scripts
		wp_register_script( 'fields-pro-input', fields_get_dir( "assets/js/fields-pro-input{$min}.js" ), false, fields_get_setting('version') );
		wp_register_script( 'fields-pro-field-group', fields_get_dir( "assets/js/fields-pro-field-group{$min}.js" ), false, fields_get_setting('version') );


		// register styles
		wp_register_style( 'fields-pro-input', fields_get_dir( 'assets/css/fields-pro-input.css' ), false, fields_get_setting('version') );
		wp_register_style( 'fields-pro-field-group', fields_get_dir( 'assets/css/fields-pro-field-group.css' ), false, fields_get_setting('version') );



		// complete loading of FieldMaster files
		$this->complete();

	}


	/*
	*  complete
	*
	*  This function will ensure all files are included
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function complete() {

		// bail early if actions have not passed 'plugins_loaded'
		// this allows all plugins / theme to hook in
		if( !did_action('plugins_loaded') ) {

			return;

		}


		// once run once
		if( fields_get_setting('complete') ) {

			return;

		}


		// update setting
		fields_update_setting('complete', true);


		// wpml
		if( defined('ICL_SITEPRESS_VERSION') ) {

			fields_include('core/wpml.php');

		}


		// include field types
		do_action('fields/include_field_types', 5);


		// include local fields
		do_action('fields/include_fields', 5);


		// final action
		do_action('fields/init');

	}


	/*
	*  wp_posts_where
	*
	*  This function will add in some new parameters to the WP_Query args allowing fields to be found via key / name
	*
	*  @type	filter
	*  @date	5/12/2013
	*  @since	5.0.0
	*
	*  @param	$where (string)
	*  @param	$wp_query (object)
	*  @return	$where (string)
	*/

	function wp_posts_where( $where, $wp_query ) {

		// global
		global $wpdb;


		// fieldmaster_field_key
		if( $field_key = $wp_query->get('fieldmaster_field_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );

	    }


	    // fieldmaster_field_name
	    if( $field_name = $wp_query->get('fieldmaster_field_name') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );

	    }


	    // fields_group_key
		if( $group_key = $wp_query->get('fields_group_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );

	    }


	    // return
	    return $where;

	}

	/*
	*  get_valid_field
	*
	*  This function will provide compatibility with FieldMaster4 fields
	*
	*  @type	function
	*  @date	23/04/2014
	*  @since	5.0.0
	*
	*  @param	$field (array)
	*  @return	$field
	*/

	function get_valid_field( $field ) {

		// extract old width
		$width = fields_extract_var( $field, 'column_width' );


		// if old width, update the new width
		if( $width ) {

			$field['wrapper']['width'] = $width;
		}


		// return
		return $field;

	}

	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function input_admin_enqueue_scripts() {

		// scripts
		wp_enqueue_script('fields-pro-input');


		// styles
		wp_enqueue_style('fields-pro-input');

	}


	/*
	*  field_group_admin_l10n
	*
	*  description
	*
	*  @type	function
	*  @date	1/05/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function field_group_admin_l10n( $l10n ) {

		// append
		$l10n['flexible_content'] = array(
			'layout_warning' => __('Flexible Content requires at least 1 layout','fields')
		);


		// return
		return $l10n;
	}


	/*
	*  field_group_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function field_group_admin_enqueue_scripts() {

		// scripts
		wp_enqueue_script('fields-pro-field-group');


		// styles
		wp_enqueue_style('fields-pro-field-group');

	}


	/*
	*  update_field
	*
	*  This function will attempt to modify the $field's parent value from a field_key into a post_id
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function update_field( $field ) {

		// bail ealry if not relevant
		if( !$field['parent'] || !fields_is_field_key($field['parent']) ) {

			return $field;

		}

		// vars
		$ref = 0;


		// create reference
		if( empty($this->ref) ) {

			$this->ref = array();

		}


		if( isset($this->ref[ $field['parent'] ]) ) {

			$ref = $this->ref[ $field['parent'] ];

		} else {

			// get parent without caching (important not to cache as parent $field will now contain new sub fields)
			$parent = fields_get_field( $field['parent'], true );


			// bail ealry if no parent
			if( !$parent ) {

				return $field;

			}


			// get ref
			$ref = $parent['ID'] ? $parent['ID'] : $parent['key'];


			// update ref
			$this->ref[ $field['parent'] ] = $ref;

		}


		// update field's parent
		$field['parent'] = $ref;


		// return
		return $field;

	}


	/*
	*  prepare_field_for_export
	*
	*  description
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function prepare_field_for_export( $field ) {

		// sub field (parent_layout)
		fields_extract_var( $field, 'parent_layout');


		// repeater
		if( $field['type'] == 'repeater' ) {

			$field['sub_fields'] = fields_prepare_fields_for_export( $field['sub_fields'] );

		// flexible content
		} elseif( $field['type'] == 'flexible_content' ) {

			foreach( $field['layouts'] as $l => $layout ) {

				$field['layouts'][ $l ]['sub_fields'] = fields_prepare_fields_for_export( $layout['sub_fields'] );

			}

		}


		// return
		return $field;

	}


	/*
	*  prepare_field_for_import
	*
	*  description
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function prepare_field_for_import( $field ) {

		// var
		$extra = array();


		// sub fields
		if( $field['type'] == 'repeater' ) {

			// extract sub fields
			$sub_fields = fields_extract_var( $field, 'sub_fields');


			// reset field setting
			$field['sub_fields'] = array();


			if( !empty($sub_fields) ) {

				foreach( array_keys($sub_fields) as $i ) {

					// extract sub field
					$sub_field = fields_extract_var( $sub_fields, $i );


					// attributes
					$sub_field['parent'] = $field['key'];


					// append to extra
					$extra[] = $sub_field;

				}

			}

		} elseif( $field['type'] == 'flexible_content' ) {

			// extract layouts
			$layouts = fields_extract_var( $field, 'layouts');


			// reset field setting
			$field['layouts'] = array();


			// validate layouts
			if( !empty($layouts) ) {

				// loop over layouts
				foreach( array_keys($layouts) as $i ) {

					// extract layout
					$layout = fields_extract_var( $layouts, $i );


					// get valid layout (fixes FieldMaster4 export code bug undefined index 'key')
					if( empty($layout['key']) ) {

						$layout['key'] = uniqid();

					}


					// extract sub fields
					$sub_fields = fields_extract_var( $layout, 'sub_fields');


					// validate sub fields
					if( !empty($sub_fields) ) {

						// loop over sub fields
						foreach( array_keys($sub_fields) as $j ) {

							// extract sub field
							$sub_field = fields_extract_var( $sub_fields, $j );


							// attributes
							$sub_field['parent'] = $field['key'];
							$sub_field['parent_layout'] = $layout['key'];


							// append to extra
							$extra[] = $sub_field;

						}

					}


					// append to layout
					$field['layouts'][] = $layout;

				}

			}

		}


		// extra
		if( !empty($extra) ) {

			array_unshift($extra, $field);

			return $extra;

		}


		// return
		return $field;

	}

}

	/*
	*  fields
	*
	*  The main function responsible for returning the one true fields Instance to functions everywhere.
	*  Use this function like you would a global variable, except without needing to declare the global.
	*
	*  Example: <?php $fields = fields(); ?>
	*
	*  @type	function
	*  @date	4/09/13
	*  @since	4.3.0
	*
	*  @param	N/A
	*  @return	(object)
	*/

	function fieldsAPI() {

		global $fieldsAPI;

		if( !isset($fieldsAPI) ) {

			$fieldsAPI = new fieldsAPI();
			$fieldsAPI->initialize();

		}

		return $fieldsAPI;

	}

endif; // class_exists check

// initialize
fieldsAPI();

?>
