# Changelog

All notable changes to **WooCommerce Product Meta Block** will be documented in this file.

## [1.1.0] - 2026-03-13

### Security
- Added WooCommerce dependency check with admin notice when WooCommerce is not active (`woocommerce-product-meta-block.php`)
- Added `current_user_can( 'activate_plugins' )` capability check before displaying the missing-WooCommerce admin notice
- Replaced static placeholder output with a server-side `render_callback` that properly sanitizes all output:
  - `sanitize_key()` applied to the meta key attribute
  - `sanitize_text_field()` applied to the label attribute
  - `esc_html()` applied to all rendered values, preventing XSS

### Added
- Server-side render callback `woo_block_product_meta_render()` with full input sanitization and output escaping
- Block attributes: `metaKey` (string) and `label` (string) for per-instance configuration
- `usesContext: ["postId", "postType"]` in `block.json` so the block can read the current product context
- Editor UI: `InspectorControls` panel with `TextControl` fields for Meta Key and Label configuration
- Editor UI: `Placeholder` shown when no meta key is configured, guiding the user
- Block supports: `color.text`, `color.background`, `typography.fontSize` for styling flexibility
- `.gitignore` to exclude `node_modules/`, IDE files, and OS artifacts from version control
- `Requires Plugins: woocommerce` header in the main plugin file

### Changed
- Plugin version bumped from `1.0.0` to `1.1.0`
- `block.json` `apiVersion` updated from `2` to `3`
- Block icon changed from `archive` to `tag` (more semantically appropriate)
- `save()` function now returns `null` (server-side rendering via `render_callback`)
- `uninstall.php` corrected — removed incorrect references to options from an unrelated plugin (neochrome-api-restrictions); this plugin stores no database options

### Fixed
- `uninstall.php` was deleting options (`neochrome_api_restrict_products`, `neochrome_api_restrict_users`) belonging to an unrelated plugin — this has been corrected

---

## [1.0.0] - 2026-03-13

### Added
- Initial release of WooCommerce Product Meta Block
- Gutenberg block registration via `block.json`
- WordPress block scaffolding with `@wordpress/scripts` build tooling
- `uninstall.php` handler (contained incorrect data — fixed in 1.1.0)

