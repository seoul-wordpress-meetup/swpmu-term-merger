<?php
/**
 * ViteScript configuration
 */

if (!defined('ABSPATH')) {
    exit;
}

return [
    'distBaseUrl'  => plugin_dir_url(SWPMU_TERM_MERGER_MAIN) . 'dist',
    'isProd'       => 'production' === wp_get_environment_type(),
    'manifestPath' => plugin_dir_path(SWPMU_TERM_MERGER_MAIN) . 'dist/.vite/manifest.json',
];
