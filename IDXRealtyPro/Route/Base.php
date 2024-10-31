<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/03/22
 * Time: 5:09 PM
 */

namespace IDXRealtyPro\Route;

use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Base extends \WP_REST_Controller
{

    public function __construct()
    {
        $this->namespace = Plugin::getWpApiNamespace();
        $this->rest_base = Plugin::getWpApiAdminRestBase();
    }

    /**
     * REST response
     *
     * @param mixed|\WP_Error  $data
     * @param \WP_REST_Request $request
     * @param int              $status
     * @param array            $headers
     *
     * @return \WP_REST_Response
     */
    public function restResponse( $data, $request, $status = 200, $headers = [] )
    {
        return new \WP_REST_Response(
            $this->prepare_item_for_response( $data, $request ), $status, $headers
        );
    }

    /**
     * Return prepared item
     *
     * @param mixed            $item
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_REST_Response
     */
    public function prepare_item_for_response( $item, $request )
    {
        return $item;
    }

    public function get_items_permissions_check( $request )
    {
        return $this->create_item_permissions_check( $request );
    }

    public function create_item_permissions_check( $request )
    {
        return current_user_can( 'manage_options' );
    }
}
