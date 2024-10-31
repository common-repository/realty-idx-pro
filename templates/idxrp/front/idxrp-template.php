<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
/**
 * @var \WP_Post $template
 */
$had_wptexturize = false;
if ( has_filter( 'the_content', 'wptexturize' ) ) {
    $had_wptexturize = true;
    remove_filter( 'the_content', 'wptexturize' );
}
$had_wpautop = false;
if ( has_filter( 'the_content', 'wpautop' ) ) {
    $had_wpautop = true;
}
?>
<script id="tmpl-idxrp-template-<?php echo $template->ID ?>" type="text/html">
    <?php
    $had_wpautop && remove_filter( 'the_content', 'wpautop' );
    $content = apply_filters( 'the_content', $template->post_content );
    $content = str_replace( ']]>', ']]&gt;', $content );
    echo $content;
    $had_wpautop && add_filter( 'the_content', 'wpautop' );
    ?>
</script>
<?php
if ( $had_wptexturize ) {
    add_filter( 'the_content', 'wptexturize' );
}
