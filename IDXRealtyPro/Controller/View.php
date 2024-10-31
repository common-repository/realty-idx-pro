<?php
/**
 * User: Paul Grejaldo
 * Date: 2016/10/08
 * Time: 10:23 PM
 */

namespace IDXRealtyPro\Controller;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class View
{

    protected static $instance;

    public function __construct()
    {

    }

    /**
     * Return an instance of this class
     *
     * @return View The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Dynamically load or return the first located template file
     *
     * @param string|array $templates    A string or array of string template file names
     * @param array        $vars         The variables to be used on the template
     * @param bool         $load         True to load the located template otherwise return the file path
     * @param bool         $require_once True to use require_once in loading the template file, otherwise uses require
     *
     * @return string The first located template file path
     */
    public function make( $templates, $vars = [], $load = true, $require_once = true )
    {
        $vars = apply_filters( 'idxrp_view_make_vars', $vars, $templates );

        if ( ! empty( $vars ) ) {
            extract( $vars, EXTR_SKIP );
        }

        $located = locate_template( $templates );
        $located = apply_filters( 'idxrp_view_locate_template', $located, $templates );

        if ( empty( $located ) ) {
            foreach ( (array) $templates as $template ) {
                if ( ! $template ) {
                    continue;
                }
                if ( file_exists( WP_CONTENT_DIR . "/$template" ) ) {
                    $located = WP_CONTENT_DIR . "/$template";
                    break;
                } else if ( file_exists( IDX_REALTY_PRO_PLUGIN_DIR_PATH . 'templates/' . $template ) ) {
                    $located = IDX_REALTY_PRO_PLUGIN_DIR_PATH . 'templates/' . $template;
                    break;
                }
            }
        }

        if ( $load && '' != $located ) {
            if ( $require_once ) {
                require_once $located;
            } else {
                require $located;
            }
        }

        return $located;
    }
}
