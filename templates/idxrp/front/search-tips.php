<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

$tips = [
    __(
        'To search for MLS number, prefix your search term with a hashtag (e.g. #1234567890)',
        'realty-idx-pro'
    )
];

?>
<script id="tmpl-idxrp-search-tips" type="text/html">
    <ul class="search-tips-list">
        <?php
        foreach ( $tips as $tip ) {
            printf( '<li>%s</li>', $tip );
        }
        ?>
    </ul>
</script>
