<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package ExtendVideoAddons
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'eva_options' );
