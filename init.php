<?php
class MD_Richtext_icons {
    /**
     * This plugin's instance.
     *
     * @var MD_Richtext_icons
     */
    private static $instance;

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
        add_filter( 'block_editor_settings', array( $this, 'block_editor_settings' ), 10, 2 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'load_assets' ) );
    }

    /**
     * Filters the settings to pass to the block editor.
     *
     * @param array  $editor_settings The editor settings.
     * @param object $post The post being edited.
     *
     * @return array Returns updated editors settings.
     */
    public function block_editor_settings( $editor_settings, $post ) {
        if ( ! isset( $editor_settings['MD_Richtext_icons'] ) ) {

            $editor_settings['MD_Richtext_icons'] = [
                'formats'    => array(
                    'name'  => 'formats',
                    'label' => __( 'Formats', 'block-options' ),
                    'items' => array(
                        'icons'        => array(
                            'name'  => 'icon',
                            'label' => __( 'Insert icon', 'gt-md-icons' ),
                            'value' => true,
                        )
                    )
                )
            ];

        }
        return $editor_settings;
    }


    /**
     * Enqueue Gutenberg block assets for both frontend + backend.
     */
    public function load_assets() {

        if (is_admin()) {
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
                    'wp-rich-text'
                ),
                filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ),
                true
            );

            // Register block editor styles for backend.
            wp_enqueue_style(
            'gt-md-richtext-icons-editor-css',
            plugins_url( 'dist/editor.css', dirname( __FILE__ ) ),
            array( 'wp-edit-blocks' ),
            filemtime( plugin_dir_path( __DIR__ ) . 'dist/editor.css' )
        );


            // WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
            $iconFile = plugin_dir_path( __DIR__ ).'src/icons.json';
            $iconFile = apply_filters('MD_Richtext_icons_iconset_file', $iconFile);

            $icons = [];
            if (file_exists($iconFile)) {
                $iconData = file_get_contents($iconFile);
                $icons = json_decode($iconData);

                $icons = apply_filters('MD_Richtext_icons_iconset', $icons);            
            }

            wp_localize_script(
                'gt-md-richtext-icons-js',
                'md_richtext_icon_settings',
                array(
                    'iconset'    => $icons,
                    'base_class' => apply_filters( 'MD_Richtext_icons_base_class', 'icon' ),
                )
            );
        }
    }
}

MD_Richtext_icons::register();