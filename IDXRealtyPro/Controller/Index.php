<?php
/**
 * User: Paul Grejaldo
 * Date: 2016/10/08
 * Time: 8:35 PM
 */

namespace IDXRealtyPro\Controller;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Cli as HCli;
use IDXRealtyPro\Model\Option;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}

/**
 * Class Index
 *
 * @package IDXRealtyPro\Controller
 */
class Index {

	protected static $instance;

	public function __construct() {
	}

	/**
	 * Return an instance of this class
	 *
	 * @return Index The class instance object
	 */
	public static function instance() {
		null === self::$instance && self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Initialize plugin hook handlers
	 */
	public function init() {
		$this->actions();
		$this->filters();

		if ( defined( 'WP_CLI' ) && WP_CLI /*&& Plugin::isLicenseValid()*/ ) {
			// add 'idxrp' command
			\WP_CLI::add_command( HCli::getCommand(), 'IDXRealtyPro\Controller\Cli' );
		}

		// Register REST routes
		add_action( 'rest_api_init', [ $this, 'routes' ] );

		// Initialize actions/filters
		Admin::instance()->init();
		Front::instance()->init();
		ScSettings::instance()->init();
		Template::instance()->init();
	}

	/**
	 * General actions
	 */
	public function actions() {
		add_action( 'init', [ $this, 'registerPostTypes' ], 100 );
		add_action( 'init', [ $this, 'registerScripts' ] );
		add_action( 'widgets_init', [ $this, 'registerWidgets' ] );
	}

	/**
	 * General filters
	 */
	public function filters() {
		add_filter( 'auto_update_plugin', [ $this, 'autoUpdatePlugin' ], 10, 2 );
		add_action( 'upgrader_process_complete', [ $this, 'upgraderProcessComplete' ], 10, 2 );
	}

	/**
	 * Register global scripts/styles
	 */
	public function registerScripts() {
		//$plugin   = Plugin::getPluginData();
		$ext      = Plugin::getScriptPart( 'ext' );
		$file_dir = Plugin::getScriptPart( 'file-dir' );
		$version  = Plugin::getScriptPart( 'version' );

		wp_register_style(
			'idxrp-bootstrap',
			plugins_url(
				"css/bootstrap/idxrpbs{$ext}.css",
				IDX_REALTY_PRO_PLUGIN_FILE
			)
		);

		wp_register_style(
			'font-awesome',
			plugins_url(
				"assets/font-awesome/css/font-awesome{$ext}.css",
				IDX_REALTY_PRO_PLUGIN_FILE
			)
		);

		wp_register_style(
			'roboto-font',
			'https://fonts.googleapis.com/css?family=Roboto:300,400,500',
			[ 'font-awesome' ],
			null
		);

		wp_register_script(
			'idxrp-manifest',
			plugins_url( "js/{$file_dir}/manifest/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
			[ 'jquery', 'jquery-serialize-object' ],
			$version,
			true
		);

		$vendor_dir = 'vendor';
		if ( ! is_admin() ) {
			$vendor_dir = 'vendor-front';
		}
		wp_register_script(
			'idxrp-vendor-deps',
			plugins_url( "js/{$file_dir}/{$vendor_dir}/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
			[ 'idxrp-manifest' ],
			$version,
			true
		);
	}

	/**
	 * Register custom post types and taxonomies used by the plugin
	 */
	public function registerPostTypes() {
		$labels = [
			'name'               => sprintf(
				_x( '%s Shortcode Settings', 'post type general name', 'realty-idx-pro' ),
				Plugin::getPluginData()->Name
			),
			'singular_name'      => _x( 'SC Setting', 'post type singular name', 'realty-idx-pro' ),
			'menu_name'          => _x( 'SC Settings', 'admin menu', 'realty-idx-pro' ),
			'name_admin_bar'     => _x( 'SC Setting', 'add new on admin bar', 'realty-idx-pro' ),
			'add_new'            => _x( 'Add New', 'property', 'realty-idx-pro' ),
			'add_new_item'       => __( 'Add New SC Setting', 'realty-idx-pro' ),
			'new_item'           => __( 'New SC Setting', 'realty-idx-pro' ),
			'edit_item'          => __( 'Edit SC Setting', 'realty-idx-pro' ),
			'view_item'          => __( 'View SC Setting', 'realty-idx-pro' ),
			'all_items'          => __( 'All SC Settings', 'realty-idx-pro' ),
			'search_items'       => __( 'Search SC Settings', 'realty-idx-pro' ),
			'parent_item_colon'  => __( 'Parent SC Settings:', 'realty-idx-pro' ),
			'not_found'          => __( 'No shortcode settings found.', 'realty-idx-pro' ),
			'not_found_in_trash' => __( 'No shortcode settings found in Trash.', 'realty-idx-pro' )
		];

		$args = [
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => false,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => Plugin::getScSettingsPostType() ],
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => [ 'title' ]
		];

		register_post_type( Plugin::getScSettingsPostType(), $args );
		//endregion

		$labels = [
			'name'               => _x( 'IDXRP Templates', 'post type general name', 'realty-idx-pro' ),
			'singular_name'      => _x( 'Template', 'post type singular name', 'realty-idx-pro' ),
			'menu_name'          => _x( 'Templates', 'admin menu', 'realty-idx-pro' ),
			'name_admin_bar'     => _x( 'Template', 'add new on admin bar', 'realty-idx-pro' ),
			'add_new'            => _x( 'Add New', 'property', 'realty-idx-pro' ),
			'add_new_item'       => __( 'Add New Template', 'realty-idx-pro' ),
			'new_item'           => __( 'New Template', 'realty-idx-pro' ),
			'edit_item'          => __( 'Edit Template', 'realty-idx-pro' ),
			'view_item'          => __( 'View Template', 'realty-idx-pro' ),
			'all_items'          => __( 'All Templates', 'realty-idx-pro' ),
			'search_items'       => __( 'Search Templates', 'realty-idx-pro' ),
			'parent_item_colon'  => __( 'Parent Templates:', 'realty-idx-pro' ),
			'not_found'          => __( 'No templates found.', 'realty-idx-pro' ),
			'not_found_in_trash' => __( 'No templates found in Trash.', 'realty-idx-pro' )
		];

		$args = [
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => false,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => Plugin::getTemplatePostType() ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => [
				'title',
				'editor',
				'revisions',
				'author',
				'custom-fields',
			]
		];

		register_post_type( Plugin::getTemplatePostType(), $args );

		$post_type = Option::instance()->get( 'post_type' );
		if ( ! empty( $post_type['name'] ) && ! empty( $post_type['singular_name'] ) && ! empty( $post_type['slug'] ) ) {
			$labels = [
				'name'               => $post_type['name'],
				'singular_name'      => $post_type['singular_name'],
				'menu_name'          => $post_type['name'],
				'name_admin_bar'     => sprintf(
					_x( '%s', 'add new on admin bar', 'realty-idx-pro' ),
					$post_type['singular_name']
				),
				'add_new'            => _x( 'Add New', 'property', 'realty-idx-pro' ),
				'add_new_item'       => sprintf(
					__( 'Add New Task', 'realty-idx-pro' ),
					$post_type['singular_name']
				),
				'new_item'           => sprintf( __( 'New %s', 'realty-idx-pro' ), $post_type['singular_name'] ),
				'edit_item'          => sprintf( __( 'Edit %s', 'realty-idx-pro' ), $post_type['singular_name'] ),
				'view_item'          => sprintf( __( 'View %s', 'realty-idx-pro' ), $post_type['singular_name'] ),
				'all_items'          => sprintf( __( 'All %s', 'realty-idx-pro' ), $post_type['name'] ),
				'search_items'       => sprintf( __( 'Search %s', 'realty-idx-pro' ), $post_type['name'] ),
				'parent_item_colon'  => sprintf( __( 'Parent %s:', 'realty-idx-pro' ), $post_type['name'] ),
				'not_found'          => sprintf(
					__( 'No %s found.', 'realty-idx-pro' ),
					strtolower( $post_type['name'] )
				),
				'not_found_in_trash' => sprintf(
					__( 'No %s found in Trash.', 'realty-idx-pro' ),
					strtolower( $post_type['name'] )
				),
			];

			$args = [
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => $post_type['slug'] ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => [
					'title',
					'editor',
					'comments',
					'revisions',
					/*'trackbacks',*/
					'author',
					'excerpt',
					'page-attributes',
					'thumbnail',
					'custom-fields',
					'post-formats'
				]
			];

			register_post_type( $post_type['slug'], $args );

			$labels = [
				'name'              => _x( 'Server ID', 'taxonomy general name', 'realty-idx-pro' ),
				'singular_name'     => _x( 'Server ID', 'taxonomy singular name', 'realty-idx-pro' ),
				'search_items'      => __( 'Search Server ID', 'realty-idx-pro' ),
				'all_items'         => __( 'All Server ID', 'realty-idx-pro' ),
				'parent_item'       => __( 'Parent Server ID', 'realty-idx-pro' ),
				'parent_item_colon' => __( 'Parent Server ID:', 'realty-idx-pro' ),
				'edit_item'         => __( 'Edit Server ID', 'realty-idx-pro' ),
				'update_item'       => __( 'Update Server ID', 'realty-idx-pro' ),
				'add_new_item'      => __( 'Add New Server ID', 'realty-idx-pro' ),
				'new_item_name'     => __( 'New Server ID Name', 'realty-idx-pro' ),
				'menu_name'         => __( 'Server ID', 'realty-idx-pro' ),
				'not_found'         => __( 'No Server ID Found', 'realty-idx-pro' )
			];

			$args = [
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			];

			register_taxonomy( Plugin::getServerIdTaxonomy(), $post_type['slug'], $args );

			$labels = [
				'name'              => _x( 'Resource ID', 'taxonomy general name', 'realty-idx-pro' ),
				'singular_name'     => _x( 'Resource ID', 'taxonomy singular name', 'realty-idx-pro' ),
				'search_items'      => __( 'Search Resource ID', 'realty-idx-pro' ),
				'all_items'         => __( 'All Resource ID', 'realty-idx-pro' ),
				'parent_item'       => __( 'Parent Resource ID', 'realty-idx-pro' ),
				'parent_item_colon' => __( 'Parent Resource ID:', 'realty-idx-pro' ),
				'edit_item'         => __( 'Edit Resource ID', 'realty-idx-pro' ),
				'update_item'       => __( 'Update Resource ID', 'realty-idx-pro' ),
				'add_new_item'      => __( 'Add New Resource ID', 'realty-idx-pro' ),
				'new_item_name'     => __( 'New Resource ID Name', 'realty-idx-pro' ),
				'menu_name'         => __( 'Resource ID', 'realty-idx-pro' ),
				'not_found'         => __( 'No Resource ID Found', 'realty-idx-pro' )
			];

			$args = [
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			];

			register_taxonomy( Plugin::getResourceIdTaxonomy(), $post_type['slug'], $args );

			$labels = [
				'name'              => _x( 'Class Name', 'taxonomy general name', 'realty-idx-pro' ),
				'singular_name'     => _x( 'Class Name', 'taxonomy singular name', 'realty-idx-pro' ),
				'search_items'      => __( 'Search Class Name', 'realty-idx-pro' ),
				'all_items'         => __( 'All Class Name', 'realty-idx-pro' ),
				'parent_item'       => __( 'Parent Class Name', 'realty-idx-pro' ),
				'parent_item_colon' => __( 'Parent Class Name:', 'realty-idx-pro' ),
				'edit_item'         => __( 'Edit Class Name', 'realty-idx-pro' ),
				'update_item'       => __( 'Update Class Name', 'realty-idx-pro' ),
				'add_new_item'      => __( 'Add New Class Name', 'realty-idx-pro' ),
				'new_item_name'     => __( 'New Class Name Name', 'realty-idx-pro' ),
				'menu_name'         => __( 'Class Name', 'realty-idx-pro' ),
				'not_found'         => __( 'No Class Name Found', 'realty-idx-pro' )
			];

			$args = [
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			];

			register_taxonomy( Plugin::getClassNameTaxonomy(), $post_type['slug'], $args );

			$labels = [
				'name'              => _x( 'Server Key', 'taxonomy general name', 'realty-idx-pro' ),
				'singular_name'     => _x( 'Server Key', 'taxonomy singular name', 'realty-idx-pro' ),
				'search_items'      => __( 'Search Server Key', 'realty-idx-pro' ),
				'all_items'         => __( 'All Server Key', 'realty-idx-pro' ),
				'parent_item'       => __( 'Parent Server Key', 'realty-idx-pro' ),
				'parent_item_colon' => __( 'Parent Server Key:', 'realty-idx-pro' ),
				'edit_item'         => __( 'Edit Server Key', 'realty-idx-pro' ),
				'update_item'       => __( 'Update Server Key', 'realty-idx-pro' ),
				'add_new_item'      => __( 'Add New Server Key', 'realty-idx-pro' ),
				'new_item_name'     => __( 'New Server Key Name', 'realty-idx-pro' ),
				'menu_name'         => __( 'Server Key', 'realty-idx-pro' ),
				'not_found'         => __( 'No Server Key Found', 'realty-idx-pro' )
			];

			$args = [
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			];

			register_taxonomy( Plugin::getServerKeyTaxonomy(), $post_type['slug'], $args );

			$taxonomies = Plugin::getRegisteredTaxonomies();
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy => $field_name ) {
					if ( ! taxonomy_exists( $taxonomy ) ) {
						$labels = [
							'name'              => $field_name,
							'singular_name'     => $field_name,
							'search_items'      => sprintf(
								__( 'Search %s', 'realty-idx-pro' ),
								$field_name
							),
							'all_items'         => sprintf(
								__( 'All %s', 'realty-idx-pro' ),
								$field_name
							),
							'parent_item'       => sprintf(
								__( 'Parent %s', 'realty-idx-pro' ),
								$field_name
							),
							'parent_item_colon' => sprintf(
								__( 'Parent %s:', 'realty-idx-pro' ),
								$field_name
							),
							'edit_item'         => sprintf(
								__( 'Edit %s', 'realty-idx-pro' ),
								$field_name
							),
							'update_item'       => sprintf(
								__( 'Update %s', 'realty-idx-pro' ),
								$field_name
							),
							'add_new_item'      => sprintf(
								__( 'Add New %s', 'realty-idx-pro' ),
								$field_name
							),
							'new_item_name'     => sprintf(
								__( 'New %s Name', 'realty-idx-pro' ),
								$field_name
							),
							'menu_name'         => $field_name,
							'not_found'         => sprintf(
								__( 'No %s Found', 'realty-idx-pro' ),
								$field_name
							),
						];

						$args = [
							'hierarchical'      => false,
							'labels'            => $labels,
							'description'       => '',
							'public'            => true,
							'show_ui'           => true,
							'show_admin_column' => false,
							'query_var'         => true,
							'show_in_rest'      => false,
							'rewrite'           => [
								'with_front' => false,
							]
						];

						register_taxonomy( $taxonomy, $post_type['slug'], $args );
					}
				}
			}
		}
	}

