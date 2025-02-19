( function ( $ ) {
    Growth_Optimizer_Kit = {
        doc              : $(document),
        button           : null,
        index            : 0,
        active_template  : -1,
        scope            : '',
        model            : '',
        selected_category: '',
        search_keyword   : '',
        typing_timer     : null,
        typing_interval  : 500,
        title            : '',
        
        init             : function() {
            if (elementorCommon) {
                let add_section_tmpl = $( '#tmpl-elementor-add-section' );
                if ( add_section_tmpl.length > 0 ) {
        
                    let action_for_add_section = add_section_tmpl.text();
        
                    action_for_add_section = action_for_add_section.replace(
                        '<div class="elementor-add-section-drag-title',                
                        `<div class="elementor-add-section-area-button elementor-growth_optimizer-template-button" title="Growth Optimizer Starter Template" style="padding:6px;">
                            <img src="`+growth_optimizer.button_icon+`" alt="" style="opacity:0.8"/>
                        </div>
                        <div class="elementor-add-section-drag-title`
                    );
        
                    add_section_tmpl.text( action_for_add_section );  
                    
                    elementor.on( 'preview:loaded', function () {
                        Growth_Optimizer_Kit.scope = $('.growth_optimizer-template-popup');
                        Growth_Optimizer_Kit.scope.addClass('ready');

                        // Open popup
                        $( elementor.$previewContents[ 0 ].body ).on(
                            'click',
                            '.elementor-growth_optimizer-template-button',
                            Growth_Optimizer_Kit._open
                        );
                        
                        Growth_Optimizer_Kit._actions();

                        // Load templates
                        Growth_Optimizer_Kit._load_templates();
                    });
        
                }        
            }
        },  
        
        _actions: function() {
            // Close popup
            Growth_Optimizer_Kit.doc.on(
                'click',
                '.popup-close',
                Growth_Optimizer_Kit._close
            );

            // Template item
            Growth_Optimizer_Kit.doc.on(
                'click',
                '.template-item',
                Growth_Optimizer_Kit._openItem
            );

            // Back to library
            Growth_Optimizer_Kit.doc.on(
                'click',
                '.kit-back-library, .kit-logo',
                Growth_Optimizer_Kit._backToLibrary
            );

            // Import template
            Growth_Optimizer_Kit.doc.on(
                'click',
                '.kit-import',
                Growth_Optimizer_Kit._import
            );

            // Refresh
            Growth_Optimizer_Kit.doc.on(
                'click',
                '.kit-refresh',
                Growth_Optimizer_Kit._refresh
            );

            // Filter by category
            Growth_Optimizer_Kit.doc.on(
                'change', 
                '#filter-categories',
                Growth_Optimizer_Kit._filter
            );
            $('#filter-categories').select2();

            // Search template
            $('#filter-search').on(
                'keyup',                            
                function() {
                    clearTimeout(Growth_Optimizer_Kit.typing_timer);                              
                    Growth_Optimizer_Kit.typing_timer = setTimeout(
                        Growth_Optimizer_Kit._search, 
                        Growth_Optimizer_Kit.typing_interval
                    );                              
                }
            );                        

            // Close popup if click outside container
            Growth_Optimizer_Kit.doc.click(function(e) { 
                var $target = $(e.target);
                if(!$target.closest('.kit-container').length && $('.kit-container').is(":visible")) {
                  Growth_Optimizer_Kit._close(null);                              
                }        
            });
        },

        _before: function() {
            if (Growth_Optimizer_Kit.button !== null)
                Growth_Optimizer_Kit.button.find('span').text('Importing...');
            Growth_Optimizer_Kit.scope.addClass('loading');
        },

        _after: function() {
            if (Growth_Optimizer_Kit.button !== null)
                Growth_Optimizer_Kit.button.find('span').text('Import');
            Growth_Optimizer_Kit.scope.removeClass('loading');
        },

        _masonry: function () {
			//create empty var masonryObj
			var masonryObj;
			var container = document.querySelector(
				'.kit-templates'
			);
			// initialize Masonry after all images have loaded
			imagesLoaded( container, function () {
				masonryObj = new Masonry( container, {
					itemSelector: '.template-item',
				} );
			} );
		},        

        _cloud_server: function(_data, callback) {            
            Growth_Optimizer_Kit._before(); 
            $.ajax({
                url     : growth_optimizer.ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : _data              
            }).done(callback);
        },

        _refresh: function(e) {
            e.preventDefault();
            Growth_Optimizer_Kit.selected_category = '';
            $('#filter-categories').val('').trigger('change.select2');
            $('#filter-search').val('');
            Growth_Optimizer_Kit.search_keyword = '';
            Growth_Optimizer_Kit._load_templates();
        },

        _load_templates: function() {                                   
            Growth_Optimizer_Kit._cloud_server({
                action: 'growth_optimizer_load_templates',
                category: Growth_Optimizer_Kit.selected_category,
                keyword: Growth_Optimizer_Kit.search_keyword
            }, function(response) {                
                $('.kit-templates').html(response.templates);    
                Growth_Optimizer_Kit._masonry();                            
                Growth_Optimizer_Kit._after();
            });
        },

        _search: function() {
            Growth_Optimizer_Kit.selected_category = '';
            $('#filter-categories').val('').trigger('change.select2');
            Growth_Optimizer_Kit.search_keyword = $('#filter-search').val();
            Growth_Optimizer_Kit._load_templates();
        },

        _filter: function() {
            $('#filter-search').val('');
            Growth_Optimizer_Kit.search_keyword = '';
            Growth_Optimizer_Kit.selected_category = $(this).val();
            Growth_Optimizer_Kit._load_templates();                        
        },

        _after_import: function(response) {            
            try {

                let page_content = response.data;

                page_content = page_content.map( function ( item ) {
                    item.id = Math.random().toString( 36 ).substr( 2, 7 );
                    return item;
                } );
            
                if ( undefined !== page_content && '' !== page_content ) {
                    if (
                        undefined != $e &&
                        'undefined' != typeof $e.internal
                    ) {
                        elementor.channels.data.trigger(
                            'document/import',
                            Growth_Optimizer_Kit.model
                        );
                        elementor
                            .getPreviewView()
                            .addChildModel(
                                page_content,
                                { at: Growth_Optimizer_Kit.index } || {}
                            );
                        elementor.channels.data.trigger(
                            'template:after:insert',
                            {}
                        );
                        $e.internal( 'document/save/set-is-modified', {
                            status: true,
                        } );
                    } else {
                        elementor.channels.data.trigger(
                            'document/import',
                            Growth_Optimizer_Kit.model
                        );
                        elementor
                            .getPreviewView()
                            .addChildModel(
                                page_content,
                                { at: Growth_Optimizer_Kit.index } || {}
                            );
                        elementor.channels.data.trigger(
                            'template:after:insert',
                            {}
                        );
                        elementor.saver.setFlagEditorChange( true );
                    }
                    
                }

                // Close the add section
                $( elementor.$previewContents[ 0 ].body)
                    .find('.elementor-add-section-close')
                        .trigger('click');

                Growth_Optimizer_Kit._close(null);  


            } catch (error) {
                Growth_Optimizer_Kit._error(error.message);
            }
                                          
            Growth_Optimizer_Kit._after();
        },

        _import: function(e) {
            e.preventDefault();
            Growth_Optimizer_Kit.button = $(this);
            Growth_Optimizer_Kit._cloud_server(
                {
                    action : 'growth_optimizer_template_kit',
                    post_id: growth_optimizer.post_id,
                    template: Growth_Optimizer_Kit.active_template                   
                }, Growth_Optimizer_Kit._after_import);            
        },

        _backToLibrary: function(e) {
            if (e !== null)
                e.preventDefault();
            Growth_Optimizer_Kit.scope.removeClass('preview-item-active');
            Growth_Optimizer_Kit.active_template = -1;
            $('.template-item').removeClass('active');            
        },
        
        _openItem: function(e) {
            e.preventDefault();
            $template = $(this);
            Growth_Optimizer_Kit.scope.addClass('preview-item-active');
            Growth_Optimizer_Kit.active_template = parseInt($template.data('template'));
            $('.template-item').removeClass('active');
            $template.addClass('active');
            Growth_Optimizer_Kit.title = $template.find('.item-title').text();
            $('.kit-template-title').text( Growth_Optimizer_Kit.title );

            // Scroll back to the top
            $('.kit-templates-container').animate({
                scrollTop: 0
            }, 100);
        },

        _open: function() {
            $( 'html,body' ).addClass( 'growth_optimizer-template-kit-open' );            

            let add_section = $( this ).closest( '.elementor-add-section' );

			if ( add_section.hasClass( 'elementor-add-section-inline' ) ) {
				Growth_Optimizer_Kit.index = add_section.prevAll().length;
			} else {
				Growth_Optimizer_Kit.index = add_section
					.prev()
					.children().length;
			}

            Growth_Optimizer_Kit.model = new Backbone.Model( {
                getTitle() {
                    return '1Dea Starter Template';
                },
            } );

        },

        _close: function(e) {
            if (e !== null)
                e.preventDefault();            
            $( 'html,body' ).removeClass( 'growth_optimizer-template-kit-open' );
            Growth_Optimizer_Kit._backToLibrary(e);
        },

        _error: function(message) {
            console.log(message);
            $sub_message = "Check the Admin page > GO Starter Kit page to see if the required plugin is available. If it's not available, you will need to manually install the required plugin.";            
            if (message.includes('uael-gf-styler')) {
                $message = 'The '+ Growth_Optimizer_Kit.title +' block requires the Ultimate Addons for Elementor Pro and Gravity Forms plugins to be imported.';
            }
            else if (message.includes('uael')) {
                $message = 'The '+ Growth_Optimizer_Kit.title +' block require the Ultimate Addons for Elementor Pro plugin to be imported.';
            }
            else if (message.includes('derick_tooltip')) {
                $message = 'The '+ Growth_Optimizer_Kit.title +' block require Derick - Tooltip plugin to be imported.';
            }            
            alert($message + ' ' + $sub_message);
        }
    }
    Growth_Optimizer_Kit.init();
} )( jQuery );