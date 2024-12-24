<?php

namespace SWM\TermMerger\Supports;

use SWM\TermMerger\Vendor\Bojaghi\Contract\Support;
use WP_Term;

class TermMerger implements Support
{
    public function __construct()
    {
    }

    /**
     * Merge terms
     *
     * Change all taxonomy_id of $targets found in term_relationships to taxonomy_id of $pivot.
     * You can also remove all $targets terms if you set $remove to true.
     * Please note that using direct query to remove them, so that no actions and filters are called.
     *
     * @param WP_Term   $pivot
     * @param WP_Term[] $targets
     * @param bool      $remove
     *
     * @return void
     */
    public function merge(WP_Term $pivot, array $targets, bool $remove = true): void
    {
        global $wpdb;

        // Filter out non-WP_Term objects in $targets.
        // Make sure that $pivot is out of $targets.
        $targets = array_filter($targets, fn($t) => $t instanceof WP_Term && $t->term_id !== $pivot->term_id);

        // Bail out if $targets is empty.
        if (empty($targets)) {
            return;
        }

        // Placeholder for $targets. Re-use several times.
        $plh0  = implode(',', array_pad([], count($targets), '%d'));
        $tVals = array_map(fn($t) => $t->term_id, $targets);

        // Step 1: Fetch current targets in term_relationships.
        $query = $wpdb->prepare("SELECT object_id, term_order FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($plh0)", $tVals);
        $trs   = $wpdb->get_results($query, ARRAY_N);

        // Step 2: Delete targets in term_relationships.
        $query = $wpdb->prepare("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($plh0)", $tVals);
        $wpdb->query($query);

        // Step 3: Insert new relationships.
        if ($trs) {
            $values = [];
            foreach ($trs as [$oi, $to]) {
                $values[] = $oi;
                $values[] = $pivot->term_taxonomy_id;
                $values[] = $to;
            }

            $plh   = implode(',', array_pad([], count($trs), '(%d, %d, %d)'));
            $query = $wpdb->prepare("INSERT IGNORE INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES $plh", $values);
            $wpdb->query($query);
        }

        // Step 4 (optional): Remove $targets.
        if ($remove) {
            // term_taxonomy
            $query = $wpdb->prepare("DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ($plh0)", $tVals);
            $wpdb->query($query);

            // terms
            $query = $wpdb->prepare("DELETE FROM $wpdb->terms WHERE term_id IN ($plh0)", $tVals);
            $wpdb->query($query);
        }
    }
}
