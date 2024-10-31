<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/11
 * Time: 4:57 PM
 */

namespace IDXRealtyPro\Controller;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\ScSettings as Model;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class ScSettings
{

    protected static $instance;

    public function __construct()
    {
    }

    /**
     * Return an instance of this class
     *
     * @return ScSettings The class instance object
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
    }

    protected function actions()
    {
        add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ] );
        add_action( 'add_meta_boxes', [ $this, 'addMetaBoxes' ], 10, 2 );
        add_action( 'post_submitbox_misc_actions', [ $this, 'forceDownloadTemplateCb' ] );
        add_action( 'save_post_' . Plugin::getScSettingsPostType(), [ $this, 'savePost' ], 10, 3 );
        add_action( 'rest_api_init', [ $this, 'routes' ] );
    }

    public function adminEnqueueScripts( $hook )
    {
        global $typenow;

        $ext      = Plugin::getScriptPart( 'ext' );
        $file_dir = Plugin::getScriptPart( 'file-dir' );
        $version  = Plugin::getScriptPart( 'version' );

        if ( in_array( $hook, [ 'post.php', 'post-new.php' ] ) && $typenow === Plugin::getScSettingsPostType() ) {
            wp_enqueue_style(
                'idxrp-sc-settings-editor',
                plugins_url( "css/sc-settings/index{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-bootstrap', 'font-awesome', 'roboto-font' ],
                $version
            );

            wp_enqueue_script(
                'idxrp-sc-settings-app',
                plugins_url( "js/{$file_dir}/idxrp-sc-settings/index.js", IDX_REALTY_PRO_PLUGIN_FILE ),
                [ 'idxrp-vendor-deps' ],
                $version,
                true
            );
            $l10n = Util::mergeL10nFiles( [ 'post-editor/idxrp-sc-settings.php' ] );
            wp_localize_script( 'idxrp-sc-settings-app', 'idxrp', $l10n );
        }
    }

    /**
     * @param $post_type
     * @param $post
     */
    public function addMetaBoxes( $post_type, $post )
    {
        if ( $post_type !== Plugin::getScSettingsPostType() ) {
            return;
        }

        add_meta_box(
            'idxrp-sc-settings',
            __( 'Shorcode Settings', 'realty-idx-pro' ),
            [ $this, 'scSettingsForm' ],
            Plugin::getScSettingsPostType(),
            'normal',
            'high',
            [ 'box' => 'idxrp-sc-settings-container' ]
        );
    }

    /**
     * Print app container element
     *
     * @param null  $post
     * @param array $callback_args
     */
    public function scSettingsForm( $post = null, $callback_args = [] )
    {
        $args = ! empty( $callback_args['args'] ) ? $callback_args['args'] : [ 'box' => '' ];
        printf( '<div id="%s" class="idxrpbs"></div>', $args['box'] );
    }

    /**
     * Print force download default templates checkbox
     *
     * @param \WP_Post $post
     */
    public function forceDownloadTemplateCb( $post )
    {
        global $typenow;

        if ( $typenow !== Plugin::getScSettingsPostType() ) {
            return;
        }
        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="idxrp_force_download_template" value="1" /> %s</label></div>',
            __( 'Force download default IDXRP templates', 'realty-idx-pro' )
        );
    }

    /**
     * Save task settings
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @param bool     $update
     */
    public function savePost( $post_id, $post, $update )
    {
        if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
             wp_is_post_revision( $post_id )  ) {
            return;
        }

        $data = array_intersect_key( $_POST, Model::instance()->fields );
        Model::instance()->savePost( $post_id, $data );

        $server_key     = sanitize_text_field( $_POST['server_key'] );
        $template_types = [ 'single', 'single-marker' ];
        $force_download = isset( $_POST['idxrp_force_download_template'] );
        $located        = '';

        // If force download checkbox is not checked, check if template file already exists
        if ( ! $force_download ) {
            foreach ( $template_types as $template_type ) {
                $located = View::instance()->make(
                    [ "idxrp/front/default/{$template_type}/{$server_key}.php", ],
                    [],
                    false
                );
                if ( ! $located ) {
                    break;
                }
            }
        }

        // If default template file does not exist, download from remote server
        if ( $force_download || ! $located ) {
            Model::instance()->downloadDefaultTemplateFiles( $server_key );
        }
    }

    /**
     * Register routes for this controller
     */
    public function routes()
    {
        $route = new \IDXRealtyPro\Route\PostEditor\ScSettings();
        $route->register_routes();
    }
}
