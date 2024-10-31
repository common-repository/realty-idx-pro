<?php
/**
 * Author: Paul Grejaldo
 * Date: 2017/08/15
 * Time: 9:52 AM
 */

namespace IDXRealtyPro\Model;

use IDXRealtyPro\Helper\Plugin;
use IDXRealtyPro\Helper\Util;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}

class Api {

	protected static $api_url = 'http://idx.idxrealtypro.com/wp-json/idxrprets/v1.0/api/';

	/**
	 * Get data from api server
	 *
	 * @param string       $ep       Api endpoint
	 * @param array|string $api_args Arguments passed to endpoint
	 *
	 * @param bool         $force    Whether to fetch results from remote server or cache
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function get( $ep, $api_args = [], $force = false ) {
		if ( ! Plugin::isLicenseValid() ) {
			return new \WP_Error(
				'plugin_license_error',
				__( 'Sorry, currently unable to process request.' ),
				[ 'status' => 403 ]
			);
		}
		$api_args  = wp_parse_args( $api_args );
		$transient = 'idxrp_' . sha1( $ep . serialize( $api_args ) );
		//delete_transient( $transient );
		$results = get_transient( $transient );
		if ( ! $results || $force ) {
			$token = self::getToken();
			if ( is_wp_error( $token ) ) {
				return $token;
			}

			$api_url = esc_url_raw( self::$api_url . $ep );
			$api_url = ! empty( $api_args ) ? $api_url . '?' . http_build_query( $api_args ) : $api_url;
			//\IDXRealtyPro\Helper\Debugger::printHtml( urldecode_deep( $api_url ), 'api_url');
			$url      = get_bloginfo( 'url' );
			$origin   = Util::cleanSiteUrl( $url );
			$response = wp_remote_get(
				$api_url,
				[
					'headers' => [
						'Origin'        => $origin,
						'Authorization' => "Bearer {$token}"
					],
					'timeout' => 30
				]
			);

			if ( is_wp_error( $response ) ) {
				set_transient( $transient, $response, 15 );

				return $response;
			} else {
				$body = wp_remote_retrieve_body( $response );

				$results = json_decode( $body, true );
				if ( self::isError( $results ) ) {
					$error = new \WP_Error( $results['code'], $results['message'], $results['data'] );
					set_transient( $transient, $error, 15 );

					return $error;
				} else {
					if ( empty( $results['posts'] ) ) {
						$expiration = 30;
					} else if ( 'search' === $ep ) {
						$expiration = HOUR_IN_SECONDS;
					} else {
						$expiration = HOUR_IN_SECONDS * 12;
					}

					set_transient( $transient, $results, $expiration );
				}
			}
		}

		return $results;
	}

	/**
	 * Get token from api server
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getToken() {
		$url    = get_bloginfo( 'url' );
		$origin = parse_url( $url, PHP_URL_HOST );
		// Check token
		$license_key = Option::instance()->get( 'license', 'key' );
		if ( ! $license_key ) {
			return new \WP_Error(
				'license_error',
				__(
					'No license key found. Please enter your license key in the plugin admin page.',
					'realty-idx-pro'
				),
				[ 'status' => 403 ]
			);
		}
		$token_args      = compact( 'origin', 'license_key' );
		$token_transient = 'idxrp_' . sha1( serialize( $token_args ) );

		$token = get_transient( $token_transient );
		if ( ! $token ) {
			$token_url      = esc_url_raw( self::$api_url . 'token/auth' );
			$token_response = wp_remote_post( $token_url, [ 'body' => $token_args ] );
			if ( is_wp_error( $token_response ) ) {
				return $token_response;
			}
			$body  = wp_remote_retrieve_body( $token_response );
			$token = json_decode( $body, true );
			if ( $token ) {
				if ( ! empty( $token['code'] ) && ! empty( $token['message'] ) && ! empty( $token['data'] ) ) {
					return new \WP_Error( $token['code'], $token['message'], $token['data'] );
				} else if ( ! empty( $token['token'] ) ) {
					$token = $token['token'];
					set_transient( $token_transient, $token, WEEK_IN_SECONDS );
				} else {
					return new \WP_Error(
						'token_error',
						__( 'Unable to retrieve token from the API server.', 'realty-idx-pro' )
					);
				}
			}
		}

		return $token;
	}

	/**
	 * Check if api response has the array elements of an error
	 *
	 * @param array $response
	 *
	 * @return bool
	 */
	public static function isError( $response ) {
		if ( ! empty( $response['code'] ) && ! empty( $response['message'] ) && ! empty( $response['data']['status'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get class data
	 *
	 * @param int    $server_id
	 * @param string $resource_id
	 * @param string $class_name
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getClassFields( $server_id, $resource_id, $class_name ) {
		$api_args = [ 'get' => 'fields', 'resource_id' => $resource_id, 'class_name' => $class_name ];

		return Api::get( "servers/{$server_id}/resource_class", $api_args );
	}

	/**
	 * Get server metadata
	 *
	 * @param int $server_id
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getServer( $server_id ) {
		return Api::get( "servers/{$server_id}" );
	}

	/**
	 * Get servers list
	 *
	 * @return array|\WP_Error
	 */
	public static function getServers() {
		return Api::get( 'servers' );
	}

	/**
	 * Get lookup values
	 *
	 * @param int    $server_id
	 * @param string $resource_id
	 * @param string $class_name
	 * @param string $field_name
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getLookup( $server_id, $resource_id, $class_name, $field_name = '' ) {
		$get      = 'lookup';
		$api_args = compact( 'get', 'resource_id', 'class_name', 'field_name' );
		$lookup   = Api::get( "servers/{$server_id}/resource_class", $api_args );

		return $lookup;
	}

	/**
	 * Get property details
	 *
	 * @param int $post_id
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getDetails( $post_id ) {
		$meta = Front::instance()->getPropertyMeta( $post_id );
		/**
		 * @var string $server_id
		 * @var string $key_field_id
		 * @var string $resource_id
		 * @var string $class_name
		 * @var string $server_key
		 */
		extract( $meta );
		$property = Api::get( "details/{$server_id}/{$resource_id}/{$class_name}/{$key_field_id}" );
		$property = ! is_wp_error( $property ) ? wp_parse_args( $meta, $property ) : $property;

		/* use the thumbnail image in case photo has "no-image.png" */
		if ( ! empty( $property['photos'][0] ) && false !== strpos( $property['photos'][0], 'no-image.png' ) &&
		     ! empty( $property['thumbnails'][0] ) && false === strpos( $property['thumbnails'][0], 'no-image.png' ) ) {
			$property['photos'] = [ $property['thumbnails'][0] ];
		}

		return $property;
	}

	/**
	 * Get Resource:Class
	 *
	 * @param int $post_id
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getResourceClasses( $post_id ) {
		return Api::get( "servers/{$post_id}/resource_class" );
	}

	/**
	 * Get listings by given field name and include values
	 *
	 * @param int    $server_id
	 * @param string $server_key
	 * @param string $rclass
	 * @param string $field_name
	 * @param array  $include
	 * @param int    $per_page
	 *
	 * @return array|\WP_Error
	 */
	public static function getListingsBy( $server_id, $server_key, $rclass, $field_name, $include, $per_page = 9 ) {
		$args = [
			'server_id'      => $server_id,
			'server_key'     => $server_key,
			'resource_class' => $rclass,
			'per_page'       => $per_page,
			$field_name      => [ 'in' => $include, ],
		];

		$transient_key = 'idxrp_' . sha1( 'featured-' . serialize( $args ) );
		$args['sid']   = $transient_key;
		$listings      = get_transient( $transient_key );
		if ( ! $listings ) {
			$api_url  = rest_url(
				Plugin::getWpApiNamespace() . '/' . Plugin::getWpApiSearchRestBase() . '/' . $args['server_id']
			);
			$api_url  = esc_url_raw( add_query_arg( $args, $api_url ) );
			$response = wp_remote_get( $api_url, [ 'sslverify' => false, 'timeout' => 30 ] );
			if ( is_wp_error( $response ) ) {
				return $response;
			}
			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );
			if ( ! empty( $body['posts'] ) ) {
				$listings = $body['posts'];
			}
			set_transient( $transient_key, $listings, HOUR_IN_SECONDS * 3 );
		}

		return $listings;
	}
}
