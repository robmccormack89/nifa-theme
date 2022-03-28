<?php
namespace Rmcc;

/*

  Fields: When ACF is available, do some custom acf stuff

  What Fields does: 

    1. Adds custom local json save/load points for ACF fields.
      
    2. Registers any backend options-pages

    3. Inits the Blocks class
    
  Fields assumes the use of:
  
    1. ACF plugin (PRO version installed as a plugin)

*/

class Fields {
  
  public function __construct() {
    global $theme_config;
    
    // local json points
    if($theme_config['acf_local_json']) add_filter('acf/settings/load_json', array($this, 'local_json_load_point'));
    if($theme_config['acf_local_json']) add_filter('acf/settings/save_json', array($this, 'local_json_save_point'));
    
    // add options pages
    if($theme_config['acf_options_page']) add_action('acf/init', array($this, 'register_options_pages'));
    
    // init Blocks
    if ($theme_config['acf_blocks'] && class_exists('Timber')) new Blocks;
    
  }
  
  public function local_json_load_point($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/inc/acf/data';
    return $paths;
  }
  public function local_json_save_point($path) {
    $path = get_stylesheet_directory() . '/inc/acf/data';
    return $path;
  }
  
  public function register_options_pages() {
    if(function_exists('acf_add_options_page')) {
      global $snippets;
      acf_add_options_page(array(
        'page_title' 	=> $snippets['options_page_title'],
        'menu_title'	=> $snippets['options_page_menu_title'],
        'menu_slug' 	=> 'theme-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
      ));
    };
  }

}