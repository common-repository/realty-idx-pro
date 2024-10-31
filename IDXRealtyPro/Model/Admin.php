<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/13
 * Time: 2:39 PM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Admin extends Base
{

    protected static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return an instance of this class
     *
     * @return Admin The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Checks and records post ID if it uses a plugin shortcode that requires conditional scripts/styles
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @param bool     $update
     */
    public function checkShortcode( $post_id, $post, $update )
    {
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        $has_shortcode = Option::instance()->get( 'has_shortcode' );
        foreach ( Shortcodes::getShortcodes( 'shortcode' ) as $shortcode ) {
            $regex = Shortcodes::getShortcodeRegex( $shortcode );
            preg_match_all( "/$regex/s", $post->post_content, $matches );

            if ( ! empty( $matches[0] ) ) {
                if ( ! empty( $matches[3] ) && array_filter( $matches[3] ) ) {
                    $parsed_atts = [];
                    foreach ( $matches[3] as $atts_key => $atts ) {
                        $parsed_atts = array_merge( $parsed_atts, shortcode_parse_atts( $atts ) );
                    }
                    $has_shortcode[ $shortcode ][ $post_id ] = $parsed_atts;
                } else {
                    $has_shortcode[ $shortcode ][ $post_id ] = $post_id;
                }
            }
            if ( strpos( $post->post_content, $shortcode ) === false &&
                 ! empty( $has_shortcode[ $shortcode ] ) &&
                 in_array( $post_id, array_keys( $has_shortcode[ $shortcode ] ) )
            ) {
                unset( $has_shortcode[ $shortcode ][ $post_id ] );
            }
        }

        Option::instance()->save( 'has_shortcode', $has_shortcode );
    }
}
