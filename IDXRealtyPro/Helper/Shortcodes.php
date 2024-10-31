<?php
/**
 * Author: Paul Grejaldo
 * Date: 2016/11/04
 * Time: 12:30 PM
 */

namespace IDXRealtyPro\Helper;

use IDXRealtyPro\Model\Api;
use IDXRealtyPro\Model\Front;
use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\User;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Shortcodes
{

    protected static $search_app_ids = [];

    protected static $shortcodes = [];

    /**
     * Register plugin shortcodes
     */
    public static function init()
    {
        self::$shortcodes = [
            'idxrp_search_app' => [ 'IDXRealtyPro\Helper\Shortcodes', 'searchApp' ],
            'idxrp_group'      => [ 'IDXRealtyPro\Helper\Shortcodes', 'searchApp' ],
            'idxrp_field'      => [ 'IDXRealtyPro\Helper\Shortcodes', 'getPropertyFieldValue' ],
            'idxrp_user_acct'  => [ 'IDXRealtyPro\Helper\Shortcodes', 'userAccount' ],
        ];

        foreach ( self::$shortcodes as $shortcode => $callback ) {
            add_shortcode( $shortcode, $callback );
        }
    }

    /**
     * Shortcode regular expression pattern
     *
     * @see get_shortcode_regex()
     *
     * @param string $shortcode
     *
     * @return string
     */
    public static function getShortcodeRegex( $shortcode )
    {
        return
            '\\['
            . '(\\[?)'
            . "($shortcode)"
            . '(?![\\w-])'
            . '('
            . '[^\\]\\/]*'
            . '(?:'
            . '\\/(?!\\])'
            . '[^\\]\\/]*'
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'
            . '\\]'
            . '|'
            . '\\]'
            . '(?:'
            . '('
            . '[^\\[]*+'
            . '(?:'
            . '\\[(?!\\/\\2\\])'
            . '[^\\[]*+'
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'
            . ')?'
            . ')'
            . '(\\]?)';
    }

    /**
     * Get shortcodes
     *
     * @param string $return If 'shortcode' will return an array of all of the plugin's shortcodes, anything else will
     *                       return $shortcode => $callback pair
     *
     * @return array
     */
    public static function getShortcodes( $return = '' )
    {
        if ( 'shortcode' === $return ) {
            return array_keys( self::$shortcodes );
        }

        return self::$shortcodes;
    }

    /**
     * [idxrp_search_page] shortcode handler
     *
     * @param array  $atts
     * @param string $content
     * @param string $shortcode
     *
     * @return string
     */
    public static function searchApp( $atts = [], $content = '', $shortcode = '' )
    {
        global $post;

        $plugin = Plugin::getPluginData();
        if ( ! Plugin::isLicenseValid() ) {
            if ( current_user_can( 'edit_posts' ) ) {
                return sprintf(
                    __( '%s plugin license is not valid. Unable to process request.', 'realty-idx-pro' ),
                    $plugin->Name
                );
            } else {
                return sprintf(
                    __( '%s plugin was not setup properly. Please contact the site administrator', 'realty-idx-pro' ),
                    $plugin->Name
                );
            }
        }

        if ( is_home() ) {
            return sprintf(
                '<p class="text-info">%s</p>',
                sprintf(
                    __( 'Search for properties in our <a href="%s">search page</a>', 'realty-idx-pro' ),
                    get_permalink()
                )
            );
        }

        static $count = 0;
        $count++;

        $defaults = [
            'settings_id' => '',
        ];

        $args = shortcode_atts( $defaults, $atts );

        $settings_id = sanitize_key( trim( $args['settings_id'] ) );

        if ( ! $settings_id ) {
            if ( current_user_can( 'edit_posts' ) ) {
                $notice = sprintf(
                    __(
                        '<code>settings_id</code> parameter is not set for shortcode %s. Please <a href="%s">edit this post</a> and assign one.'
                    ),
                    $shortcode,
                    get_edit_post_link( $post->ID )
                );
            } else {
                $notice = __( 'Listings search is not setup properly, please contact the website administrator.' );
            }

            return sprintf( '<div class="idxrpbs"><p class="text-danger">%s</p></div>', $notice );
        }

        $html = sprintf(
            '<div id="idxrp-search-app-%d-%d" class="idxrp-search-app idxrpbs"></div>',
            $settings_id,
            $count
        );

        return $html;
    }

    /**
     * Get shortcode error message
     *
     * @param string $shortcode
     * @param string $type
     * @param array  $args
     *
     * @return string
     */
    public static function getShortcodeErrorMessage( $shortcode, $type, $args = [] )
    {
        $message = '';
        $error   = sprintf(
            __(
                'This section is not properly configured. Please contact the site administrator.',
                'realty-idx-pro'
            )
        );
        switch ( $shortcode ) {
            case 'idxrp_search_app':
            case 'idxrp_group':
                if ( current_user_can( 'edit_posts' ) ) {
                    switch ( $type ) {
                        case 'id_not_unique':
                            $message = sprintf(
                                __(
                                    'The id %s attribute passed to [%s] shortcode is already taken.',
                                    'realty-idx-pro'
                                ),
                                ! empty( $args['id'] ) ? "<code>{$args['id']}</code>" : '',
                                $shortcode
                            );
                            break;
                        case 'required_id':
                            $message = sprintf(
                                __(
                                    'Required attribute <code>id</code> for shortcode [%s] is missing or empty.',
                                    'realty-idx-pro'
                                ),
                                $shortcode
                            );
                            break;
                        case 'required_resource':
                            $message = sprintf(
                                __(
                                    'Required attribute <code>resource</code> for shortcode [%s] is missing or empty.',
                                    'realty-idx-pro'
                                ),
                                $shortcode
                            );
                            break;
                        case 'unsupported_resource':
                            $resources = RetsCredentials::instance()->getResourceSettings( 'enabled_resource' );
                            $message   = sprintf(
                                __(
                                    'Given attribute <code>resource</code> for shortcode [%s] is unsupported or not enabled. Please set it to one of the following: %s',
                                    'realty-idx-pro'
                                ),
                                $shortcode,
                                "'" . implode( "', '", $resources ) . "'"
                            );
                            break;
                        case 'required_post_type':
                            $message = sprintf(
                                __(
                                    'Required attribute <code>post_type</code> for shortcode [%s] is missing or empty.',
                                    'realty-idx-pro'
                                ),
                                $shortcode
                            );
                            break;
                        case 'unsupported_post_type':
                            $post_types = RetsCredentials::instance()->getEnabledPostTypes();
                            if ( ! empty( $post_types ) ) {
                                $message = sprintf(
                                    __(
                                        'Given attribute <code>post_type</code> for shortcode [%s] is not supported. Please set it to one of the following values: %s',
                                        'realty-idx-pro'
                                    ),
                                    $shortcode,
                                    "'" . implode( "', '", array_values( $post_types ) ) . "'"
                                );
                            } else {
                                $message = $error;
                            }
                            break;
                        case 'required_taxonomy_terms':
                            $message = sprintf(
                                __( 'Taxonomy => term(s) pair are required for shortcode [%s].', 'realty-idx-pro' ),
                                $shortcode
                            );
                            break;
                    }
                } else {
                    $message = $error;
                }
                break;
        }

        return $message;
    }

    /**
     * [idxrp_field] shortcode handler
     *
     * @param array  $atts      Shortcode attributes
     * @param string $content   Shortcode content
     * @param string $shortcode Shortcode tag
     *
     * @return string|array HTML markup string output or mixed if 'context' is 'raw'
     */
    public static function getPropertyFieldValue( $atts = [], $content = '', $shortcode = '' )
    {
        $defaults = [
            'field'   => '',
            'na'      => '--',
            'context' => 'display',
            'post'    => null,
            'excerpt' => false,
        ];

        $defaults = apply_filters( 'idxrp_get_property_field_value_defaults', $defaults );

        $args = shortcode_atts( $defaults, $atts );

        //$no_image = plugins_url( 'images/no-image.png', IDX_REALTY_PRO_PLUGIN_FILE );

        $post = null;
        if ( isset( $args['post'] ) ) {
            if ( $args['post'] instanceof \WP_Post ) {
                $post = $args['post'];
            } else if ( is_numeric( $args['post'] ) ) {
                $post = get_post( intval( $args['post'] ) );
            }
        }

        $post = is_null( $post ) ? get_post() : $post;
        $meta = Front::instance()->getPropertyMeta( $post->ID );
        /**
         * @var string $server_id
         * @var string $key_field_id
         * @var string $resource_id
         * @var string $class_name
         * @var string $server_key
         */
        extract( $meta );
        $na       = $args['na'];
        $field    = "{$args['field']}";// force string format for MLS that uses numeric field names
        $context  = $args['context'];
        $excerpt  = ! ! intval( $args['excerpt'] );
        $value    = null;
        $property = Api::getDetails( $post->ID );
        if ( is_wp_error( $property ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                return sprintf( '<p class="text-danger">%s</p>', $property->get_error_message() );
            } else {
                return $na;
            }
        }

        $datetime_format = 'F d, Y H:i:s';
        $date_format     = 'F d, Y';

        switch ( $field ) {
            case 'edit_post_link':
                if ( ! $url = get_edit_post_link( $post->ID ) ) {
                    return '';
                }

                switch ( $context ) {
                    case 'display':
                        $link = '<a class="post-edit-link" href="' . $url . '">' . __(
                                'Edit',
                                'realty-idx-pro'
                            ) . '</a>';
                        break;
                    case 'attr':
                    case 'rest':
                        $link = esc_attr( $url );
                        break;
                    default:
                        $link = $url;
                }

                return $link;
            case 'permalink':
                $permalink = get_permalink( $post );
                $value     = 'display' !== $context ? $permalink : sprintf( '<a href="%1$s">%1$s</a>', $permalink );

                return $value;
            case 'ID':
                return $post->ID;
            case 'post_title':
            case 'title':
                return get_the_title( $post );
            case 'photos':
                $value = $property[ $field ];
                if ( 'display' === $context ) {
                    $value = sprintf(
                        '<div class="row photos">%s</div>',
                        implode(
                            PHP_EOL,
                            array_map(
                                function ( $photo_url ) {
                                    return sprintf(
                                        '<li class="col-2"><img class="img-responsive" src="%s" /></li>',
                                        esc_attr( $photo_url )
                                    );
                                },
                                $value
                            )
                        )
                    );
                }

                return $value;
            case 'thumbnails':
                $value  = '';
                $thumbs = $property[ $field ];

                if ( 'display' === $context ) {
                    if ( ! empty( $thumbs[0] ) ) {
                        $img_html = '<img class="attachment-post-thumbnail size-post-thumbnail wp-post-image img-fluid" src="%s" alt="%s">';
                        if ( false !== strpos( $thumbs[0], 'no-image.png' ) ) {
                            $value = sprintf(
                                $img_html,
                                $thumbs[0],
                                esc_attr(
                                    sprintf(
                                        __( 'No property images for "%s"', 'realty-idx-pro' ),
                                        get_the_title( $post )
                                    )
                                )
                            );
                        } else {
                            $value = sprintf(
                                $img_html,
                                $thumbs[0],
                                esc_attr(
                                    sprintf(
                                        __( 'Property image for "%s"', 'realty-idx-pro' ),
                                        get_the_title( $post )
                                    )
                                )
                            );
                        }
                    }
                }

                return $value;
            case 'google_maps':
                if ( is_single() ) {
                    $info = '';
                    if ( ! Option::instance()->get( 'general', 'google_maps_api_key' ) ) {
                        $info_text = current_user_can( 'edit_posts' )
                            ? __( 'Google Maps API key not found. Unable to display map.', 'realty-idx-pro' )
                            : __(
                                'Map is not setup properly. Please contact the site administrator.',
                                'realty-idx-pro'
                            );
                        $info      = sprintf( '<p class="text-info text-center"><em>%s</em></p>', $info_text );
                    }
                    $value = sprintf( '<div id="google-map">%s</div>', $info );
                } else {
                    $value = '';
                }

                return $value;
            case 'favorite_btn':
                if ( is_single() ) {
                    if ( is_user_logged_in() ) {
                        $favorites   = User::instance()->getUserFavoriteProperties();
                        $is_favorite = ! empty( $favorites ) && in_array( $key_field_id, $favorites ) ? 'is-favorite'
                            : '';
                        $value       = sprintf(
                            '<a id="favorite-btn" class="btn btn-light%s" data-key-id="%s" href="#"><i class="fa fa-heart"></i> %s</a>',
                            " {$is_favorite}",
                            $key_field_id,
                            __( 'Favorite' )
                        );
                    } else {
                        $value = '<div id="login-reg-form"></div>';
                    }
                } else {
                    $value = '';
                }

                return $value;
        }

        if ( is_null( $value ) ) {
            if ( isset( $property[ $field ] ) ) {
                $value = $property[ $field ];
                if ( 'raw' !== $context && $excerpt ) {
                    return wp_trim_words( $property[ $field ] );
                }
                $class_data = Api::getClassFields( $server_id, $resource_id, $class_name );
                if ( is_wp_error( $class_data ) ) {
                    if ( current_user_can( 'manage_options' ) ) {
                        return sprintf( '<p class="text-danger">%s</p>', $class_data->get_error_message() );
                    } else {
                        return $na;
                    }
                }
                $field_data = wp_list_filter( $class_data['fields'], [ 'system_name' => $field ] );
                if ( ! empty( $field_data ) ) {
                    $field_data = call_user_func_array( 'array_merge', $field_data );
                    switch ( strtolower( $field_data['data_type'] ) ) {
                        case 'decimal':
                            $value = number_format_i18n( floatval( $property[ $field ] ), 4 );
                            break;
                        case 'int':
                        case 'bigint':
                        case 'long':
                        case 'small':
                            $value = intval( $property[ $field ] );
                            break;
                        case 'bool':
                        case 'boolean':
                        case 'char':
                            $value = ( 1 === intval( $property[ $field ] ) ) ? __( 'Yes' ) : __( 'No' );
                            break;
                        case 'date':
                            $value = date( $date_format, strtotime( $property[ $field ] ) );
                            break;
                        case 'datetime':
                            $value = date( $datetime_format, strtotime( $property[ $field ] ) );
                            break;
                        case 'character':
                        default:
                            if ( $excerpt ) {
                                $value = wp_trim_words( $property[ $field ] );
                            }
                    }
                }
            }
        }

        return $value ? $value : $na;
    }

    /**
     * [idxrp_user_acct] shortcode handler - returns entry HTML markup for user account app
     *
     * @param array  $atts
     * @param string $content
     * @param string $shortcode
     *
     * @return string
     */
    public static function userAccount( $atts = [], $content = '', $shortcode = '' )
    {
        if ( is_home() ) {
            if ( is_user_logged_in() ) {
                return sprintf(
                    '<p class="text-info">%s</p>',
                    sprintf(
                        __( 'Visit your <a href="%s">profile page</a> to view your account.', 'realty-idx-pro' ),
                        esc_url( get_permalink() )
                    )
                );
            } else {
                return sprintf(
                    '<p class="text-info">%s</p>',
                    sprintf(
                        __( '<a href="%s">Login</a> to view your account.', 'realty-idx-pro' ),
                        esc_url( wp_login_url( get_permalink() ) )
                    )
                );
            }
        }

        $defaults = [
            'id'          => 'idxrp-user-account',
            'search_page' => 0
        ];

        $args = shortcode_atts( $defaults, $atts );

        if ( empty( $args['id'] ) ) {
            if ( current_user_can( 'edit_posts' ) ) {
                return sprintf(
                    '<p class="text-info">%s</p>',
                    sprintf(
                        __(
                            'Shortcode [%s] is missing the required attribute <code>id</code>',
                            'realty-idx-pro'
                        ),
                        $shortcode
                    )
                );
            } else {
                return sprintf(
                    '<p class="text-info">%s</p>',
                    __(
                        'It seems this page is not setup properly. Please notify the website administrator.',
                        'realty-idx-pro'
                    )
                );
            }
        }

        $html = sprintf(
            '<div id="%s" class="idxrpbs idxrp-user-account"></div>',
            esc_attr( sanitize_key( $args['id'] ) )
        );

        return $html;
    }
}
