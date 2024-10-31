<?php
/*
Plugin Name: Resolution Toolkit
Plugin URI: https://wordpress.org/plugins/resolution-toolkit
Description: A specific plugin use in Resolution Lite Theme to help you display tweet widget.
Version: 1.0.3
Author: Kopatheme
Author URI: http://kopatheme.com
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The Resolution Toolkit plugin, Copyright 2015 Kopatheme.com
The Resolution Toolkit plugin is distributed under the terms of the GNU GPL

Requires at least: 4.1
Tested up to: 4.5.3
Text Domain: resolution-toolkit
Domain Path: /languages/
*/

add_action( 'after_setup_theme', array( 'Resolution_Toolkit','after_setup_theme' ), 20 );
add_action( 'plugins_loaded', array( 'Resolution_Toolkit','plugins_loaded' ) );

class Resolution_Toolkit {

  function __construct() {

    require_once 'inc/widgets/social/widget-flickr.php';
    require_once 'inc/widgets/social/widget-twitter.php';    
    require_once 'inc/meta-boxes/featured-content.php';  

    add_filter( 'user_contactmethods', array( $this, 'add_contactmethods' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );    
  }

  function enqueue_scripts(){
    
    if( Resolution_Toolkit::version_compare( '1.3' ) ) {      
      wp_enqueue_script( 'resolution-toolkit-jquery-flickrfeed', plugins_url( 'assets/js/jquery-flickrfeed.js', __FILE__ ), array(), null, true );
      wp_enqueue_script( 'resolution-toolkit-custom', plugins_url( 'assets/js/custom.js', __FILE__ ), array(), null, true );
    }

  }

  function add_contactmethods( $methods ) {
    $methods['rl_facebook']   = esc_html__('Facebook URL', 'resolution-toolkit');
    $methods['rl_twitter']    = esc_html__('Twitter URL', 'resolution-toolkit');
    $methods['rl_linkedin']   = esc_html__('Linkedin URL', 'resolution-toolkit');
    $methods['rl_gplus']      = esc_html__('Google Plus URL', 'resolution-toolkit');
    return $methods;
  }

  static function version_compare( $version, $operator = '>=' ) {    
    $result   = false;
    $rl_theme = wp_get_theme( 'resolution-lite' );

    if( $rl_theme->exists() ){    
      if ( version_compare( $rl_theme->get( 'Version' ), $version, $operator ) ) {
        $result = true;
      }
    }

    return $result;
  }

  static function after_setup_theme() {
    if (!class_exists('Kopa_Framework')) {
      return;
    } else
    new Resolution_Toolkit();
  }

  static function plugins_loaded() {
    load_plugin_textdomain( 'resolution-toolkit', false, dirname( plugin_basename(__FILE__) ) . '/languages/');
  }

}
