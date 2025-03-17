<?php

use Enpii_Rest_Api\App\Support\Enpii_Rest_Api_Helper;
use Enpii_Rest_Api\Enpii_Rest_Api_Plugins_Installer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Initialize plugin installer
new Enpii_Rest_Api_Plugins_Installer();

// Check for missing directories
$error_message = '';
$error_message = Enpii_Rest_Api_Helper::check_missing_directories();

// Check for required plugin (Enpii Base)
if ( ! Enpii_Rest_Api_Helper::check_enpii_base_plugin() ) {
	$error_message .= Enpii_Rest_Api_Helper::get_missing_plugin_message();
} else {
	if ( ! class_exists( \Enpii_Rest_Api\App\WP\Enpii_Rest_Api_WP_Plugin::class ) ) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
	}

	if ( \Enpii_Base\App\Support\Enpii_Base_Helper::is_setup_app_completed() ) {
		add_action(
			'plugins_loaded',
			function () {
				\Enpii_Rest_Api\App\WP\Enpii_Rest_Api_WP_Plugin::init_with_wp_app(
					ENPII_REST_API_PLUGIN_SLUG,
					__DIR__,
					plugin_dir_url( __FILE__ )
				);
			},
			-111
		);
	}
}


// Display error messages in admin notice if needed
if ( ! empty( $error_message ) ) {
	add_action(
		'admin_notices',
		function () use ( $error_message ) {
			Enpii_Rest_Api_Helper::display_admin_notice( $error_message );
		}
	);
}
