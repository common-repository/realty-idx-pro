<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/12
 * Time: 6:02 PM
 */

namespace IDXRealtyPro\Controller;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Shortcodes;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;
use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\ScSettings as ScSettingsModel;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Front
{

    protected static $instance;

    protected $print_templates;

    protected $app_settings;

    public function __construct()
    {
        $this->print_templates = [];
        $this->app_settings    = [];
    }

    /**
     * Return an instance of this class
     *
     * @return Front The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Initialize plugin hook handlers
     */
    public function init()
    {
        $this->actions();
        $this->filters();
        Shortcodes::init();
    }

    protected function actions()
    {
        add_action( 'after_setup_theme', [ $this, 'afterSetupTheme' ], 999 );
        add_action( 'rest_api_init', [ $this, 'routes' ] );
        add_action( 'wp_head', [ $this, 'openGraphMeta' ] );
        add_action( 'wp_head', [ $this, 'metaDescription' ] );
    }

    protected function filters()
    {
        add_filter( 'single_template', [ $this, 'fullWidthSinglePropertyTemplate' ] );
        add_filter( 'archive_template', [ $this, 'fullWidthArchivePropertyTemplate' ] );
    }

    /**
     * Check enqueued scripts/styles in after_setup_theme hook
     */
    public function afterSetupTheme()
    {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ], 999 );
    }

    /**
     * Enqueue scripts styles
     */
    public function enqueueScripts()
    {
        $ext      = Plugin::getScriptPart( 'ext' );
        $file_dir = Plugin::getScriptPart( 'file-dir' );
        $version  = Plugin::getScriptPart( 'version' );

        $enqueue_search_app    = false;
        $enqueue_user_acct_app = false;
        $user_account_settings = [];
        $has_shortcode         = Option::instance()->get( 'has_shortcode' );

        $general_settings = Option::instance()->get( 'general' );
        $search_settings  = Option::instance()->get( 'search' );
        $load_google_maps = false;
        if ( ! empty( $general_settings['google_maps_api_key'] ) ) {
            $load_google_maps = true;
            $google_maps_url  = add_query_arg(
                [ 'key' => $general_settings['google_maps_api_key'] ],
                '//maps.googleapis.com/maps/api/js'
            );

            wp_register_script( 'google-maps', $google_maps_url, [], null, true );
        }

        $post_id = get_the_ID();
        if ( ( is_single() || is_page() ) && ! empty( $has_shortcode ) ) {
            foreach ( $has_shortcode as $shortcode => $atts ) {
                if ( 'idxrp_search_app' === $shortcode ) {
                    if ( isset( $atts[ $post_id ] ) ) {
                        if ( $atts[ $post_id ]['settings_id'] ) {
                            $atts[ $post_id ]['settings_id'] = intval( $atts[ $post_id ]['settings_id'] );
                        }
                        $settings_id = $atts[ $post_id ]['settings_id'];
                        if ( isset( $this->app_settings[ $settings_id ] ) || ! $settings_id ) {
                            continue;
                        }
                        $settings = ScSettingsModel::instance()->get( $settings_id );
                        if ( is_wp_error( $settings ) ) {
                            add_filter(
                                'the_content',
                                function ( $content ) use ( $settings ) {
                                    if ( current_user_can( 'edit_posts' ) ) {
                                        $content = sprintf(
                                            '<p class="text-danger">%s</p>',
                                            $settings->get_error_message()
                                        );
                                    } else {
                                        $content = sprintf(
                                            __(
                                                'An error occurred while trying to render this page. Please contact the site administrator.',
                                                'realty-idx-pro'
                                            )
                                        );
                                    }

                                    return $content;
                                }
                            );
                            continue;
                        }

                        $settings['default_order'] = empty( $settings['default_order'] ) ?
                            'asc' : $settings['default_order'];
                        $settings['default_view']  = empty( $settings['default_view'] ) ?
                            'list' : $settings['default_view'];

                        $settings['search_page_permalink'] = ! empty( $settings['search_page'] ) ?
                            get_permalink( $settings['search_page'] ) : '';

                        if ( ! empty( $settings['class_select'] ) ) {
                            $class_options = [];
                            foreach ( $settings['class_select'] as $class_data ) {
                                $class_options[] = "{$class_data['class_label']}:{$class_data['resource_id']}:{$class_data['class_name']}";
                            }
                            $class_select = [
                                'field_name' => 'resource_class',
                                'input_type' => 'Select',
                                'label'      => '',
                                'long_name'  => __( 'Property Type', 'realty-idx-pro' ),
                                'options'    => implode( PHP_EOL, $class_options )
                            ];

                            $settings['class_select'] = $class_select;
                        }

                        $this->app_settings[ $settings_id ] = $settings;

                        $templates             = [
                            'list_template'   => 0,
                            'photo_template'  => 0,
                            'map_template'    => 0,
                            'marker_template' => 0
                        ];
                        $templates             = array_intersect_key( $settings, $templates );
                        $this->print_templates = array_merge(
                            $this->print_templates,
                            array_values( array_intersect_key( $settings, $templates ) )
                        );
                        $this->print_templates = array_filter( $this->print_templates );
                        $enqueue_search_app    = true;
                    }
                } else if ( 'idxrp_user_acct' === $shortcode && isset( $atts[ $post_id ] ) ) {
                    $search_page           = ! empty( $atts[ $post_id ]['search_page'] )
                        ? esc_attr( get_permalink( $atts[ $post_id ]['search_page'] ) ) : '';
                    $user_account_settings = $atts[ $post_id ];

                    $user_account_settings['search_page'] = $search_page;

                    $enqueue_user_acct_app = true;
                }
            }
        } else if ( is_tax() ) {
            $tax_archive = Option::instance()->get( 'tax_archive' );
            $taxonomies  = Plugin::getRegisteredTaxonomies();
            if ( ! empty( $tax_archive['settings_id'] ) && ! empty( $taxonomies ) ) {
                $settings = ScSettingsModel::instance()->get( $tax_archive['settings_id'] );
                if ( is_wp_error( $settings ) ) {
                    add_filter(
                        'the_content',
                        function ( $content ) use ( $settings ) {
                            if ( current_user_can( 'edit_posts' ) ) {
                                $content = sprintf(
                                    '<p class="text-danger">%s</p>',
                                    $settings->get_error_message()
                                );
                            } else {
                                $content = sprintf(
                                    __(
                                        'An error occurred while trying to render this page. Please contact the site administrator.',
                                        'realty-idx-pro'
                                    )
                                );
                            }

                            return $content;
                        }
                    );
                } else {
                    $term       = get_queried_object();// e.g. naples
                    $field_name = $taxonomies[ $term->taxonomy ];

                    // add default settings
                    $settings['settings'][] = [
                        'label'            => sprintf(
                            __( '%s equals to %s', 'realty-idx-pro' ),
                            $field_name,
                            $term->name
                        ),
                        'query_field_name' => $field_name,
                        'operator'         => 'equals',
                        'value'            => $term->name,
                    ];

                    $this->app_settings[ $tax_archive['settings_id'] ] = $settings;

                    $templates             = [
                        'list_template'   => 0,
                        'photo_template'  => 0,
                        'map_template'    => 0,
                        'marker_template' => 0
                    ];
                    $templates             = array_intersect_key( $settings, $templates );
                    $this->print_templates = array_merge(
                        $this->print_templates,
                        array_values( array_intersect_key( $settings, $templates ) )
                    );
                    $this->print_templates = array_filter( $this->print_templates );

                    $enqueue_search_app = true;
                }
            }
        }

        if ( ! wp_style_is( 'idxrp-bootstrap' ) && ! wp_style_is( 'idxrp-bootstrap', 'registered' ) ) {
            wp_register_style(
                'idxrp-bootstrap',
                plugins_url(
                    "css/bootstrap/idxrpbs{$ext}.css",
                    IDX_REALTY_PRO_PLUGIN_FILE
                )
            );
        }

        wp_register_script(
            'search-suggest',
            plugins_url( "js/search-suggest/index{$ext}.js", IDX_REALTY_PRO_PLUGIN_FILE ),
            [ 'jquery', 'jquery-serialize-object' ],
            $version,
            true
        );

        if ( $enqueue_search_app ) {
            wp_enqueue_style(
                'idxrp-search-app',
                plugins_url( "css/front/search-app{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'font-awesome', 'roboto-font', 'idxrp-bootstrap' ]
            );

            $search_app_deps = [ 'idxrp-vendor-deps' ];
            if ( ! empty( $general_settings['google_maps_api_key'] ) ) {
                $search_app_deps[] = 'google-maps';
            }

            if ( ! isset( $search_settings['instant_search'] ) || ! intval( $search_settings['instant_search'] ) ) {
                $search_app_deps[] = 'search-suggest';
            }

            wp_enqueue_script(
                'idxrp-search-app',
                plugins_url( "js/{$file_dir}/front/search-app/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                $search_app_deps,
                $version,
                true
            );

            $l10n = Util::mergeL10nFiles( [ 'front/search-app.php' ] );
            $l10n += [ 'app_settings' => $this->app_settings ];

            wp_localize_script( 'idxrp-search-app', 'idxrp', $l10n );

            if ( ! empty( $this->print_templates ) ) {
                $this->print_templates = array_unique( $this->print_templates );
            }
            add_action( 'wp_footer', [ $this, 'searchAppTemplates' ] );
        }

        $post_type = Plugin::getPropertyPostType();
        if ( ! empty( $post_type ) && is_singular( $post_type ) ) {
            wp_enqueue_style(
                'idxrp-single',
                plugins_url(
                    "css/front/single{$ext}.css",
                    IDX_REALTY_PRO_PLUGIN_FILE
                ),
                [ 'idxrp-bootstrap', 'font-awesome' ],
                $version
            );

            $single_deps = [ 'jquery', 'backbone', 'underscore', 'idxrp-vendor-deps' ];
            if ( $load_google_maps ) {
                $single_deps[] = 'google-maps';
            }

            wp_enqueue_script(
                'idxrp-single',
                plugins_url( "js/{$file_dir}/front/single/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                $single_deps,
                $version,
                true
            );

            $l10n = Util::mergeL10nFiles( [ 'front/single.php' ] );
            $l10n += compact( 'load_google_maps' );
            wp_localize_script( 'idxrp-single', 'idxrp', $l10n );
            add_action( 'wp_footer', [ $this, 'singleTemplates' ] );
        }

        if ( $enqueue_user_acct_app ) {
            wp_enqueue_style(
                'idxrp-user-acct-style',
                plugins_url( "css/front/user-account{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'font-awesome', 'roboto-font', 'idxrp-bootstrap' ]
            );

            wp_enqueue_script(
                'idxrp-user-acct-app',
                plugins_url(
                    "js/{$file_dir}/front/user-account/index.js",
                    IDX_REALTY_PRO_PLUGIN_FILE
                ),
                [ 'idxrp-vendor-deps' ],
                $version,
                true
            );

            $l10n = [ 'front/user-account.php' ];

            $user_account_l10n = Util::mergeL10nFiles( $l10n );

            $user_account_l10n['user_account']['constants'] += $user_account_settings;
            wp_localize_script( 'idxrp-user-acct-app', 'idxrp', $user_account_l10n );
        }
    }

    /**
     * Add Open Graph meta tags
     */
    public function openGraphMeta()
    {
        $post_type = Plugin::getPropertyPostType();
        if ( ! empty( $post_type ) && is_singular( $post_type ) ) {
            $single_settings = Option::instance()->get( 'single' );

            $property = Api::getDetails( get_the_ID() );
            $photos   = [ plugins_url( 'images/no-image.png', IDX_REALTY_PRO_PLUGIN_FILE ) ];
            if ( ! is_wp_error( $property ) ) {
                if ( ! empty( $property['thumbnails'] ) ) {
                    $photos = $property['thumbnails'];
                } else if ( ! empty( $property['photos'] ) ) {
                    $photos = $property['photos'];
                }
            }

            $content = ! empty( $single_settings['meta_description'] ) ?
                Util::doReplaceFieldNameVars( $single_settings['meta_description'] ) : '';

            $ogs = [
                'url'         => esc_url( get_permalink() ),
                'type'        => 'website',
                'image'       => ! empty( $photos ) && is_array( $photos ) ? esc_url( $photos[0] ) : '',
                'title'       => esc_attr( get_the_title() ),
                'description' => esc_attr( $content )
            ];
            $ogs = apply_filters( 'idxrp_open_graph_meta', $ogs );

            foreach ( $ogs as $og => $content ) {
                printf( '<meta property="og:%s" content="%s" />', $og, $content );
            }
        }
    }

    /**
     * Meta tag description
     */
    public function metaDescription()
    {
        $post_type = Plugin::getPropertyPostType();
        if ( ! empty( $post_type ) && is_singular( $post_type ) ) {
            $single_settings = Option::instance()->get( 'single' );
            if ( ! empty( $single_settings['meta_description'] ) ) {
                $content = Util::doReplaceFieldNameVars( $single_settings['meta_description'] );
                printf( '<meta name="description" content="%s" />', esc_attr( $content ) );
            }
        }
    }

    /**
     * Print template markup placeholder
     */
    public function searchAppTemplates()
    {
        if ( ! empty( $this->print_templates ) ) {
            foreach ( $this->print_templates as $template_id ) {
                $template = get_post( $template_id );
                include View::instance()->make( [ 'idxrp/front/idxrp-template.php' ], [], false );
            }
        }

        $default_templates = [
            'list-view',
            'photo-view',
            'map-view',
            'marker-info'
        ];
        foreach ( $default_templates as $default_template ) {
            View::instance()->make(
                [
                    "idxrp/front/default/{$default_template}.php",
                ]
            );
        }

        View::instance()->make( [ 'idxrp/front/user/login-form.php', ] );
        View::instance()->make( [ 'idxrp/front/user/registration-form.php', ] );
        View::instance()->make( [ 'idxrp/front/search-tips.php', ] );
    }

    /**
     * Override single page template
     *
     * @param string|array $template The template or array of template for single-property
     *
     * @return string|array The template/s to load
     */
    public function fullWidthSinglePropertyTemplate( $template )
    {
        $post_type = get_post_type();
        if ( $post_type === Plugin::getPropertyPostType() ) {
            $_template = View::instance()->make(
                [ "idxrp/front/single-{$post_type}.php", "single-{$post_type}.php" ],
                [],
                false
            );
            if ( $_template ) {
                $template = $_template;
            }
        }

        return $template;
    }

    /**
     * Override taxonomy archive page template
     *
     * @param string $template
     *
     * @return string
     */
    public function fullWidthArchivePropertyTemplate( $template )
    {
        $taxonomies     = Plugin::getRegisteredTaxonomies();
        $queried_object = get_queried_object();
        if ( false === stripos( $template, $queried_object->taxonomy ) &&
             false === stripos( $template, $queried_object->slug ) &&
             isset( $taxonomies[ $queried_object->taxonomy ] )
        ) {
            $_template = View::instance()->make(
                [ "idxrp/front/idxrp-taxonomy.php", "idxrp-taxonomy.php" ],
                [],
                false
            );
            if ( $_template ) {
                $template = $_template;
            }
        }

        return $template;
    }

    /**
     * Print templates for single property post pages
     */
    public function singleTemplates()
    {
        View::instance()->make( [ 'idxrp/front/info-window.php' ] );
        View::instance()->make( [ 'idxrp/front/user/login-form.php', ] );
        View::instance()->make( [ 'idxrp/front/user/registration-form.php', ] );
    }

    public function routes()
    {
        $route_classes = [
            '\IDXRealtyPro\Route\Front\User',
            '\IDXRealtyPro\Route\Front\Search',
        ];
        foreach ( $route_classes as $route_class ) {
            $route = new $route_class();
            $route->register_routes();
        }
    }
}
