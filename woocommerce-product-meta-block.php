<?php
/**
 * Plugin Name:       WooCommerce Product Meta Block
 * Description:       A Gutenberg block to display product metadata for WooCommerce products.
 * Version:           1.1.0
 * Author:            Hypercart
 * Author URI:        https://hypercart.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woo-block-product-meta
 * Requires Plugins:  woocommerce
 *
 * @package           woo-block-product-meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check WooCommerce is active; show admin notice if not.
 */
function woo_block_product_meta_check_dependencies() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woo_block_product_meta_missing_woo_notice' );
	}
}
add_action( 'plugins_loaded', 'woo_block_product_meta_check_dependencies' );

/**
 * Admin notice shown when WooCommerce is not active.
 */
function woo_block_product_meta_missing_woo_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'WooCommerce Product Meta Block requires WooCommerce to be installed and active.', 'woo-block-product-meta' );
	echo '</p></div>';
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function woo_block_product_meta_block_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'woo_block_product_meta_render',
		)
	);
}
add_action( 'init', 'woo_block_product_meta_block_init' );

/**
 * Server-side render callback for the Product Meta block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block inner content (unused).
 * @param WP_Block $block      Block instance, providing context.
 * @return string Rendered HTML output.
 */
function woo_block_product_meta_render( $attributes, $content, $block ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	$post_id = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;

	if ( ! $post_id ) {
		return '';
	}

	$meta_key = isset( $attributes['metaKey'] ) ? sanitize_key( $attributes['metaKey'] ) : '';

	if ( empty( $meta_key ) ) {
		return '';
	}

	$meta_value = get_post_meta( $post_id, $meta_key, true );

	if ( '' === $meta_value || false === $meta_value ) {
		return '';
	}

	$label             = isset( $attributes['label'] ) ? sanitize_text_field( $attributes['label'] ) : '';
	$wrapper_attrs     = get_block_wrapper_attributes( array( 'class' => 'woo-product-meta-block' ) );

	$output  = '<div ' . $wrapper_attrs . '>';
	if ( ! empty( $label ) ) {
		$output .= '<span class="woo-product-meta-block__label">' . esc_html( $label ) . '</span>';
	}
	$output .= '<span class="woo-product-meta-block__value">' . esc_html( $meta_value ) . '</span>';
	$output .= '</div>';

	return $output;
}
