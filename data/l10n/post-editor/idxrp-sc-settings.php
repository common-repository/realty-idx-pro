<?php

use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;
use IDXRealtyPro\Model\Option;
use IDXRealtyPro\Model\ScSettings;
use IDXRealtyPro\Model\TemplatePost;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
global $post, $wp_post_types;

$settings_id  = absint( $post->ID );
$post_title   = get_the_title( $post );
$servers_list = Api::getServers();
if ( is_wp_error( $servers_list ) ) {
    $error        = $servers_list;
    $servers_list = [];
    add_action(
        'admin_notices',
        function () use ( $error ) {
            echo Util::getAdminNotice( $error->get_error_message() );
        }
    );
}
$general_settings = Option::instance()->get( 'general' );
$google_maps_api  = ! empty( $general_settings['google_maps_api_key'] );

$string_operator    = [
    [ 'label' => __( 'Equals to', 'realty-idx-pro' ), 'value' => 'equals' ],
    [ 'label' => __( 'Contains', 'realty-idx-pro' ), 'value' => 'contains' ],
    [ 'label' => __( 'Any (IN)', 'realty-idx-pro' ), 'value' => 'in' ],
    [ 'label' => __( 'Not Any (NOT IN)', 'realty-idx-pro' ), 'value' => 'not_in' ],
];
$numeric_operator   = [
    [ 'label' => __( 'Equals to', 'realty-idx-pro' ), 'value' => 'equals' ],
    [ 'label' => __( 'Greater than or equals to', 'realty-idx-pro' ), 'value' => 'gte' ],
    [ 'label' => __( 'Less than or equals to', 'realty-idx-pro' ), 'value' => 'lte' ],
    [ 'label' => __( 'Not equal to', 'realty-idx-pro' ), 'value' => 'ne' ],
    [ 'label' => __( 'Between', 'realty-idx-pro' ), 'value' => 'between' ],
];
$date_time_operator = $numeric_operator;// same as numeric operator

$settings         = ScSettings::instance()->get( $settings_id );
$resource_classes = ! empty( $settings['server_id'] )
    ? Api::getResourceClasses( $settings['server_id'] ) : [];
if ( is_wp_error( $resource_classes ) ) {
    $error            = $resource_classes;
    $resource_classes = [];
    add_action(
        'admin_notices',
        function () use ( $error ) {
            echo Util::getAdminNotice( $error->get_error_message() );
        }
    );
}

$class_data         = new \stdClass();
$search_fields_data = [];
if ( ! empty( $settings['server_id'] ) && ! empty( $settings['resource_class'] ) ) {
    list( $resource_id, $class_name ) = explode( ':', $settings['resource_class'] );
    $class_data = Api::getClassFields( $settings['server_id'], $resource_id, $class_name );
    if ( ! is_wp_error( $class_data ) && ! empty( $class_data['fields'] ) ) {
        $search_fields_data = Util::classDataSearchFieldsList( $class_data['fields'] );
    }
}

$search_app_pages = get_posts(
    [
        'post_type'      => 'page',
        'posts_per_page' => -1,
        's'              => '[idxrp_search_app',
        'post_status'    => 'publish',
    ]
);

$template_posts = TemplatePost::instance()->getTemplatesList();

$post_types = wp_filter_object_list( $wp_post_types, [ 'public' => true ], 'and', 'label' );

$input_types = [
    'Select',
    'MultiCheckbox',
    'MultiSelect',
];

$values_type = [
    [ 'label' => 'Min/Max Price', 'value' => 'min_max_price' ],
    //[ 'label' => 'Exact Price', 'value' => 'exact_price' ],
    [ 'label' => 'Min Number', 'value' => 'min_num' ],
    [ 'label' => 'Exact Number', 'value' => 'exact_num' ],
    [ 'label' => 'Lookup Values', 'value' => 'lookup' ],
];

$views = [
    [ 'label' => __( 'List', 'realty-idx-pro' ), 'value' => 'list' ],
    [ 'label' => __( 'Photo', 'realty-idx-pro' ), 'value' => 'photo' ],
];
if ( $google_maps_api ) {
    $views[] = [ 'label' => __( 'Map', 'realty-idx-pro' ), 'value' => 'map' ];
}

