<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-default-list-view" type="text/html">
    <?php echo isset( $_GET['wpdebug'] ) ? '<# console.log(post) #>' : ''; ?>
    <div class="col-12 col-sm-8">
        <div class="pull-left list-item-thumbnail">
            <a href="{{ post.link }}">
                <img src="{{ post.thumbnails[0] }}" alt="{{ post.title }}"
                     class="img-responsive img-thumbnail" />
            </a>
            <div class="favorite-btn-container">
                <a href="#" id="favorite-{{ post.key_field_id }}"
                   class="favorite-btn{{ post.is_favorite ? ' is-favorite' : '' }}"
                   title="{{ post.is_favorite ? '<?php esc_attr_e(
                       'Remove from Favorites',
                       'realty-idx-pro'
                   ); ?>' : '<?php esc_attr_e( 'Save to Favorites', 'realty-idx-pro' ); ?>' }}"
                   data-key-id="{{ post.key_field_id }}">
                </a>
            </div>
        </div>
        <p class="property-excerpt">
            <a href="{{ post.link }}">{{ post.title }}</a>
        </p>
    </div>
    <div class="col-12 col-sm-4">
        <div class="property-price">{{ post.price }}</div>
        <div class="property-mls">{{ post.mls_number }}</div>
        <div class="property-permalink">
            <a class="btn btn-primary btn-view-details" href="{{ post.link }}">
                <?php _e( 'View Details', 'realty-idx-pro' ) ?>
            </a>
        </div>
    </div>
</script>
