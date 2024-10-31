<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-idxrp-login-form" type="text/html">
    <form id="idxrp-login-form" class="">
        <div class="form-group row">
            <label for="user_login" class="control-label col-sm-4 col-12"><?php _e( 'Username', 'realty-idx-pro' ); ?></label>
            <div class="col-sm-8 col-12">
                <input
                        id="user_login"
                        type="text"
                        name="user_login"
                        class="form-control"
                        size="20"
                        required="required"
                />
            </div>
        </div><!--
        <div class="form-group row">
            <label class="control-label col-4"><?php /*_e( 'Password', 'realty-idx-pro' ); */ ?></label>
            <div class="col-8">
                <input type="password" name="user_password" class="form-control" size="20" required="required" />
            </div>
        </div>-->
        <div class="form-group row">
            <div class="offset-sm-4"></div>
            <div class="col-12 col-sm-8">
                <div class="form-check">
                    <input id="remember-cb" type="checkbox" name="remember" value="1" class="form-check-input" />
                    <label class="form-check-label" for="remember-cb">
                        <?php _e( 'Remember Me', 'realty-idx-pro' ); ?>
                    </label>
                </div>
            </div>
        </div>
    </form>
</script>
