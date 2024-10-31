<?php
/**
 * Bail immediately if ABSPATH is not defined
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

/**
 * Class IDX_Realty_Pro_Loader initializes the plugin
 */
class IDX_Realty_Pro_Loader
{

    /**
     * Autoloader initializer method
     */
    public static function init()
    {
        if ( defined( 'IDXRP_LIB_DEPENDENCY_AUTOLOAD' ) &&
             false !== strpos( IDXRP_LIB_DEPENDENCY_AUTOLOAD, 'autoload.php' ) &&
             file_exists( IDXRP_LIB_DEPENDENCY_AUTOLOAD )
        ) {
            require_once IDXRP_LIB_DEPENDENCY_AUTOLOAD;
        } else if ( file_exists( WP_CONTENT_DIR . '/vendor/autoload.php' ) ) {
            require_once WP_CONTENT_DIR . '/vendor/autoload.php';
        }

        // Register auto loader
        spl_autoload_register( [ 'IDX_Realty_Pro_Loader', 'autoload' ] );

        // Register actions on plugin activation
        register_activation_hook(
            IDX_REALTY_PRO_PLUGIN_FILE,
            [ 'IDX_Realty_Pro_Loader', 'activationActions' ]
        );
        // Register actions on plugin deactivation
        register_deactivation_hook(
            IDX_REALTY_PRO_PLUGIN_FILE,
            [ 'IDX_Realty_Pro_Loader', 'deactivationActions' ]
        );

        // Initialize plugin controllers on "plugins_loaded" hook with lower priority to ensure dependency plugin is
        // already loaded
        add_action( 'plugins_loaded', [ 'IDX_Realty_Pro_Loader', 'initControllers' ], 99 );
    }

    /**
     * Autoloader method
     *
     * @param string $class
     */
    public static function autoload( $class )
    {
        if ( 'IDXRealtyPro' === mb_substr( $class, 0, 12 ) ) {
            $file = sprintf( '%s%s.php', IDX_REALTY_PRO_PLUGIN_DIR_PATH, str_replace( '\\', '/', $class ) );

            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }
    }

    /**
     * Initialize plugin controllers
     */
    public static function initControllers()
    {
        \IDXRealtyPro\Controller\Index::instance()->init();
    }

    /**
     * Run plugin activation routine
     */
    public static function activationActions()
    {
        \IDXRealtyPro\Controller\Index::instance()->registerPostTypes();
        \IDXRealtyPro\Model\ScSettings::instance()->downloadAppsDefaultTemplate();
        flush_rewrite_rules();
    }

    /**
     * Run deactivation routine
     */
    public static function deactivationActions()
    {
        flush_rewrite_rules();
    }
}

// Initialize auto loader
IDX_Realty_Pro_Loader::init();
