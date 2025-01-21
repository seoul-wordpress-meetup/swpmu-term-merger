<?php

namespace SWPMU\TermMerger\Supports;

use SWPMU\TermMerger\Vendor\Bojaghi\Contract\Support;
use WP_Error;
use WP_Term;
use WP_Term_Query;

class Taxonomy implements Support
{
    /**
     * Fetch all taxonomies
     *
     * @param bool $includeNonPublic
     *
     * @return array
     */
    public function getTaxonomies(bool $includeNonPublic = false): array
    {
        $taxonomies = get_taxonomies(
            $includeNonPublic ? ['hierarchical' => false] : ['hierarchical' => false, 'public' => true],
            'objects',
        );

        $mustExclude = ['post_format'];

        foreach ($mustExclude as $exclude) {
            if (isset($taxonomies[$exclude])) {
                unset($taxonomies[$exclude]);
            }
        }

        return array_map(fn($tax) => $tax->labels->singular_name, $taxonomies);
    }

    /**
     * Get terms by given taxonomy
     *
     * @param string       $taxonomy
     * @param string|array $args
     *
     * @return \WP_Term[]|WP_Error
     */
    public function getTerms(string $taxonomy, string|array $args = ''): array|WP_Error
    {
        if (!taxonomy_exists($taxonomy)) {
            return new WP_Error('error', _x('Taxonomy does not exist', 'error message', 'swpmu-term-merger'));
        }

        $args = wp_parse_args($args);
        // Fix taxonomy, and hide_empty.
        $args['taxonomy']   = $taxonomy;
        $args['hide_empty'] = false;

        return array_map(
            fn(WP_Term $t) => [
                'term_id'     => $t->term_id,
                'name'        => $t->name,
                'slug'        => $t->slug,
                'count'       => $t->count,
                'description' => $t->description,
            ],
            (new WP_Term_Query())->query($args),
        );
    }
}
