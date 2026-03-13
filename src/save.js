/**
 * Save function returns null because this block is rendered server-side
 * via the render_callback in woocommerce-product-meta-block.php.
 *
 * @return {null} Server-side rendered — no static save output.
 */
export default function save() {
	return null;
}
