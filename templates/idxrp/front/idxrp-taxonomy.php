<?php
global $wp;

$term = get_queried_object();

$container   = esc_attr__( "idxrp-{$term->taxonomy}-{$term->name}" );
$settings_id = \IDXRealtyPro\Model\Option::instance()->get( 'tax_archive', 'settings_id' );

get_header();
?>
    <div class="idxrpbs idxrp-search-app">
        <div id="idxrp-search-app-<?php echo $settings_id; ?>" class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-10"><?php //archive property navigation ?></div>
            </div>
            <div class="row">
                <div id="primary" class="col-12">
                    <div id="idxrp-taxonomy-app" class="idxrp-search-app idxrpbs <?php echo $container; ?>"><?php
                        if ( ! $settings_id ) {
                            ?>
                            <p class="text-danger"><?php
                                if ( current_user_can( 'edit_posts' ) ) {
                                    printf(
                                        __(
                                            'Taxonomy Settings ID is not set. Please set in the <a href="%s">plugin admin page</a> under <code>Settings > Taxonomy Archive Page</code> tab.',
                                            'realty-idx-pro'
                                        ),
                                        admin_url( 'admin.php?page=realty-idx-pro-admin' )
                                    );
                                } else {
                                    _e(
                                        'Sorry, this page is not setup properly. Please contact the site administrator.'
                                    );
                                }
                                ?></p>
                            <?php
                        }
                        ?></div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
