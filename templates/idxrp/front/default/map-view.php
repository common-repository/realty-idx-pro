<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-default-map-view" type="text/html">
    <?php echo isset( $_GET['wpdebug'] ) ? '<# console.log(post) #>' : ''; ?>
    <img src="{{ post.thumbnails[0] }}" alt="{{post.title}}" class="img-responsive" />
    <div class="photo-item-wrapper">
        <div class="photo-item-content">
            <div class="photo-item-text">
                <a class="photo-item-title" href="{{ post.link }}">
                    {{ post.title }}<br />{{ post.price }}
                </a>
            </div>
        </div>
    </div>
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
</script>
