<?php
if (!function_exists('shared_side_featured_logic')) {
    /**
     * Returns [$sidebar_posts, $featured_post] for widgets with side/featured post logic.
     *
     * @param array $atts Shortcode attributes
     * @return array [$sidebar_posts, $featured_post]
     */
    function shared_side_featured_logic($atts) {
        $sidebar_ids = [];
        if (!empty($atts['post_ids_sidebar'])) {
            if (is_array($atts['post_ids_sidebar'])) {
                $sidebar_ids = array_map('intval', $atts['post_ids_sidebar']);
            } else {
                $sidebar_ids = array_filter(array_map('intval', explode(',', $atts['post_ids_sidebar'])));
            }
        }
        $featured_id = intval($atts['post_id_featured']);
        $category = isset($atts['category']) ? sanitize_text_field($atts['category']) : '';
        $tag = isset($atts['tag']) ? sanitize_text_field($atts['tag']) : '';
        $featured_from_sticky = isset($atts['featured_from_sticky']) ? $atts['featured_from_sticky'] : 'no';

        if ($category === '---' || empty($category)) {
            $category = '';
        }

        $sidebar_posts = [];
        $featured_post = null;

        // 1. Sidebar: If user selected posts, use those (ignore filters for selected posts)
        if (!empty($sidebar_ids)) {
            $sidebar_posts = get_posts([
                'post__in' => $sidebar_ids,
                'orderby' => 'post__in',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ]);
        }

        // 2. Featured: If user selected a featured post, use it if it is not in sidebar
        if ($featured_id) {
            $candidate = get_post($featured_id);
            if (
                $candidate && $candidate->post_status === 'publish'
                && (!in_array($candidate->ID, wp_list_pluck($sidebar_posts, 'ID')))
            ) {
                $featured_post = $candidate;
            }
        }

        // 3. If no sidebar posts selected, build sidebar from filters
        if (empty($sidebar_posts)) {
            $args = [
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ];
            if (!empty($category)) $args['category_name'] = $category;
            if (!empty($tag)) $args['tag'] = $tag;
            if ($featured_from_sticky === 'no') $args['post__not_in'] = get_option('sticky_posts');
            $all_posts = get_posts($args);
            $post_count = count($all_posts);
            if ($post_count > 1) {
                $featured_post = $featured_post ?: $all_posts[$post_count - 1];
                $sidebar_posts = array_slice($all_posts, 0, $post_count - 1);
            } elseif ($post_count === 1) {
                $featured_post = $featured_post ?: $all_posts[0];
                $sidebar_posts = [];
            } else {
                $featured_post = null;
                $sidebar_posts = [];
            }
        } else {
            if (!$featured_post) {
                $args = [
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'rand',
                    'post__not_in' => wp_list_pluck($sidebar_posts, 'ID'),
                ];
                if (!empty($category)) $args['category_name'] = $category;
                if (!empty($tag)) $args['tag'] = $tag;
                if ($featured_from_sticky === 'no') $args['post__not_in'] = array_merge((array)$args['post__not_in'], get_option('sticky_posts'));
                $featured_candidates = get_posts($args);
                $featured_post = !empty($featured_candidates) ? $featured_candidates[0] : null;
            }
        }

        // 4. Featured from sticky (if enabled and no featured yet)
        if ($featured_from_sticky === 'yes' && !$featured_post) {
            $sticky_posts = get_option('sticky_posts');
            if (!empty($sticky_posts)) {
                $sticky_args = [
                    'post__in' => $sticky_posts,
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'rand',
                ];
                if (!empty($category)) $sticky_args['category_name'] = $category;
                if (!empty($tag)) $sticky_args['tag'] = $tag;
                $sticky_result = get_posts($sticky_args);
                if (!empty($sticky_result)) {
                    $featured_post = $sticky_result[0];
                }
            }
        }
        // If sticky is not used or no sticky found, fallback to filtered posts (already handled above)

        $sidebar_posts = array_slice($sidebar_posts, 0, 5);
        return [$sidebar_posts, $featured_post];
    }
} 