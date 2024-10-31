<?php
/**
 * User: Paul Grejaldo
 * Date: 2016/10/08
 * Time: 9:02 PM
 */

namespace IDXRealtyPro\Helper;

use IDXRealtyPro\Model\Option;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Plugin
{

    /**
     * @param string $file
     *
     * @return object
     */
    public static function getPluginData( $file = IDX_REALTY_PRO_PLUGIN_FILE )
    {
        $default_headers = [
            'Name'        => 'Plugin Name',
            'PluginURI'   => 'Plugin URI',
            'Version'     => 'Version',
            'Description' => 'Description',
            'Author'      => 'Author',
            'AuthorURI'   => 'Author URI',
            'TextDomain'  => 'Text Domain',
            'DomainPath'  => 'Domain Path',
            'Network'     => 'Network',
            // Site Wide Only is deprecated in favor of Network.
            '_sitewide'   => 'Site Wide Only',
        ];

        return (object) get_file_data( $file, $default_headers, 'plugin' );
    }

    /**
     * Get script/css extension or file dir
     *
     * @param string $which Either 'ext' or 'file-dir'
     *
     * @return mixed
     */
    public static function getScriptPart( $which )
    {
        $str = '';
        switch ( $which ) {
            case 'ext':
                $str = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
                break;
            case 'file-dir':
                $str = 'dist';
                if ( defined( 'IDXRP_DEV' ) && IDXRP_DEV ) {
                    $str = 'dev';
                }
                break;
            case 'version':
                if ( defined( 'IDXRP_DEV' ) && IDXRP_DEV ) {
                    $str = time();
                } else {
                    $plugin = Plugin::getPluginData();
                    $str    = $plugin->Version;
                }
                break;
        }

        return $str;
    }

    /**
     * Get the license transient hash key
     *
     * @param string $license_key The license key to include in the hash
     *
     * @return string The hashed license key
     */
    public static function getLicenseTransientKey( $license_key = '' )
    {
        return sha1( "realty-idx-pro-{$license_key}" );
    }

    /**
     * Get plugin's WP REST API namespace
     *
     * @return string
     */
    public static function getWpApiNamespace()
    {
        $version = self::getPluginData()->Version;

        return "idxrp/v{$version}";
    }

    /**
     * Get plugin admin REST base
     *
     * @return string
     */
    public static function getWpApiAdminRestBase()
    {
        return 'admin';
    }

    /**
     * Get plugin admin REST base
     *
     * @return string
     */
    public static function getWpApiPostEditorRestBase()
    {
        return 'post_editor';
    }

    /**
     * Get plugin admin REST base
     *
     * @return string
     */
    public static function getWpApiSearchRestBase()
    {
        return 'search';
    }

    /**
     * Get User REST base
     *
     * @return string
     */
    public static function getWpApiUserRestbase()
    {
        return 'user';
    }

    /**
     * Get template post type
     *
     * @return string
     */
    public static function getTemplatePostType()
    {
        return 'idxrp_template';
    }

    /**
     * Get property post type created from plugin admin page
     *
     * @param string $key Empty string returns all elements, 'slug', 'singular_name', 'name'
     *
     * @return bool|mixed|null|string
     */
    public static function getPropertyPostType( $key = 'slug' )
    {
        if ( 'all' === $key || ! in_array( $key, [ 'slug', 'singular_name', 'name' ] ) ) {
            $key = '';
        }
        $post_type = Option::instance()->get( 'post_type', $key );
        $post_type = $post_type ? $post_type : '';

        return $post_type;
    }

    /**
     * Get registered taxonomies in 'taxonomy' => 'field_name' pair
     *
     * @return array
     */
    public static function getRegisteredTaxonomies()
    {
        $taxonomies = [];
        $registered = Option::instance()->get( 'tax_archive', 'taxonomies' );
        if ( ! empty( $registered ) ) {
            foreach ( (array) $registered as $field_name ) {
                $taxonomy = self::getRetsFieldTaxonomy( $field_name );

                $taxonomies[ $taxonomy ] = $field_name;
            }
        }

        return $taxonomies;
    }

    /**
     * Server Resource Class
     *
     * @return string
     */
    public static function getServerIdTaxonomy()
    {
        return 'server_id';
    }

    /**
     * Server Resource Class
     *
     * @return string
     */
    public static function getResourceIdTaxonomy()
    {
        return 'resource_id';
    }

    /**
     * Server Resource Class
     *
     * @return string
     */
    public static function getClassNameTaxonomy()
    {
        return 'class_name';
    }

    /**
     * Server Key
     *
     * @return string
     */
    public static function getServerKeyTaxonomy()
    {
        return 'server_key';
    }

    /**
     * Set flush rewrite rules
     */
    public static function setFlushRewriteRules()
    {
        set_transient( '_idxrp_flush_rewrite_rules', 1 );
    }

    /**
     * Flush rewrite rules
     */
    public static function flushRewriteRules()
    {
        $flush_rewrite_rules = get_transient( '_idxrp_flush_rewrite_rules' );
        if ( $flush_rewrite_rules ) {
            flush_rewrite_rules( false );
            delete_transient( '_idxrp_flush_rewrite_rules' );
        }
    }

    /**
     * Get shortcode settings post type
     *
     * @return string
     */
    public static function getScSettingsPostType()
    {
        return 'idxrp_sc_settings';
    }

    public static function isReservedTerm( $term )
    {
        $reserved = [
            'attachment',
            'attachment_id',
            'author',
            'author_name',
            'calendar',
            'cat',
            'category',
            'category__and',
            'category__in',
            'category__not_in',
            'category_name',
            'comments_per_page',
            'comments_popup',
            'custom',
            'customize_messenger_channel',
            'customized',
            'cpage',
            'day',
            'debug',
            'embed',
            'error',
            'exact',
            'feed',
            'hour',
            'link_category',
            'm',
            'minute',
            'monthnum',
            'more',
            'name',
            'nav_menu',
            'nonce',
            'nopaging',
            'offset',
            'order',
            'orderby',
            'p',
            'page',
            'page_id',
            'paged',
            'pagename',
            'pb',
            'perm',
            'post',
            'post__in',
            'post__not_in',
            'post_format',
            'post_mime_type',
            'post_status',
            'post_tag',
            'post_type',
            'posts',
            'posts_per_archive_page',
            'posts_per_page',
            'preview',
            'robots',
            's',
            'search',
            'second',
            'sentence',
            'showposts',
            'static',
            'subpost',
            'subpost_id',
            'tag',
            'tag__and',
            'tag__in',
            'tag__not_in',
            'tag_id',
            'tag_slug__and',
            'tag_slug__in',
            'taxonomy',
            'tb',
            'term',
            'terms',
            'theme',
            'title',
            'type',
            'w',
            'withcomments',
            'withoutcomments',
            'year',
        ];

        $reserved = array_flip( $reserved );

        return isset( $reserved[ $term ] );
    }

    /**
     * Get RETS Field taxonomy
     *
     * @param string $field_name RETS field name
     *
     * @return string Prefixed taxonomy name if sanitized $field_name matches a reserved WP term, otherwise,
     * just sanitized $field_name
     */
    public static function getRetsFieldTaxonomy( $field_name )
    {
        $taxonomy_name = sanitize_key( $field_name );

        if ( self::isReservedTerm( $taxonomy_name ) ) {
            $taxonomy_name = "idxrp_{$taxonomy_name}";
        }

        if ( 32 < strlen( $taxonomy_name ) ) {
            $taxonomy_name = substr( $taxonomy_name, 0, 32 );
        }

        return $taxonomy_name;
    }

    /**
     * Get masked license key string
     *
     * @return string
     */
    public static function getMaskedLicenseKey()
    {
        $key = Option::instance()->get( 'license', 'key' );

        return Util::getMaskedValue( $key );
    }

    /**
     * Check if license is valid
     *
     * @return array|bool
     */
    public static function isLicenseValid()
    {
        $is_valid = self::processLicense();

        if ( is_wp_error( $is_valid ) ) {
            return false;
        }

        return $is_valid;
    }

    /**
     * Retrieve the cached license data if it exists
     *
     * @return bool|object
     */
    public static function getLicenseData()
    {
        return Option::instance()->get( 'license', 'key' ) ? get_transient(
            self::getLicenseTransientKey( Option::instance()->get( 'license', 'key' ) )
        ) : false;
    }

    /**
     * Either "activate", "deactivate", or "check" the current saved license key
     *
     * @param string $action Either to "activate", "deactivate", or "check" license.
     * @param bool   $force  If true, force remote license check, otherwise, check cached license data.
     *
     * @return array|bool|\WP_Error
     */
    public static function processLicense( $action = 'check', $force = false )
    {
        $license_data = self::getLicenseData();
        if ( in_array( $action, [ 'activate', 'deactivate' ] ) || $force || ! $license_data ) {
            $license_key = Option::instance()->get( 'license', 'key' )
                ? sanitize_text_field( Option::instance()->get( 'license', 'key' ) )
                : '';

            if ( empty( $license_key ) ) {
                return new \WP_Error( 'unknown_license_key', __( 'Unknown license key to check.', 'realty-idx-pro' ) );
            }

            $api_args = [
                'edd_action' => "{$action}_license",
                'license'    => $license_key,
                'item_name'  => urlencode( self::getPluginData()->Name )
            ];

            $remote_reponse = wp_remote_get(
                esc_url_raw( add_query_arg( $api_args, 'http://idxrealtypro.com/' ) ),
                [ 'timeout' => 15, 'sslverify' => false ]
            );

            if ( is_wp_error( $remote_reponse ) ) {
                return $remote_reponse;
            }

            $license_data = json_decode( wp_remote_retrieve_body( $remote_reponse ) );

            $license_data_arr = (array) $license_data;

            if ( empty( $license_data_arr ) ) {
                return new \WP_Error(
                    'no_response',
                    sprintf(
                        __(
                            'The remote server did not respond. Please contact the plugin author [%s].',
                            'realty-idx-pro'
                        ),
                        self::getPluginData()->Author
                    ),
                    [ 'status' => 500 ]
                );
            }

            if ( 'invalid' === $license_data->license ) {
                $error_messages = [
                    __( 'ERROR: Unknown error encountered while processing license key.', 'realty-idx-pro' ),
                    __( 'ERROR: License Key does not exist.', 'realty-idx-pro' ),
                    __( 'ERROR: License Key was revoked.', 'realty-idx-pro' ),
                    __( 'ERROR: License Key is expired.', 'realty-idx-pro' ),
                    __( 'ERROR: License Key activation limit reached.', 'realty-idx-pro' ),
                    __( 'ERROR: License Key mismatch.', 'realty-idx-pro' ),
                    __( 'ERROR: Item name mismatch.', 'realty-idx-pro' )
                ];
                switch ( $license_data->error ) {
                    case 'missing':
                        $msg_num = 1;
                        break;
                    case 'revoked':
                        $msg_num = 2;
                        break;
                    case 'expired':
                        $msg_num = 3;
                        break;
                    case 'no_activations_left':
                        $msg_num = 4;
                        break;
                    case 'key_mismatch':
                        $msg_num = 5;
                        break;
                    case 'item_name_mismatch':
                        $msg_num = 6;
                        break;
                    default:
                        $msg_num = 0;
                }

                return new \WP_Error(
                    "license_key_{$license_data->error}",
                    $error_messages[ $msg_num ],
                    [ 'status' => 500 ]
                );
            }

            set_transient( self::getLicenseTransientKey( $license_key ), $license_data, DAY_IN_SECONDS * 5 );
        }

        return self::getLicenseData() && self::getLicenseData()->license === 'valid';
    }
}
