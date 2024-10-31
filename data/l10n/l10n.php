<?php
use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

if ( defined( 'IDXRP_DEV' ) && IDXRP_DEV ) {
    $file_dir = 'dev';
} else {
    $file_dir = 'dist';
}

return [
    'wp_api_root_url'              => esc_url_raw( rtrim( rest_url(), '/' ) ),
    'wp_api_nonce'                 => wp_create_nonce( 'wp_rest' ),
    'wp_api_namespace'             => Plugin::getWpApiNamespace(),
    'wp_api_admin_rest_base'       => Plugin::getWpApiAdminRestBase(),
    'wp_api_post_editor_rest_base' => Plugin::getWpApiPostEditorRestBase(),
    'wp_api_search_rest_base'      => Plugin::getWpApiSearchRestBase(),
    'wp_api_user_rest_base'        => Plugin::getWpApiUserRestbase(),
    'loading_img_url'              => plugins_url( 'images/loading.gif', IDX_REALTY_PRO_PLUGIN_FILE ),
    'public_path'                  => plugins_url( "js/{$file_dir}/", IDX_REALTY_PRO_PLUGIN_FILE ),
    'l10n'                         => [
        'label'       => [
            'add'      => __( 'Add' ),
            'save'     => __( 'Save' ),
            'close'    => __( 'Close' ),
            'cancel'   => __( 'Cancel' ),
            'submit'   => __( 'Submit' ),
            'edit'     => __( 'Edit' ),
            'select'   => __( '- Select -', 'realty-idx-pro' ),
            'start'    => __( 'Start', 'realty-idx-pro' ),
            'stop'     => __( 'Stop', 'realty-idx-pro' ),
            'done'     => __( 'Done' ),
            'delete'   => __( 'Delete' ),
            'remove'   => __( 'Remove' ),
            'register' => __( 'Register' ),
            'login'    => __( 'Login' ),
            'no'       => __( 'No' ),
            'saved'    => __( 'Settings saved!', 'realty-idx-pro' ),
        ],
        'description' => [
            'register'          => __( "Don't have an account? %s", 'realty-idx-pro' ),
            'login'             => __( "Already have an account? %s", 'realty-idx-pro' ),
            'login_register'    => __( 'Login or Register to Save the Property', 'realty-idx-pro' ),
            'register_disabled' => __( 'Registration is currently disabled!', 'realty-idx-pro' ),
        ]
    ]
];
