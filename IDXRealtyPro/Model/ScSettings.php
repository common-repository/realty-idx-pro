<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/06/11
 * Time: 7:36 PM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Plugin as HPlugin;
use IDXRealtyPro\Helper\Util;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class ScSettings extends Base
{

    protected static $instance;

    protected $fields;

    public function __construct()
    {
        parent::__construct();

        $this->fields = [
            'server_id'       => 0,
            'server_key'      => '',
            'resource_class'  => '',
            'settings'        => [],
            'orderby'         => [],
            'primary_fields'  => [],
            'adv_fields'      => [],
            'default_order'   => 'asc',
            'default_view'    => 'list',
            'list_template'   => 0,
            'photo_template'  => 0,
            'map_template'    => 0,
            'marker_template' => 0,
            'search_only'     => false,
            'hide_search'     => false,
            'class_select'    => [],
            'search_page'     => 0,
        ];
    }

    /**
     * Magic get
     *
     * @param string $key
     *
     * @return null
     */
    public function __get( $key )
    {
        if ( isset( $this->$key ) ) {
            return $this->$key;
        }

        return null;
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
     * @param string $return
     *
     * @return array
     */
    public function getScSettingsList( $return = 'all' )
    {
        $settings = get_posts(
            [
                'post_type'      => HPlugin::getScSettingsPostType(),
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => $return
            ]
        );

        if ( 'ids' === $return ) {
            return $settings;
        }

        $app_settings = [];
        foreach ( $settings as $setting ) {
            foreach ( $this->fields as $meta_key => $default_value ) {
                $app_settings[ $setting->ID ][ $meta_key ] = get_post_meta( $setting->ID, $meta_key, true );

                if ( 'server_id' === $meta_key ) {
                    $app_settings[ $setting->ID ][ $meta_key ] = absint( $app_settings[ $setting->ID ][ $meta_key ] );
                } else if ( in_array(
                    $meta_key,
                    [ 'settings', 'orderby', 'primary_fields', 'adv_fields', 'class_select' ]
                ) ) {
                    if ( ! empty( $app_settings[ $setting->ID ][ $meta_key ] ) ) {
                        foreach ( $app_settings[ $setting->ID ][ $meta_key ] as &$datum ) {
                            $datum = json_decode( $datum, true );
                        }
                    } else {
                        $app_settings[ $setting->ID ][ $meta_key ] = $default_value;
                    }
                }
            }
        }

        return $app_settings;
    }

    /**
     * Get shortcode settings
     *
     * @param int $settings_id
     *
     * @return array|\WP_Error
     */
    public function get( $settings_id )
    {
        $cache_key = 'idxrp_' . sha1( "sc-settings-{$settings_id}" );

        $data = get_transient( $cache_key );
        if ( ! $data ) {
            $post = get_post( $settings_id );
            if ( ! $post ) {
                return new \WP_Error(
                    'idxrp_app_error',
                    __( 'Given shortcode settings ID %d not found.', 'realty-idx-pro' ),
                    [ 'status' => 500 ]
                );
            } else if ( $post->post_type !== HPlugin::getScSettingsPostType() ) {
                return new \WP_Error(
                    'idxrp_app_error',
                    sprintf(
                        __( 'Given post ID %d is not of %s post type.', 'realty-idx-pro' ),
                        $post->ID,
                        HPlugin::getScSettingsPostType()
                    ),
                    [ 'status' => 500 ]
                );
            }

            foreach ( $this->fields as $key => $value ) {
                $data[ $key ] = get_post_meta( $post->ID, $key, true );
                if ( in_array(
                    $key,
                    [ 'server_id', 'list_template', 'photo_template', 'map_template', 'marker_template' ]
                ) ) {
                    $data[ $key ] = absint( $data[ $key ] );
                } else if ( in_array(
                    $key,
                    [ 'settings', 'orderby', 'primary_fields', 'adv_fields', 'class_select' ]
                ) ) {
                    if ( ! empty( $data[ $key ] ) ) {
                        foreach ( $data[ $key ] as &$datum ) {
                            $datum = json_decode( $datum, true );
                        }
                    } else {
                        $data[ $key ] = $value;
                    }
                } else if ( in_array( $key, [ 'search_only', 'hide_search' ] ) ) {
                    $data[ $key ] = ! ! intval( $data[ $key ] );
                }
            }

            set_transient( $cache_key, $data, HOUR_IN_SECONDS );
        }

        return $data;
    }

    /**
     * Save shortcode settings
     *
     * @param int   $setting_id
     * @param mixed $data
     *
     * @return array
     */
    public function savePost( $setting_id, $data )
    {
        if ( $data ) {
            $data = wp_parse_args( Util::sanitizeTextFieldR( $data ), $this->fields );
            foreach ( $data as $meta_key => &$meta_value ) {
                update_post_meta( $setting_id, $meta_key, $meta_value );
            }
            $sc_settings_transient = 'idxrp_' . sha1( "sc-settings-{$setting_id}" );
            delete_transient( $sc_settings_transient );
            $rest_fields_transient = 'idxrp_' . sha1( "rest-fields-{$setting_id}" );
            delete_transient( $rest_fields_transient );
        }

        return $data;
    }

    /**
     * Download default template files for given server key
     *
     * @param string $server_key
     *
     * @return array|bool|mixed|\WP_Error
     */
    public function downloadDefaultTemplateFiles( $server_key )
    {
        $plugin   = HPlugin::getPluginData();
        $url      = trailingslashit( $plugin->PluginURI ) . "idxrp_template/{$server_key}";
        $license  = Option::instance()->get( 'license', 'key' );
        $site     = home_url();
        $version  = $plugin->Version;
        $request  = esc_url_raw( add_query_arg( compact( 'license', 'site', 'version' ), $url ) );
        $response = wp_remote_get( $request, [ 'timeout' => 30 ] );
        if ( is_wp_error( $response ) ) {
            error_log( $response->get_error_message(), E_USER_WARNING );

            return $response;
        }
        $body     = wp_remote_retrieve_body( $response );
        $zip_file = wp_tempnam();

        $fp = fopen( $zip_file, 'w' );
        fwrite( $fp, $body );
        if ( ! fclose( $fp ) ) {
            $error = new \WP_Error(
                'template_download_error',
                sprintf( __( 'Error closing file pointer to: %s', 'realty-idx-pro' ), $zip_file ),
                [ 'status' => 500 ]
            );
            error_log( $error->get_error_message(), E_USER_WARNING );

            return $error;
        }

        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        WP_Filesystem();
        $extracted = unzip_file( $zip_file, WP_CONTENT_DIR );
        if ( is_wp_error( $extracted ) ) {
            error_log( $extracted->get_error_message(), E_USER_WARNING );

            return $extracted;
        } else if ( file_exists( $zip_file ) ) {
            unlink( $zip_file );
        }

        return true;
    }

    /**
     * Download default template files for all generated app shortcodes
     *
     * @return bool
     */
    public function downloadAppsDefaultTemplate()
    {
        $idxrp_scs = get_posts(
            [
                'post_type'      => HPlugin::getScSettingsPostType(),
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids'
            ]
        );
        if ( empty( $idxrp_scs ) ) {
            return true;
        }

        $server_keys = [];
        foreach ( $idxrp_scs as $sc_id ) {
            $server_keys[] = get_post_meta( $sc_id, 'server_key', true );
        }
        $server_keys = array_flip( array_flip( $server_keys ) );
        foreach ( $server_keys as $server_key ) {
            $this->downloadDefaultTemplateFiles( $server_key );
        }

        return true;
    }
}
