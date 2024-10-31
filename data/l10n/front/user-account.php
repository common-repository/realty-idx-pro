<?php
use IDXRealtyPro\Model\Listing;
use IDXRealtyPro\Model\User;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

$user = wp_get_current_user();

$user_favorites = [];
$listing_ids    = is_user_logged_in() ? User::instance()->getUserFavoriteProperties( $user->ID ) : [];
if ( ! empty( $listing_ids ) ) {
    $post_ids = \IDXRealtyPro\Model\IdxrpServer::instance()->getPostIdsByKeyFieldIds( $listing_ids );
    if ( ! empty( $post_ids ) ) {
        foreach ( $post_ids as $post_id ) {
            $post    = get_post( $post_id );
            $listing = Listing::getInstance( $post_id );

            $post->thumbnails   = $listing->getField( 'thumbnails' );
            $post->key_field_id = $listing->getField( $listing->getKeyField() );
            $post->permalink    = get_permalink( $post_id );

            $user_favorites[] = $post;
        }
    }
}

$user_account = [
    'user_favorites' => [
        'l10n'  => [
            'label' => [
                'user_favorites' => __( 'Favorites', 'realty-idx-pro' ),
            ],
            'btn'   => [
                'remove' => __( 'Remove' ),
            ]
        ],
        'props' => [
            'items' => $user_favorites
        ]
    ],
    'constants'      => []
];

return apply_filters( 'idxrp_user_account_app_i18ns', compact( 'user_account' ) );
