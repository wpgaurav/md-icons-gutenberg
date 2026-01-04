<?php
/**
 * MD Richtext Icons - Main initialization class
 *
 * @package MD_Richtext_Icons
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MD_Richtext_icons {
    /**
     * This plugin's instance.
     *
     * @var MD_Richtext_icons
     */
    private static $instance;

    /**
     * Cached icons array for performance.
     *
     * @var array|null
     */
    private static $cached_icons = null;

    /**
     * Plugin version for cache busting.
     *
     * @var string
     */
    const VERSION = '1.1.1';

    /**
     * Registers the plugin.
     */
    public static function register() {
        if ( null === self::$instance ) {
            self::$instance = new MD_Richtext_icons();
        }
    }

    /**
     * The Constructor.
     */
    private function __construct() {
        // Use block_editor_settings_all instead of deprecated block_editor_settings (deprecated since WP 5.8)
        add_filter( 'block_editor_settings_all', array( $this, 'block_editor_settings' ), 10, 2 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'load_assets' ) );
    }

    /**
     * Filters the settings to pass to the block editor.
     *
     * @param array                   $editor_settings Default editor settings.
     * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
     *
     * @return array Returns updated editor settings.
     */
    public function block_editor_settings( $editor_settings, $block_editor_context ) {
        if ( ! isset( $editor_settings['MD_Richtext_icons'] ) ) {
            $editor_settings['MD_Richtext_icons'] = array(
                'formats' => array(
                    'name'  => 'formats',
                    'label' => __( 'Formats', 'gt-md-icons' ),
                    'items' => array(
                        'icons' => array(
                            'name'  => 'icon',
                            'label' => __( 'Insert icon', 'gt-md-icons' ),
                            'value' => true,
                        ),
                    ),
                ),
            );
        }
        return $editor_settings;
    }

    /**
     * Get the file version for cache busting.
     *
     * @param string $file_path The file path.
     * @return string The version string.
     */
    private function get_file_version( $file_path ) {
        if ( file_exists( $file_path ) ) {
            $filemtime = filemtime( $file_path );
            if ( false !== $filemtime ) {
                return (string) $filemtime;
            }
        }
        return self::VERSION;
    }

    /**
     * Get icons from file with caching.
     *
     * @return array The icons array.
     */
    private function get_icons() {
        if ( null !== self::$cached_icons ) {
            return self::$cached_icons;
        }

        $icon_file = plugin_dir_path( __DIR__ ) . 'src/icons.json';
        $icon_file = apply_filters( 'MD_Richtext_icons_iconset_file', $icon_file );

        $icons = array();
        if ( file_exists( $icon_file ) && is_readable( $icon_file ) ) {
            $icon_data = file_get_contents( $icon_file );
            if ( false !== $icon_data ) {
                $decoded = json_decode( $icon_data, false );
                if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
                    $icons = $decoded;
                }
            }
        }

        $icons = apply_filters( 'MD_Richtext_icons_iconset', $icons );
        self::$cached_icons = $icons;

        return $icons;
    }

    /**
     * Enqueue Gutenberg block assets for the block editor.
     */
    public function load_assets() {
        $js_file = plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js';
        $css_file = plugin_dir_path( __DIR__ ) . 'dist/editor.css';

        // Register block editor script for backend.
        wp_enqueue_script(
            'gt-md-richtext-icons-js',
            plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
            array(
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-block-editor',
                'wp-compose',
                'wp-data',
                'wp-dom',
                'wp-rich-text',
            ),
            $this->get_file_version( $js_file ),
            true
        );

        // Register block editor styles for backend.
        wp_enqueue_style(
            'gt-md-richtext-icons-editor-css',
            plugins_url( 'dist/editor.css', dirname( __FILE__ ) ),
            array( 'wp-edit-blocks' ),
            $this->get_file_version( $css_file )
        );

        // Pass icon data to JavaScript.
        wp_localize_script(
            'gt-md-richtext-icons-js',
            'md_richtext_icon_settings',
            array(
                'iconset'    => $this->get_icons(),
                'base_class' => apply_filters( 'MD_Richtext_icons_base_class', 'icon' ),
            )
        );
    }
}

MD_Richtext_icons::register();