<?php

use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<div id="idxrp-plugin-help-page" class="wrap idxrpbs">
    <h2 class="sticky-top"><?php printf( '%s Help', Plugin::getPluginData()->Name ); ?></h2>
    <div id="idxrp-help-app"></div>
</div>
