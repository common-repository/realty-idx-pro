<?php

use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;
use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\ScSettings;
use IDXRealtyPro\Model\TemplatePost;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

$settings_general_props = Option::instance()->get( 'general' );
$settings_general_props = wp_parse_args( $settings_general_props, Option::instance()->settings_general_defaults );

$settings_general_props['auto_update_plugin'] = isset( $settings_general_props['auto_update_plugin'] ) ?
    ! ! intval( $settings_general_props['auto_update_plugin'] ) : false;
if ( ! empty( $settings_general_props['aws_s3_secret_key'] ) ) {
    $settings_general_props['aws_s3_secret_key'] = Util::getMaskedValue( $settings_general_props['aws_s3_secret_key'] );
}

$settings_single_props = Option::instance()->get( 'single' );
$settings_single_props = wp_parse_args( $settings_single_props, Option::instance()->settings_single_defaults );

$settings_search_props = Option::instance()->get( 'search' );
$settings_search_props = wp_parse_args( $settings_search_props, Option::instance()->settings_search_defaults );

$settings_search_props['instant_search'] = isset( $settings_search_props['instant_search'] ) ?
    ! ! intval( $settings_search_props['instant_search'] ) : false;

$settings_tax_archive_props = Option::instance()->get( 'tax_archive' );
$settings_tax_archive_props = wp_parse_args(
    $settings_tax_archive_props,
    Option::instance()->settings_tax_archive_defaults
);

$settings_tax_archive_props['lookups'] = [];
if ( ! empty( $settings_tax_archive_props['taxonomies'] ) ) {
    $taxonomies = [];
    list( $resource_id, $class_name ) = explode( ':', $settings_tax_archive_props['resource_class'] );
    foreach ( (array) $settings_tax_archive_props['taxonomies'] as $field_name ) {
        $settings_tax_archive_props[ $field_name ] = ! empty( $settings_tax_archive_props[ $field_name ] )
            ? $settings_tax_archive_props[ $field_name ] : [];

        $lookups = Api::getLookup(
            $settings_tax_archive_props['server_id'],
            $resource_id,
            $class_name,
            $field_name
        );

        if ( ! is_wp_error( $lookups ) ) {
            $settings_tax_archive_props['lookups'] += $lookups;
        }
    }
}

$sc_settings = ScSettings::instance()->getScSettingsList( 'ids' );
$sc_settings = ! empty( $sc_settings ) ? $sc_settings = array_map( 'get_post', $sc_settings ) : [];

$servers_list     = Api::get( 'servers' );
$class_data       = new stdClass();
$resource_classes = [];
if ( ! empty( $settings_tax_archive_props['server_id'] ) ) {
    $resource_classes = Api::get( "servers/{$settings_tax_archive_props['server_id']}/resource_class" );
    if ( ! empty( $settings_tax_archive_props['resource_class'] ) ) {
        list( $resource_id, $class_name ) = explode( ':', $settings_tax_archive_props['resource_class'] );
        $class_data = Api::getClassFields( $settings_tax_archive_props['server_id'], $resource_id, $class_name );
    }
}

$constants = [
    'templates'    => TemplatePost::instance()->getTemplatesList(),
    'sc_settings'  => $sc_settings,
    'servers_list' => is_wp_error( $servers_list ) ? $servers_list->get_error_message() : $servers_list
];

$settings = [
    'l10n'  => [
        'label'      => [
            'tab' => __( 'Settings', 'realty-idx-pro' ),
        ],
        'general'    => [
            'label'       => [
                'general'            => __( 'General' ),
                'photos_dir_name'    => __( 'Photos Directory Name', 'realty-idx-pro' ),
                'auto_update_plugin' => __( 'Auto Update', 'realty-idx-pro' ),
            ],
            'description' => [
                'google_maps_api_key' => __( 'Enter your google maps API key', 'realty-idx-pro' ),
                'aws_s3_key'          => __( 'Enter AWS S3 Key', 'realty-idx-pro' ),
                'aws_s3_secret_key'   => __( 'Enter AWS S3 Secret Key', 'realty-idx-pro' ),
                'photos_dir_name'     => __( 'Enter Photos Directory Name' ),
            ]
        ],
        'posttype'   => [
            'label'       => [
                'posttype'      => __( 'Post Type' ),
                'add_post_type' => __( 'Add Post Type', 'realty-idx-pro' ),
                'name'          => __( 'Name', 'realty-idx-pro' ),
                'singular_name' => __( 'Singular Name', 'realty-idx-pro' ),
                'slug'          => __( 'Slug Name', 'realty-idx-pro' ),
            ],
            'description' => [
                'name'           => __( 'Enter post type name', 'realty-idx-pro' ),
                'singular_name'  => __( 'Enter singular name', 'realty-idx-pro' ),
                'slug'           => __( 'Alphanumeric, dash and/or underscore only.', 'realty-idx-pro' ),
                'field_required' => __( '%s field is required.', 'realty-idx-pro' ),
                'post_type_dupe' => __( '%s post type is already registered.', 'realty-idx-pro' ),
            ]
        ],
        'single'     => [
            'label' => [
                'single'            => __( 'Single' ),
                'templates_heading' => __( 'Single Post Templates', 'realty-idx-pro' ),
                'single_template'   => __( 'Single Property Template', 'realty-idx-pro' ),
                'marker_template'   => __( 'Map Marker Template', 'realty-idx-pro' ),
                'misc_settings'     => __( 'Misc. Settings', 'realty-idx-pro' ),
                'meta_description'  => __( 'Meta Description', 'realty-idx-pro' ),
            ]
        ],
        'search'     => [
            'label'       => [
                'search'         => __( 'Search' ),
                'instant_search' => __( 'Enable Instant Search', 'realty-idx-pro' ),
            ],
            'description' => [
                'instant_search' => __(
                    'When enabled, any changes to the search form input will trigger searching automatically.',
                    'realty-idx-pro'
                )
            ]
        ],
        'taxarchive' => [
            'label' => [
                'taxarchive'        => __( 'Taxonomy Archive Page', 'realty-idx-pro' ),
                'settings'          => __( 'Settings', 'realty-idx-pro' ),
                'settings_id'       => __( 'Select Settings ID', 'realty-idx-pro' ),
                'taxonomies'        => __( 'Taxonomies' ),
                'select_server'     => __( 'Select Server', 'realty-idx-pro' ),
                'resource_class'    => __( 'Select Resource:Class', 'realty-idx-pro' ),
                'select_taxonomy'   => __( 'Select Taxonomy Fields', 'realty-idx-pro' ),
                'selected_taxonomy' => __( 'Selected Taxonomy Fields', 'realty-idx-pro' ),
                'select_terms'      => __( 'Select %s Terms', 'realty-idx-pro' ),
                'selected_terms'    => __( 'Selected %s Terms', 'realty-idx-pro' ),
            ],
        ],
    ],
    'props' => [
        'general'     => $settings_general_props,
        'post_type'   => Option::instance()->get(
            'post_type',
            '',
            [ 'name' => '', 'singular_name' => '', 'slug' => '' ]
        ),
        'single'      => $settings_single_props,
        'search'      => $settings_search_props,
        'tax_archive' => $settings_tax_archive_props + compact( 'class_data', 'resource_classes' ),
    ]
];

$settings += compact( 'constants' );

return compact( 'settings' );
