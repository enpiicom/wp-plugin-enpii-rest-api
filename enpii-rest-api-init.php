<?php

use Enpii_Base\App\Support\Enpii_Base_Helper;
use Enpii_Rest_Api\App\Support\Enpii_Rest_Api_Helper;
use Enpii_Rest_Api\App\WP\Enpii_Rest_Api_WP_Plugin;
use Enpii_Rest_Api\Enpii_Rest_Api_Plugins_Installer;

if (!defined('ABSPATH')) {
    exit;
}

// Initialize plugin installer
new Enpii_Rest_Api_Plugins_Installer();

// Check for missing directories
$error_message = Enpii_Rest_Api_Helper::check_missing_directories();

// Check for required plugin (Enpii Base)
if (!Enpii_Rest_Api_Helper::check_enpii_base_plugin()) {
    $error_message .= ($error_message ? '<br />' : '') . Enpii_Rest_Api_Helper::get_missing_plugin_message();
} else {
	if (!class_exists(\Enpii_Rest_Api\App\WP\Enpii_Rest_Api_WP_Plugin::class)) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
	}

    Enpii_Rest_Api_Helper::load_plugin();
}

// Display error messages in admin notice if needed
if ($error_message) {
    add_action('admin_notices', function () use ($error_message) {
        Enpii_Rest_Api_Helper::display_admin_notice($error_message);
    });
}
