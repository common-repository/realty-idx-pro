<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-idxrp-register-form" type="text/html">
    <form name="registerform" id="idxrp-register-form" class="" method="post">
        <!--<div class="form-group row">
            <label for="user_login" class="col-4 control-label"><?php _e( 'Username' ) ?></label>

            <div class="col-8">
                <input type="text" name="user_login" id="user_login" class="input" size="25" required="required" />
            </div>
        </div>-->

        <div class="form-group row">
            <label for="first_name" class="col-4 control-label"><?php _e( 'First Name', 'realty-idx-pro' ) ?></label>

            <div class="col-8">
                <input type="text" name="first_name" id="first_name" class="input" size="25" required="required" />
            </div>
        </div>

        <div class="form-group row">
            <label for="last_name" class="col-4 control-label"><?php _e( 'Last Name', 'realty-idx-pro' ) ?></label>

            <div class="col-8">
                <input type="text" name="last_name" id="last_name" class="input" size="25" required="required" />
            </div>
        </div>

        <div class="form-group row">
            <label for="user_email" class="col-4 control-label"><?php _e( 'E-mail' ) ?></label>

            <div class="col-8">
                <input type="email" name="user_email" id="user_email" class="input" size="25" required="required" />
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-4 control-label"><?php _e( 'Phone', 'realty-idx-pro' ) ?></label>

            <div class="col-8">
                <input type="text" name="phone" id="phone" class="input" size="25" required="required" />
            </div>
        </div>
        <?php
        /**
         * Fires following the 'E-mail' field in the user registration form.
         *
         * @since 2.1.0
         */
        do_action( 'register_form' );
        ?>
        <div id="reg_passmail" class="form-group row">
            <div class="col-offset-4 col-8"><?php _e( 'A password reset link will be e-mailed to you.' ) ?></div>
        </div>
    </form>
</script>
