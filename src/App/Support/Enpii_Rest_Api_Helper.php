<?php

declare(strict_types=1);

namespace Enpii_Rest_Api\App\Support;

class Enpii_Rest_Api_Helper {
	public static function check_mandatory_prerequisites(): bool {
		return version_compare( phpversion(), '7.3.0', '>=' );
	}

	public static function check_enpii_base_plugin(): bool {
		return (bool) class_exists( \Enpii_Base\App\WP\WP_Application::class );
	}

	public static function get_enpii_plugins_installer_url(): string {
		return admin_url( 'admin.php?page=enpii-plugins-installer' );
	}

	public static function check_wp_upload_and_upgrade_dirs_existence(): bool {
		$upload_dir = wp_upload_dir();
		$upgrade_dir = WP_CONTENT_DIR . '/upgrade';
	
		$uploads_exists = ! empty( $upload_dir['basedir'] ) && is_dir( $upload_dir['basedir'] );
		$upgrade_exists = is_dir( $upgrade_dir );

		return (bool) ( $uploads_exists && $upgrade_exists );
	}

	/**
	 * Checks if the upload and upgrade directories exist.
	 * Returns an error message if any of them are missing.
	 */
	public static function check_missing_directories(): string {

		if ( static::check_wp_upload_and_upgrade_dirs_existence() ) {
			return '';
		}

		$upload_dir = wp_upload_dir();
		$upload_path = $upload_dir['basedir'] ?? '';
		$upgrade_path = WP_CONTENT_DIR . '/upgrade';

		$missing_dirs = [];

		if ( empty( $upload_path ) || ! is_dir( $upload_path ) ) {
			$missing_dirs[] = __( 'Uploads directory is missing. Please create it with permission 0777.', 'enpii-rest-api' );
		}

		if ( ! is_dir( $upgrade_path ) ) {
			$missing_dirs[] = __( 'Upgrade directory is missing. Please create it with permission 0777.', 'enpii-rest-api' );
		}

		return implode( '<br />', $missing_dirs );
	}

	/**
	 * Returns the error message for missing plugin.
	 */
	public static function get_missing_plugin_message(): string {
		return sprintf(
			__( 'Plugin <strong>%1$s</strong> is required. Please <a href="%2$s">click here</a> to install and activate it first.', 'enpii-rest-api' ),
			'Enpii Base',
			static::get_enpii_plugins_installer_url()
		);
	}

	/**
	 * Loads the Enpii Rest API plugin if all requirements are met.
	 */
	public static function load_plugin() {
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

	/**
	 * Displays an admin notice for errors.
	 */
	public static function display_admin_notice( string $error_message ) {
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
}
