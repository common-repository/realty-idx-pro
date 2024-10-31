<?php
/**
 * Author: Paul Grejaldo
 * Date: 2018/02/25
 * Time: 7:14 AM
 */

namespace IDXRealtyPro\Widget;

use IDXRealtyPro\Controller\View;
use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}

class Featured extends \WP_Widget {

	protected static $instance;

	public function __construct() {
		$widget_ops = [
			'classname'   => 'featured_properties',
			'description' => __( 'Display featured properties', 'realty-idx-pro' ),
		];
		parent::__construct( 'featured_properties', __( 'IDXRP Featured Properties', 'realty-idx-pro' ), $widget_ops );

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
		}
	}

	/**
	 * Enqueue scripts/styles for widget
	 */
	public function enqueueScripts() {
		$ext = Plugin::getScriptPart( 'ext' );
		wp_enqueue_style(
			'idxrp-search-app',
			plugins_url( "css/front/search-app{$ext}.css", IDX_REALTY_PRO_PLUGIN_FILE ),
			[ 'font-awesome', 'roboto-font', 'idxrp-bootstrap' ]
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		include View::instance()->make( 'idxrp/front/widget/featured.php', [], false );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		include View::instance()->make( 'idxrp/admin/widget/featured.php', [], false );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		if ( ! empty( $new_instance['server_id'] ) ) {
			$new_instance['server_id'] = intval( $new_instance['server_id'] );
		}
		if ( ! empty( $new_instance['resource_class'] ) ) {
			$new_instance['resource_class'] = (array) $new_instance['resource_class'];
		}
		if ( ! empty( $new_instance['include'] ) ) {
			$include = trim( $new_instance['include'] );
			if ( $include ) {
				$include = explode( ',', $include );

				$new_instance['include'] = array_map( 'trim', $include );
			}
		}

		return $new_instance;
	}
}
