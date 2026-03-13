<?php
/**
 * Plugin Name: KISS API Guard Plugin
 * Description: Strips sensitive product data (stock quantities, sales counts, cost-of-goods) from unauthenticated WooCommerce REST API responses. Authenticated API key holders see the full response.
 * Version: 1.0.2
 * Author: Neochrome
 * Requires Plugins: woocommerce
 * Text Domain: kiss-api-guard
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "API Restrictions" section under WooCommerce > Settings > Advanced.
 *
 * @param array<string, string> $sections Existing advanced settings sections.
 * @return array<string, string>
 */
add_filter( 'woocommerce_get_sections_advanced', function( $sections ) {
	$sections['api_restrictions'] = __( 'API Restrictions', 'kiss-api-guard' );
	return $sections;
} );

/**
 * Add settings fields for the API Restrictions section.
 *
 * @param array<int, array<string, mixed>> $settings        Existing advanced settings.
 * @param string                           $current_section Current WooCommerce settings section.
 * @return array<int, array<string, mixed>>
 */
add_filter( 'woocommerce_get_settings_advanced', function( $settings, $current_section ) {
	if ( 'api_restrictions' !== $current_section ) {
		return $settings;
	}

	return array(
		array(
			'title' => __( 'API Restrictions', 'kiss-api-guard' ),
			'type'  => 'title',
			'desc'  => __( 'Control what data is visible to unauthenticated REST API requests. Authenticated API key holders are not affected by these settings.', 'kiss-api-guard' ),
			'id'    => 'kiss_api_guard_options',
		),
		array(
			'title'    => __( 'Filter unauthenticated product responses', 'kiss-api-guard' ),
			'desc'     => __( 'Remove stock quantities, total sales, and cost-of-goods from product responses when the request has no API key. Disable this if you need the public products endpoint to return the full product object.', 'kiss-api-guard' ),
			'id'       => 'kiss_api_guard_restrict_products',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Disable users endpoint', 'kiss-api-guard' ),
			'desc'     => __( 'Block the /wp/v2/users endpoint for unauthenticated requests. This endpoint exposes admin usernames and can aid brute-force attacks.', 'kiss-api-guard' ),
			'id'       => 'kiss_api_guard_restrict_users',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'type' => 'sectionend',
			'id'   => 'kiss_api_guard_options',
		),
	);
}, 10, 2 );

/**
 * Strip sensitive fields from a product or variation API response for unauthenticated requests.
 *
 * @param WP_REST_Response $response The response object.
 * @param WC_Data          $object   Product or variation object.
 * @param WP_REST_Request  $request  The request object.
 * @return WP_REST_Response
 */
function kiss_api_guard_filter_sensitive_product_data( $response, $object, $request ) {
	if ( 'yes' !== get_option( 'kiss_api_guard_restrict_products', 'yes' ) ) {
		return $response;
	}

	// If the request is authenticated, return full response.
	if ( kiss_api_guard_is_authenticated_request() ) {
		return $response;
	}

	$data = $response->get_data();

	// Remove sensitive inventory and sales fields.
	$data['stock_quantity'] = null;
	if ( array_key_exists( 'total_sales', $data ) ) {
		$data['total_sales'] = null;
	}

	// Remove cost-of-goods entries from meta_data.
	$sensitive_meta_keys = array(
		'_ni_cost_goods',
		'_alg_wc_cog_cost',
	);

	/**
	 * Filter the list of meta_data keys stripped from unauthenticated responses.
	 *
	 * @param array $sensitive_meta_keys Meta key names to remove.
	 */
	$sensitive_meta_keys = apply_filters( 'kiss_api_guard_sensitive_meta_keys', $sensitive_meta_keys );

	if ( ! empty( $data['meta_data'] ) && is_array( $data['meta_data'] ) ) {
		$data['meta_data'] = array_values( array_filter(
			$data['meta_data'],
			function( $meta ) use ( $sensitive_meta_keys ) {
				$key = is_array( $meta ) ? ( $meta['key'] ?? '' ) : ( $meta->key ?? '' );
				return ! in_array( $key, $sensitive_meta_keys, true );
			}
		) );
	}

	$response->set_data( $data );

	return $response;
}

add_filter( 'woocommerce_rest_prepare_product_object', 'kiss_api_guard_filter_sensitive_product_data', 10, 3 );
add_filter( 'woocommerce_rest_prepare_product_variation_object', 'kiss_api_guard_filter_sensitive_product_data', 10, 3 );

/**
 * Block the /wp/v2/users endpoint for unauthenticated requests.
 *
 * @param mixed            $result  Response to replace the requested version with, or null to continue dispatching.
 * @param WP_REST_Server   $server  Server instance.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @return mixed
 */
add_filter( 'rest_pre_dispatch', function( $result, $server, $request ) {
	if ( 'yes' !== get_option( 'kiss_api_guard_restrict_users', 'yes' ) ) {
		return $result;
	}

	$route = $request->get_route();

	if ( preg_match( '#^/wp/v2/users(/|$)#', $route ) && ! kiss_api_guard_is_authenticated_request() ) {
		return new WP_Error(
			'rest_cannot_view',
			__( 'Access to this endpoint is restricted.', 'kiss-api-guard' ),
			array( 'status' => 401 )
		);
	}

	return $result;
}, 10, 3 );

/**
 * Check whether the current REST API request is authenticated.
 *
 * By the time WooCommerce REST response filters fire, valid API keys have
 * already been verified and the current user has been set. Checking
 * is_user_logged_in() is sufficient and avoids treating the mere presence
 * of unvalidated credentials (e.g. a bogus consumer_key) as authenticated.
 *
 * @return bool
 */
function kiss_api_guard_is_authenticated_request() {
	return is_user_logged_in();
}
