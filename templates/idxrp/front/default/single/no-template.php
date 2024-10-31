<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<div id="idxrp-property-{$ID}" class="idxrp-property">
    <div class="row">
        <div class="col-12">
            <p class="text-danger"><?php _e(
                    'No template found to display listing details of {$post_title}',
                    'realty-idx-pro'
                ) ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12"><p>{$edit_post_link}</p></div>
    </div>
</div>
