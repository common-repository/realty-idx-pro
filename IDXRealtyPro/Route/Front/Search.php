<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/08/17
 * Time: 10:24 AM
 */

namespace IDXRealtyPro\Route\Front;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;
use IDXRealtyPro\Model\Front;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Search extends \IDXRealtyPro\Route\Base
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = Plugin::getWpApiSearchRestBase();
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @access public
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/(?P<server_id>[\d]+)",
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'searchResults' ],
                'permission_callback' => [ $this, 'get_items_permissions_check' ]
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/(?P<server_id>[\d]+)/suggest",
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'searchSuggestions' ],
                'permission_callback' => [ $this, 'get_items_permissions_check' ]
            ]
        );
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return bool|\WP_Error
     */
    public function get_items_permissions_check( $request )
    {
        return true;
    }

    /**
     * Search results
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function searchResults( $request )
    {
        $params = $request->get_query_params();

        $setting_id    = $params['sid'];
        $server_key    = sanitize_key( $params['server_key'] );
        $transient_key = 'idxrp_' . sha1( "rest-fields-{$setting_id}" );
        $fields        = get_transient( $transient_key );
        if ( ! $fields ) {
            $fields = [
                'key_field_id',
                'thumbnails',
                'display_address',
                'mls_number',
                'price',
                'lat',
                'lng'
            ];

            $template_post_js_tags = Util::getTemplatePostTagNames( null, 'js' );
            $list_js_tags          = Util::getDefaultTemplateFileTagNames( 'list-view', $server_key );
            $grid_js_tags          = Util::getDefaultTemplateFileTagNames( 'photo-view', $server_key );
            $fields                = array_merge( $fields, $template_post_js_tags, $list_js_tags, $grid_js_tags );
            $fields                = ! empty( $fields ) ? array_unique( $fields ) : [];

            set_transient( $transient_key, $fields, HOUR_IN_SECONDS );
        }

        $api_args = Util::arrayExcept( $params, [ 'sid', 'resource_class', 'XDEBUG_SESSION_START' ] );
        $per_page = ! empty( $params['per_page'] ) ? intval( $params['per_page'] ) : 9;
        list( $resource_id, $class_name ) = explode( ':', $params['resource_class'] );
        $api_args += compact( 'resource_id', 'class_name', 'per_page' );
        $api_args = wp_parse_args( $api_args, [ '_select' => $fields ] );
        $response = Api::get( 'search', $api_args );
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        if ( $response['total'] ) {
            foreach ( $response['posts'] as &$post ) {
                $property = Front::instance()->getPropertyPostByKeyFieldId( $post['key_field_id'] );
                if ( empty( $property ) ) {
                    $property = Front::instance()->insertPost(
                        "{$post['title']}",
                        $post['key_field_id'],
                        $params['server_id'],
                        $resource_id,
                        $class_name,
                        $server_key
                    );
                }
                $post['link'] = get_permalink( $property );
            }
        }

        return $this->restResponse( $response, $request );
    }

    /**
     * Get suggestions
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function searchSuggestions( $request )
    {
        $params      = $request->get_query_params();
        $search      = urldecode( $params['q'] );
        $server_id   = intval( $request->get_param( 'server_id' ) );
        $resource_id = sanitize_text_field( $params['resource_id'] );
        $class_name  = sanitize_text_field( $params['class_name'] );
        $api_args    = compact( 'server_id', 'resource_id', 'class_name', 'search' );
        $response    = Api::get( 'search/suggest', $api_args );
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        $suggestions = implode( PHP_EOL, $response );

        return $this->restResponse( $suggestions, $request );
    }
}
