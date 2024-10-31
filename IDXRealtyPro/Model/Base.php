<?php
/**
 * Author: Paul Grejaldo
 * Date: 2016/10/16
 * Time: 10:05 AM
 */

namespace IDXRealtyPro\Model;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Base
{

    protected static $instance;

    protected $mls_config_dir;

    public function __construct()
    {
    }

    /**
     * Return an instance of this class
     *
     * @return Base The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Get the global $wpdb object
     *
     * @return \wpdb Returns the $wpdb object.
     */
    public function getDb()
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * Delete all transients prefixed by 'idxrp_'
     */
    public function clearTransients()
    {
        global $wpdb;

        $keys = $wpdb->get_col(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_idxrp_%'"
        );
        if ( ! empty( $keys ) ) {
            foreach ( $keys as $key ) {
                $transient = str_replace( '_transient_', '', $key );
                delete_transient( $transient );
            }
        }
    }

    /**
     * Whether commit current db insert/update query or suspend commit
     *
     * @param bool $suspend If false, commits current pending db insert/update, if true suspends commit
     */
    public function suspendDbCommit( $suspend = true )
    {
        $wpdb = $this->getDb();

        $suspend = (bool) $suspend;

        wp_defer_term_counting( $suspend );
        wp_defer_comment_counting( $suspend );
        wp_suspend_cache_invalidation( $suspend );

        $auto_commit = $suspend
            ? 0
            : 1;

        if ( ! $suspend ) {
            $wpdb->query( 'COMMIT' );
        }

        $wpdb->query( $wpdb->prepare( 'SET autocommit = %d;', $auto_commit ) );
    }

}
