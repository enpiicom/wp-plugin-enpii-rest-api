<?php

declare(strict_types=1);

namespace Enpii_Rest_Api;

class Enpii_Rest_Api_Plugins_Installer {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'wp_ajax_enpii_install_plugin', [ $this, 'install_plugin' ] );
		add_action( 'wp_ajax_enpii_activate_plugin', [ $this, 'activate_plugin' ] );
		add_action( 'wp_ajax_enpii_deactivate_plugin', [ $this, 'deactivate_plugin' ] );
		add_action( 'wp_ajax_enpii_rest_api_dismiss_notice', [ $this,'dismiss_admin_notice' ] );
		add_action( 'wp_ajax_nopriv_enpii_rest_api_dismiss_notice', [ $this,'dismiss_admin_notice' ] );      
	}

	public function enqueue_admin_scripts( $hook ) {
		wp_enqueue_style(
			'enpii-rest-api-admin-style',
			plugin_dir_url( __FILE__ ) . '../public-assets/dist/css/admin.css',
			[],
			ENPII_REST_API_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'enpii-rest-api-admin-script',
			plugin_dir_url( __FILE__ ) . '../public-assets/dist/js/admin.js',
			[ 'jquery' ],
			ENPII_REST_API_PLUGIN_VERSION,
			false
		);

		$enpiiDismissNotice = [
			'nonce' => wp_create_nonce( 'enpii_dismiss_notice' ),
		];

		wp_localize_script( 'enpii-rest-api-admin-script', 'enpiiDismissNotice', $enpiiDismissNotice );
	}

	/**
	 * Handles the AJAX request to dismiss the notice.
	 */
	public function dismiss_admin_notice() {
		set_transient( 'enpii_rest_api_dismiss_notice', true, 86400 ); // Hide for 24 hours
		wp_die();
	}


	// Define required plugins
	protected function get_required_plugins() {
		return [
			'enpii-base' => [
				'name' => 'Enpii Base',
				'zip_url' => 'http://enpii-com-demo.dev-srv.net/wp-content/uploads/2025/02/enpii-base-0.9.2.1.zip',
				'type' => 'mu-plugins',
				'folder' => 'enpii-base',
				'main_file' => 'enpii-base',
			],
			'enpii-html-components' => [
				'name' => 'Enpii HTML Components',
				'zip_url' => 'http://enpii-com-demo.dev-srv.net/wp-content/uploads/2025/02/enpii-html-components-0.0.1.zip',
				'type' => 'plugins',
				'folder' => 'enpii-html-components',
				'main_file' => 'enpii-html-components',
			],
		];
	}

	// Add admin menu page
	public function add_admin_menu() {
		add_menu_page( 'Enpii Plugins Installer', 'Enpii Plugins Installer', 'manage_options', 'enpii-plugins-installer', [ $this, 'admin_page' ] );
	}

	// Admin page content
	public function admin_page() {
		$required_plugins = $this->get_required_plugins();
		
		$counts = [
			'all' => 0,
			'active' => 0,
			'inactive' => 0,
			'Must-Use' => 0,
			'not-installed' => 0,
		];
		
		foreach ( $required_plugins as $plugin ) {
			$plugin_path = WP_CONTENT_DIR . '/' . esc_attr( $plugin['type'] ) . '/' . esc_attr( $plugin['folder'] );
			$plugin_file = esc_attr( $plugin['folder'] ) . '/' . esc_attr( $plugin['main_file'] ) . '.php';
	
			$is_mu_plugin = $plugin['type'] === 'mu-plugins';
			$is_installed = is_dir( $plugin_path );
			$is_active = is_plugin_active( $plugin_file );
			$is_registered = array_key_exists( $plugin_file, get_plugins() );
	
			$status = $is_active || ( $is_installed && $is_mu_plugin ) ? 'active' 
					: ( $is_registered && ! $is_mu_plugin ? 'inactive' : 'not-installed' );
	
			if ( $is_mu_plugin && $is_installed ) {
				$status = 'Must-Use';
			}
	
			++$counts[ $status ];
			++$counts['all'];
		}
		?>
		<div class="wrap enpii-plugins-installer">
			<h2 class="enpii-plugins-installer__title">Enpii Required Plugins Installer</h2>
			<ul class="enpii-plugins-installer__tabs">
				<li class="all"><a href="#" class="current" aria-current="page">All <span class="count">(<?php echo esc_html( $counts['all'] ); ?>)</span></a> |</li>
				<li class="active"><a href="#">Active <span class="count">(<?php echo esc_html( $counts['active'] ); ?>)</span></a> |</li>
				<li class="inactive"><a href="#">Inactive <span class="count">(<?php echo esc_html( $counts['inactive'] ); ?>)</span></a> |</li>
				<li class="not-installed"><a href="#">Not Installed <span class="count">(<?php echo esc_html( $counts['not-installed'] ); ?>)</span></a> |</li>
				<li class="Must-Use"><a href="#">Must-Use <span class="count">(<?php echo esc_html( $counts['Must-Use'] ); ?>)</span></a></li>
			</ul>
			<table class="enpii-plugins-installer__table">
				<thead>
					<tr>
						<th>Plugin Name</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $required_plugins as $slug => $plugin ) :
						$plugin_path = WP_CONTENT_DIR . '/' . esc_attr( $plugin['type'] ) . '/' . esc_attr( $plugin['folder'] );
						$plugin_file = esc_attr( $plugin['folder'] ) . '/' . esc_attr( $plugin['main_file'] ) . '.php';
	
						$is_mu_plugin = $plugin['type'] === 'mu-plugins';
						$is_installed = is_dir( $plugin_path );
						$is_active = is_plugin_active( $plugin_file );
						$is_registered = array_key_exists( $plugin_file, get_plugins() );
						$status = ( $is_active || ( $is_installed && $is_mu_plugin ) ) ? 'active' 
								: ( $is_registered && ! $is_mu_plugin ? 'inactive' : 'not-installed' );
	
						if ( $is_mu_plugin && $is_installed ) {
							$status = 'Must-Use';
						}
						?>
						<tr>
							<td><?php echo esc_html( $plugin['name'] ); ?></td>
							<td class="enpii-plugins-installer__status enpii-plugins-installer__status--<?php echo esc_attr( $status ); ?>">
								<?php echo esc_html( ucfirst( $status ) ); ?>
							</td>
							<td>
								<?php if ( ! $is_installed ) : ?>
									<button class="enpii-plugins-installer__button enpii-plugins-installer__button--install" data-slug="<?php echo esc_attr( $slug ); ?>">Install</button>
								<?php elseif ( ! $is_active && $is_mu_plugin ) : ?>
									<span class="enpii-plugins-installer__status enpii-plugins-installer__status--active">Must-Use Activated</span>
								<?php elseif ( ! $is_active && ! $is_mu_plugin ) : ?>
									<button class="enpii-plugins-installer__button enpii-plugins-installer__button--activate" data-path="<?php echo esc_attr( $plugin['folder'] ); ?>" data-file="<?php echo esc_attr( $plugin['main_file'] ); ?>">Activate</button>
								<?php else : ?>
									<button class="enpii-plugins-installer__button enpii-plugins-installer__button--deactivate" data-path="<?php echo esc_attr( $plugin['folder'] ); ?>" data-file="<?php echo esc_attr( $plugin['main_file'] ); ?>">Deactivate</button>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div id="plugin-action-spinner">
			<div class="lds-roller">
				<div></div><div></div><div></div><div></div>
				<div></div><div></div><div></div><div></div>
			</div>
			<p>Processing</p>
		</div>
		<?php
	}
	
	// Handle plugin installation
	public function install_plugin() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$slug = sanitize_text_field( $_POST['plugin_slug'] );
		$plugins = $this->get_required_plugins();

		if ( ! isset( $plugins[ $slug ] ) ) {
			wp_send_json_error( 'Plugin not found.' );
		}

		$plugin = $plugins[ $slug ];
		$zip_url = $plugin['zip_url'];
		$zip_path = WP_CONTENT_DIR . '/uploads/' . basename( $zip_url );

		// Download the ZIP file
		$response = wp_remote_get( $zip_url, [ 'timeout' => 300 ] );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( 'Download failed: ' . $response->get_error_message() );
		}

		$file_contents = wp_remote_retrieve_body( $response );
		if ( ! file_put_contents( $zip_path, $file_contents ) ) {
			wp_send_json_error( 'Failed to save ZIP file.' );
		}

		// Extract the ZIP file
		WP_Filesystem();
		$unzip_result = unzip_file( $zip_path, WP_CONTENT_DIR . '/' . $plugin['type'] );
		unlink( $zip_path ); // Remove zip file after extraction

		if ( is_wp_error( $unzip_result ) ) {
			wp_send_json_error( 'Extraction failed.' );
		}

		wp_send_json_success(
			[
				'message' => 'Installed successfully.',
				'plugin_path' => $plugin['folder'],
				'plugin_file' => basename( $plugin['main_file'], '.php' ),
			]
		);
	}

	// Handle plugin activation
	public function activate_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$plugin_path = sanitize_text_field( $_POST['plugin_path'] );
		$plugin_file = sanitize_text_field( $_POST['plugin_file'] ) . '.php';

		$full_plugin_path = WP_PLUGIN_DIR . '/' . $plugin_path . '/' . $plugin_file;

		// Validate that the file exists
		if ( ! file_exists( $full_plugin_path ) ) {
			wp_send_json_error( 'Plugin file not found: ' . $full_plugin_path );
		}

		// Activate the plugin
		$result = activate_plugin( $plugin_path . '/' . $plugin_file );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( 'Activation failed: ' . $result->get_error_message() );
		}

		wp_send_json_success( 'Activated successfully.' );
	}

	public function deactivate_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$plugin_path = sanitize_text_field( $_POST['plugin_path'] );
		$plugin_file = sanitize_text_field( $_POST['plugin_file'] ) . '.php';

		$full_plugin_path = WP_PLUGIN_DIR . '/' . $plugin_path . '/' . $plugin_file;

		// Validate that the plugin exists before deactivating
		if ( ! file_exists( $full_plugin_path ) ) {
			wp_send_json_error( 'Plugin file not found: ' . $full_plugin_path );
		}

		// Deactivate the plugin
		deactivate_plugins( $plugin_path . '/' . $plugin_file );

		// Check if successfully deactivated
		if ( is_plugin_active( $plugin_path . '/' . $plugin_file ) ) {
			wp_send_json_error( 'Deactivation failed.' );
		}

		wp_send_json_success( 'Plugin deactivated successfully.' );
	}
}
