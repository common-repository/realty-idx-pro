<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/12
 * Time: 10:30 AM
 */

namespace IDXRealtyPro\Route\PostEditor;

use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class ScSettings extends \IDXRealtyPro\Route\Base
{

    protected static $instance;

    public function __construct()
    {
        parent::__construct();

        $this->rest_base = 'post_editor';
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/field-values",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getFieldValues' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/resource-classes/(?P<server_id>[\d]+)",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getResourceClasses' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/resource-class/(?P<server_id>[\d]+)/fields",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getResourceClassFields' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/resource-class/(?P<server_id>[\d]+)/lookup",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'getFieldsLookupValues' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ]
        );
    }

    /**
     * Get field values from DB
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getFieldValues( $request )
    {
        $params = $request->get_json_params();
        // $range = 'min' or 'max'
        $field_name = explode( ':', $params['field_name'] );
        $field_name = $field_name[0];
        $long_name  = $params['long_name'];
        $server_id  = intval( $params['server_id'] );

        $response_key = ! empty( $params['response_key'] ) ? $params['response_key'] : 'field_values';
        $value_type   = $params['value_type'];
        $data_type    = $params['data_type'];
        list( $resource_id, $class_name ) = explode( ':', $params['resource_class'] );
        $api_args = compact( 'server_id', 'resource_id', 'class_name', 'field_name', 'value_type' );

        $values = [ "{$long_name}:" ];
        if ( Util::isNumericDataType( $data_type ) ) {
            switch ( $value_type ) {
                case 'min_max_price':
                    $results = Api::get( "field_values/{$server_id}", $api_args );
                    if ( is_wp_error( $results ) ) {
                        return $results;
                    }
                    if ( ! is_null( $results ) ) {
                        $min = intval( $results['min'] );
                        $max = intval( $results['max'] );

                        while ( $min <= $max ) {
                            if ( $min > 999999 && $min < 999999999 ) {
                                $label = round( ( $min / 1000000 ), 1 ) . 'M';
                            } else if ( $min > 999999999 ) {
                                $label = round( ( $min / 1000000000 ), 1 ) . 'B';
                            } else if ( $min > 99999 && $min <= 999999 ) {
                                $label = round( ( $min / 1000 ), 1 ) . 'K';
                            } else {
                                $label = number_format_i18n( $min );
                            }

                            $values[] = sprintf( '$%s+:%s', $label, $min );

                            if ( $min >= 0 && $min <= 999999 ) {
                                $min += 100000;
                            } else if ( $min > 999999 && $min <= 9999999 ) {
                                $min += 1000000;
                            } else {
                                $min += 10000000;
                            }
                        }
                    }
                    break;
                case 'exact_price':
                    $results = Api::get( "field_values/{$server_id}", $api_args );
                    if ( is_wp_error( $results ) ) {
                        return $results;
                    }
                    if ( ! is_null( $results ) ) {
                        foreach ( $results as $result ) {
                            $values[] = sprintf(
                                '$%s:%s',
                                number_format_i18n( $result['value'] ),
                                intval( $result['value'] )
                            );
                        }
                    }
                    break;
                case 'min_num':
                    for ( $i = 0; $i <= 10; $i++ ) {
                        $values[] = "{$i}+:{$i}";
                    }
                    break;
                case 'exact_num':
                    /*$results = Model::instance()->getDb()->get_results(
                        sprintf(
                            'SELECT DISTINCT `%1$s` AS `value` FROM `%2$s` ORDER BY `value` ASC',
                            $field_name,
                            $table
                        ),
                        ARRAY_A
                    );*/
                    $results = Api::get( "field_values/{$server_id}", $api_args );
                    if ( is_wp_error( $results ) ) {
                        return $results;
                    }
                    if ( ! is_null( $results ) ) {
                        foreach ( $results as $result ) {
                            $values[] = sprintf( '%1$s:%1$s', intval( $result['value'] ) );
                        }
                    }
                    break;
            }
        } else if ( 'lookup' === $value_type ) {
            $_values = Api::get( "field_values/{$server_id}", $api_args );
            if ( is_wp_error( $_values ) ) {
                return $_values;
            }
            $_values = array_map(
                function ( $val ) {
                    $val = trim( $val );

                    return "{$val}:{$val}";
                },
                $_values
            );
            uasort(
                $_values,
                function ( $a, $b ) {
                    return strcmp( $a, $b );
                }
            );
            $values = array_merge( $values, $_values );
        }
        $values = ! empty( $values ) ? implode( "\n", $values ) : '';

        return $this->restResponse( [ $response_key => $values ], $request );
    }

    /**
     * Get field values from api URL
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getResourceClasses( $request )
    {
        $server_id = intval( $request->get_param( 'server_id' ) );
        if ( ! $server_id ) {
            return new \WP_Error( 'unknown_server_id', "Unknown server ID #{$server_id}", [ 'status' => 500 ] );
        }
        $resource_classes = Api::get( "servers/{$server_id}/resource_class" );
        if ( is_wp_error( $resource_classes ) ) {
            return $resource_classes;
        }

        return $this->restResponse( compact( 'resource_classes' ), $request );
    }

    /**
     * Get resource class
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getResourceClassFields( $request )
    {
        $params = $request->get_json_params();

        $server_id  = intval( $params['server_id'] );
        $class_data = Api::getClassFields(
            $server_id,
            sanitize_text_field( $params['resource_id'] ),
            sanitize_text_field( $params['class_name'] )
        );
        if ( is_wp_error( $class_data ) ) {
            return $class_data;
        }
        $search_fields_data = [];
        if ( ! empty( $class_data['fields'] ) ) {
            $search_fields_data = Util::classDataSearchFieldsList( $class_data['fields'] );
        }

        return $this->restResponse( compact( 'class_data', 'search_fields_data' ), $request );
    }

    /**
     * Get fields lookup values
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function getFieldsLookupValues( $request )
    {
        $params = $request->get_json_params();
        if ( empty( $params['query_field_name'] ) ) {
            return new \WP_Error( 'lookup_error', 'Unknown field to retrieve lookup values for.', [ 'status' => 500 ] );
        }
        $server_id = intval( $params['server_id'] );
        $defaults  = [ 'get' => 'lookup', 'resource_id' => '', 'class_name' => '' ];
        $api_args  = Util::arrayExcept( $params, [ 'server_id' ] );
        $api_args  = wp_parse_args( $api_args, $defaults );
        $lookup    = Api::get( "servers/{$server_id}/resource_class", $api_args );
        if ( is_wp_error( $lookup ) ) {
            return $lookup;
        }
        $field_name  = sanitize_text_field( $params['query_field_name'] );
        $long_values = isset( $lookup[ $field_name ]['longname_values'] )
            ? array_values( $lookup[ $field_name ]['longname_values'] ) : [];

        return $this->restResponse( compact( 'long_values' ), $request );
    }
}
