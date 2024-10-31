<?php
/**
 * Author: Paul Grejaldo
 * Date: 2016/10/17
 * Time: 11:32 AM
 */

namespace IDXRealtyPro\Controller;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Cli extends \WP_CLI_Command
{

    /**
     * Clear transients
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     * wp idxrp clear_transients
     *
     */
    public function clear_transients( $args = [], $assoc_args = [] )
    {
        \IDXRealtyPro\Model\Admin::instance()->clearTransients();

        \IDXRealtyPro\Helper\Cli::printLine( 'Transients cleared!' );
    }

    /**
     * Just a test.
     * ## OPTIONS
     * ## EXAMPLES
     * wp idxrp test
     *
     */
    public function test( $args = [], $assoc_args = [] )
    {
        try {
            $filename = WP_CONTENT_DIR . '/idxrp-test-file.php';
            if ( file_exists( $filename ) ) {
                include $filename;
            }
        } catch ( \Exception $exception ) {
            \WP_CLI::error( $exception->getMessage() );
        }
    }
}
