<?php

namespace GO_Toolkit;

# Admin class
trait GO_Admin
{
    use Go_Helper;

    /**
     * Start
     */
    public function admin()
    {          
        
        # Add admin menu
        add_action( 
            'admin_menu', 
            [$this, 'admin_menu'], 
            99 
        );

        # Admin scripts
        add_action(
            'admin_enqueue_scripts',
            [$this, 'admin_scripts']
        );

        # Admin parts
        add_action('admin-part-api', [$this, 'admin_part_api'], 10, 2);
        add_action('admin-part-global-settings', [$this, 'admin_part_global_settings']);
        add_action('admin-part-plugins', [$this, 'admin_part_plugins']);
        add_action('admin-part-loop-items', [$this, 'admin_part_loop_items']);
        add_action('admin-part-acf', [$this, 'admin_part_acf']);
        add_action('admin-part-gforms', [$this, 'admin_part_gforms']);
        add_action('go-lottie', [$this, 'lottie']);

        # Add custom CSS in admin page
        add_action('admin_head', [$this, 'admin_head_css']);
    }


    /**
     * Admin menu
     * @return void
     */
    public function admin_menu()
    {
        # Add custom admin management
        add_menu_page(
            __( $this->plugin_title, 'go-kit' ),
            __( $this->plugin_title, 'go-kit' ),
            'manage_options',
            $this->slug,
            [$this, 'admin_page'],
            $this->plugin_url . 'assets/img/logo-icon-white.svg',
            4
        );        
        add_submenu_page(
            $this->slug,
            __( 'API', 'go-kit' ),
            __( 'API', 'go-kit' ),
            'manage_options',
            $this->slug.'-api',
            [$this, 'admin_page']
        );
        add_submenu_page(
            $this->slug,
            __( 'Global Settings', 'go-kit' ),
            __( 'Global Settings', 'go-kit' ),
            'manage_options',
            $this->slug.'-global-settings',
            [$this, 'admin_page']
        );
        add_submenu_page(
            $this->slug,
            __( 'Plugins', 'go-kit' ),
            __( 'Plugins', 'go-kit' ),
            'manage_options',
            $this->slug.'-plugins',
            [$this, 'admin_page']
        );
        add_submenu_page(
            $this->slug,
            __( 'Loop Items', 'go-kit' ),
            __( 'Loop Items', 'go-kit' ),
            'manage_options',
            $this->slug.'-loop-items',
            [$this, 'admin_page']
        );
        add_submenu_page(
            $this->slug,
            __( 'Gravity Forms', 'go-kit' ),
            __( 'Gravity Forms', 'go-kit' ),
            'manage_options',
            $this->slug.'-gforms',
            [$this, 'admin_page']
        );
        add_submenu_page(
            $this->slug,
            __( 'ACF', 'go-kit' ),
            __( 'ACF', 'go-kit' ),
            'manage_options',
            $this->slug.'-acf',
            [$this, 'admin_page']
        );
    }


    /**
     * Admin scripts
     * @return void
     */
    public function admin_scripts() 
    {

        wp_enqueue_style( 
            'growth-optimizer-admin-css', 
            $this->css('admin'), 
            array(), 
            uniqid(), 
            'all' 
        );

        wp_register_script( 
            'growth-optimizer-admin-script', 
            $this->script('admin'), 
            array( 'jquery' ), 
            uniqid(), 
            true
        );   
        wp_localize_script( 'growth-optimizer-admin-script', 'growth_optimizer', array(
            'ajaxurl'   => esc_url( admin_url( 'admin-ajax.php' ) ),
            'asset_url' => $this->plugin_url
        ) );
    }


    /**
     * Admin part api field
     * @param mixed $api_token
     * @param boolean $is_active
     * @return void
     */
    public function admin_part_api( $api_token, $is_active )
    {
        $this->template(
            'admin_part-api',
            [
                'api_token' => $api_token,
                'is_active' => $is_active
            ]
        );        
    }


    /**
     * Admin part global settings
     * 
     * @return void
     */
    public function admin_part_global_settings()
    {        
        $is_installed = get_option($this->global_settings_option_key, '') == 'installed';
        $this->template(
            'admin_part-global-settings',
            [
                'is_installed' => $is_installed
            ]
        );         
    }


    /**
     * Admin part plugins
     * 
     * @return void
     */
    public function admin_part_plugins()
    {   
        
        $plugins = $this->cloud_server('required-plugins');

        if ($this->is_not_authorize( $plugins )) return;

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }        
        $installed_plugins = get_plugins();

        $this->template(
            'admin_part-plugins',
            [
                'plugins'           => $plugins,
                'installed_plugins' => $installed_plugins
            ]
        );        
    }


    /**
     * Admin part loop items
     * 
     * @return void
     */
    public function admin_part_loop_items()
    {
        $imported   = [];
        $loop_items = $this->cloud_server('loop-items');

        if ($this->is_not_authorize( $loop_items )) return;

        # Check loop items if installed
        foreach ($loop_items as $item) {            
            $found_post_id = $this->is_slug_exists( $item['slug'] );
            if ($found_post_id) {
                $imported[$item['ID']] = $found_post_id;
            }                              
        }

        $this->template(
            'admin_part-loop-items',
            [
                'imported'   => $imported,
                'loop_items' => $loop_items
            ]
        );
        
    }


    /**
     * Part ACF
     * @return void
     */
    public function admin_part_acf()
    {
        $imported  = [];
        $acf_items = $this->cloud_server('acf');

        if ($this->is_not_authorize( $acf_items )) return;

        # Check loop items if installed
        foreach ($acf_items as $item) {            
            if ($this->is_slug_exists( $item['post_name'] )) {
                $imported[] = $item['ID'];
            }                              
        }

        $this->template(
            'admin_part-acf',
            [
                'imported'  => $imported,
                'acf_items' => $acf_items
            ]
        );
        
    }


    /**
     * Lottie
     * @return void
     */
    public function lottie()
    {
        $this->template('admin_part_lottie');        
    }


    /**
     * Part Gravity Forms
     * @return void
     */
    public function admin_part_gforms()
    {
        $imported  = [];
        $gform_items = $this->cloud_server('gforms');

        if ($this->is_not_authorize( $gform_items )) return;

        # Check loop items if installed
        foreach ($gform_items as $item) {            
            if ($this->is_form_exists( $item['title'] )) {
                $imported[] = $item['form_id'];
            }                              
        }        

        $this->template(
            'admin_part-gforms',
            [
                'imported'    => $imported,
                'gform_items' => $gform_items
            ]
        );
        
    }


    /**
     * Admin page
     * @return void
     */
    public function admin_page()
    {
        wp_enqueue_script( 'growth-optimizer-admin-script' );
        $tab = str_replace($this->slug.'-', '', $_GET['page']);        
        $is_active = $this->is_active();  
        $api_key_token = $this->get_api_token();

        $this->template(
            'admin',
            [
                'tab'           => $tab,
                'is_active'     => $is_active,
                'api_key_token' => $api_key_token
            ]
        );
        
    }


    /**
     * Admin head custom css
     * @return void
     */
    public function admin_head_css()
    {
        ?>
        <style>
            #toplevel_page_go-starter-kit .wp-first-item {
                display: none!important;
            }
        </style>
        <?php
    }

}