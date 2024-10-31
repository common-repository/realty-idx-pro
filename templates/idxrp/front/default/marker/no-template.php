<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<div class="container-fluid">
    <div class="gmaps-info-window-header row">
        <div class="col-6">{$post_title}</div>
        <div class="col-6"></div>
    </div>
    <div class="gmaps-info-window-body row">
        <div class="col-5">{$thumbnail}</div>
        <div class="col-7 container-fluid">
            <div class="row">
                <div class="col-12">
                    <h3 class="price"></h3>
                </div>
                <div class="col-12">
                    <?php
                    if ( current_user_can( 'edit_posts' ) ) {
                        _e( 'No template found to display marker info.', 'realty-idx-pro' );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="gmaps-info-window-footer row">

    </div>
</div>
