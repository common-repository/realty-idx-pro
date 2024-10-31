<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
$servers = \IDXRealtyPro\Model\Api::getServers();

?>
<div class="idxrpbs idxrp-dialog">
    <div class="modal-body">
        <div class="container-fluid">
            <div id="">
                <div class="form-group row">
                    <label for="idxrp-server-select" class="col-4 control-label"><?php _e(
                            'Server',
                            'realty-idx-pro'
                        ); ?></label>
                    <div class="col-8">
                        <select id="idxrp-server-select">
                            <option value=""><?php _e( '- Select -' ) ?></option>
                            <?php
                            foreach ( $servers as $server ) {
                                printf( '<option value="%d">%s</option>', $server['server_id'], $server['name'] );
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group classes-options"></div>
                <div class="form-group rets-fields-options"></div>
                <div class="form-group tag-options" style="display: none;">
                    <div class="radio">
                        <label>
                            <input type="radio" name="tag_type" value="raw" checked>
                            <?php _e( 'Field Tag' ) ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tag_type" value="template">
                            <?php _e( 'Template Tag' ) ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tag_type" value="js">
                            <?php _e( 'JS Tag' ) ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button"
                id="insert-rets-field"
                class="btn btn-primary btn-sm"><?php _e(
                'Insert RETS Field',
                'realty-idx-pro'
            ); ?></button>
        <button type="button"
                id="cancel-insert-rets-field"
                class="btn btn-default"><?php _e( 'Cancel' );
            ?></button>
    </div>
</div>
