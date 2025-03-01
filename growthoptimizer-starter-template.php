<?php
/**
 * Plugin Name:     Growth Optimizer Starter Template
 * Plugin URI:      https://growthoptimizer.com
 * Description:     Starter template
 * Author:          Growth Optimizer
 * Author URI:      https://growthoptimizer.com/
 * Text Domain:     go-kit
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         growthoptimizer-starter-template
 */
namespace Elementor\TemplateLibrary;

# Avoid direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

# Making sure the elementor plugin is installed first
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

# Constant variables
define('GROWTH_OPTIMIZER_TITLE', 'GO Starter Kit');
define('GROWTH_OPTIMIZER_SLUG', 'go-starter-kit');
define('GROWTH_OPTIMIZER_CLOUD_API', 'https://templates.1dea.ca/wp-json/%s/v2');
define('GROWTH_OPTIMIZER_DIR', plugin_dir_path( __FILE__ ));
define('GROWTH_OPTIMIZER_URL', plugin_dir_url( __FILE__ ));
define('GROWTH_OPTIMIZER_ADMIN_PAGE', 'admin.php?page='.GROWTH_OPTIMIZER_SLUG);
define('GROWTH_OPTIMIZER_API_TOKEN_OPTION_KEY', 'go_api_token');
define('GROWTH_OPTIMIZER_GLOBAL_SETTINGS_OPTION_KEY', 'go_global_settings');
define('GROWTH_OPTIMIZER_PLUGIN_INSTALLED_KEY', 'go_plugin_installed');

# Load elementor
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_elementor.php');
# Admin class
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_admin.php');
# Editor class
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_editor.php');

class Growth_Optimizer_Template_Kit extends GO_Elementor
{
    # Plugin title
    public $plugin_title;

    # Api URL
    public $cloud_api;

    # Plugin URL
    public $plugin_url;

    # Plugin directory
    public $plugin_directory;

    # API Token option key
    public $api_token_option_key;

    # Global settings option key
    public $global_settings_option_key;

    # Plugin installed option key
    public $plugin_installed_option_key;

    # Selected plugin
    public $selected_plugin;

    # Slug
    public $slug;

    /**
     * Prepare needed system variables
     * @param string $title
     * @param string $slug
     * @param string $_cloud_api
     * @param string $_plugin_url
     * @param string $_plugin_dir
     * @param string $api_token
     * @param string $global_settings
     * @param string $plugin_intalled
     */
    function __construct( $title, $slug, $_cloud_api, $_plugin_url, $_plugin_dir, $api_token, $global_settings, $plugin_intalled )
    {
        $this->plugin_title                = $title;
        $this->slug                        = $slug;
        $this->cloud_api                   = $_cloud_api;
        $this->plugin_url                  = $_plugin_url;
        $this->plugin_directory            = $_plugin_dir;
        $this->api_token_option_key        = $api_token;
        $this->global_settings_option_key  = $global_settings;
        $this->plugin_installed_option_key = $plugin_intalled;
    }

    /**
     * Start initializing hooks
     * to start the template kit
     * @return void
     */
    public function actions()
    {       

        # Ajax request to import selected template
        add_action(
            'wp_ajax_growth_optimizer_template_kit',
            [$this, 'ajax_import_template']
        );

        # Ajax to get cloud templates
        add_action(
            'wp_ajax_growth_optimizer_load_templates',
            [$this, 'ajax_cloud_templates']
        );

        # Ajax request to import global settings
        add_action(
            'wp_ajax_growth_optimizer_import_global_settings',
            [$this, 'import_global_settings']
        );

        # Ajax for api token access
        add_action(
            'wp_ajax_growth_optimizer_activate_api_token',
            [$this, 'api_tokens_activate']
        );
        
        # Ajax install plugin
        add_action(
            'wp_ajax_growth_optimizer_install_plugin',
            [$this, 'install_plugin']
        );

        # Ajax import loop items
        add_action(
            'wp_ajax_growth_optimizer_import_loop_items',
            [$this, 'import_loop_items']
        );

        # Ajax import ACF items
        add_action(
            'wp_ajax_growth_optimizer_import_acf_items',
            [$this, 'import_acf_items']
        );

        # Ajax import Gravity forms
        add_action(
            'wp_ajax_growth_optimizer_import_gform_items',
            [$this, 'import_gform_items']
        );

        # Unauthorize
        add_action(
            'kit-unauthorize',
            [$this, 'unauthorize'],
            10,
            1
        );        
        
    }



