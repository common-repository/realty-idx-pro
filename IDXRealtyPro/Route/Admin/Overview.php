<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/03/22
 * Time: 5:08 PM
 */

namespace IDXRealtyPro\Route\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Overview extends \IDXRealtyPro\Route\Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/overview",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'overviewActions' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
    }

    /**
     * Admin > Overview tab actions
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function overviewActions( $request )
    {
        $data   = [];
        $params = $request->get_json_params();
        if ( empty( $params['action'] ) || empty( $params['type'] ) ) {
            return new \WP_Error( 'action_error', __( 'Unknown request', 'realty-idx-pro' ) );
        }

        switch ( $params['action'] ) {
            default:
        }

        return $this->restResponse( $data, $request );
    }
}
