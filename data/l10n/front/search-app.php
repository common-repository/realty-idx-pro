<?php

use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\ScSettings;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

$post_id = get_the_ID();

$general_settings = Option::instance()->get( 'general' );
$load_google_maps = ! empty( $general_settings['google_maps_api_key'] );
$favorites        = (array) \IDXRealtyPro\Model\User::instance()->getUserFavoriteProperties();

$search         = Option::instance()->get( 'search' );
$instant_search = isset( $search['instant_search'] ) ? ! ! intval( $search['instant_search'] ) : false;

$search_app = [
    'l10n'      => [
        'label'       => [
            'reset'           => __( 'Reset' ),
            'advanced'        => __( 'Advanced' ),
            'saveToFavorites' => __( 'Save to Favorites', 'realty-idx-pro' ),
            'loading'         => __( 'Loading...', 'realty-idx-pro' ),
        ],
        'description' => [
            'search'             => esc_attr_x(
                'Neighborhood, Address, City, Zip, MLS#',
                'placeholder',
                'realty-idx-pro'
            ),
            'total_results'      => __( '%1$s sorted by %2$s from %3$s', 'realty-idx-pro' ),
            'no_posts'           => __( 'No listings found!', 'realty-idx-pro' ),
            'search_tip_title'   => __( 'Search Tip', 'realty-idx-pro' ),
        ],
    ],
    'constants' => [
        'load_google_maps'   => $load_google_maps,
        'instant_search'     => $instant_search,
        'current_page_id'    => $post_id,
        'is_user_logged_in'  => is_user_logged_in(),
        'users_can_register' => ! ! intval( get_option( 'users_can_register' ) ),
        'fav_icon_url'       => plugins_url( 'images/fav_sprite.png', IDX_REALTY_PRO_PLUGIN_FILE ),
        'views'              => [
            [ 'label' => __( 'List', 'realty-idx-pro' ), 'value' => 'list' ],
            [ 'label' => __( 'Photo', 'realty-idx-pro' ), 'value' => 'photo' ],
            [ 'label' => __( 'Map', 'realty-idx-pro' ), 'value' => 'map' ],
        ],
        'order'              => [
            [ 'label' => __( 'Lowest to Highest', 'realty-idx-pro' ), 'value' => 'asc' ],
            [ 'label' => __( 'Highest to Lowest', 'realty-idx-pro' ), 'value' => 'desc' ],
        ],
        'public_path'        => plugins_url( 'js/dist/', IDX_REALTY_PRO_PLUGIN_FILE ),
    ],
    'props'     => compact( 'favorites' )
];

return compact( 'search_app' );
