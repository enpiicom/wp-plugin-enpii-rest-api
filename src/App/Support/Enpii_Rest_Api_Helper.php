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
}
