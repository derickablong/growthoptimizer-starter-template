<?php

namespace Elementor\TemplateLibrary;

use Elementor\Plugin;

abstract class GO_Elementor extends Source_Local
{
    # Required method to call
    abstract public function actions();

    /**
     * Translate elementor data into valid elementor data
     * 
     * @param string $elementor_data
     * @return array
     */
    public function translate_elementor_data( $elementor_data )
    {
        $data = json_decode( $elementor_data, true );
            
        Plugin::$instance->uploads_manager->set_elementor_upload_state( true );
        // Import the data.
        $data = $this->process_export_import_content( $data, 'on_import' );			

        Plugin::$instance->uploads_manager->set_elementor_upload_state( false );
        // !important, Clear the cache after images import.
        Plugin::$instance->posts_css_manager->clear_cache();

        return $data;
    }
}