<?php
/**
 * Plugin Name: MD Icons into Block Editor
 * Description: Add MD icons, or icons from a custom icon set to rich text fields in the Gutenberg block editor. Original code by Joris van Montfort
 * Version: 1.1
 * Author: Gaurav Tiwari
 * Author URI: https://stage.gauravtiwari.org
 * Text Domain: gt-md-icons
 * Domain Path: languages
 *
 * @category Gutenberg
 * @author gauravtiwari
 * @version 1.1
 * @package MD rich text icons
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';