<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-default-photo-view" type="text/html">
    <?php echo isset( $_GET['wpdebug'] ) ? '<# console.log(post) #>' : ''; ?>
    <a href="{{ post.link }}">
        <img
                src="{{ post.thumbnails[0] }}"
                alt="{{ post.title }}"
                class="center-block img-responsive"
        />
        <div class="photo-item-wrapper">
            <div class="photo-item-content">
                <div class="photo-item-text">
                        <span class="photo-item-title" href="{{ post.link }}">
                            {{ post.title }}<br />{{ post.price }}
                        </span>
                </div>
            </div>
        </div>
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
</script>
