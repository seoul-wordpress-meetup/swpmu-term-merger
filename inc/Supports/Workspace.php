<?php

namespace SWM\TermMerger\Supports;

use SWM\TermMerger\Vendor\Bojaghi\Contract\Support;
use WP_Query;

final class Workspace implements Support
{
    public const POST_TYPE     = 'swm_tmgr_workspace';
    public const META_KEY_DATA = '_swm_tmgr_workspace';

    public function __construct()
    {
        if (!post_type_exists(self::POST_TYPE)) {
            register_post_type(self::POST_TYPE, self::getCptArgs());
        }
    }

    public static function getCptArgs(): array
    {
        return [
            'label'               => __('Workspaces', 'swm_tmgr'),
            'labels'              => [
                'name'          => _x('Workspaces', 'swm_tmgr_workspace label', 'swm_tmgr'),
                'singular_name' => _x('Workspace', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'add_new'                  => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'add_new_item'             => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'edit_item'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'new_item'                 => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'view_item'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'view_items'               => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'search_items'             => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'not_found'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'not_found_in_trash'       => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'parent_item_colon'        => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'all_items'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'archives'                 => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'attributes'               => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'insert_into_item'         => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'uploaded_to_this_item'    => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'featured_image'           => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'set_featured_image'       => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'remove_featured_image'    => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'use_featured_image'       => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'menu_name'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'filter_items_list'        => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'filter_by_date'           => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'items_list_navigation'    => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'items_list'               => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_published'           => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_published_privately' => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_reverted_to_draft'   => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_trashed'             => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_scheduled'           => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_updated'             => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_link'                => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
                // 'item_link_description'    => _x('', 'swm_tmgr_workspace label', 'swm_tmgr'),
            ],
            // 'description'                     => _x('', 'Description of swm_tmgr_workspace', 'swm_tmgr'),
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

        update_post_meta($p, self::META_KEY_DATA, json_encode($data));
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
