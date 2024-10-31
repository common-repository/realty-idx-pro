<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/02/07
 * Time: 9:43 AM
 */

namespace IDXRealtyPro\Model;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class User extends Base
{

    protected static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return an instance of this class
     *
     * @return User The class instance object
     */
    public static function instance()
    {
        null === self::$instance && self::$instance = new self;

        return self::$instance;
    }

    /**
     * Save user search data
     *
     * @param int   $user_id     User's ID to tie the search data to
     * @param array $search_data Search parameters data
     *
     * @return bool
     */
    public function saveUserSearchData( $user_id, $search_data )
    {
        update_user_meta( $user_id, 'idxrp_searches', $search_data );

        return true;
    }

    /**
     * Get user's search data
     *
     * @param int    $user_id User's ID
     * @param string $name    Name of search to retrieve
     *
     * @return array
     */
    public function getUserSearchData( $user_id = 0, $name = '' )
    {
        if ( ! $user_id && ! is_user_logged_in() ) {
            return [];
        }
        $user_id = wp_get_current_user()->ID;

        $search_data = get_user_meta( $user_id, 'idxrp_searches', true );
        if ( ! empty( $search_data ) && $name ) {
            $name_key = sanitize_key( $name );
            if ( isset( $search_data[ $name_key ] ) ) {
                return $search_data[ $name_key ];
            }
        }

        return ! empty( $search_data ) ? $search_data : [];
    }

    /**
     * Save first_name, last_name, and phone user meta
     *
     * @param int   $user_id The user's ID to update metadata for
     * @param array $user_meta
     *
     * @return bool True on success, false on failure
     */
    public function saveUserMeta( $user_id, $user_meta )
    {
        $user_data_defaults = [
            'first_name' => '',
            'last_name'  => '',
            'phone'      => ''
        ];

        $args = array_filter( wp_parse_args( $user_meta, $user_data_defaults ) );

        if ( empty( $args ) ) {
            return false;
        }

        foreach ( $args as $meta_key => $meta_value ) {
            update_user_meta( $user_id, $meta_key, $meta_value );
        }

        return true;
    }

    /**
     * Get user favorite properties
     *
     * @param int $user_id User's ID - current logged in user ID will be used if this is not given
     *
     * @return mixed
     */
    public function getUserFavoriteProperties( $user_id = 0 )
    {
        $user_id = intval( $user_id );
        if ( ! $user_id && ! is_user_logged_in() ) {
            return null;
        } else if ( ! $user_id && is_user_logged_in() ) {
            $user_id = wp_get_current_user()->ID;
        }
        $favorites = get_user_meta( $user_id, 'idxrp_favorite_properties', true );
        $favorites = ! empty( $favorites ) ? array_map( 'strval', $favorites ) : [];

        return $favorites;
    }

    /**
     * Set user favorite properties
     *
     * @param int    $user_id User ID
     * @param int    $key_id  Post ID to add/remove from favorites
     * @param string $type    Either 'add' or 'remove' to or from favorites
     *
     * @return array|mixed
     */
    public function setUserFavoriteProperties( $user_id, $key_id, $type = 'add' )
    {
        $favorites = $this->getUserFavoriteProperties( $user_id );
        $hash      = md5( serialize( $favorites ) );
        if ( 'add' === $type ) {
            if ( ! in_array( $key_id, $favorites ) ) {
                $favorites[] = $key_id;
            }
        } else if ( 'remove' === $type ) {
            $key = array_search( $key_id, $favorites );
            if ( false !== $key ) {
                unset( $favorites[ $key ] );
            }
            // re-index array so as to avoid js from converting array into js object
            $favorites = array_values( $favorites );
        }

        if ( $hash !== md5( serialize( $favorites ) ) ) {
            $favorites = array_map( 'strval', $favorites );
            update_user_meta( $user_id, 'idxrp_favorite_properties', $favorites );
        }

        return $favorites;
    }
}
