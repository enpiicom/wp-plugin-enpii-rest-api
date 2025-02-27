"use strict";

(function ($) {
    $(document).ready(function () {
        const spinner = $('#plugin-action-spinner');

        function handlePluginAction(button, action, data, successText) {
            showSpinner();
            button.text(`${action}ing...`).prop('disabled', true);

            $.post(ajaxurl, data)
                .done(response => {
                    if (response.success && successText) {
                        button.replaceWith(`<span class="enpii-plugins-installer__status-text enpii-plugins-installer__status-text--active">${successText}</span>`);
                    }
                    setTimeout(() => location.reload(), 100);
                })
                .fail(() => {
                    button.text(`${action} Failed`).css('background-color', 'red').prop('disabled', false);
                })
                .always(() => hideSpinner());
        }

        function showSpinner() {
            spinner.css({ display: 'flex', opacity: '1' });
        }

        function hideSpinner() {
            spinner.css('opacity', '0');
            setTimeout(() => spinner.css('display', 'none'), 500);
        }

        $(document).on('click', '.enpii-plugins-installer__button--install', function () {
            handlePluginAction($(this), 'Install', {
                action: 'enpii_install_plugin',
                plugin_slug: $(this).data('slug')
            });
        });

        $(document).on('click', '.enpii-plugins-installer__button--activate', function () {
            handlePluginAction($(this), 'Activate', {
                action: 'enpii_activate_plugin',
                plugin_path: $(this).data('path'),
                plugin_file: $(this).data('file')
            }, 'Activated');
        });

        $(document).on('click', '.enpii-plugins-installer__button--deactivate', function () {
            handlePluginAction($(this), 'Deactivate', {
                action: 'enpii_deactivate_plugin',
                plugin_path: $(this).data('path'),
                plugin_file: $(this).data('file')
            }, 'Deactivated');
        });
    });
})(jQuery);
