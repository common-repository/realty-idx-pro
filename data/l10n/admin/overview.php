<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

$wp_ver = str_replace( [ '-src' ], '', $GLOBALS['wp_version'] );

$overview = [
    'l10n' => [
        'label'        => [
            'tab'      => __( 'Overview', 'realty-idx-pro' ),
            'update'   => __( 'Update' ),
            'requires' => __( 'Requires at least:', 'realty-idx-pro' ),
            'yours'    => __( 'Yours:', 'realty-idx-pro' ),
            'install'  => __( 'Install', 'realty-idx-pro' ),
        ],
        'requirements' => [
            [
                'label'    => 'PHP',
                'required' => IDX_REALTY_PRO_REQUIRED_PHP,
                'current'  => PHP_VERSION,
                'passed'   => version_compare( PHP_VERSION, IDX_REALTY_PRO_REQUIRED_PHP, '>=' ) ? 'success' : 'danger',
                'info'     => '',
            ],
            [
                'label'    => 'Wordpress',
                'required' => IDX_REALTY_PRO_REQUIRED_WP,
                'current'  => $wp_ver,
                'passed'   => version_compare( $wp_ver, IDX_REALTY_PRO_REQUIRED_WP, '>=' ) ? 'success' : 'danger',
                'info'     => '',
            ],
        ],
    ]
];

return compact( 'overview' );