$sc_settings = [
    'l10n'      => [
        'label'       => [
            'settings'              => __( 'Query Settings', 'realty-idx-pro' ),
            'query_fields'          => __( 'Query Fields', 'realty-idx-pro' ),
            'select_post_type'      => __( 'Select Listings Post Type', 'realty-idx-pro' ),
            'select_server'         => __( 'Select Server', 'realty-idx-pro' ),
            'select_class'          => __( 'Select Resource:Class', 'realty-idx-pro' ),
            'select_field'          => __( 'Select Field', 'realty-idx-pro' ),
            'select_operator'       => __( 'Operator', 'realty-idx-pro' ),
            'orderby'               => __( 'Order By', 'realty-idx-pro' ),
            'orderby_fields'        => __( 'Order By Fields', 'realty-idx-pro' ),
            'asc'                   => __( 'Ascending (Lowest to Highest)', 'realty-idx-pro' ),
            'desc'                  => __( 'Descending (Highest to Lowest)', 'realty-idx-pro' ),
            'order'                 => __( 'Order', 'realty-idx-pro' ),
            'default_order'         => __( 'Select Default Order', 'realty-idx-pro' ),
            'minimum'               => __( 'Minimum', 'realty-idx-pro' ),
            'maximum'               => __( 'Maximum', 'realty-idx-pro' ),
            'values'                => __( 'Values', 'realty-idx-pro' ),
            'value'                 => __( 'Value', 'realty-idx-pro' ),
            'no_options'            => __( 'No Options', 'realty-idx-pro' ),
            'between'               => __( '%1$s between %2$s and %3$s', 'realty-idx-pro' ),
            'lte'                   => __( '%1$s less than or equal to %2$s', 'realty-idx-pro' ),
            'gte'                   => __( '%1$s greater than or equal to %2$s', 'realty-idx-pro' ),
            'ne'                    => __( '%1$s not equal to %2$s', 'realty-idx-pro' ),
            'equals'                => __( '%1$s equals to %2$s', 'realty-idx-pro' ),
            'like'                  => __( '%1$s like %2$s', 'realty-idx-pro' ),
            'contains'              => __( '%1$s contains %2$s', 'realty-idx-pro' ),
            'in'                    => __( '%1$s in %2$s', 'realty-idx-pro' ),
            'not_in'                => __( '%1$s not in %2$s', 'realty-idx-pro' ),
            'datetime_between'      => __( '%1$s is between %2$s', 'realty-idx-pro' ),
            'copy'                  => __( 'Copy to clipboard', 'realty-idx-pro' ),
            'copied'                => __( 'Copied', 'realty-idx-pro' ),
            'primary_fields'        => __( 'Primary Search Fields', 'realty-idx-pro' ),
            'adv_fields'            => __( 'Advanced Search Fields', 'realty-idx-pro' ),
            'search_fields'         => __( 'Search Fields', 'realty-idx-pro' ),
            'select_input_type'     => __( 'Select Input Type', 'realty-idx-pro' ),
            'input_type'            => __( 'Input Type', 'realty-idx-pro' ),
            'label'                 => __( 'Label', 'realty-idx-pro' ),
            'options'               => __( 'Options', 'realty-idx-pro' ),
            'field_name'            => __( 'Field Name', 'realty-idx-pro' ),
            'load_options'          => __( 'Load options', 'realty-idx-pro' ),
            'load'                  => __( 'Load', 'realty-idx-pro' ),
            'loading'               => __( 'Loading...', 'realty-idx-pro' ),
            'show_hide'             => __( 'Show/hide', 'realty-idx-pro' ),
            'mls_number_field'      => __( 'MLS Number Field', 'realty-idx-pro' ),
            'address_field'         => __( 'Address Field', 'realty-idx-pro' ),
            'neighborhood_field'    => __( 'Neighborhood Field', 'realty-idx-pro' ),
            'city_field'            => __( 'City Field', 'realty-idx-pro' ),
            'county_field'          => __( 'County Field', 'realty-idx-pro' ),
            'state_field'           => __( 'State Field', 'realty-idx-pro' ),
            'postal_code_field'     => __( 'Postal Code Field', 'realty-idx-pro' ),
            'display_address_field' => __( 'Display Address Field', 'realty-idx-pro' ),
            'price_field'           => __( 'Price Field', 'realty-idx-pro' ),
            'lat_field'             => __( 'Latitude Field', 'realty-idx-pro' ),
            'lng_field'             => __( 'Longitude Field', 'realty-idx-pro' ),
            'default_view'          => __( 'Default View', 'realty-idx-pro' ),
            'list_template'         => __( 'List View Template', 'realty-idx-pro' ),
            'photo_template'        => __( 'Photo View Template', 'realty-idx-pro' ),
            'map_template'          => __( 'Map View Template', 'realty-idx-pro' ),
            'marker_template'       => __( 'Map Marker Template', 'realty-idx-pro' ),
            'search_only'           => __( 'Search Form Only', 'realty-idx-pro' ),
            'search_page'           => __( 'Search Page', 'realty-idx-pro' ),
            'hide_search'           => __( 'Hide Search Form', 'realty-idx-pro' ),
            'class_select'          => __( 'Class Select', 'realty-idx-pro' ),
        ],
        'description' => [
            'any_value'        => __( 'Enter multiple values separated by a newline', 'realty-idx-pro' ),
            'add_options'      => __(
                'Enter multiple values separated by a newline. Format should be "Label:Value"',
                'realty-idx-pro'
            ),
            'default_view'     => __( 'Select Default View', 'realty-idx-pro' ),
            'search_page'      => __(
                'No search app pages found! Please create one first (content should contain the shortcode [idxrp_search_app])',
                'realty-idx-pro'
            ),
            'search_page_note' => __(
                'Select a search app page that can display the search results.',
                'realty-idx-pro'
            ),
            'class_select'     => __(
                'Add class dropdown select. (Hold down [ctrl] or [cmd] key to select multiple options)',
                'realty-idx-pro'
            ),
        ]
    ],
    'props'     => compact(
                       'settings_id',
                       'post_title',
                       'servers_list',
                       'resource_classes',
                       'class_data',
                       'search_fields_data'
                   ) + $settings,
    'constants' => compact(
        'numeric_operator',
        'date_time_operator',
        'string_operator',
        'input_types',
        'values_type',
        'post_types',
        'views',
        'template_posts',
        'search_app_pages'
    ),
];

return compact( 'sc_settings' );
