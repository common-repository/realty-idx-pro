<?php

use IDXRealtyPro\Helper\Util;
use IDXRealtyPro\Model\Api;

global $post;

$meta = \IDXRealtyPro\Model\Front::instance()->getPropertyMeta( $post->ID );
/**
 * @var string $server_id
 * @var string $key_field_id
 * @var string $resource_id
 * @var string $class_name
 * @var string $server_key
 */
extract( $meta );
$property         = Api::getDetails( $post->ID );
$default_template = Util::getDefaultDisplayTemplate( 'single', $server_key );
$single_template  = \IDXRealtyPro\Model\Option::instance()->get(
    'single',
    'single_template_' . sanitize_key( $resource_id )
);
$single_template  = ! empty( $single_template ) ? $single_template : '';
$back_to_search   = '';
if ( ! empty( $_COOKIE['IDXRP_LAST_SEARCH'] ) && ! empty( $_COOKIE['IDXRP_LAST_SEARCH_PAGE'] ) ) {
    $back_to_search = sprintf(
        '<a id="idxrp-back-to-search" href="%s">%s</a>',
        esc_url( get_permalink( intval( $_COOKIE['IDXRP_LAST_SEARCH_PAGE'] ) ) ),
        esc_html__( 'Back to search', 'realty-idx-pro' )
    );
} else if ( ! empty( $_COOKIE['IDXRP_LAST_SEARCH_PAGE'] ) ) {
    $back_to_search = sprintf(
        '<a id="idxrp-back-to-search" href="%s">%s</a>',
        esc_url( get_permalink( intval( $_COOKIE['IDXRP_LAST_SEARCH_PAGE'] ) ) ),
        esc_html__( 'Back', 'realty-idx-pro' )
    );
}

get_header();
?>
    <div class="idxrpbs">
        <div id="idxrp-single-property-container" class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-2">
                    <p><?php echo $back_to_search; ?></p>
                </div>
                <div class="col-12 col-md-10"><?php //single property navigation ?></div>
            </div>
            <div class="row">
                <div id="primary" class="col-12">
                    <div id="property-<?php the_ID(); ?>" class="idxrp-property-container">
                        <?php
                        if ( $single_template ) {
                            $content = get_post_field( 'post_content', absint( $single_template ) );
                        } else {
                            $content = $default_template;
                        }
                        echo str_replace(
                            ']]>',
                            ']]&gt;',
                            apply_filters( 'the_content', Util::doReplaceFieldNameVars( $content ) )
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
