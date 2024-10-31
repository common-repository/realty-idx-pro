<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/04/23
 * Time: 11:26 AM
 */

namespace IDXRealtyPro\Route\Admin;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Option;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class License extends \IDXRealtyPro\Route\Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/license",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'licenseActions' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
    }

    /**
     * Admin > License tab actions
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function licenseActions( $request )
    {
        $params = $request->get_json_params();

        if ( empty( $params['license_key'] ) ) {
            Plugin::processLicense( 'deactivate' );
        }

        $license_key = $this->saveLicenseKey( $params['license_key'] );

        switch ( $params['process_type'] ) {
            case 'activate':
                $process_type = 'deactivate';
                break;
            case 'deactivate':
                $process_type = 'activate';
                break;
        }
        $is_license_active = Plugin::processLicense( $params['process_type'], true );
        if ( is_wp_error( $is_license_active ) ) {
            return $is_license_active;
        }

        return $this->restResponse( compact( 'license_key', 'process_type', 'is_license_active' ), $request );
    }

    /**
     * Save license key and return a modified key for display
     *
     * @param string $key Raw license key
     *
     * @return string
     */
    protected function saveLicenseKey( $key )
    {
        $old_key      = Option::instance()->get( 'license', 'key' );
        $modified_key = Util::getMaskedValue( $old_key );

        if ( $key === $modified_key ) {
            $key = $old_key;
        } else {
            $key = sanitize_text_field( $key );
        }

        Option::instance()->save( 'license', compact( 'key' ) );

        return $modified_key;
    }
}
