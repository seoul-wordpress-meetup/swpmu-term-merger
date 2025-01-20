<?php

namespace SWPMU\TermMerger\Modules;

use SWPMU\TermMerger\Supports\Taxonomy;
use SWPMU\TermMerger\Vendor\Bojaghi\Contract\Module;
use SWPMU\TermMerger\Vendor\Bojaghi\ViteScripts\ViteScript;

/**
 * Module for adding administration menus
 */
class AdminMenu implements Module
{
    public function __construct()
    {
        add_submenu_page(
            'tools.php',
            _x('SWPMU Term Merger', 'Menu label', 'swpmu-term-merger'),
            _x('SWPMU Term Merger', 'Menu label', 'swpmu-term-merger'),
            'administrator',
            'swpmu-term-merger',
            [$this, 'outputTermMergerPage'],
        );
    }

    public function outputTermMergerPage(): void
    {
        echo '<h1 class="wp-heading-inline">' . esc_html_x('Term Merger', 'H1 title', 'swpmu-term-merger') . '</h1>' . PHP_EOL;
        echo '<hr class="wp-header-end"/>' . PHP_EOL;
        echo '<div class="wrap swpmu-term-merger" id="term-merger-root" data-vite-script-root="true"></div>' . PHP_EOL;

        $vite = swpmuTmgrGet(ViteScript::class);
        if ($vite) {
            /**
             * @see conf/admin-ajax.php
             */
            $vite
                ->add('swpmu-term-merger', 'src/term-merger.tsx')
                ->scriptTranslation('swpmu-term-merger')
                ->vars(
                    'swpmuTermMerger',
                    [
                        'actions'      => [
                            'getTerms'   => [
                                'action' => 'swpmu/getTerms',
                                'key'    => '_swpmu_nonce',
                                'nonce'  => wp_create_nonce('swpmu/getTerms'),
                            ],
                            'mergeTerms' => [
                                'action' => 'swpmu/mergeTerms',
                                'key'    => '_swpmu_nonce',
                                'nonce'  => wp_create_nonce('swpmu/mergeTerms'),
                            ],
                        ],
                        'endpoint'     => admin_url('admin-ajax.php'),
                        'initialState' => wp_get_environment_type() === 'production' ? [
                            'currentStep' => '',
                            'selected'    => [],
                            'taxonomies'  => swpmuTmgrGet(Taxonomy::class)->getTaxonomies(),
                            'taxonomy'    => '',
                        ] : [
                            'currentStep' => 'taxonomy-select',
                            'selected'    => [],
                            'taxonomies'  => swpmuTmgrGet(Taxonomy::class)->getTaxonomies(),
                            'taxonomy'    => '',
                        ],
                    ],
                )
            ;
        }
    }
}
