<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/19
 * Time: 8:15 AM
 */

namespace IDXRealtyPro\Route\PostEditor;

use IDXRealtyPro\Controller\View;
use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Model\Api;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Template extends \IDXRealtyPro\Route\Base
{

    protected static $instance;

    public function __construct()
    {
        parent::__construct();

        $this->rest_base = Plugin::getWpApiPostEditorRestBase();
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @since  4.7.0
     * @access public
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/idxrp_load_template",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getDefaultTemplate' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/change_server",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getRetsFields' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
    }

    /**
     * Get default template
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getDefaultTemplate( $request )
    {
        $json      = $request->get_json_params();
        $template  = isset( $json['template'] ) ? $json['template'] : '';
        $server_id = isset( $json['server_id'] ) ? intval( $json['server_id'] ) : 0;
        if ( ! $template ) {
            return new \WP_Error(
                'templateError',
                __( 'Unknown template to load.', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        }
        if ( in_array( $template, [ 'single', 'single-marker' ] ) && $server_id ) {
            $server = Api::getServer( $server_id );
            if ( is_wp_error( $server ) ) {
                return $server;
            }
            $server_key    = $server['server_key'];
            $template_file = "idxrp/front/default/{$template}/{$server_key}.php";
        } else {
            $template_file = "idxrp/front/default/{$template}.php";
        }

        $template_file = View::instance()->make(
            [ $template_file ],
            [],
            false
        );
        if ( ! file_exists( $template_file ) ) {
            return new \WP_Error(
                'template_error',
                sprintf( __( 'Default template for %s not found.', 'realty-idx-pro' ), $template )
            );
        }
        ob_start();
        require $template_file;
        $data = ob_get_clean();

        add_filter( 'wp_kses_allowed_html', [ $this, 'allowedHtmlAttributes' ], 10, 2 );
        $allowed_html = wp_kses_allowed_html( 'post' );
        remove_filter( 'wp_kses_allowed_html', [ $this, 'allowedHtmlAttributes' ], 10 );

        $data = wp_kses( $data, $allowed_html );

        return $this->restResponse( $data, $request );
    }

    /**
     * Add custom html attributes
     *
     * @param array  $allowed
     * @param string $context
     *
     * @return mixed
     */
    public function allowedHtmlAttributes( $allowed, $context )
    {
        $allowed_html_attributes = [
            'data-key-id' => true,
        ];

        $allowed['a']      = array_merge( $allowed['a'], $allowed_html_attributes );
        $allowed['button'] = array_merge( $allowed['button'], $allowed_html_attributes );

        return $allowed;
    }

    /**
     * Get RETS fields for selected/give server ID
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getRetsFields( $request )
    {
        $params    = $request->get_json_params();
        $server_id = absint( $params['server_id'] );
        if ( ! $server_id ) {
            return new \WP_Error(
                'rets_fields_error',
                sprintf( __( 'Unknown server %d to fetch fields from.', 'realty-idx-pro' ), $server_id ),
                [ 'status' => 404 ]
            );
        }

        $data = [];

        if ( ! empty( $params['resource_id'] ) && ! empty( $params['class_name'] ) ) {
            $class_data = Api::getClassFields(
                $server_id,
                sanitize_text_field( $params['resource_id'] ),
                sanitize_text_field( $params['class_name'] )
            );
            if ( is_wp_error( $class_data ) ) {
                return $class_data;
            }

            if ( ! empty( $class_data['fields'] ) ) {
                $data['fields'] = $class_data['fields'];
                $data['fields'] = array_merge(
                    [
                        [
                            'system_name' => 'photos',
                            'long_name'   => __( 'Photos' ),
                            'data_type'   => 'character',
                        ],
                        [
                            'system_name' => 'lat',
                            'long_name'   => __( 'Latitude' ),
                            'data_type'   => 'decimal',
                        ],
                        [
                            'system_name' => 'lng',
                            'long_name'   => __( 'Longitude' ),
                            'data_type'   => 'decimal',
                        ],
                        [
                            'system_name' => 'post_title',
                            'long_name'   => __( 'Post Title (Complete Address)', 'realty-idx-pro' ),
                            'data_type'   => 'character',
                        ],
                        [
                            'system_name' => 'ID',
                            'long_name'   => __( 'Post ID', 'realty-idx-pro' ),
                            'data_type'   => 'int',
                        ],
                    ],
                    $data['fields']
                );
                usort(
                    $data['fields'],
                    function ( $a, $b ) {
                        return strcmp( $a['long_name'], $b['long_name'] );
                    }
                );
            }
        } else {
            $resource_classes = Api::get( "servers/{$server_id}/resource_class" );
            if ( is_wp_error( $resource_classes ) ) {
                return $resource_classes;
            }
            $data = wp_list_filter( $resource_classes, [ 'resource_id' => 'Property' ] );
            $data = array_values( $data );
        }

        return $this->restResponse( $data, $request );
    }
}
