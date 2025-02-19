(function($) {
    Growth_Optimizer_Settings = {

        scope                : null,
        doc                  : $(document),
        plugin_index         : -1,
        plugin_to_install    : [],
        loop_item_index      : -1,
        loop_item_to_install : [],
        acf_item_index       : -1,
        acf_item_to_install  : [],
        gform_item_index     : -1,
        gform_item_to_install: [],

        init: function() {
            // Import global settings
            Growth_Optimizer_Settings.doc.on(
                'click', 
                '.go-import-global-settings', 
                Growth_Optimizer_Settings._import_settings
            );

            // Activate api token
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.go-activate-api-token',
                Growth_Optimizer_Settings._activate_api_token
            );

            // Install plugin
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.go-install-plugins',
                Growth_Optimizer_Settings._install_plugin
            );

            // Import loop items
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.go-import-loop-items',
                Growth_Optimizer_Settings._import_loop_items
            );

            // Import ACF items
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.go-import-acf-items',
                Growth_Optimizer_Settings._import_acf_items
            );

            // Import Gravity Forms
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.go-import-gforms',
                Growth_Optimizer_Settings._import_gforms
            );

            // Copy to clipboard when clicking the post ID
            Growth_Optimizer_Settings.doc.on(
                'click',
                '.installed .post-id',
                Growth_Optimizer_Settings.copy_to_clipboard_id
            );
        },

        _before: function($el) {
            $el.addClass('loading');
        },

        _after: function($el) {
            $el.removeClass('loading');
            setTimeout(function() {
                $('.go-success-popup').fadeOut('fast', function() {
                    $('.go-success-popup').remove();
                });
            }, 1000);
        },

        _server: function(data, callback) {
            Growth_Optimizer_Settings._before(
                Growth_Optimizer_Settings.scope
            );
            $.ajax({
                url     : growth_optimizer.ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : data
            })
            .done(callback)
            .fail(function() {
                Growth_Optimizer_Settings._error('Invalid request');
                Growth_Optimizer_Settings._after(
                    Growth_Optimizer_Settings.scope
                );
            });
        },

        _import_settings: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent(); 
            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_import_global_settings'
            }, function(response) {
                console.log(response);      
                Growth_Optimizer_Settings._message(
                    response.success,
                    'Successfully imported',
                    'Invalid request'
                );
                window.location.reload();
            });
        },

        _activate_api_token: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent();
            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_activate_api_token',
                token : $('input#api-token-input').val()
            }, function(response) {
                console.log(response);      
                Growth_Optimizer_Settings._message(
                    response.success,
                    'API access token activated',
                    'API access token is invalid'
                );
                window.location.reload();
            });
        },

        _install_process: function() {
            
            if (Growth_Optimizer_Settings.plugin_index < 0) {
                Growth_Optimizer_Settings.plugin_index = 0;
            }
            
            const to_install  = Growth_Optimizer_Settings.plugin_to_install.length;
            const next_plugin = Growth_Optimizer_Settings.plugin_to_install[
                Growth_Optimizer_Settings.plugin_index
            ];

            next_plugin.closest('li').addClass('process');

            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_install_plugin',
                plugin: next_plugin.val()
            }, function(response) {
                console.log(response);
                next_plugin
                    .closest('li')
                    .addClass('installed')
                    .removeClass('process');

                if (Growth_Optimizer_Settings.plugin_index + 1 >= to_install) {                    
                    window.location.reload();
                    Growth_Optimizer_Settings._message(
                        response.success,
                        'Plugin installation performed.'
                    );                    
                } else {
                    Growth_Optimizer_Settings.plugin_index += 1;
                    Growth_Optimizer_Settings._install_process();
                }

            });
            
        },

        _install_plugin: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent();
            Growth_Optimizer_Settings.plugin_to_install = [];
            Growth_Optimizer_Settings.plugin_index = -1;
            const plugins = $('.plugin-item:checked');           
            plugins.each(function() {
                Growth_Optimizer_Settings.plugin_to_install.push( $(this) );
            });

            if (Growth_Optimizer_Settings.plugin_to_install.length)
                Growth_Optimizer_Settings._install_process();
            else
                alert('Please select plugin to install.');
            
        },

        _import_process: function() {
            if (Growth_Optimizer_Settings.loop_item_index < 0) {
                Growth_Optimizer_Settings.loop_item_index = 0;
            }
            
            const to_import      = Growth_Optimizer_Settings.loop_item_to_install.length;
            const next_loop_item = Growth_Optimizer_Settings.loop_item_to_install[
                Growth_Optimizer_Settings.loop_item_index
            ];

            next_loop_item.closest('li').addClass('process');            

            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_import_loop_items',
                id    : next_loop_item.val(),
                name  : next_loop_item.data('name')
            }, function(response) {

                next_loop_item
                    .closest('li')
                    .addClass('installed')
                    .removeClass('process');

                if (Growth_Optimizer_Settings.loop_item_index + 1 >= to_import) {
                    window.location.reload();
                    Growth_Optimizer_Settings._message(
                        response.success,
                        'Selected loop item imported.'
                    );                    
                } else {
                    Growth_Optimizer_Settings.loop_item_index += 1;
                    Growth_Optimizer_Settings._import_process();
                }

            });
        },

        _import_loop_items: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent();
            Growth_Optimizer_Settings.loop_item_to_install = [];
            Growth_Optimizer_Settings.loop_item_index = -1;
            const items = $('.loop-item:checked');           
            items.each(function() {
                Growth_Optimizer_Settings.loop_item_to_install.push( $(this) );
            });

            if (Growth_Optimizer_Settings.loop_item_to_install.length)
                Growth_Optimizer_Settings._import_process();
            else
                alert('Please select loop item to import.');
        },

        _import_acf_process: function() {
            if (Growth_Optimizer_Settings.acf_item_index < 0) {
                Growth_Optimizer_Settings.acf_item_index = 0;
            }
            
            const to_import     = Growth_Optimizer_Settings.acf_item_to_install.length;
            const next_acf_item = Growth_Optimizer_Settings.acf_item_to_install[
                Growth_Optimizer_Settings.acf_item_index
            ];

            next_acf_item.closest('li').addClass('process');            

            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_import_acf_items',
                id    : next_acf_item.val(),
                name  : next_acf_item.data('name')
            }, function(response) {

                next_acf_item
                    .closest('li')
                    .addClass('installed')
                    .removeClass('process');

                if (Growth_Optimizer_Settings.acf_item_index + 1 >= to_import) {
                    window.location.reload();
                    Growth_Optimizer_Settings._message(
                        response.success,
                        'Selected ACF item imported.'
                    );                    
                } else {
                    Growth_Optimizer_Settings.acf_item_index += 1;
                    Growth_Optimizer_Settings._import_acf_process();
                }

            });
        },

        _import_acf_items: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent();
            Growth_Optimizer_Settings.acf_item_to_install = [];
            Growth_Optimizer_Settings.acf_item_index = -1;
            const items = $('.acf-item:checked');           
            items.each(function() {
                Growth_Optimizer_Settings.acf_item_to_install.push( $(this) );
            });

            if (Growth_Optimizer_Settings.acf_item_to_install.length)
                Growth_Optimizer_Settings._import_acf_process();
            else
                alert('Please select ACF item to import.');
        },

        _import_gform_process: function() {
            if (Growth_Optimizer_Settings.gform_item_index < 0) {
                Growth_Optimizer_Settings.gform_item_index = 0;
            }
            
            const to_import       = Growth_Optimizer_Settings.gform_item_to_install.length;
            const next_gform_item = Growth_Optimizer_Settings.gform_item_to_install[
                Growth_Optimizer_Settings.gform_item_index
            ];

            next_gform_item.closest('li').addClass('process');            

            Growth_Optimizer_Settings._server({
                action: 'growth_optimizer_import_gform_items',
                id    : next_gform_item.val(),
                name  : next_gform_item.data('name')
            }, function(response) {
                console.log(response);
                next_gform_item
                    .closest('li')
                    .addClass('installed')
                    .removeClass('process');

                if (Growth_Optimizer_Settings.gform_item_index + 1 >= to_import) {
                    window.location.reload();
                    Growth_Optimizer_Settings._message(
                        response.success,
                        'Selected form item imported.'
                    );                    
                } else {
                    Growth_Optimizer_Settings.gform_item_index += 1;
                    Growth_Optimizer_Settings._import_gform_process();
                }

            });
        },

        _import_gforms: function(e) {
            e.preventDefault();
            Growth_Optimizer_Settings.scope = $(this).parent();
            Growth_Optimizer_Settings.gform_item_to_install = [];
            Growth_Optimizer_Settings.gform_item_index = -1;
            const items = $('.gform-item:checked');           
            items.each(function() {
                Growth_Optimizer_Settings.gform_item_to_install.push( $(this) );
            });

            if (Growth_Optimizer_Settings.gform_item_to_install.length)
                Growth_Optimizer_Settings._import_gform_process();
            else
                alert('Please select form item to import.');
        },

        copy_to_clipboard_id: function(e) {
            e.preventDefault();
            
            const ID = $(this).data('id');
            let aux = document.createElement("input");            
            aux.setAttribute("value", ID);            
            document.body.appendChild(aux);
            aux.select();            
            document.execCommand("copy");
            document.body.removeChild(aux);
            alert('Post ID '+ ID +' copied.');
        },

        _message: function(success, success_message, error_message) {
            if (success) {
                Growth_Optimizer_Settings._success(success_message);
            } else {
                Growth_Optimizer_Settings._error(error_message);
            }            
        },

        _success: function(message) {
            $('body').append(`
                <div class="go-success-popup success">
                    <div class="message">
                        <dotlottie-player src="`+growth_optimizer.asset_url+`/assets/img/supprise.lottie" background="transparent" speed="1" style="width:100%; height:100%; max-width:300px;max-height:300px;" loop autoplay></dotlottie-player>                        
                    </div>
                </div>
            `);
        },

        _error: function(message) {
            $('body').append(`
                <div class="go-success-popup error">
                    <div class="message">
                        <svg height="32" style="overflow:visible;enable-background:new 0 0 32 32" viewBox="0 0 32 32" width="32" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g id="Error_1_"><g id="Error"><circle cx="16" cy="16" id="BG" r="16" style="fill:#D72828;"/><path d="M14.5,25h3v-3h-3V25z M14.5,6v13h3V6H14.5z" id="Exclamatory_x5F_Sign" style="fill:#E6E6E6;"/></g></g></g></svg>
                        <div style="font-size:14px;margin-top:10px;">`+message+`</div>
                    </div>
                </div>
            `);
        }
    }
    Growth_Optimizer_Settings.init();
})(jQuery);