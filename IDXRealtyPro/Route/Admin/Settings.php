<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/05/27
 * Time: 12:50 AM
 */

namespace IDXRealtyPro\Route\Admin;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Model\Option;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Settings extends \IDXRealtyPro\Route\Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/settings",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'save' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/save_post_type",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'savePostType' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
    }

    /**
     * Admin > Settings tab actions
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function save( $request )
    {
        $params = $request->get_json_params();
        $group  = $params['group'];
        $data   = [];

        switch ( $group ) {
            case 'general':
                $defaults = Option::instance()->settings_general_defaults;
                $data     = array_intersect_key( $params, $defaults );
                $data     = wp_parse_args( $data, $defaults );

                $data['auto_update_plugin'] = isset( $data['auto_update_plugin'] ) ?
                    ! ! intval( $data['auto_update_plugin'] ) : false;
                break;
            case 'single':
                $defaults = Option::instance()->settings_single_defaults;
                $data     = array_intersect_key( $params, $defaults );
                $data     = wp_parse_args( $data, $defaults );
                break;
            case 'search':
                $defaults = Option::instance()->settings_search_defaults;
                $data     = array_intersect_key( $params, $defaults );
                $data     = wp_parse_args( $data, $defaults );

                $data['instant_search'] = isset( $data['instant_search'] ) ?
                    ! ! intval( $data['instant_search'] ) : false;
                break;
            case 'tax_archive':
                $defaults = Option::instance()->settings_tax_archive_defaults;
                $data     = array_intersect_key( $params, $defaults );
                $data     = wp_parse_args( $data, $defaults );
                if ( ! empty( $data['taxonomies'] ) ) {
                    $taxonomies = array_intersect_key( $params, array_flip( $data['taxonomies'] ) );
                    if ( ! empty( $taxonomies ) ) {
                        foreach ( $taxonomies as $field_name => &$terms ) {
                            $taxonomy = Plugin::getRetsFieldTaxonomy( $field_name );
                            $terms    = (array) $terms;
                            foreach ( $terms as $term ) {
                                if ( ! term_exists( $term, $taxonomy ) ) {
                                    wp_insert_term( $term, $taxonomy );
                                }
                            }
                        }
                        $data = wp_parse_args( $data, $taxonomies );
                    }
                }

                Plugin::setFlushRewriteRules();
                break;
        }

        Option::instance()->save( $group, $data );

        return $this->restResponse( $data, $request );
    }

    /**
     * Admin > Settings tab > Post Types actions
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function savePostType( $request )
    {
        $post_type = $request->get_json_params();
        $defaults  = [
            'name'          => '',
            'singular_name' => '',
            'slug'          => '',
        ];
        $post_type = wp_parse_args( $post_type, $defaults );
        if ( empty( $post_type['name'] ) || empty( $post_type['singular_name'] ) || empty( $post_type['slug'] ) ) {
            return new \WP_Error(
                'save_error',
                __( 'All fields are required!', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        }
        Option::instance()->save( 'post_type', $post_type );
        $data = compact( 'post_type' );
        Plugin::setFlushRewriteRules();

        return $this->restResponse( $data, $request );
    }
}
