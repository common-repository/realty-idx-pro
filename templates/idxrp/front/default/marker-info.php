<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-marker-info" type="text/html">
    <div id="gmaps-info-window">
        <div class="container-fluid">
            <div class="gmaps-info-window-header row">
                <div class="col-6"><a href="{{ post.link }}">{{ post.title }}</a></div>
                <div class="col-6"><!-- MLS # --></div>
            </div>
            <div class="gmaps-info-window-body row">
                <div class="col-5">
                    <a href="{{ post.link }}"><img
                                src="{{ post.thumbnails[0] }}"
                                class="attachment-post-thumbnail size-post-thumbnail wp-post-image img-responsive"
                                alt=""
                        /></a>
                </div>
                <div class="col-7 container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="price">{{ post.price }}</h3>
                        </div>
                        <div class="col-12"><a href="{{ post.link }}" class="btn btn-info">Details</a></div>
                    </div>
                </div>
            </div>
            <div class="gmaps-info-window-footer row">

            </div>
        </div>
    </div>
</script>
