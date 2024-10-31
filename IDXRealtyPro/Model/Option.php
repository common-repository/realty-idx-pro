<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/04/24
 * Time: 5:07 PM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Util;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Option
{

    private $prefix;

    protected static $instance;

    protected $settings_general_defaults;

    protected $settings_single_defaults;

    protected $settings_search_defaults;

    protected $settings_tax_archive_defaults;

    public function __construct()
    {
        $this->prefix = 'idxrp_';

        $this->settings_general_defaults = [
            'google_maps_api_key' => '',
            'auto_update_plugin'  => false,
        ];

        $this->settings_single_defaults = [
            'single_template_property' => '',
            'marker_template_property' => '',
            'meta_description'         => '',
        ];

        $this->settings_search_defaults = [
            'instant_search' => false,
        ];

        $this->settings_tax_archive_defaults = [
            'settings_id'    => 0,
            'server_id'      => 0,
            'resource_class' => '',
            'taxonomies'     => [],
        ];
    }

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
     * @return Option
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Get option value
     *
     * @param string     $group   Option group
     * @param string     $key     Option group key
     * @param bool|mixed $default Default returned value
     *
     * @return bool|mixed|null
     */
    public function get( $group, $key = '', $default = false )
    {
        $plugin_settings = null;
        $option_key      = "{$this->prefix}{$group}";
        $option_key      = sanitize_key( $option_key );

        $plugin_settings = get_option( $option_key, $default );

        if ( ! empty( $key ) ) {
            if ( isset( $plugin_settings[ $key ] ) ) {
                $plugin_settings = $plugin_settings[ $key ];
            } else {
                $plugin_settings = false;
            }
        }

        return $plugin_settings;
    }

    /**
     * Save option
     *
     * @param string $group Option group
     * @param mixed  $data  Option data to save
     *
     * @return bool
     */
    public function save( $group, $data )
    {
        $option_key = "{$this->prefix}{$group}";
        $option_key = sanitize_key( $option_key );

        if ( ! empty( $data ) ) {
            $data = Util::sanitizeTextFieldR( $data );
        }

        return update_option( $option_key, $data );
    }
}
