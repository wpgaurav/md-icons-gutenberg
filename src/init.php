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
        add_action( 'init', array( $this, 'load_assets') );
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
                'gt-md-richhtext-icons-js', // Handle.
                plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
                array( 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
                null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
                true // Enqueue the script in the footer.
            );

            // Register block editor styles for backend.
            wp_enqueue_style(
                'gt-md-richhtext-icons-editor-css', // Handle.
                plugins_url( 'dist/editor.css', dirname( __FILE__ ) ), // Block editor CSS.
                array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
                filemtime( plugin_dir_path( __DIR__ ) . 'dist/editor.css' ) // Version: File modification time.
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
                'gt-md-richhtext-icons-js',
                'md_richtext_icon_settings', // Array containing dynamic data for a JS Global.
                [
                    'iconset' => $icons,
                    'base_class' => apply_filters('MD_Richtext_icons_base_class', 'icon')
                ]
            );
        }

        // Icon set CSS
        $fontCssFile = apply_filters('MD_Richtext_icons_css_file', $fontCssFile);

        if (!empty($fontCssFile)) {
            wp_enqueue_style(
                'gt-md-richhtext-icons-icon-font-css', // Handle.
                $fontCssFile, 
            );
        }
    }
}

MD_Richtext_icons::register();