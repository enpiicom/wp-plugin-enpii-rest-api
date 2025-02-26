<?php

declare(strict_types=1);

namespace Enpii_Rest_Api\App\WP;

use Enpii_Base\Foundation\WP\WP_Plugin;

class Enpii_Rest_Api_WP_Plugin extends WP_Plugin {
	/**
	 * All hooks shuold be registered here, inside this method
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function manipulate_hooks(): void {
	}

	public function get_name(): string {
		return 'Enpii REST API';
	}

	public function get_version(): string {
		return ENPII_REST_API_PLUGIN_VERSION;
	}
}
