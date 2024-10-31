<?php
use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
$license_data      = Plugin::getLicenseData();
$license_key       = Plugin::getMaskedLicenseKey();
$is_license_active = intval( $license_data && isset( $license_data->license ) && $license_data->license === 'valid' );

$license = [
    'l10n'  => [
        'label'       => [
            'tab'        => __( 'License', 'realty-idx-pro' ),
            'activate'   => __( 'Activate' ),
            'deactivate' => __( 'Deactivate' ),
            'active'     => __( 'License is valid and active!', 'realty-idx-pro' )
        ],
        'license_key' => [
            'placeholder' => __( 'Enter your license', 'realty-idx-pro' ),
        ]
    ],
    'props' => [
        'license_key'       => $license_key,
        'is_license_active' => $is_license_active
    ]
];

return compact( 'license' );
