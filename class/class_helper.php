<?php

namespace GO_Toolkit;

trait Go_Helper
{    

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
        include $this->plugin_directory . "/parts/{$file}.php";
    }

    
    /**
     * CSS file url
     * 
     * @param string $file
     * @return string
     */
    public function css($file)
    {
        return $this->plugin_url . "assets/css/{$file}.css";        
    }


    /**
     * JS file url
     * 
     * @param string $file
     * @return string
     */
    public function script($file)
    {
        return $this->plugin_url . "assets/js/{$file}.js";        
    }

}