<?php
use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

return [
    'ajaxurl'    => esc_url_raw( rtrim( rest_url(), '/' ) ) . '/' .
                    Plugin::getWpApiNamespace() . '/' . Plugin::getWpApiPostEditorRestBase() . '/',
    'rest_nonce' => wp_create_nonce( 'wp_rest' ),
    'l10n'       => [
        'empty'        => __( 'Template returned an empty string.', 'realty-idx-pro' ),
        'list'         => __( 'Insert IDXRP List Template', 'realty-idx-pro' ),
        'single'       => __( 'Insert IDXRP Single Template', 'realty-idx-pro' ),
        'photo'        => __( 'Insert IDXRP Grid Template', 'realty-idx-pro' ),
        'map'          => __( 'Insert IDXRP Map Grid Template', 'realty-idx-pro' ),
        'marker'       => __( 'Insert IDXRP Marker Template', 'realty-idx-pro' ),
        'select_class' => __( 'Select Class', 'realty-idx-pro' ),
        'select_field' => __( 'Select Field', 'realty-idx-pro' ),
        'loading'      => __( 'Loading...', 'realty-idx-pro' ),
        'no_field'     => __( 'No field selected.', 'realty-idx-pro' ),
    ]
];