	/**
	 * Auto-update plugin
	 *
	 * @param bool   $update
	 * @param object $item
	 *
	 * @return bool
	 */
	public function autoUpdatePlugin( $update, $item ) {
		$auto_update = intval( Option::instance()->get( 'general', 'auto_update_plugin' ) );
		if ( $auto_update && dirname( plugin_basename( IDX_REALTY_PRO_PLUGIN_FILE ) ) === $item->slug ) {
			$new_version = $item->new_version;
			$old_version = Plugin::getPluginData()->Version;
			$pos         = 1;

			$new = explode( ".", $new_version );
			$old = explode( ".", $old_version );

			//check if it's a major version update
			$is_major_update = version_compare( $new[ $pos ], $old[ $pos ], '>' ) ||
			                   version_compare( intval( $new_version ), intval( $old_version ), '>' );

			//check if it's a minor update
			$is_minor_update = ( ! $is_major_update && version_compare(
					strstr( $new_version, '.' ),
					strstr( $old_version, '.' ),
					'>'
				) );

			$update = $is_minor_update;
		}

		return $update;
	}

	/**
	 * Download default template files after plugin upgrade
	 *
	 * @param \Plugin_Upgrader $plugin_upgrader
	 * @param array            $data
	 */
	public function upgraderProcessComplete( $plugin_upgrader, $data ) {
		if ( 'plugin' !== $data['type'] || 'install' === $data['action'] ||
		     ( is_array( $data['plugins'] ) && ! in_array( 'realty-idx-pro/main.php', $data['plugins'] ) ) ) {
			return;
		}

		\IDXRealtyPro\Model\ScSettings::instance()->downloadAppsDefaultTemplate();
	}

	/**
	 * Register plugin widgets
	 */
	public function registerWidgets() {
		register_widget( '\IDXRealtyPro\Widget\Featured' );
	}

	/**
	 * WP REST API routes for admin
	 */
	public function routes() {
		$route_classes = [
			'\IDXRealtyPro\Route\Admin\Overview',
			'\IDXRealtyPro\Route\Admin\License',
			'\IDXRealtyPro\Route\Admin\Settings',
		];
		foreach ( $route_classes as $route_class ) {
			$route = new $route_class();
			$route->register_routes();
		}
	}
}
