<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/02/02
 * Time: 10:05 AM
 */

namespace IDXRealtyPro\Route\Front;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\User as UModel;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class User extends \IDXRealtyPro\Route\Base
{

    public function __construct()
    {
        parent::__construct();
        $this->rest_base = Plugin::getWpApiUserRestbase();
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @access public
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/favorite",
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [ $this, 'favorite' ],
                'permission_callback' => [ $this, 'favoritePermissionsCheck' ]
            ]
        );
        register_rest_route(
            $this->namespace,
            "/{$this->rest_base}/account/(?P<action>[^/]+)",
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'userAccount' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ]
            ]
        );
    }

    public function create_item_permissions_check( $request )
    {
        $action = $request->get_param( 'action' );
        if ( in_array(
            $action,
            [ 'login_save_search', 'register_save_search', 'login_favorite', 'register_favorite', ]
        ) ) {
            return true;
        }

        return is_user_logged_in();
    }

    /**
     * Favorite permissions
     *
     * @param \WP_REST_Request $request
     *
     * @return bool
     */
    public function favoritePermissionsCheck( $request )
    {
        return true;
    }

    /**
     * User actions route
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function favorite( $request )
    {
        $data   = [];
        $method = $request->get_method();
        $params = $request->get_params();
        $json   = $request->get_json_params();

        switch ( $method ) {
            case 'POST':
                if ( ! is_user_logged_in() ) {
                    if ( isset( $json['login_data'] ) ) {
                        if ( empty( $json['login_data'] ) ) {
                            return new \WP_Error(
                                'user_action_error',
                                __( 'Login credentials are empty.', 'realty-idx-pro' ),
                                [ 'status' => 403 ]
                            );
                        }

                        $user = $this->userLogin( $json['login_data'] );
                        if ( is_wp_error( $user ) ) {
                            return $user;
                        }
                        $data['reload'] = true;
                    } else if ( isset( $json['register_data'] ) ) {
                        if ( empty( $json['register_data'] ) ) {
                            return new \WP_Error(
                                'user_action_error',
                                __( 'Registration data are empty.', 'realty-idx-pro' ),
                                [ 'status' => 403 ]
                            );
                        } else if ( empty( $json['register_data']['user_email'] ) ) {
                            return new \WP_Error(
                                'user_action_error',
                                __( 'Email is required.', 'realty-idx-pro' ),
                                [ 'status' => 403 ]
                            );
                        }

                        $user_id = $this->registerUser( $json['register_data'] );
                        if ( is_wp_error( $user_id ) ) {
                            return new \WP_Error(
                                'user_action_error',
                                $user_id->get_error_message(),
                                [ 'status' => 500 ]
                            );
                        }
                        $json['user_id'] = $user_id;
                        $message         = __(
                            'Registered! Please check your email to set your password.',
                            'realty-idx-pro'
                        );
                    }
                }

                $favorite = $this->favoriteAction( $json );
                if ( is_wp_error( $favorite ) ) {
                    return $favorite;
                }
                $data = array_merge( $data, $favorite );
                if ( isset( $message ) ) {
                    $data['modal_message'] = $message;
                }
                break;
        }

        return $this->restResponse(
            apply_filters( 'idxrp_favorite_rest_response', $data, $method, $params, $json ),
            $request
        );
    }

    /**
     * User favorite action
     *
     * @param array $args
     *
     * @return array|\WP_Error
     */
    protected function favoriteAction( $args )
    {
        if ( ! isset( $args['fav_type'] ) ) {
            return new \WP_Error(
                'user_action_error',
                __( 'Unknown favorite type.', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        } else if ( ! isset( $args['key_id'] ) ) {
            return new \WP_Error(
                'user_action_error',
                __( 'Unknown property to save to favorites.', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        } else if ( ! is_user_logged_in() && empty( $args['user_id'] ) ) {
            return new \WP_Error(
                'user_action_error',
                __( 'Unknown user to save favorites to.', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        }

        $user   = is_user_logged_in() ? wp_get_current_user() : new \WP_User( $args['user_id'] );
        $key_id = $args['key_id'];

        if ( 'add' === $args['fav_type'] ) {
            $data = [
                'modal_message' => __( 'This property has been added to your favorites.', 'realty-idx-pro' ),
                'key_id'        => $key_id
            ];
        } else {
            $data = [
                'modal_message' => __( 'This property has been removed from your favorites.', 'realty-idx-pro' ),
                'key_id'        => $key_id
            ];
        }
        $data['favorites'] = UModel::instance()->setUserFavoriteProperties( $user->ID, $key_id, $args['fav_type'] );

        return $data;
    }

    /**
     * User save search action
     *
     * @param string $action
     * @param array  $json
     *
     * @return array|\WP_Error
     */
    protected function saveSearchAction( $action, $json )
    {
        $search_form_data = array_filter( Util::arrayExcept( $json['search_data'], [ 'orderby', 'order' ] ) );
        if ( empty( $search_form_data ) ) {
            return new \WP_Error(
                'user_action_error',
                __( "Search form seems to be empty. Unable to save search.", 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        }
        $data            = [ 'reload' => false ];
        $search_name     = $json['search_name'];
        $search_name_key = sanitize_key( $json['search_name'] );
        $search_data     = [];
        switch ( $action ) {
            case 'save_search':
                $user = wp_get_current_user();

                $old_search_data = UModel::instance()->getUserSearchData( $user->ID );
                if ( ! empty( $old_search_data ) ) {
                    $old_search_data[ $search_name_key ] = compact( 'search_name' ) + $json['search_data'];

                    $search_data = $old_search_data;
                } else {
                    $search_data[ $search_name_key ] = compact( 'search_name' ) + $json['search_data'];
                }

                UModel::instance()->saveUserSearchData( $user->ID, $search_data );
                break;
            case 'login_save_search':
                $user = $this->userLogin( $json['login_data'] );
                if ( is_wp_error( $user ) ) {
                    return new \WP_Error( 'user_action_error', $user->get_error_message(), [ 'status' => 500 ] );
                }

                $old_search_data = UModel::instance()->getUserSearchData( $user->ID );
                if ( ! empty( $old_search_data ) ) {
                    $old_search_data[ $search_name_key ] = compact( 'search_name' ) + $json['search_data'];

                    $search_data = $old_search_data;
                } else {
                    $search_data[ $search_name_key ] = compact( 'search_name' ) + $json['search_data'];
                }

                UModel::instance()->saveUserSearchData( $user->ID, $search_data );
                $data['message']      = __( 'Search data saved!', 'realty-idx-pro' );
                $data['idxrp_search'] = $json['search_data'];
                $data['reload']       = true;
                break;
            case 'register_save_search':
                if ( ! get_option( 'users_can_register' ) ) {
                    return new \WP_Error(
                        'user_action_error',
                        __( 'User registration is currently disabled.', 'realty-idx-pro' ),
                        [ 'status' => 500 ]
                    );
                }

                $user_id = $this->registerUser( $json['register_data'] );
                if ( is_wp_error( $user_id ) ) {
                    return new \WP_Error( 'user_action_error', $user_id->get_error_message(), [ 'status' => 500 ] );
                }
                UModel::instance()->saveUserSearchData( $user_id, compact( 'search_name' ) + $json['search_data'] );
                $data['message']      = __( 'Search data saved!', 'realty-idx-pro' );
                $data['idxrp_search'] = $json['search_data'];
                break;
        }

        return $data;
    }

    /**
     * Login user
     *
     * @param array $login_data User's login data
     *
     * @return false|\WP_Error|\WP_User
     */
    public function userLogin( $login_data )
    {
        $users       = get_users( [ 'role__in' => [ 'administrator' ], 'fields' => [ 'user_login', 'user_email' ] ] );
        $admin_users = array_merge(
            wp_list_pluck( $users, 'user_login' ),
            wp_list_pluck( $users, 'user_email' )
        );

        $login_data['remember'] = isset( $login_data['remember'] ) ? ! ! intval( $login_data['remember'] ) : false;

        $error = new \WP_Error(
            'user_action_error',
            sprintf( __( 'Unable to login username %s.', 'realty-idx-pro' ), $login_data['user_login'] ),
            [ 'data' => 500 ]
        );

        if ( empty( $login_data['user_login'] ) || in_array( $login_data['user_login'], $admin_users ) ) {
            return $error;
        }

        $user_name = sanitize_user( $login_data['user_login'] );
        $user      = get_user_by( 'login', $user_name );
        if ( ! $user ) {
            $user = get_user_by( 'email', $user_name );
        }

        if ( ! $user ) {
            return $error;
        }

        if ( user_can( $user, 'edit_posts' ) ) {
            return $error;
        }

        wp_set_auth_cookie( $user->ID, $login_data['remember'] );
        wp_set_current_user( $user->ID );
        /*$secure_cookie = '';
        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( ! force_ssl_admin() ) {
            if ( get_user_option( 'use_ssl', $user->ID ) ) {
                $secure_cookie = true;
                force_ssl_admin( true );
            }
        }
        $user = wp_signon( $login_data, $secure_cookie );
        if ( is_wp_error( $user ) ) {
            if ( $user->get_error_message() !== '' ) {
                $message = $user->get_error_message();
            } else {
                $message = __( 'Username/Password is empty.', 'realty-idx-pro' );
            }

            return new \WP_Error( 'user_action_error', $message, [ 'status' => 500 ] );
        }*/

        return $user;
    }

    /**
     * Register new user
     *
     * @param array $user_data User login name and email
     *
     * @return int|\WP_Error
     */
    public function registerUser( $user_data )
    {
        $user_data_defaults = [
            'user_login' => '',
            'user_email' => '',
        ];

        $register_data = wp_parse_args( $user_data, $user_data_defaults );

        $user_email = $register_data['user_email'];
        $user_login = ! empty( $register_data['user_login'] ) ? $register_data['user_login'] : $user_email;
        if ( empty( $user_email ) ) {
            return new \WP_Error(
                'user_action_error',
                __( 'Email field is required.', 'realty-idx-pro' ),
                [ 'status' => 500 ]
            );
        }

        $user_id = register_new_user( $user_login, $user_email );
        if ( is_wp_error( $user_id ) ) {
            if ( $user_id->get_error_message( 'username_exists' ) && $user_id->get_error_message( 'email_exists' ) ) {
                return new \WP_Error(
                    'user_action_error',
                    wp_strip_all_tags( $user_id->get_error_message( 'email_exists' ) ),
                    [ 'status' => 403 ]
                );
            }

            return new \WP_Error(
                'user_action_error',
                wp_strip_all_tags( $user_id->get_error_message() ),
                [ 'status' => 403 ]
            );
        }

        UModel::instance()->saveUserMeta( $user_id, $register_data );

        return apply_filters( 'idxrp_register_user', $user_id );
    }

    /**
     * User account route
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function userAccount( $request )
    {
        if ( ! is_user_logged_in() ) {
            return new \WP_Error(
                'userAccountError',
                __( 'You have to login to view your account.', 'realty-idx-pro' ),
                [ 'status' => 401 ]
            );
        }
        $user = wp_get_current_user();

        $action = $request->get_param( 'action' );
        $json   = $request->get_json_params();
        $data   = [];

        switch ( $action ) {
            case 'update_saved_searches':
                if ( ! isset( $json['items'] ) ) {
                    return new \WP_Error(
                        'userAccountError',
                        __( 'No updated search items to save.', 'realty-idx-pro' ),
                        [ 'status' => 500 ]
                    );
                }

                $updated_searches = [];
                foreach ( $json['items'] as $item ) {
                    $search_name_key = sanitize_key( $item['search_name'] );

                    $updated_searches[ $search_name_key ] = Util::arrayExcept( $item, [ 'id' ] );

                    $data['items'][] = wp_parse_args( [ 'id' => $search_name_key ], $item );
                }

                UModel::instance()->saveUserSearchData( $user->ID, $updated_searches );
                break;
        }

        return $this->restResponse( $data, $request );
    }
}
