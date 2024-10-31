<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/19
 * Time: 8:04 AM
 */

namespace IDXRealtyPro\Controller;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Template
{

    protected static $instance;

    public function __construct()
    {
    }

    /**
     * Return an instance of this class
     *
     * @return Template The class instance object
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

    protected function actions()
    {
        add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ] );
        add_action( 'rest_api_init', [ $this, 'routes' ] );
        add_action( 'admin_footer-post.php', [ $this, 'templatesDialog' ] );
        add_action( 'admin_footer-post-new.php', [ $this, 'templatesDialog' ] );
    }

    protected function filters()
    {
    }

    public function adminEnqueueScripts( $hook_suffix )
    {
        global $typenow;

        if ( in_array( $hook_suffix, [ 'post-new.php', 'post.php' ] ) ) {
            $ext      = Plugin::getScriptPart( 'ext' );
            $file_dir = Plugin::getScriptPart( 'file-dir' );
            $version  = Plugin::getScriptPart( 'version' );

            if ( $typenow === Plugin::getTemplatePostType() ) {
                wp_enqueue_style(
                    'idxrp-template-styles',
                    plugins_url( "css/template/index{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                    [ 'idxrp-bootstrap' ],
                    $version
                );

                wp_enqueue_script(
                    'idxrp-template-qt',
                    plugins_url( "js/qt/idxrp-load-template{$ext}.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                    [ 'jquery', 'quicktags', 'wp-util', 'idxrp-vendor-deps' ],
                    $version,
                    true
                );
                wp_localize_script(
                    'idxrp-template-qt',
                    'idxrp',
                    Util::mergeL10nFiles( [ 'post-editor/idxrp-load-template-qt.php' ], true )
                );

                add_action( 'admin_footer', [ $this, 'retsFieldSelectTemplate' ] );
            }
        }
    }

    /**
     * Print hidden RETS fields app entry point markup
     */
    public function templatesDialog()
    {
        global $typenow;

        if ( $typenow === Plugin::getTemplatePostType() ) {
            ?>
            <div class="idxrpbs" title="<?php esc_attr_e(
                'RETS Fields',
                'realty-idx-pro'
            ); ?>">
                <div class="idxrp-rets-fields-dialog modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <?php
                            View::instance()->make(
                                [
                                    'IDXRPRealtyPro/dialog/rets-fields.php',
                                    'idxrp/dialog/rets-fields.php'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="idxrpbs" title="<?php esc_attr_e(
                'Server IDs',
                'realty-idx-pro'
            ); ?>">
                <div class="idxrp-rets-server-dialog modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <?php
                            View::instance()->make(
                                [
                                    'IDXRPRealtyPro/dialog/server-select.php',
                                    'idxrp/dialog/server-select.php'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function retsFieldSelectTemplate()
    {
        View::instance()->make( [ 'idxrp/post-editor/rets-field-dialog-controls.php' ], [] );
    }

    /**
     * Register routes for this controller
     */
    public function routes()
    {
        $route = new \IDXRealtyPro\Route\PostEditor\Template();
        $route->register_routes();
    }
}
