<?php
use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<div id="idxrp-plugin-admin-page" class="wrap">
    <h2><?php printf( '%s Admin', Plugin::getPluginData()->Name ); ?></h2>
    <div id="idxrp-admin-app" class="idxrpbs"></div>
</div>
