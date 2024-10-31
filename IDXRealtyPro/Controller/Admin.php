<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/03/14
 * Time: 2:32 PM
 */

namespace IDXRealtyPro\Controller;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Admin as Model;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Admin
{

    protected static $instance;

    protected $admin_page_slug;

    protected $admin_page_hook_suffix;

    protected $help_page_slug;

    protected $help_page_hook_suffix;

    public function __construct()
    {
        $this->admin_page_slug = 'realty-idx-pro-admin';
        $this->help_page_slug  = 'realty-idx-pro-help';
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
     * Initialize plugin hook handlers
     */
    public function init()
    {
        $this->actions();
        $this->filters();
    }

    /**
     * Plugin actions
     */
    public function actions()
    {
        add_action( 'init', [ $this, 'clearCachedData' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ], 100 );
        add_action( 'admin_menu', [ $this, 'adminMenu' ], 11 );
        add_action( 'save_post', [ Model::instance(), 'checkShortcode' ], 10, 3 );
        add_action( 'admin_bar_menu', [ $this, 'adminBarMenu' ], 9999 );
    }

    /**
     * Plugin filters
     */
    public function filters()
    {
        add_filter( 'parent_file', [ $this, 'fixParentFile' ] );
    }

    /**
     * Add admin bar
     *
     * @param \WP_Admin_Bar $wp_admin_bar
     */
    public function adminBarMenu( $wp_admin_bar )
    {
        $tools = [
            [
                'id'    => 'idxrp-tools',
                'title' => 'IDXRP',
                'href'  => '#',
                'meta'  => [ 'class' => 'idxrp-tools-menu' ]
            ],
            [
                'id'     => 'idxrp-clear-cache',
                'title'  => __( 'Clear Cached Data', 'realty-idx-pro' ),
                'href'   => esc_url_raw(
                    add_query_arg(
                        [ 'idxrp_tool' => 'clear-cache', '_wpnonce' => wp_create_nonce( 'idxrp-tool-clear-cache' ) ]
                    )
                ),
                'meta'   => [],
                'parent' => 'idxrp-tools',
            ]
        ];

        foreach ( $tools as $tool ) {
            $wp_admin_bar->add_node( $tool );
        }
    }

    /**
     * Clear cached data
     */
    public function clearCachedData()
    {
        if ( ! empty( $_GET['idxrp_tool'] ) && 'clear-cache' === $_GET['idxrp_tool'] &&
             ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'idxrp-tool-clear-cache' ) ) {
            Model::instance()->clearTransients();

            wp_safe_redirect( esc_url_raw( remove_query_arg( [ 'idxrp_tool', '_wpnonce' ] ) ) );
            exit;
        }
    }

    /**
     * Register admin menus
     */
    public function adminMenu()
    {
        $settings_page_title = sprintf( __( '%s Admin', 'realty-idx-pro' ), Plugin::getPluginData()->Name );

        $this->admin_page_hook_suffix = add_menu_page(
            $settings_page_title,
            Plugin::getPluginData()->Name,
            'manage_options',
            $this->admin_page_slug,
            [ $this, 'renderPluginAdminPage' ],
            plugins_url( 'images/admin-menu-icon.png', IDX_REALTY_PRO_PLUGIN_FILE )
        );
        add_submenu_page(
            $this->admin_page_slug,
            $settings_page_title,
            __( 'Admin', 'realty-idx-pro' ),
            'manage_options',
            $this->admin_page_slug,
            [ $this, 'renderPluginAdminPage' ]
        );

        if ( Plugin::isLicenseValid() ) {
            add_submenu_page(
                $this->admin_page_slug,
                sprintf( __( '%s Shortcode Settings', 'realty-idx-pro' ), Plugin::getPluginData()->Name ),
                __( 'SC Settings', 'realty-idx-pro' ),
                'manage_options',
                'edit.php?post_type=' . Plugin::getScSettingsPostType()
            );

            add_submenu_page(
                $this->admin_page_slug,
                sprintf( __( '%s Views Template', 'realty-idx-pro' ), Plugin::getPluginData()->Name ),
                __( 'Template', 'realty-idx-pro' ),
                'manage_options',
                'edit.php?post_type=' . Plugin::getTemplatePostType()
            );

            $post_type = Plugin::getPropertyPostType( 'all' );
            if ( $post_type ) {
                add_submenu_page(
                    $this->admin_page_slug,
                    $post_type['name'],
                    $post_type['name'],
                    'manage_options',
                    'edit.php?post_type=' . $post_type['slug']
                );
            }
        }

        $this->help_page_hook_suffix = add_submenu_page(
            $this->admin_page_slug,
            __( 'IDX Realty Pro Help' ),
            __( 'Help' ),
            'manage_options',
            $this->help_page_slug,
            [ $this, 'renderHelpPage' ]
        );
    }

    /**
     * Renders plugin admin page
     */
    public function renderPluginAdminPage()
    {
        View::instance()->make( 'idxrp/admin/index.php' );
    }

    public function renderHelpPage()
    {
        View::instance()->make( 'idxrp/admin/help.php' );
    }

    /**
     * Enqueue scripts and styles for plugin admin page
     *
     * @param string $hook
     */
    public function adminEnqueueScripts( $hook )
    {
        $ext      = Plugin::getScriptPart( 'ext' );
        $file_dir = Plugin::getScriptPart( 'file-dir' );
        $version  = Plugin::getScriptPart( 'version' );

        if ( $hook === $this->admin_page_hook_suffix ) {
            wp_enqueue_style(
                'idxrp-admin-page',
                plugins_url( "css/admin/index{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-bootstrap', 'font-awesome', 'roboto-font' ]
            );

            wp_enqueue_script(
                'idxrp-admin-app',
                plugins_url( "js/{$file_dir}/admin/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-vendor-deps' ],
                $version,
                true
            );

            $l10n = Util::mergeL10nFiles( [ 'admin/overview.php', 'admin/license.php', 'admin/settings.php' ] );

            wp_localize_script( 'idxrp-admin-app', 'idxrp', $l10n );
        } else if ( $hook === $this->help_page_hook_suffix ) {
            wp_enqueue_style(
                'idxrp-admin-help',
                plugins_url( "css/help/index{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-bootstrap', 'font-awesome', 'roboto-font' ]
            );
            wp_enqueue_script(
                'idxrp-admin-help',
                plugins_url( "js/{$file_dir}/help/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-vendor-deps' ],
                $version,
                true
            );
            $l10n = Util::mergeL10nFiles( [ 'help/content.php' ] );
            wp_localize_script( 'idxrp-admin-help', 'idxrp', $l10n );
        }
    }

    /**
     * Fix active admin menu item
     *
     * @param string $parent_file
     *
     * @return string
     */
    public function fixParentFile( $parent_file )
    {
        global $submenu_file, $current_screen;

        $post_types = [
            Plugin::getTemplatePostType(),
            Plugin::getScSettingsPostType()
        ];
        $post_type  = Plugin::getPropertyPostType();
        if ( $post_type ) {
            $post_types[] = $post_type;
        }
        if ( in_array( $current_screen->post_type, $post_types ) ) {
            $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            $parent_file  = $this->admin_page_slug;
        }

        return $parent_file;
    }
}
