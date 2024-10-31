<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/17
 * Time: 1:46 PM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Front extends Base
{

    protected static $instance;

    public function __construct()
    {
        parent::__construct();
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
     * Insert property post
     *
     * @param string $post_title
     * @param string $key_field_id
     * @param string $server_id
     * @param string $resource_id
     * @param string $class_name
     * @param string $server_key
     *
     * @return int|\WP_Error
     */
    public function insertPost( $post_title, $key_field_id, $server_id, $resource_id, $class_name, $server_key )
    {
        $args  = [
            'post_type'   => Plugin::getPropertyPostType(),
            'post_status' => 'publish',
            'post_title'  => $post_title,
            'post_author' => 0,
        ];
        $terms = compact( 'server_id', 'resource_id', 'class_name', 'server_key' );

        $post_id = wp_insert_post( $args );
        foreach ( $terms as $taxonomy => $term ) {
            wp_add_object_terms( $post_id, "{$term}", $taxonomy );
        }
        add_post_meta( $post_id, 'key_field_id', $key_field_id );

        return $post_id;
    }

    /**
     * Get property meta data 'resource_id', 'class_name', 'server_id', 'key_field_id'
     *
     * @param int $post_id
     *
     * @return array
     */
    public function getPropertyMeta( $post_id )
    {
        $server_id = wp_get_object_terms( $post_id, Plugin::getServerIdTaxonomy() );
        $server_id = $server_id[0]->name;

        $resource_id = wp_get_object_terms( $post_id, Plugin::getResourceIdTaxonomy() );
        $resource_id = $resource_id[0]->name;

        $class_name = wp_get_object_terms( $post_id, Plugin::getClassNameTaxonomy() );
        $class_name = $class_name[0]->name;

        $server_key = wp_get_object_terms( $post_id, Plugin::getServerKeyTaxonomy() );
        $server_key = $server_key[0]->name;

        $key_field_id = get_post_meta( $post_id, 'key_field_id', true );

        return compact(
            'server_id',
            'resource_id',
            'class_name',
            'server_key',
            'key_field_id'
        );
    }

    /**
     * Get property post by key field id
     *
     * @param string $key_field_id
     *
     * @return \WP_Post|null
     */
    public function getPropertyPostByKeyFieldId( $key_field_id )
    {
        $property = get_posts(
            [
                'post_type'      => Plugin::getPropertyPostType(),
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'meta_key'       => 'key_field_id',
                'meta_value'     => $key_field_id,
            ]
        );
        if ( ! empty( $property[0] ) ) {
            return $property[0];
        }

        return null;
    }
}
