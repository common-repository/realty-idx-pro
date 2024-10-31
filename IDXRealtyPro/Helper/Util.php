<?php
/**
 * Author: Paul Grejaldo
 * Date: 2016/10/16
 * Time: 6:18 PM
 */

namespace IDXRealtyPro\Helper;

use IDXRealtyPro\Controller\View;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Util
{

    /**
     * Template tag pattern
     *
     * @return string
     */
    public static function getTemplateTagPattern()
    {
        //return '\\{\\$([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\\}';// allows only string that starts with non-numeric char
        return '\\{\\$([a-zA-Z0-9_\\x7f-\\xff]*)\\}';// allows string that starts with numeric char
    }

    /**
     * Template tag pattern
     *
     * @return string
     */
    public static function getJsTemplateTagPattern()
    {
        return '\\{\\{\\s*post\\.([^\\s}]+)\\s*\\}\\}';// allows string that starts with numeric char
    }

    /**
     * Post title format pattern
     *
     * @return string
     */
    public static function getPostTitleFormatPattern()
    {
        //$pattern = '\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}';// allows string that starts with non-numeric char
        return '\\{([a-zA-Z0-9_\\x7f-\\xff]*)\\}';// allows string that starts with numeric char
    }

    /**
     * Filter the given $array excluding the $keys given
     *
     * @param array $array The original array to process.
     * @param array $keys  Array of keys to exclude.
     *
     * @return array The array with the keys removed.
     */
    public static function arrayExcept( $array, $keys )
    {
        return array_diff_key( $array, array_flip( (array) $keys ) );
    }

    /**
     * Recursive text field sanitization
     *
     * @param mixed $data The data to be sanitized
     *
     * @return array
     */
    public static function sanitizeTextFieldR( $data )
    {
        if ( ! $data ) {
            return $data;
        }

        foreach ( $data as &$_data ) {
            if ( is_array( $_data ) ) {
                $_data = self::sanitizeTextFieldR( $_data );
            } else {
                $_data = sanitize_text_field( $_data );
            }
        }

        return $data;
    }

    /**
     * Get masked value of a string
     *
     * @param string $str
     *
     * @return string
     */
    public static function getMaskedValue( $str )
    {
        return str_repeat( '*', strlen( mb_substr( $str, 0, ( strlen( $str ) - 4 ) ) ) ) .
               mb_substr( $str, ( strlen( $str ) - 4 ) );
    }

    /**
     * Append a string to the file given
     *
     * @param string $file   The file path to append to
     * @param string $string The string to append to the file
     */
    public static function appendToFile( $file, $string )
    {
        $fh = fopen( $file, 'a' );
        fwrite( $fh, $string . "\n" );
        fclose( $fh );
    }

    /**
     * Read the last line from the file
     *
     * @param string $file The file path to read
     *
     * @return null|bool|string False if file does not exist, otherwise the last line read from the file
     */
    public static function readFileLastLine( $file )
    {
        if ( ! file_exists( $file ) ) {
            return null;
        }
        $handle = fopen( $file, 'r' );
        $cursor = -1;
        fseek( $handle, $cursor, SEEK_END );
        $char = fgetc( $handle );
        $line = '';
        /**
         * Trim trailing line separator
         */
        while ( $char === "\n" || $char === "\r" ) {
            fseek( $handle, $cursor--, SEEK_END );
            $char = fgetc( $handle );
        }

        while ( $char !== false && $char !== "\n" && $char !== "\r" ) {
            $line = $char . $line;
            fseek( $handle, $cursor--, SEEK_END );
            $char = fgetc( $handle );
        }

        fclose( $handle );

        return $line;
    }

    /**
     * Get admin notice message markup
     *
     * @param string $message
     * @param string $type
     *
     * @return string
     */
    public static function getAdminNotice( $message, $type = 'error' )
    {
        return sprintf( '<div id="idxrp-message" class="%s"><p>%s</p></div>', esc_attr( $type ), $message );
    }

    /**
     * Merge all passed i18n files relative to "data/i18n/" directory with root data
     *
     * @param array $files
     *
     * @param bool  $exclude_root
     *
     * @return array|mixed
     */
    public static function mergeL10nFiles( $files, $exclude_root = false )
    {
        $root = $exclude_root ? [] : include IDX_REALTY_PRO_PLUGIN_DIR_PATH . 'data/l10n/l10n.php';
        foreach ( $files as $file ) {
            $file_path = IDX_REALTY_PRO_PLUGIN_DIR_PATH . "data/l10n/$file";
            if ( file_exists( $file_path ) ) {
                $l10n = include $file_path;

                $root = array_merge_recursive( $root, $l10n );
            }
        }

        return $root;
    }

    /**
     * Get memory usage in MB
     *
     * @return string
     */
    public static function getMemoryUsage()
    {
        return floor( memory_get_peak_usage( true ) / 1024 / 1024 ) . 'MB';
    }

    /**
     * Get a list of all the template tags used in a template file
     *
     * @param string $template_file Absolute file path
     *
     * @return array
     */
    public static function getTemplateTagNames( $template_file )
    {
        if ( file_exists( $template_file ) ) {
            ob_start();
            include $template_file;
            $subject = ob_get_clean();
            $pattern = self::getTemplateTagPattern();
            preg_match_all( "/$pattern/s", $subject, $matches );

            if ( ! empty( $matches[1] ) ) {
                return array_unique( $matches[1] );
            }
        }

        return [];
    }

    /**
     * Get a list of template tags used in a default template file
     *
     * @param string $type Possible values: 'photo-view', 'list-view', 'map-view', 'marker', 'single', or
     *                     'single-marker'
     *
     * @param string $server_key
     *
     * @return array
     */
    public static function getDefaultTemplateTagNames( $type, $server_key = '' )
    {
        if ( false !== strpos( $type, 'single' ) && $server_key ) {
            $file = WP_CONTENT_DIR . "/idxrp/front/default/{$type}/{$server_key}.php";
        } else {
            $file = IDX_REALTY_PRO_PLUGIN_DIR_PATH . "templates/idxrp/front/default/{$type}.php";
        }

        return self::getTemplateTagNames( $file );
    }

    /**
     * Replace the variable names in the given $content
     *
     * @param string $content The content that contains raw variable names
     *
     * @return mixed The content with raw variable names replaced with their value
     */
    public static function doReplaceFieldNameVars( $content )
    {
        if ( false === strpos( $content, '$' ) ) {
            return $content;
        }

        $pattern = self::getTemplateTagPattern();

        return preg_replace_callback(
            "/$pattern/",
            function ( $matches ) {
                /**
                 * $matches contains:
                 * $matches[0] = {$FieldName}
                 * $matches[1] = FieldName
                 */

                return Shortcodes::getPropertyFieldValue( [ 'field' => $matches[1] ] );
            },
            $content
        );
    }

    /**
     * Get default display template
     *
     * @param string $type       Either "single" or "list"
     *
     * @param string $server_key Server meta data
     *
     * @return string The default HTML markup output
     */
    public static function getDefaultDisplayTemplate( $type, $server_key )
    {
        ob_start();

        $located = View::instance()->make( [ "idxrp/front/default/{$type}/{$server_key}.php", ], [], false );

        if ( $located ) {
            if ( 'single' === $type ) {
                require_once $located;
            } else {
                require $located;
            }

            $template = ob_get_clean();
        } else {
            $template = sprintf(
                '<p class="text-danger">%s</p>',
                sprintf( __( 'Unable to locate "%s" template.', 'realty-idx-pro' ), $type )
            );
        }

        return $template;
    }

    /**
     * Check if char data type
     *
     * @param string $data_type
     *
     * @return bool True if $data_type is of char data type, false otherwise
     */
    public static function isCharDataType( $data_type )
    {
        if ( ! in_array(
            strtolower( $data_type ),
            [ 'decimal', 'int', 'long', 'small', 'tiny', 'boolean', 'date', 'datetime' ]
        ) ) {
            return true;
        }

        return false;
    }

    /**
     * Check if char data type
     *
     * @param string $data_type
     *
     * @return bool True if $data_type is of date/datetime data type, false otherwise
     */
    public static function isDateTimeDataType( $data_type )
    {
        if ( in_array( strtolower( $data_type ), [ 'date', 'datetime' ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Check if numeric data type
     *
     * @param string $data_type
     *
     * @return bool True if $data_type is of numeric data type, false otherwise
     */
    public static function isNumericDataType( $data_type )
    {
        if ( in_array( strtolower( $data_type ), [ 'decimal', 'int', 'long', 'small', 'tiny' ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Check if numeric data type
     *
     * @param string $data_type
     *
     * @return bool True if $data_type is of numeric data type, false otherwise
     */
    public static function isBoolDataType( $data_type )
    {
        if ( in_array( $data_type, [ 'bool', 'boolean' ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get a list of template tags used in all `idxrp_template` post type
     *
     * @param null|int $post_id Post ID of template
     * @param string   $type    'single' for tags that were used in single post, anything else for 'js' templates
     *
     * @return mixed
     */
    public static function getTemplatePostTagNames( $post_id = null, $type = 'single' )
    {
        $cache_key = sha1( "sc-settings-{$post_id}-{$type}" );

        $fields = get_transient( $cache_key );
        if ( ! $fields ) {
            if ( $post_id ) {
                $posts = get_post( $post_id );
                if ( $posts ) {
                    $posts = [ $posts ];
                }
            } else {
                $posts = get_posts(
                    [
                        'post_type'      => Plugin::getTemplatePostType(),
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                    ]
                );
            }

            $fields = [];
            if ( ! empty( $posts ) ) {
                if ( $type === 'single' ) {
                    $pattern = self::getTemplateTagPattern();
                } else {
                    $pattern = self::getJsTemplateTagPattern();
                }
                foreach ( $posts as $post ) {
                    preg_match_all( "/$pattern/s", $post->post_content, $matches );
                    if ( ! empty( $matches[1] ) ) {
                        $fields += $matches[1];
                    }
                }
                if ( ! empty( $fields ) ) {
                    $fields = array_unique( $fields );
                    set_transient( $cache_key, $fields, HOUR_IN_SECONDS );
                }
            }
        }

        return $fields;
    }

    /**
     * Get a list of template tags used in a default template file
     *
     * @param string $template_type Possible values: 'photo-view', 'list-view', 'map-view', 'single', or 'single-marker'
     *
     * @param string $server_key
     *
     * @return array
     */
    public static function getDefaultTemplateFileTagNames( $template_type, $server_key = '' )
    {
        if ( false !== strpos( $template_type, 'single' ) && $server_key ) {
            $file = WP_CONTENT_DIR . "/idxrp/front/default/{$template_type}/{$server_key}.php";
            $type = 'single';
        } else {
            $file = IDX_REALTY_PRO_PLUGIN_DIR_PATH . "templates/idxrp/front/default/{$template_type}.php";
            $type = 'js';
        }

        return self::getTemplateFileTagNames( $file, $type );
    }

    /**
     * Get a list of all the template tags used in a template file
     *
     * @param string $template_file Absolute file path
     *
     * @param string $type          'single' or 'js' type
     *
     * @return array
     */
    public static function getTemplateFileTagNames( $template_file, $type = 'single' )
    {
        if ( file_exists( $template_file ) ) {
            ob_start();
            require $template_file;
            $subject = ob_get_clean();
            $pattern = 'single' === $type ? self::getTemplateTagPattern() : self::getJsTemplateTagPattern();
            preg_match_all( "/$pattern/s", $subject, $matches );

            if ( ! empty( $matches[1] ) ) {
                return array_unique( $matches[1] );
            }
        }

        return [];
    }

    /**
     * Get modified class data search fields
     *
     * @param array $fields
     *
     * @return array
     */
    public static function classDataSearchFieldsList( $fields )
    {
        $search_fields = [];
        $numeric       = [ 'decimal', 'int', 'long', 'small', 'tiny' ];
        $datetime      = [ 'date', 'datetime' ];
        foreach ( $fields as $field ) {
            $search_fields[] = $field;
            if ( in_array( strtolower( $field['data_type'] ), $numeric ) ||
                 in_array( strtolower( $field['data_type'] ), $datetime ) ) {
                $system_name     = $field['system_name'] . ':min';
                $long_name       = $field['long_name'] . '(min)';
                $new_field       = wp_parse_args( compact( 'system_name', 'long_name' ), $field );
                $search_fields[] = $new_field;

                $system_name     = $field['system_name'] . ':max';
                $long_name       = $field['long_name'] . ' (max)';
                $new_field       = wp_parse_args( compact( 'system_name', 'long_name' ), $field );
                $search_fields[] = $new_field;
            }
        }

        return $search_fields;
    }

    /**
     * Lowercases site URL's, strips HTTP protocols and strips www subdomains.
     *
     * @see \EDD_Software_Licensing::clean_site_url
     *
     * @param string $url
     *
     * @return string
     */
    public static function cleanSiteUrl( $url )
    {
        $url  = strtolower( $url );
        $url  = str_replace( [ '://www.', ':/www.' ], '://', $url );
        $url  = str_replace( [ 'http://', 'https://', 'http:/', 'https:/' ], '', $url );
        $port = parse_url( $url, PHP_URL_PORT );

        if ( $port ) {
            // strip port number
            $url = str_replace( ':' . $port, '', $url );
        }

        return sanitize_text_field( $url );
    }
}
