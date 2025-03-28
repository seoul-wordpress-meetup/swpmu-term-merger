<?php

namespace SWPMU\TermMerger\Tests\Supports;

use SWPMU\TermMerger\Supports\TermMerger;
use WP_UnitTestCase;

class TestTermMerger extends WP_UnitTestCase
{
    public function test_merge(): void
    {
        // Create test posts.
        $p0 = $this->factory()->post->create_and_get();
        $p1 = $this->factory()->post->create_and_get();
        $p2 = $this->factory()->post->create_and_get();

        // Create test terms.
        $t10 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T10']); // Pivot
        $t11 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T11']);
        $t12 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T12']);
        $t20 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T20']); // Pivot
        $t21 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T21']);
        $t30 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T30']);

        // Create some relationships.
        wp_set_object_terms($p0->ID, [$t10->term_id, $t11->term_id, $t12->term_id], 'post_tag');
        wp_set_object_terms($p1->ID, [$t11->term_id, $t20->term_id, $t21->term_id], 'post_tag');
        wp_set_object_terms($p2->ID, [$t10->term_id, $t11->term_id, $t12->term_id, $t20->term_id, $t21->term_id, $t30->term_id], 'post_tag');

        /**
         * Before:
         * -------
         * T10 ----+---- P0, P2
         * T11 ----+---- P0, P1, P2
         * T12 ----+---- P0, P2
         * T20 ----+---- P1, P2
         * T21 ----+---- P1, P2
         * T30 ----+---- P2
         *
         * After
         * -----
         *  T10 ----+---- P0, P1, P2
         *  T20 ----+---- P1, P2
         *  T30 ----+---- P2
         */

        $m = new TermMerger();
        $m->merge($t10, [$t10, $t11, $t10 /* intentional duplication */, $t12]);
        $m->merge($t20, [$t20, $t21]);

        global $wpdb;

        // Check if term T11, T12, and T21 do not exist.
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->terms WHERE name IN (%d, %d, %d)",
            $t11->term_id,
            $t12->term_id,
            $t21->term_id,
        );
        $termCnt = (int)$wpdb->get_var($query);
        $this->assertEquals(0, $termCnt);

        // Check if term_taxonomy T11, T12, and T21 do not exist.
        $query  = $wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN (%d, %d, %d)",
            $t11->term_taxonomy_id,
            $t12->term_taxonomy_id,
            $t21->term_taxonomy_id,
        );
        $taxCnt = (int)$wpdb->get_var($query);
        $this->assertEquals(0, $taxCnt);

        // Check if term_relationships of T11, T12, T21 does exist.
        $query  = $wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (%d, %d, %d)",
            $t11->term_taxonomy_id,
            $t12->term_taxonomy_id,
            $t21->term_taxonomy_id,
        );
        $relCnt = (int)$wpdb->get_var($query);
        $this->assertEquals(0, $relCnt);

        // Check count of T1, T2, T3 in term_relationships.
        $query = $wpdb->prepare(
            "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id=%d ORDER BY object_id",
            $t10->term_taxonomy_id,
        );
        $objT1 = array_map(fn($v) => intval($v), $wpdb->get_col($query));
        $this->assertEquals([$p0->ID, $p1->ID, $p2->ID], $objT1);

        $query = $wpdb->prepare(
            "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id=%d ORDER BY object_id",
            $t20->term_taxonomy_id,
        );
        $objT2 = array_map(fn($v) => intval($v), $wpdb->get_col($query));
        $this->assertEquals([$p1->ID, $p2->ID], $objT2);

        $query = $wpdb->prepare(
            "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id=%d ORDER BY object_id",
            $t30->term_taxonomy_id,
        );
        $objT3 = array_map(fn($v) => intval($v), $wpdb->get_col($query));
        $this->assertEquals([$p2->ID], $objT3);
    }
}
