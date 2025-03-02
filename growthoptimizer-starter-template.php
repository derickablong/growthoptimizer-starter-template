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
namespace GO_Toolkit;

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

# Helper class
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_helper.php');
# Admin class
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_admin.php');
# Editor class
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_editor.php');
# Elementor template data handler
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_elementor.php');
# Toolkit
require_once(GROWTH_OPTIMIZER_DIR . 'class/class_toolkit.php');



# When plugin loaded, start the starter kit
add_action('plugins_loaded', function() {
    # Start the template kit
    $go_starter_template_kit = new GO_Template_Kit(
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

    # Start elementor editor
    //new GO_Editor($go_starter_template_kit);
});

# Import global settings on activate plugin
add_action( 'activated_plugin', function($plugin) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( GROWTH_OPTIMIZER_ADMIN_PAGE ) ) );
    }
} );