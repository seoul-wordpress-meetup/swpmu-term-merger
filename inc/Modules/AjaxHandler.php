<?php

namespace SWPMU\TermMerger\Modules;

use SWPMU\TermMerger\Supports\Taxonomy;
use SWPMU\TermMerger\Supports\TermMerger;
use SWPMU\TermMerger\Vendor\Bojaghi\Continy\Continy;
use SWPMU\TermMerger\Vendor\Bojaghi\Contract\Module;

class AjaxHandler implements Module
{
    public function __construct(private Continy $continy)
    {
    }

    public function getTaxonomies(): void
    {
        check_ajax_referer('swpmuTmgr/getTaxonomies', '_swpmu_tmgr_nonce');

        $taxonomies = $this->continy->get(Taxonomy::class)->getTaxonomies();

        wp_send_json_success($taxonomies);
    }

    public function getTerms(): void
    {
        check_ajax_referer('swpmuTmgr/getTerms', '_swpmu_tmgr_nonce');

        $taxonomy = sanitize_key($_REQUEST['taxonomy'] ?? '');
        $terms    = $this->continy->get(Taxonomy::class)->getTerms($taxonomy);

        if (is_wp_error($terms)) {
            wp_send_json_error($terms);
        }

        wp_send_json_success($terms);
    }

    /**
     * Required parameters:
     *
     * - head:     Pivot term ID
     * - target:   Array of term ID. Head ID may be included or not.
     * - taxonomy: Hint to confirm that all term IDs are valid terms of the taxonomy.
     */
    public function mergeTerms(): void
    {
        check_ajax_referer('swpmuTmgr/mergeTerms', '_swpmu_tmgr_nonce');

        $error    = new \WP_Error();
        $head     = absint($_REQUEST['head'] ?? '0');
        $target   = array_filter(array_map('absint', (array)($_REQUEST['target'] ?? '0')), fn($v) => $v !== $head);
        $taxonomy = sanitize_key($_REQUEST['taxonomy'] ?? '');

        if (!taxonomy_exists($taxonomy)) {
            $error->add('error', 'Taxonomy does not exist');
        }

        $headTerm = get_term_by('term_id', $head, $taxonomy);
        if (is_wp_error($headTerm)) {
            $error->merge_from($headTerm);
        }

        $targetTerms = get_terms(
            [
                'hide_empty' => false,
                'include'    => $target,
                'exclude'    => [$head],
                'taxonomy'   => $taxonomy,
            ],
        );
        if (is_wp_error($targetTerms)) {
            $error->merge_from($targetTerms);
        }

        if ($error->has_errors()) {
            wp_send_json_error($error);
        }

        $this->continy->get(TermMerger::class)->merge($headTerm, $targetTerms);

        wp_send_json_success();
    }
}
