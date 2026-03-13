<?php
/**
 * Uninstall handler for KISS API Guard Plugin.
 *
 * Removes plugin options from the database when the plugin is deleted
 * via the WordPress admin.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'kiss_api_guard_restrict_products' );
delete_option( 'kiss_api_guard_restrict_users' );
