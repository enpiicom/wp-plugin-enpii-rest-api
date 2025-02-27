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
	}

	public function enqueue_admin_scripts( $hook ) {
		wp_enqueue_style( 'enpii-rest-api-admin-style', plugin_dir_url( __FILE__ ) . '../public-assets/dist/css/admin.css', [], ENPII_REST_API_PLUGIN_VERSION );
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
		?>
		<div class="enpii-plugins-installer">
			<h2 class="enpii-plugins-installer__title">Required Plugins</h2>
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
						$plugin_path = WP_CONTENT_DIR . '/' . $plugin['type'] . '/' . $plugin['folder'];
						$plugin_file = $plugin['folder'] . '/' . $plugin['main_file'] . '.php';

						$is_mu_plugin = $plugin['type'] === 'mu-plugins';
						$is_installed = is_dir( $plugin_path );
						$is_active = is_plugin_active( $plugin_file );
						$is_registered = array_key_exists( $plugin_file, get_plugins() );
						?>
						<tr>
							<td><?php echo esc_html( $plugin['name'] ); ?></td>
							<td class="enpii-plugins-installer__status enpii-plugins-installer__status--<?php echo $is_active || ( $is_installed && $is_mu_plugin ) ? 'active' : ( $is_registered && ! $is_mu_plugin ? 'inactive' : 'not-installed' ); ?>">
								<?php echo $is_active || ( $is_installed && $is_mu_plugin ) ? 'Active' : ( $is_registered && ! $is_mu_plugin ? 'Not Active' : 'Not Installed' ); ?>
							</td>
							<td>
								<?php if ( ! $is_installed ) : ?>
									<button class="enpii-plugins-installer__button enpii-plugins-installer__button--install" data-slug="<?php echo esc_attr( $slug ); ?>">Install</button>
								<?php elseif ( ! $is_active && $is_mu_plugin ) : ?>
									<span class="enpii-plugins-installer__status-text enpii-plugins-installer__status-text--active">Must-Use Activated</span>
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
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
			<p>Processing</p>
		</div>

		<script>
			jQuery(document).ready(function($) {
				function handlePluginAction(button, action, data, successText) {
					$('#plugin-action-spinner').css('display', 'flex').css('opacity', '1');
					button.text(action + 'ing...').prop('disabled', true);
					$.post(ajaxurl, data, function(response) {
						if (response.success) {
							if (successText) {
								button.replaceWith('<span class="enpii-plugins-installer__status-text enpii-plugins-installer__status-text--active">' + successText + '</span>');
							}
							setTimeout(() => window.location.reload(), 1000);
						} else {
							button.text(action + ' Failed').css('background-color', 'red').prop('disabled', false);
						}
					}).always(function() {
						$('#plugin-action-spinner').css('opacity', '0');
						setTimeout(() => $('#plugin-action-spinner').css('display', 'none'), 500);
					});
				}

				$('.enpii-plugins-installer__button--install').on('click', function() {
					handlePluginAction($(this), 'Install', {
						action: 'enpii_install_plugin',
						plugin_slug: $(this).data('slug')
					});
				});

				$(document).on('click', '.enpii-plugins-installer__button--activate', function() {
					handlePluginAction($(this), 'Activate', {
						action: 'enpii_activate_plugin',
						plugin_path: $(this).data('path'),
						plugin_file: $(this).data('file')
					}, 'Activated');
				});

				$(document).on('click', '.enpii-plugins-installer__button--deactivate', function() {
					handlePluginAction($(this), 'Deactivate', {
						action: 'enpii_deactivate_plugin',
						plugin_path: $(this).data('path'),
						plugin_file: $(this).data('file')
					}, 'Deactivated');
				});
			});
		</script>
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