    /**
     * Import custom fonts 
     * from cloud server
     * @return void
     */
    public function import_custom_fonts()
    {
        $custom_fonts = $this->cloud_server('custom-fonts');
        if ($custom_fonts && !$this->is_not_authorize( $custom_fonts )) {
            foreach ($custom_fonts as $font) {
                if ($this->is_slug_exists($font['slug'])) continue;

                $ID = wp_insert_post([
                    'post_title'  => $font['title'],
                    'post_name'   => $font['slug'],
                    'post_type'   => 'elementor_font',
                    'post_status' => 'publish'
                ]);
                if (!is_wp_error( $ID )) {
                    update_post_meta($ID, 'elementor_font_files', $font['font_files']);
                    update_post_meta($ID, 'elementor_font_face', $font['font_face']);
                    update_post_meta($ID, '_edit_last', $font['edit_last']);
                    update_post_meta($ID, '_edit_lock', $font['edit_lock']);
                }
            }
        }
    }


    /**
     * Import global settings from 
     * the cloud settings to inherit
     * templates styling
     * @return bool
     */
    public function import_global_settings( $data = [] )
    {
        $data = $this->cloud_server('global-settings');
          
        if( !$data ) {
            $success = false;
        } else {           
            

            if ( $this->is_not_authorize( $data ) ) {
                $success = false;
            } else {                
                
                $global_settings = $data[0];
                $global_settings['site_name'] = get_bloginfo( 'name' );
                $elementor_active_kit = (int)get_option('elementor_active_kit');
                update_post_meta( $elementor_active_kit, '_elementor_page_settings', $global_settings );

                update_option(
                    $this->global_settings_option_key,
                    'installed',
                    true
                );

                $success = true;   
                
                # Import custom fonts
                $this->import_custom_fonts();
            }
        }   
        
        wp_send_json([
            'success' => $success,
            'data'    => $elementor_active_kit
        ]);
        wp_die();
    }


    /**
     * Get site domain
     * @return mixed|string
     */
    public function get_domain()
    {
        $domain = parse_url(home_url());
        return $domain['host'];
    }


    /**
     * Activate API token
     * @return void
     */
    public function api_tokens_activate()
    {
        
        $success   = false;
        $api_token = $_POST['token'];
        $domain    = $this->get_domain();

        $request = $this->cloud_server(
            'activate-token',
            [
                'referrer' => $domain,
                'token'    => $api_token
            ]
        );         

        if(!$request) {
            $success = false;           
        } else {
            $success = $request;
        }    

        if ($success) {
            update_option(
                $this->api_token_option_key,
                $api_token,
                true
            );
            update_option(
                $this->global_settings_option_key,
                '',
                true
            );
        }

        wp_send_json([
            'success' => $success
        ]);            
        wp_die();
    }


    /**
     * Get published templates
     * from the cloud server
     * @return void
     */
    public function ajax_cloud_templates()
    {
        ob_start();
        $category = $_POST['category'];
        $keyword = $_POST['keyword'];

        $templates = $this->get_templates();   
        
        if ( $this->is_not_authorize( $templates ) ) {            
            do_action('kit-unauthorize', $this->get_domain());
        } else {        
            do_action('kit-templates', $templates, $category, $keyword);
        }

        wp_send_json([
            'templates' => ob_get_clean()
        ]);
        wp_die();
    }

    /**
	 * Update post meta.
	 *
	 * @since 2.0.0
	 * @param  integer $post_id Post ID.
	 * @param  array   $data Elementor Data.
	 * @return array   $data Elementor Imported Data.
	 */
    public function import($post_id = 0, $data = array())
    {
        if ( ! empty( $post_id ) && ! empty( $data ) ) {
			return $this->translate_elementor_data($data);
		}
		return array();
    }

