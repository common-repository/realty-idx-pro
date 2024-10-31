<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/18
 * Time: 11:21 PM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Plugin as HPlugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class TemplatePost
{

    protected static $instance;

    public function __construct()
    {
    }

    /**
     * Return an instance of this class
     *
     * @return TemplatePost The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Get template post
     *
     * @param int $post_id
     *
     * @return array|null|\WP_Error|\WP_Post
     */
    public function get( $post_id )
    {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return new \WP_Error(
                'template_post_error',
                sprintf( __( 'Template Post ID #%d not found.', 'realty-idx-pro' ), $post_id )
            );
        }

        if ( HPlugin::getTemplatePostType() !== $post->post_type ) {
            return new \WP_Error(
                'template_post_error',
                sprintf( __( 'Post ID #%d is not a templates post.', 'realty-idx-pro' ), $post_id )
            );
        }

        return $post;
    }

    /**
     * Get a list of template posts
     *
     * @param string $return
     *
     * @return array
     */
    public function getTemplatesList( $return = 'all' )
    {
        return get_posts(
            [
                'post_type'      => HPlugin::getTemplatePostType(),
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => $return
            ]
        );
    }
}
