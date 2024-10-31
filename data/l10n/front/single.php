<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
global $post;

$property = \IDXRealtyPro\Model\Api::getDetails( $post->ID );
$defaults = [
    'title'           => '',
    'lat'             => 0,
    'lng'             => 0,
    'display_address' => '',
];
$photos   = [];
if ( is_wp_error( $property ) ) {
    $error = $property;
    add_action(
        'admin_notices',
        function () use ( $error ) {
            printf( '<div class="error"><p>%s</p></div>', $error->get_error_message() );
        }
    );
    $property = [];
} else if ( ! empty( $property['photos'] ) ) {
    $photos = $property['photos'];
    foreach ( $photos as &$photo_url ) {
        $photo_url = [ 'original' => $photo_url, 'thumbnail' => $photo_url ];
    }
}
$property = wp_parse_args( $property, $defaults );

$single = [
    'l10n'  => [
        'dont_show_address' => __(
            'Address for this property is not allowed to be displayed publicly.',
            'realty-idx-pro'
        ),
        'favorite'          => __( 'Favorite' ),
        'for_sale'          => __( 'For Sale', 'realty-idx-pro' ),
    ],
    'props' => [
        'is_user_logged_in'  => is_user_logged_in(),
        'users_can_register' => ! ! intval( get_option( 'users_can_register' ) ),
        'photos'             => $photos,
        'address'            => $property['title'],
        'lat'                => floatval( $property['lat'] ),
        'lng'                => floatval( $property['lng'] ),
        'show_location'      => $property['display_address'],
        'post_id'            => get_the_ID(),
        'key_field_id'       => get_post_meta( get_the_ID(), 'key_field_id', true ),
    ]
];

return apply_filters( 'idxrp_single_l10n', $single );
