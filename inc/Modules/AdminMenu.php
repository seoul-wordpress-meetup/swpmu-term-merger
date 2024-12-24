<?php

namespace SWM\TermMerger\Modules;

use SWM\TermMerger\Supports\Taxonomy;
use SWM\TermMerger\Vendor\Bojaghi\Contract\Module;
use SWM\TermMerger\Vendor\Bojaghi\ViteScripts\ViteScript;

/**
 * Module for adding administration menus
 */
class AdminMenu implements Module
{
    public function __construct()
    {
        add_submenu_page(
            'tools.php',
            _x('Term Merger', 'Menu label', 'swm-term-merger'),
            _x('Term Merger', 'Menu label', 'swm-term-merger'),
            'administrator',
            'swm-term-merger',
            [$this, 'outputTermMergerPage'],
        );
    }

    public function outputTermMergerPage(): void
    {
        echo '<h1 class="wp-heading-inline">' . _x('Term Merger', 'H1 title', 'swm-term-merger') . '</h1>' . PHP_EOL;
        echo '<hr class="wp-header-end"/>' . PHP_EOL;
        echo '<div class="wrap swm-term-merger" id="term-merger-root" data-vite-script-root="true"></div>' . PHP_EOL;

        $vite = swmTmgrGet(ViteScript::class);
        if ($vite) {
            /**
             * @see conf/admin-ajax.php
             */
            $vite
                ->add('swm-term-merger', 'src/term-merger.tsx')
                ->vars(
                    'swmTermMerger',
                    [
                        'actions'      => [
                            'getTerms'   => [
                                'action' => 'swmTmgr/getTerms',
                                'key'    => '_swm_tmgr_nonce',
                                'nonce'  => wp_create_nonce('swmTmgr/getTerms'),
                            ],
                            'mergeTerms' => [
                                'action' => 'swmTmgr/mergeTerms',
                                'key'    => '_swm_tmgr_nonce',
                                'nonce'  => wp_create_nonce('swmTmgr/mergeTerms'),
                            ],
                        ],
                        'endpoint'     => admin_url('admin-ajax.php'),
                        'initialState' => wp_get_environment_type() === 'production' ? [
                            'currentStep' => 'taxonomy-select',
                            'selected'    => [],
                            'taxonomies'  => swmTmgrGet(Taxonomy::class)->getTaxonomies(),
                            'taxonomy'    => '',
                        ] : [
                            'currentStep' => 'merge-complete',
                            'selected'    => [],
                            'taxonomies'  => swmTmgrGet(Taxonomy::class)->getTaxonomies(),
                            'taxonomy'    => '',
                        ],
                    ],
                )
            ;

            wp_set_script_translations(
                'swm-term-merger',
                'swm-term-merger',
                plugin_dir_path(SWM_TERM_MERGER_MAIN) . 'languages',
            );
        }
    }
}
