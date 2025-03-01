<?php

namespace Elementor\TemplateLibrary;

class Go_Helper
{
    # Parent instance
    public $parent;

    /**
     * Instance
     * 
     * @param object $parent
     * @return void
     */
    public function helper($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Load template
     * 
     * @param string $file
     * @param array $variables
     * @return void
     */
    public function template($file, $variables = array())
    {
        if (!empty($variables) && is_array($variables)) {
            extract($variables);
        }
        include $this->parent->plugin_directory . "/parts/{$file}.php";
    }

    
    /**
     * CSS file url
     * 
     * @param string $file
     * @return string
     */
    public function css($file)
    {
        return $this->parent->plugin_url . "assets/css/{$file}.css";        
    }


    /**
     * JS file url
     * 
     * @param string $file
     * @return string
     */
    public function script($file)
    {
        return $this->parent->plugin_url . "assets/js/{$file}.js";        
    }

}