    /**
     * Import selected template
     * and make it viewable on the
     * elementor page editor
     * @return void
     */
    public function ajax_import_template()
    {        

        $templates = $this->get_templates();
        $post_id   = $_POST['post_id'];
        $data      = $templates[ (int)$_POST['template'] ]['data'];

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'go-kit' ) );
        }               
        
        $import_data = $this->import( $post_id, $data );
        
        wp_send_json([
            'data' => $import_data
        ]);
        wp_die();
    }


    /**
     * Get site api key token
     */
    public function get_api_token()
    {
        return get_option( $this->api_token_option_key, '' );
    }


    /**
     * Cloud server
     * @param string $action
     * @param array $authorization
     */
    public function cloud_server( $action, $authorization = [] )
    {
        $request = wp_remote_get(
            sprintf($this->cloud_api, $action),
            [
                'headers' => !empty($authorization) ? $authorization : $this->get_authorization()
            ]
        );
        if( is_wp_error( $request ) ) {
            return false;
        }               
        return json_decode( wp_remote_retrieve_body( $request ), true );
    }


    /**
     * Check if site api token is 
     * still active or authorize
     */
    public function is_active()
    {
        return $this->cloud_server('is-active');
    }


    /**
     * Get requried plugins
     */
    public function get_required_plugins()
    {
        return $this->cloud_server('required-plugins');
    }


    /**
     * Activate plugin
     * @param string $plugin
     * @param string $license_key
     * @return null
     */
    public function activate_plugin( $plugin, $license_key )
    {
        $plugin  = trim( $plugin );
        $current = get_option( 'active_plugins' );
        $plugin  = plugin_basename( $plugin );

        if ( !in_array( $plugin, $current ) ) {
            $current[] = $plugin;
            sort( $current );
            do_action( 'activate_plugin', $plugin );
            update_option( 'active_plugins', $current );
            do_action( 'activate_' . $plugin );
            do_action( 'activated_plugin', $plugin );

            # Let's activate the license data
            $this->apply_activation_keys(
                $this->selected_plugin,
                $license_key
            );
        }

        return null;        
    }


    /**
     * Import loop items
     * @return void
     */
    public function import_loop_items()
    {
        $loop_items    = $this->cloud_server('loop-items');
        $selected_item = (int)$_POST['id'];
        $item          = $loop_items[$selected_item];
        $success       = true;

        $ID = wp_insert_post([
            'post_title'   => $item['title'],
            'post_name'    => $item['slug'],
            'post_content' => $item['content'],
            'post_type'    => 'elementor_library',
            'post_status'  => 'publish'
        ]);
        if (!is_wp_error( $ID )) {
            update_post_meta($ID, '_elementor_edit_mode', $item['postmeta']['_elementor_edit_mode']);
            update_post_meta($ID, '_elementor_template_type', $item['postmeta']['_elementor_template_type']);
            update_post_meta($ID, '_elementor_version', $item['postmeta']['_elementor_version']);
            update_post_meta($ID, '_elementor_pro_version', $item['postmeta']['_elementor_pro_version']);
            update_post_meta($ID, '_edit_lock', $item['postmeta']['_edit_lock']);
            update_post_meta($ID, '_wp_page_template', $item['postmeta']['_wp_page_template']);
            update_post_meta($ID, '_elementor_page_settings', $item['postmeta']['_elementor_page_settings']);            
            update_post_meta($ID, '_elementor_page_assets', $item['postmeta']['_elementor_page_assets']);
            update_post_meta($ID, '_elementor_controls_usage', $item['postmeta']['_elementor_controls_usage']);
            update_post_meta($ID, '_elementor_css', $item['postmeta']['_elementor_css']);
            update_post_meta($ID, '_elementor_screenshot', $item['postmeta']['_elementor_screenshot']);
            update_post_meta($ID, '_edit_last', $item['postmeta']['_edit_last']);
            update_post_meta($ID, '_thumbnail_id', $item['postmeta']['_thumbnail_id']);

            
            $elementor_data = $this->translate_elementor_data($item['postmeta']['_elementor_data']);
            update_post_meta($ID, '_elementor_data', $elementor_data);
			

            $message = 'Loop item ' . $_POST['name'] . ' imported.';
        } else {
            $message = 'Loop item ' . $_POST['name'] . ' import failed.';
            $success = false;
        }

        wp_send_json([
            'message' => $message,
            'success' => $success
        ]);
        wp_die();
    }


    /**
     * Let's activate the license key
     * @param string $plugin
     * @param string | array $license_key
     * @return void
     */
    public function apply_activation_keys($plugin, $license_key)
    {
        if (empty($license_key)) return;

        # Gravity forms
        if ('gravityforms' == $plugin && class_exists( 'GFForms' )) {
            \RGFormsModel::save_key( $license_key );
            \GFCommon::cache_remote_message();
            \GFCommon::get_version_info( false );  
        }

        # Ultimate add-ons for elementor pro
        if ('ultimate-elementor' == $plugin) {
            if (!defined('BSF_UPDATER_PATH')) {
                define('BSF_UPDATER_PATH', WP_PLUGIN_DIR  . '/ultimate-elementor/admin/bsf-core');
            }
            require_once(BSF_UPDATER_PATH  . '/parts/helpers.php');
            require_once(BSF_UPDATER_PATH  . '/class-bsf-license-manager.php');            
            $ultimate_addons = new \BSF_License_Manager();
            $ultimate_addons->bsf_process_license_activation(
                [
                    'license_key' => $license_key,
                    'product_id' => 'uael'
                ]
            );
        }

        # Advance Custom Fields Pro
        if ('advanced-custom-fields-pro' == $plugin) {            
            update_option('acf_pro_license', $license_key['key']);
            update_option('acf_pro_license_status', $license_key['status']);
        }
    }


    /**
     * Import ACF items
     * @return void
     */
    public function import_acf_items()
    {
        $acf_items     = $this->cloud_server('acf');
        $selected_item = (int)$_POST['id'];
        $item          = $acf_items[$selected_item];
        $success       = true;

        $ID = wp_insert_post([
            'post_title'     => $item['post_title'],
            'post_name'      => $item['post_name'],
            'post_content'   => $item['post_content'],
            'post_type'      => $item['post_type'],
            'post_excerpt'   => $item['post_excerpt'],
            'comment_status' => $item['comment_status'],
            'ping_status'    => $item['ping_status'],            
            'post_status'    => 'publish'
        ]);
        if (!is_wp_error( $ID )) {
            update_post_meta($ID, '_edit_last', $item['postmeta']['_edit_last']);
            update_post_meta($ID, '_edit_lock', $item['postmeta']['_edit_lock']);

            # Get ACF fields assigned
            foreach ($item['fields'] as $field) {
                wp_insert_post([
                    'post_title'     => $field['post_title'],
                    'post_name'      => $field['post_name'],
                    'post_content'   => $field['post_content'],
                    'post_type'      => $field['post_type'],
                    'post_excerpt'   => $field['post_excerpt'],
                    'comment_status' => $field['comment_status'],
                    'ping_status'    => $field['ping_status'],                    
                    'post_parent'    => $ID,
                    'post_status'    => 'publish'
                ]);
            }   


            $message = 'ACF item ' . $_POST['name'] . ' imported.';
        } else {
            $message = 'ACF item ' . $_POST['name'] . ' import failed.';
            $success = false;
        }

        wp_send_json([
            'message' => $message,
            'success' => $success
        ]);
        wp_die();
    }


    /**
     * Import Gravity forms
     * @return void
     */
    public function import_gform_items()
    {
        global $wpdb;

        $gform_items   = $this->cloud_server('gforms');
        $selected_item = (int)$_POST['id'];
        $item          = $gform_items[$selected_item];
        $success       = true;
        $message       = '';

        # Insert first the form
        $wpdb->insert(
            $wpdb->prefix.'gf_form',
            [
                'title'        => $item['title'],
                'date_created' => current_time('mysql', 1),
                'date_updated' => current_time('mysql', 1),
                'is_active'    => $item['is_active'],
                'is_trash'     => $item['is_trash']
            ]
        );

        # Get form id
        $form_id = $wpdb->insert_id;

        # Next insert form meta
        $form_meta = $item['meta'];
        if (!empty($form_meta)) {
            foreach ($form_meta as $meta) {
                $wpdb->insert(
                    $wpdb->prefix.'gf_form_meta',
                    [
                        'form_id'           => $form_id,
                        'display_meta'      => !empty($meta['display_meta']) ? json_encode($meta['display_meta']) : '',
                        'entries_grid_meta' => !empty($meta['entries_grid_meta']) ? json_encode($meta['entries_grid_meta']) : '',
                        'confirmations'     => !empty($meta['confirmations']) ? json_encode($meta['confirmations']) : '',
                        'notifications'     => !empty($meta['notifications']) ? json_encode($meta['notifications']) : ''
                    ]
                );
            }
        }

        # Next insert form revisions
        $form_revisions = $item['revisions'];
        if (!empty($form_revisions)) {
            foreach ($form_revisions as $revisions) {
                $wpdb->insert(
                    $wpdb->prefix.'gf_form_revisions',
                    [
                        'form_id'      => $form_id,
                        'display_meta' => !empty($revisions['display_revisions']) ? json_encode($revisions['display_revisions']) : ''
                    ]
                );
            }
        }

        wp_send_json([
            'message' => $message,
            'success' => $success,
            'meta'    => $form_meta
        ]);
        wp_die();
    }


    /**
     * Install selected plugins
     * @return void
     */
    public function install_plugin()
    {
        WP_Filesystem();
        
        $success          = false;
        $message          = [];
        $destination_path = plugin_dir_path(__DIR__);

        # Set selected plugin
        $this->selected_plugin = $_POST['plugin'];

        # Get plugin details from the cloud server
        $plugin = $this->cloud_server('required-plugins');

        # If cloud server has the plugin, then let's install
        if (!empty($plugin)) {

            $success = true;
            $file    = $plugin['file'];
            $source  = stripslashes($plugin['url']);
            $name    = $plugin['name'];
                    
        
            $zip_name = end(explode('/', $source));
            $file_target = $destination_path.$zip_name;

            # Download zip file
            file_put_contents(
                $file_target, 
                fopen($source, 'r')
            );


            # Create a new ZipArchive instance
            $zip = new \ZipArchive();

            # Open the zip file
            if ($zip->open($file_target) === TRUE) {
                # Extract the contents to the specified folder
                if ($zip->extractTo($destination_path.'/'.$this->selected_plugin)) {
                    # Activate plugin
                    $this->activate_plugin($file, $plugin['license_key']);

                    # Success message
                    $message[] = "Plugin {$name} installed.";

                    # Plugin this plugin as installed
                    update_option(
                        $this->plugin_installed_option_key,
                        'installed',
                        true
                    );
                } else {
                    # Failed message
                    $message[] = "Plugin {$name} failed to install.";

                    # Plug as failed
                    $success = false;
                }

                # Close the zip file
                $zip->close();

            } else {

                # Failed message
                $message[] = "Plugin {$name} failed to install.";

                # Plug as failed
                $success = false;
            }
            

            # Remove downloaded file
            unlink($file_target);           
            
        } else {
            $message = 'The requested plugin to install was not found in the cloud server.';
        }

        wp_send_json([
            'message' => $message,
            'success' => $success,
            'plugin'  => $plugin,
            'target'  => $file_target
        ]);
        wp_die();
    }


    /**
     * Check post slug exists
     * @param string $slug
     * @return bool
     */
    public function is_slug_exists($slug)
    {
        global $wpdb;
        $find_post = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->prefix}posts WHERE post_name=%s",
                $slug
            )
        );
        if($find_post) {
            return $find_post->ID;
        }
        return false;
    }
    

    /**
     * Check form if exists
     * @param string $form
     * @return bool
     */
    public function is_form_exists($form)
    {
        global $wpdb;
        $find_form = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT title FROM {$wpdb->prefix}gf_form WHERE title=%s",
                trim($form)
            )
        );
        if($find_form) {
            return $find_form->title;
        }
        return false;
    }


    /**
     * Unauthorize message to display 
     * if site is not subscribe to the 
     * cloud server
     * @return void
     */
    public function unauthorize( $domain )
    {
        $admin_page = admin_url(GROWTH_OPTIMIZER_ADMIN_PAGE);
        include $this->plugin_directory . '/parts/editor_unauthorize.php';
    }

    /**
     * Check if this site subscribe to the
     * cloud server
     * @param mixed $response
     * @return bool
     */
    public function is_not_authorize( $response )
    {
        return is_array($response) && array_key_exists('code', $response) && $response['code'] == 401;
    }


    /**
     * Get authorization access
     * @return array{referrer: mixed, token: string}
     */
    public function get_authorization()
    {
        return [
            'referrer' => $this->get_domain(),
            'token'    => $this->get_api_token(),
            'plugin'   => $this->selected_plugin
        ];
    }


    /**
     * Request available templates
     * from the cloud server
     */
    public function get_templates()
    {
        return $this->cloud_server('template-kit');
    }


    /**
     * Request template categories
     * from the cloud server
     */
    public function get_template_categories()
    {
        return $this->cloud_server('template-categories');
    }

}

# When plugin loaded, start the starter kit
add_action('plugins_loaded', function() {
    # Start the template kit
    $go_starter_template_kit = new Growth_Optimizer_Template_Kit(
        GROWTH_OPTIMIZER_TITLE,
        GROWTH_OPTIMIZER_SLUG,
        GROWTH_OPTIMIZER_CLOUD_API,
        GROWTH_OPTIMIZER_URL,
        GROWTH_OPTIMIZER_DIR,
        GROWTH_OPTIMIZER_API_TOKEN_OPTION_KEY,
        GROWTH_OPTIMIZER_GLOBAL_SETTINGS_OPTION_KEY,
        GROWTH_OPTIMIZER_PLUGIN_INSTALLED_KEY
    );
    # Start the system
    $go_starter_template_kit->actions();

    # Start admin
    new GO_Admin($go_starter_template_kit);

    # Start elementor editor
    new GO_Editor($go_starter_template_kit);
});

# Import global settings on activate plugin
add_action( 'activated_plugin', function($plugin) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( GROWTH_OPTIMIZER_ADMIN_PAGE ) ) );
    }
} );