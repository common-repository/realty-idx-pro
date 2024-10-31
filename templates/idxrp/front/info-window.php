<?php

use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Option;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
global $property, $post;

$meta                 = \IDXRealtyPro\Model\Front::instance()->getPropertyMeta( $post->ID );
$resource_key         = sanitize_key( $meta['resource_id'] );
$default_template     = Util::getDefaultDisplayTemplate( 'single-marker', $meta['server_key'] );
$marker_info_template = Option::instance()->get( 'single', "marker_template_{$resource_key}" );

if ( $marker_info_template ) {
    ?>
    <script id="tmpl-marker-info" type="text/html">
        <div id="gmaps-info-window">
            <?php
            $had_wpautop = false;
            if ( has_filter( 'the_content', 'wpautop' ) ) {
                $had_wpautop = true;
            }
            remove_filter( 'the_content', 'wpautop' );
            echo Util::doReplaceFieldNameVars(
                apply_filters(
                    'the_content',
                    get_post_field( 'post_content', absint( $marker_info_template ) )
                )
            );
            $had_wpautop && add_filter( 'the_content', 'wpautop' );
            ?>
        </div>
    </script>
    <?php
} else {
    $info_window = do_shortcode( Util::doReplaceFieldNameVars( $default_template ) );
    echo $info_window;
}
