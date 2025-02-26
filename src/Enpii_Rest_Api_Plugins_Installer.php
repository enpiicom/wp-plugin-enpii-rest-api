<?php

declare(strict_types=1);

namespace Enpii_Rest_Api;

class Enpii_Rest_Api_Plugins_Installer
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_enpii_install_plugin', [$this, 'install_plugin']);
        add_action('wp_ajax_enpii_activate_plugin', [$this, 'activate_plugin']);
        add_action('wp_ajax_enpii_deactivate_plugin', [$this, 'deactivate_plugin']);
    }

    public function enqueue_admin_scripts($hook) {
        wp_enqueue_style('enpii-rest-api-admin-style', plugin_dir_url(__FILE__) . '../public-assets/dist/css/admin.css', [], ENPII_REST_API_PLUGIN_VERSION);
    }    

    // Define required plugins
    protected function get_required_plugins()
    {
        return [
            'enpii-base' => [
                'name' => 'Enpii Base',
                'zip_url' => 'http://enpii-com-demo.dev-srv.net/wp-content/uploads/2025/02/enpii-base-0.9.2.1.zip',
                'type' => 'mu-plugins',
                'folder' => 'enpii-base',
                'main_file' => 'enpii-base'
            ],
            'enpii-html-components' => [
                'name' => 'Enpii HTML Components',
                'zip_url' => 'http://enpii-com-demo.dev-srv.net/wp-content/uploads/2025/02/enpii-html-components-0.0.1.zip',
                'type' => 'plugins',
                'folder' => 'enpii-html-components',
                'main_file' => 'enpii-html-components'
            ]
        ];
    }

    // Add admin menu page
    public function add_admin_menu()
    {
        add_menu_page('Enpii Plugins Installer', 'Enpii Plugins Installer', 'manage_options', 'enpii-plugins-installer', [$this, 'admin_page']);
    }

    // Admin page content
    public function admin_page()
    {
        $required_plugins = $this->get_required_plugins();

        echo '<div class="wrap"><h2>Required Plugins</h2><table class="widefat">';
        echo '<thead><tr><th>Plugin Name</th><th>Status</th><th>Action</th></tr></thead><tbody>';

        foreach ($required_plugins as $slug => $plugin) {
            $plugin_path = WP_CONTENT_DIR . '/' . $plugin['type'] . '/' . $plugin['folder'];
            $plugin_file = $plugin['folder'] . '/' . $plugin['main_file'] . '.php';

            $is_mu_plugin = $plugin['type'] === 'mu-plugins';
            $is_installed = is_dir($plugin_path);
            $is_active = is_plugin_active($plugin_file);
            $is_registered = array_key_exists($plugin_file, get_plugins());

            echo "<tr><td>{$plugin['name']}</td>";

            // Update status column
            if ($is_active || ($is_installed && $is_mu_plugin)) {
                echo '<td style="color: green;">Active</td>';
            } elseif ($is_registered && !$is_mu_plugin) {
                echo '<td style="color: orange;">Not Active</td>';
            } else {
                echo '<td style="color: red;">Not Installed</td>';
            }

            echo '<td>';
            if (!$is_installed) {
                echo '<button class="button enpii-install-plugin" data-slug="' . $slug . '">Install</button>';
            } elseif ($is_mu_plugin) {
                echo '<text style="color: green;>Must-Use Activated</text>';
            } elseif (!$is_active && !$is_mu_plugin) {
                echo '<button class="button enpii-activate-plugin" data-path="'.$plugin['folder'].'" data-file="'.$plugin['main_file'].'">Activate</button>';
            } else {
                echo '<button class="button enpii-deactivate-plugin" data-path="'.$plugin['folder'].'" data-file="'.$plugin['main_file'].'">Deactivate</button>';
            }
            echo '</td></tr>';
        }

        echo '</tbody></table></div>';
?>
        <script>
            jQuery(document).ready(function($) {
                $('.enpii-install-plugin').on('click', function() {
                    var button = $(this);
                    var slug = button.data('slug');

                    button.text('Installing...').prop('disabled', true);

                    $.post(ajaxurl, {
                        action: 'enpii_install_plugin',
                        plugin_slug: slug
                    }, function(response) {
                        if (response.success) {
                            button.replaceWith('<button class="button enpii-activate-plugin" data-path="' +
                                response.plugin_path + '" data-file="' + response.plugin_file + '">Activate</button>');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000); // Refresh after 1 second
                        } else {
                            button.text('Install Failed').css('background-color', 'red').prop('disabled', false);
                        }
                    });
                });

                $(document).on('click', '.enpii-activate-plugin', function() {
                    var button = $(this);
                    var pluginPath = button.data('path');
                    var pluginFile = button.data('file');

                    button.text('Activating...').prop('disabled', true);

                    $.post(ajaxurl, {
                        action: 'enpii_activate_plugin',
                        plugin_path: pluginPath,
                        plugin_file: pluginFile
                    }, function(response) {
                        if (response.success) {
                            button.replaceWith('<span style="color: green;">Activated</span>');
                            setTimeout(function() {
                            window.location.reload();
                        }, 1000); // Refresh after 1 second
                        } else {
                            button.text('Activate Failed').css('background-color', 'red').prop('disabled', false);
                        }
                    });
                });

                $(document).on('click', '.enpii-deactivate-plugin', function() {
                var button = $(this);
                var pluginPath = button.data('path');
                var pluginFile = button.data('file');

                button.text('Deactivating...').prop('disabled', true);

                $.post(ajaxurl, { 
                    action: 'enpii_deactivate_plugin', 
                    plugin_path: pluginPath, 
                    plugin_file: pluginFile 
                }, function(response) {
                    if (response.success) {
                        button.text('Deactivated').css('background-color', 'orange');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000); // Refresh after 1 second
                    } else {
                        button.text('Deactivate Failed').css('background-color', 'red').prop('disabled', false);
                    }
                });
            });

            });
        </script>
