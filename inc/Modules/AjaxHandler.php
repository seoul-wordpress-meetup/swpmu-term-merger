<?php

namespace SWM\TermMerger\Modules;

use SWM\TermMerger\Supports\Taxonomy;
use SWM\TermMerger\Supports\TermMerger;
use SWM\TermMerger\Vendor\Bojaghi\Continy\Continy;
use SWM\TermMerger\Vendor\Bojaghi\Continy\ContinyException;
use SWM\TermMerger\Vendor\Bojaghi\Contract\Module;

class AjaxHandler implements Module
{
    public function __construct(private Continy $continy)
    {
    }

    /**
     * @throws ContinyException
     */
    public function getTaxonomies(): void
    {
        $taxonomies = $this->continy->get(Taxonomy::class)->getTaxonomies();

        wp_send_json_success($taxonomies);
    }

    /**
     * @throws ContinyException
     */
    public function getTerms(): void
    {
        $taxonomy = $_REQUEST['taxonomy'] ?? '';
        $terms    = $this->continy->get(Taxonomy::class)->getTerms($taxonomy);

        if (is_wp_error($terms)) {
            wp_send_json_error($terms);
        }

        wp_send_json_success($terms);
    }

    /**
     * Required paramters:
     *
     * - head:     Pivot term ID
     * - target:   Array of term ID. Head ID may be included or not.
     * - taxonomy: Hint to confirm that all term IDs are valiid terms of the taxonomy.
     *
     * @throws ContinyException
     */
    public function mergeTerms(): void
    {
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
