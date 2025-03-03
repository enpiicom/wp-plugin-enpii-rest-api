<?php

use Enpii_Base\App\Support\Enpii_Base_Helper;
use Enpii_Rest_Api\App\Support\Enpii_Rest_Api_Helper;
use Enpii_Rest_Api\App\WP\Enpii_Rest_Api_WP_Plugin;
use Enpii_Rest_Api\Enpii_Rest_Api_Plugins_Installer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

new Enpii_Rest_Api_Plugins_Installer();

if ( Enpii_Rest_Api_Helper::check_enpii_base_plugin() ) {
	if ( ! class_exists( Enpii_Rest_Api_WP_Plugin::class ) ) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
	}

	if ( Enpii_Base_Helper::is_setup_app_completed() ) {
		add_action(
			'plugins_loaded',
			function () {
				Enpii_Rest_Api_WP_Plugin::init_with_wp_app(
					ENPII_REST_API_PLUGIN_SLUG,
					__DIR__,
					plugin_dir_url( __FILE__ )
				);
			},
			-111 
		);
	}
} else {
	$error_message = '';
	$error_message .= $error_message ? '<br />' : '';
	$error_message .= sprintf(
		__(
			'Plugin <strong>%1$s</strong> is required. 
    Please <a href="%2$s">click here</a> to install and activate it first.',
			'enpii-rest-api' 
		), 
		'Enpii Base', 
		Enpii_Rest_Api_Helper::get_enpii_plugins_installer_url() 
	);

	if ( $error_message ) {
		add_action(
			'admin_notices',
			function () use ( $error_message ) {
				$error_message = sprintf(
					__( 'Plugin <strong>%s</strong> is not functioning.', 'enpii-rest-api' ),
					'Enpii Rest Api Plugin'
				) . '<br />' . $error_message;

				?>
		<div class="notice notice-warning is-dismissible">
				<p><?php echo $error_message; ?></p>
		</div>
				<?php
			}
		);
	}
}
