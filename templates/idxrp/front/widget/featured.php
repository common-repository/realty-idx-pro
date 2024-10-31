<?php

use IDXRealtyPro\Controller\View;
use IDXRealtyPro\Model\Api;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}
/**
 * @var array $instance The widget options
 */
$defaults        = [
	'title'          => '',
	'col_num'        => 1,
	'server_id'      => 0,
	'resource_class' => [],
	'field_name'     => '',
	'server_key'     => '',
	'include'        => [],
];
$widget_instance = wp_parse_args( $instance, $defaults );
/**
 * @var string $title
 * @var int    $col_num
 * @var int    $server_id
 * @var array  $resource_class
 * @var string $field_name
 * @var string $server_key
 * @var array  $include
 */
extract( $widget_instance );
?>
<div class="idxrp-search-app idxrpbs">
	<div class="search-results-row">
		<div class="container-fluid">
			<?php if ( empty( $server_id ) || empty( $resource_class ) || empty( $field_name ) || empty( $include ) ) : ?>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<p class="text-danger"><?php _e(
							'Required widget options are not set properly.',
							'realty-idx-pro'
						); ?></p>
				<?php else : ?>
					<p class="text-info"><?php _e( 'No featured properties to show.', 'realty-idx-pro' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<?php
				$listings = [];
				foreach ( $resource_class as $rc ) {
					$_listings = Api::getListingsBy(
						$server_id,
						$server_key,
						$rc,
						$field_name,
						$include,
						count( $include )
					);
					if ( is_wp_error( $_listings ) ) {
						$listings[] = current_user_can( 'manage_options' )
							? $_listings->get_error_message()
							: __(
								'Unable to retrieve data for some listings.',
								'realty-idx-pro'
							);
					} else if ( $_listings ) {
						$listings = array_merge( $listings, $_listings );
					}
				}
				?>
				<?php if ( ! empty( $listings ) ) : ?>
					<div class="row">
						<?php foreach ( $listings as $listing ) : ?>
							<?php if ( is_string( $listing ) ) : ?>
								<p class="text-info"><?php echo $listing; ?></p>
							<?php else : ?>
								<div class="photo-item col-<?php echo 12 / $col_num; ?>">
									<a href="<?php echo $listing['link']; ?>">
										<img
											src="<?php echo $listing['thumbnails'][0]; ?>"
											alt="<?php echo $listing['title']; ?>"
											class="center-block img-responsive"
										/>
										<div class="photo-item-wrapper">
											<div class="photo-item-content">
												<div class="photo-item-text">
                        <span class="photo-item-title" href="<?php echo $listing['link']; ?>">
                            <?php echo $listing['title']; ?><br /><?php echo $listing['price']; ?>
                        </span>
												</div>
											</div>
										</div>
									</a>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