<?php
    }

    // Handle plugin installation
    public function install_plugin()
    {
        if (!current_user_can('install_plugins')) {
            wp_send_json_error('Permission denied.');
        }

        $slug = sanitize_text_field($_POST['plugin_slug']);
        $plugins = $this->get_required_plugins();

        if (!isset($plugins[$slug])) {
            wp_send_json_error('Plugin not found.');
        }

        $plugin = $plugins[$slug];
        $zip_url = $plugin['zip_url'];
        $zip_path = WP_CONTENT_DIR . '/uploads/' . basename($zip_url);

        // Download the ZIP file
        $response = wp_remote_get($zip_url, ['timeout' => 300]);

        if (is_wp_error($response)) {
            wp_send_json_error('Download failed: ' . $response->get_error_message());
        }

        $file_contents = wp_remote_retrieve_body($response);
        if (!file_put_contents($zip_path, $file_contents)) {
            wp_send_json_error('Failed to save ZIP file.');
        }

        // Extract the ZIP file
        WP_Filesystem();
        $unzip_result = unzip_file($zip_path, WP_CONTENT_DIR . '/' . $plugin['type']);
        unlink($zip_path); // Remove zip file after extraction

        if (is_wp_error($unzip_result)) {
            wp_send_json_error('Extraction failed.');
        }

        wp_send_json_success([
            'message' => 'Installed successfully.',
            'plugin_path' => $plugin['folder'],
            'plugin_file' => basename($plugin['main_file'], '.php')
        ]);
    }

    // Handle plugin activation
    public function activate_plugin()
    {
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error('Permission denied.');
        }

        $plugin_path = sanitize_text_field($_POST['plugin_path']);
        $plugin_file = sanitize_text_field($_POST['plugin_file']) . '.php';

        $full_plugin_path = WP_PLUGIN_DIR . '/' . $plugin_path . '/' . $plugin_file;

        // Validate that the file exists
        if (!file_exists($full_plugin_path)) {
            wp_send_json_error('Plugin file not found: ' . $full_plugin_path);
        }

        // Activate the plugin
        $result = activate_plugin($plugin_path . '/' . $plugin_file);

        if (is_wp_error($result)) {
            wp_send_json_error('Activation failed: ' . $result->get_error_message());
        }

        wp_send_json_success('Activated successfully.');
    }

    public function deactivate_plugin() {
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error('Permission denied.');
        }
    
        $plugin_path = sanitize_text_field($_POST['plugin_path']);
        $plugin_file = sanitize_text_field($_POST['plugin_file']) . '.php';
    
        $full_plugin_path = WP_PLUGIN_DIR . '/' . $plugin_path . '/' . $plugin_file;
    
        // Validate that the plugin exists before deactivating
        if (!file_exists($full_plugin_path)) {
            wp_send_json_error('Plugin file not found: ' . $full_plugin_path);
        }
    
        // Deactivate the plugin
        deactivate_plugins($plugin_path . '/' . $plugin_file);
    
        // Check if successfully deactivated
        if (is_plugin_active($plugin_path . '/' . $plugin_file)) {
            wp_send_json_error('Deactivation failed.');
        }
    
        wp_send_json_success('Plugin deactivated successfully.');
    }    
}
