<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Videtect
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'videtect_options' );
