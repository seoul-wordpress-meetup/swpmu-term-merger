<?php

namespace SWPMU\TermMerger\Supports;

use SWPMU\TermMerger\Vendor\Bojaghi\Contract\Support;
use WP_Query;

final class Workspace implements Support
{
    public const POST_TYPE     = 'swpmu_workspace';
    public const META_KEY_DATA = '_swpmu_workspace';

    public function __construct()
    {
        if (!post_type_exists(self::POST_TYPE)) {
            register_post_type(self::POST_TYPE, self::getCptArgs());
        }
    }

    public static function getCptArgs(): array
    {
        return [
            'label'               => __('Workspaces', 'swpmu-term-merger'),
            'labels'              => [
                'name'          => _x('Workspaces', 'swpmu_workspace label', 'swpmu-term-merger'),
                'singular_name' => _x('Workspace', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'add_new'                  => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'add_new_item'             => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'edit_item'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'new_item'                 => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'view_item'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'view_items'               => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'search_items'             => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'not_found'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'not_found_in_trash'       => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'parent_item_colon'        => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'all_items'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'archives'                 => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'attributes'               => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'insert_into_item'         => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'uploaded_to_this_item'    => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'featured_image'           => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'set_featured_image'       => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'remove_featured_image'    => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'use_featured_image'       => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'menu_name'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'filter_items_list'        => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'filter_by_date'           => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'items_list_navigation'    => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'items_list'               => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_published'           => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_published_privately' => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_reverted_to_draft'   => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_trashed'             => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_scheduled'           => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_updated'             => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_link'                => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
                // 'item_link_description'    => _x('', 'swpmu_workspace label', 'swpmu-term-merger'),
            ],
            // 'description'                     => _x('', 'Description of swpmu_workspace', 'swpmu-term-merger'),
            'public'              => false,
            'hierarchical'        => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'show_in_rest'        => false,
            'menu_icon'           => null,
            'map_meta_cap'        => true,
            'supports'            => ['title', 'editor'],
            'has_archive'         => false,
            'query_var'           => false,
        ];
    }

    public function create(int $userId, string $name): int
    {
        $p = $this->fetch($userId, $name);

        if (!$p) {
            $p = wp_insert_post(
                [
                    'post_author' => $userId,
                    'post_type'   => self::POST_TYPE,
                    'post_status' => 'publish',
                    'post_title'  => $name,
                ],
            );
            if (is_wp_error($p)) {
                $p = 0;
            }
        }

        return $p;
    }

    public function delete(int $userId, string $name): void
    {
        $p = $this->fetch($userId, $name);

        if ($p) {
            wp_delete_post($p, true);
        }
    }

    public function rename(int $userId, string $oldName, string $newName): void
    {
        $p = $this->fetch($userId, $oldName);

        if ($p) {
            wp_update_post(
                [
                    'ID'         => $p,
                    'post_title' => $newName,
                ],
            );
        }
    }

    public function list(int $userId, string|array $args = ''): array
    {
        $args = wp_parse_args(
            $args,
            [
                'author'      => $userId,
                'post_type'   => self::POST_TYPE,
                'post_status' => 'publish',
                'orderby'     => 'ID',
                'order'       => 'ASC',
            ],
        );

        // Fixed, no matter what.
        $args['author']    = $userId;
        $args['post_type'] = self::POST_TYPE;

        $query    = new WP_Query($args);
        $total    = $query->found_posts;
        $ppp      = (int)$query->get('posts_per_page');
        $page     = (int)$query->get('paged', 0);
        $lastPage = (int)ceil((float)$total / (float)$ppp);

        return [
            'user_id'   => $userId,
            'total'     => $total,
            'page'      => $page,
            'last_page' => $lastPage,
            'items'     => array_map(fn($p) => $p->post_title, $query->posts)
        ];
    }

    public function get(int $userId, string $name): array|null
    {
        $p = $this->fetch($userId, $name);

        if (!$p) {
            return null;
        }

        return json_decode(get_post_meta($p, self::META_KEY_DATA, true)) ?: null;
    }

    public function update(int $userId, string $name, array $data): void
    {
        $p = $this->fetch($userId, $name);

        if (!$p) {
            $p = $this->create($userId, $name);
            if (!$p) {
                return;
            }
        }

        update_post_meta($p, self::META_KEY_DATA, wp_json_encode($data));
    }

    private function fetch(int $userId, string $name): int|null
    {
        $query = new WP_Query(
            [
                'author'         => $userId,
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'publish',
                'no_found_rows'  => true,
                'posts_per_page' => 1,
                'title'          => $name,
            ],
        );

        return $query->have_posts() ? $query->posts[0]->ID : null;
    }
}
