<?php
/**
 * Uninstall handler for Neochrome API Access Restrictions.
 *
 * Removes plugin options from the database when the plugin is deleted
 * via the WordPress admin.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'neochrome_api_restrict_products' );
delete_option( 'neochrome_api_restrict_users' );
