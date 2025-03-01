<?php

namespace Elementor\TemplateLibrary;

class GO_Editor extends Go_Helper
{

    # Parent variable holder
    public $parent;

    function __construct($parent)
    {
        # Assign parent variable
        $this->parent = $parent;

        # Helper
        $this->instance($this->parent);

        # Register actions
        # Register elementor footer scripts
        add_action(
            'elementor/editor/footer',
            [$this, 'elementor_footer'],
            99
        );

        # Action for templates
        add_action(
            'kit-templates',
            [$this, 'load_templates'],
            10,
            3
        );

        # Popup filters
        add_action(
            'kit-filters',
            [$this, 'popup_filters']
        );

        # Filter categories
        add_action(
            'kit-filter-item',
            [$this, 'popup_filter_item'],
            10,
            1
        );

        # Popup
        add_action(
            'growth_optimizer-template-popup',
            [$this, 'popup']
        );

        # Popup template item
        add_action(
            'growth_optimizer-template-item',
            [$this, 'popup_template_item'],
            10,
            4
        );

    }


    /**
     * Load template items
     * @param integer $index
     * @param string $template
     * @param integer $selected_category
     * @param string $search_keyword
     * @return void
     */
    public function popup_template_item( $index, $template, $selected_category = 0, $search_keyword = '' )
    {
        $terms_slugs    = [];
        $template_terms = !empty($template['categories']) ? $template['categories'] : [];
        $terms          = [];

        foreach($template_terms as $term) {
            $terms_slugs[] = 'template-item-'.$term['term_id'];
            $terms[]       = (int)$term['term_id'];
        } 
        
        # Search keyword
        if ($search_keyword && !str_contains( strtolower($template['title']), strtolower($search_keyword) )) {
            return;
        }
        # Category
        else if (empty($search_keyword) && $selected_category > 0 && !in_array($selected_category, $terms)) {
            return;
        }

        $template_terms = implode(' ', $terms_slugs);

        $this->template(
            'editor_popup_template-item',
            [
                'template_terms' => $template_terms,
                'template'       => $template,
                'index'          => $index
            ]
        );
        
    }


    /**
     * Load templates
     * @param array $templates
     * @return void
     */
    public function load_templates( $templates, $category, $keyword )
    {   
        foreach( $templates as $index => $template ) {
            if ($template['title'] == 'Default Kit')
                continue;
            do_action('growth_optimizer-template-item', $index, $template, $category, $keyword);
        }                
    }


    /**
     * Popup filters
     * @return void
     */
    public function popup_filters()
    {
        $categories          = ['' => 'Category'];
        $template_categories = $this->parent->get_template_categories();

        if ($this->parent->is_not_authorize( $template_categories )) {
            // Do nothing
        } else {
            $terms = !empty($template_categories) ? $template_categories : [];
            foreach($terms as $term) {
                $categories[$term['term_id']] = $term['name'];
            }            
            do_action('kit-filter-item', $categories);
        }        
    }


    /**
     * Categories filter item
     * @param array $categories
     * @return void
     */
    public function popup_filter_item( $categories )
    {
        $this->template(
            'editor_popup_filter',
            [
                'categories' => $categories
            ]
        );        
    }


    /**
     * Main frame for the template kit
     * popup
     * @return void
     */
    public function popup()
    {
        $this->template('editor_popup');        
    }


    /**
     * Load template kit libraries inside
     * elementor editor footer
     * @return void
     */
    public function elementor_footer()
    {
        wp_enqueue_style( 
            'growth-optimizer-template-kit-css', 
            $this->parent->plugin_url . 'assets/css/editor.css', 
            array(), 
            uniqid(), 
            'all' 
        );

        wp_enqueue_script( 
            'growth-optimizer-template-kit-script', 
            $this->parent->plugin_url . 'assets/js/editor-script.js', 
            array( 'jquery', 'masonry', 'imagesloaded' ), 
            uniqid(), 
            true
        );   

        wp_localize_script( 'growth-optimizer-template-kit-script', 'growth_optimizer', array(
            'ajaxurl'     => esc_url( admin_url( 'admin-ajax.php' ) ),
            'button_icon' => $this->parent->plugin_url . 'assets/img/logo-icon.svg',
            'post_id'     => isset($_GET['post']) ? $_GET['post'] : 0        
        ) );

        do_action('growth_optimizer-template-popup');
    }
}