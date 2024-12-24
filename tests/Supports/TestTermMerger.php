<?php

namespace SWM\TermMerger\Tests\Supports;

use SWM\TermMerger\Supports\TermMerger;
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
        $t1  = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T1']); // Pivot
        $t11 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T11']);
        $t12 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T12']);
        $t2  = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T2']); // Pivot
        $t21 = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T21']);
        $t3  = $this->factory()->term->create_and_get(['taxonomy' => 'post_tag', 'name' => 'T3']);

        // Create some relationships.
        wp_set_object_terms($p0->ID, [$t1->term_id, $t11->term_id, $t12->term_id], 'post_tag');
        wp_set_object_terms($p1->ID, [$t2->term_id, $t21->term_id], 'post_tag');
        wp_set_object_terms($p2->ID, [$t1->term_id, $t11->term_id, $t12->term_id, $t2->term_id, $t21->term_id, $t3->term_id], 'post_tag');

        /**
         * T1  ----+---- P0, P2
         * T11 ----+
         * T12 ----+
         *
         * T2  ----+---- P1, P2
         * T21 ----+
         *
         * T3  ----+---- P2
         */

        $m = new TermMerger();
        $m->merge($t1, [$t1, $t11, $t1, $t12]);
        $m->merge($t2, [$t2, $t21]);

        global $wpdb;

        // Check if term T11, T12, and T21 do not exist.
        $query   = $wpdb->prepare(
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

        // Check count of T1, T2 in term_relationships.
        $query = $wpdb->prepare(
            "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d",
            $t1->term_taxonomy_id,
        );
        $objT1 = array_map(fn($v) => intval($v), $wpdb->get_col($query));
        $this->assertEquals([$p0->ID, $p2->ID], $objT1);

        $query = $wpdb->prepare(
            "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d",
            $t2->term_taxonomy_id,
        );
        $objT2 = array_map(fn($v) => intval($v), $wpdb->get_col($query));
        $this->assertEquals([$p1->ID, $p2->ID], $objT2);
    }
}
