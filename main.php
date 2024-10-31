<?php
/*
Plugin Name: IDX Realty Pro
Plugin URI: https://idxrealtypro.com/
Description: Add property search function to your website using your MLS provider's data.
Version: 3.2.0
Author: IDX Realty, LLC
Author URI: https://idxrealtypro.com/
License: GPL v2 or later
GitLab Plugin URI: https://gitlab.com/pcgrejaldo/realty-idx-pro
*/
/*
This file is part of IDX Realty Pro.

IDX Realty Pro is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IDX Realty Pro is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/gpl-3.0.html>.
*/

/**
 * If ABSPATH is not defined, bail immediately
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

define( 'IDX_REALTY_PRO_PLUGIN_FILE', __FILE__ );
define( 'IDX_REALTY_PRO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'IDX_REALTY_PRO_REQUIRED_WP', '4.7' );
define( 'IDX_REALTY_PRO_REQUIRED_PHP', '5.5.0' );

if ( ! function_exists( 'get_plugin_data' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Load text domain
 */
function idxrp_load_plugin_textdomain()
{
    load_plugin_textdomain( 'realty-idx-pro', false, 'realty-idx-pro/languages' );
}

/**
 * Load text domain on init
 */
add_action( 'init', 'idxrp_load_plugin_textdomain' );
//@formatter:off
if ( version_compare(
    PHP_VERSION,
    IDX_REALTY_PRO_REQUIRED_PHP,
    '<'
) ) { // If PHP is less than the required version, print admin notice
    function idxrp_required_php_version()
    {
        ?>
        <div class='error' id='message'>
            <p><?php printf(
                    __(
                        'IDX Realty Pro plugin requires at least PHP %s to work properly. Your server is currently using PHP %s.',
                        'realty-idx-pro'
                    ),
                    IDX_REALTY_PRO_REQUIRED_PHP,
                    PHP_VERSION
                ); ?></p>
        </div>
        <?php
    }

    add_action( 'admin_notices', 'idxrp_required_php_version' );
} else if ( version_compare(
    str_replace( array( '-src' ), '', $GLOBALS['wp_version'] ),
    IDX_REALTY_PRO_REQUIRED_WP,
    '<'
) ) { // If Wordpress is less than the required version, print admin notice
    function idxrp_required_wp_version()
    {
        ?>
        <div class='error' id='message'>
            <p><?php printf(
                    __(
                        'IDX Realty Pro plugin requires Wordpress %s to work properly.',
                        'realty-idx-pro'
                    ),
                    IDX_REALTY_PRO_REQUIRED_WP
                ); ?></p>
        </div>
        <?php
    }

    add_action( 'admin_notices', 'idxrp_required_wp_version' );
} else {
    // Requirements are met, require autoload file
    require_once 'autoload.php';
}
//@formatter:on
