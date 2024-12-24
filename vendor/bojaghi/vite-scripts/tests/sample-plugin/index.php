<?php
/**
 * Plugin Name: Sample Plugin (vite-scripts)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('VITE_SCRIPTS_DIR') && file_exists(VITE_SCRIPTS_DIR) && is_dir(VITE_SCRIPTS_DIR)) {
    require_once VITE_SCRIPTS_DIR . '/vendor/autoload.php';
} else {
    wp_die('Please set the constant <code>VITE_SCRIPTS_DIR</code>.');
}

if (!function_exists('vite_scripts_template_redirect')) {
    function vite_scripts_template_redirect(): void
    {
        get_header();
        ?>
        <div class="wrap">
            <h1>Vite Scripts</h1>
            <hr class="wp-header-end">
            <p>This is a sample plugin using Vite Scripts.</p>
            <div id="vite-script-1-root"></div>
            <div id="vite-script-2-root"></div>
            <pre><?php echo wp_get_environment_type(); ?></pre>
        </div>
        <?php
        $viteScripts = new \Bojaghi\ViteScripts\ViteScript(
            [
                'distBaseUrl'  => plugin_dir_url(__FILE__) . 'dist',
                'isProd'       => 'production' === wp_get_environment_type(),
                'manifestPath' => plugin_dir_path(__FILE__) . 'dist/.vite/manifest.json',
            ],
        );

        $viteScripts->add(
            handle: 'script-1',
            relPath: 'src/script-1.tsx',
        )->vars(
            varName: 'script1',
            varValue: [
                'id' => 'script1-component',
            ],
        );

        $viteScripts->add(
            handle: 'script-2',
            relPath: 'src/script-2.tsx',
        )->vars(
            varName: 'script2',
            varValue: [
                'id' => 'script2-component',
            ],
        );

        get_footer();
        exit;
    }

    add_action('template_redirect', 'vite_scripts_template_redirect', 1000);
}
