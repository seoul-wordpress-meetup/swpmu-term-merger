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
                                'action' => 'swpmuTmgr/getTerms',
                                'key'    => '_swpmu_tmgr_nonce',
                                'nonce'  => wp_create_nonce('swpmuTmgr/getTerms'),
                            ],
                            'mergeTerms' => [
                                'action' => 'swpmuTmgr/mergeTerms',
                                'key'    => '_swpmu_tmgr_nonce',
                                'nonce'  => wp_create_nonce('swpmuTmgr/mergeTerms'),
                            ],
                        ],
                        'endpoint'     => admin_url('admin-ajax.php'),
                        'initialState' => wp_get_environment_type() === 'production' ? [
                            'taxonomies' => swpmuTmgrGet(Taxonomy::class)->getTaxonomies(),
                        ] : [
                            'currentStep' => 'term-merge',
                            // 'groups'      => $this->getDevRandomGroups(),
                            'maxGroups'   => 3,
                            'selected'    => [],
                            'taxonomies'  => swpmuTmgrGet(Taxonomy::class)->getTaxonomies(),
                            'taxonomy'    => 'post_tag',
                        ],
                    ],
                )
            ;
        }
    }

    private function getDevRandomGroups(string $taxonomy = 'post_tag', int $size = 3, int $numGroups = 1): array
    {
        $query = new \WP_Term_Query(
            [
                'fields'     => 'ids',
                'hide_empty' => false,
                'number'     => $size * $numGroups,
                'taxonomy'   => $taxonomy,
            ],
        );

        $groups = [];
        $id     = 1;
        $terms  = $query->get_terms();

        for ($i = 0; $i < $numGroups; ++$i) {
            $offset = $i * $size;
            if ($offset >= count($terms)) {
                break;
            }
            $groups[] = [
                'id'    => $id,
                'terms' => array_slice($terms, $offset, $size),
                'title' => "Group #{$id}",
            ];
            ++$id;
        }

        return $groups;
    }
}
