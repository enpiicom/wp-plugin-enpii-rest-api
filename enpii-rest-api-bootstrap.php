<?php

$enpii_base_existed = defined( 'ENPII_BASE_PLUGIN_VERSION' );

// General fixed constants
defined( 'DIR_SEP' ) || define( 'DIR_SEP', DIRECTORY_SEPARATOR );

// Update these constants whenever you bump the version
defined( 'ENPII_REST_API_PLUGIN_VERSION' ) || define( 'ENPII_REST_API_PLUGIN_VERSION', '0.0.2' );

// We set the slug for the plugin here.
// This slug will be used to identify the plugin instance from the WP_Applucation container
defined( 'ENPII_REST_API_PLUGIN_SLUG' ) || define( 'ENPII_REST_API_PLUGIN_SLUG', 'enpii-rest-api' );

require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Enpii_Rest_Api_Plugins_Installer.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'Enpii_Rest_Api_Helper.php';

$autoload_file = __DIR__ . DIR_SEP . 'vendor' . DIR_SEP . 'autoload.php';

if ( file_exists( $autoload_file ) && ! $enpii_base_existed ) {
	require_once $autoload_file;
}
