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
                    <label for="rets-server-select" class="col-4 control-label"><?php _e(
                            'Server',
                            'realty-idx-pro'
                        ); ?></label>
                    <div class="col-8">
                        <select id="rets-server-select">
                            <option value=""><?php _e( '- Select -' ) ?></option>
                            <?php
                            foreach ( $servers as $server ) {
                                printf( '<option value="%d">%s</option>', $server['server_id'], $server['name'] );
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button"
                id="insert-template"
                class="btn btn-primary btn-sm"><?php _e(
                'Insert Template',
                'realty-idx-pro'
            ); ?> <span class="loading-template" style="display: none;"><img src="<?php echo plugins_url(
                    'images/loading.gif',
                    IDX_REALTY_PRO_PLUGIN_FILE
                ); ?>" style="width: 1rem;" /></span></button>
        <button type="button"
                id="cancel-insert-template"
                class="btn btn-default"><?php _e( 'Cancel' );
            ?></button>
    </div>
</div>
