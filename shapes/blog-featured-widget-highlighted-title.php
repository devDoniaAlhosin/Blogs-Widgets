<?php
require_once __DIR__ . '/includes/shared-banner-vc.php';

function blog_featured_widget_highlighted_title_vc_map() {
    if (!function_exists('vc_map')) {
        return;
    }
    $params = blog_widgets_shared_banner_vc_array(
        __('Featured Blog Widget (Highlighted Title)', 'blog-widgets'),
        'blog_featured_widget_highlighted_title',
        __('Custom Widgets', 'blog-widgets'),
        __('A featured blog/news widget with a bold highlighted main title and large image.', 'blog-widgets')
    );
    // Add WPBakery Design Options
    $params['params'][] = array(
        'type' => 'css_editor',
        'heading' => __('CSS box', 'blog-widgets'),
        'param_name' => 'css',
        'group' => __('Design Options', 'blog-widgets'),
    );
    $params['params'][] = array(
        'type' => 'textfield',
        'heading' => __('Extra class name', 'blog-widgets'),
        'param_name' => 'el_class',
        'description' => __('Add a class name for custom styling.', 'blog-widgets'),
        'group' => __('Design Options', 'blog-widgets'),
    );
    vc_map($params);
}
add_action('vc_before_init', 'blog_featured_widget_highlighted_title_vc_map');

if (!function_exists('blog_featured_widget_highlighted_title_shortcode')) {
    function blog_featured_widget_highlighted_title_shortcode($atts) {
        blog_widgets_enqueue_shape_css('blog_featured_widget_highlighted_title');
        $atts = shortcode_atts([
            'post_id' => '',
            'short_title' => '',
            'category' => '',
            'random_from_category' => 'no',
            'tag' => '',
            'sticky_only' => 'no',
            'css' => '',
            'el_class' => '',
        ], $atts, 'blog_featured_widget_highlighted_title');
        if (!empty($atts['post_id'])) {
            $post_id = intval($atts['post_id']);
            if (!$post_id) {
                return '<div class="blog-widget blog-featured-widget-highlighted-title" style="padding:2rem;text-align:center;background:#eee;">No post ID provided.</div>';
            }
            $post = get_post($post_id);
            if (!$post || $post->post_status !== 'publish') {
                return '<div class="blog-widget blog-featured-widget-highlighted-title" style="padding:2rem;text-align:center;background:#eee;">Post not found.</div>';
            }
        } else {
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 1,
            );
            if ($atts['sticky_only'] === 'yes') {
                $args['post__in'] = get_option('sticky_posts');
                if (empty($args['post__in'])) {
                    return '<div class="blog-widget blog-featured-widget-highlighted-title" style="padding:2rem;text-align:center;background:#eee;">No sticky posts found.</div>';
                }
            } else {
                $args['post__not_in'] = get_option('sticky_posts');
            }
            if (!empty($atts['category'])) {
                $args['category_name'] = $atts['category'];
            }
            if (!empty($atts['tag'])) {
                $args['tag'] = $atts['tag'];
            }
            if ($atts['random_from_category'] === 'yes') {
                $args['orderby'] = 'rand';
            }
            $query = new WP_Query($args);
            if (!$query->have_posts()) {
                return '<div class="blog-widget blog-featured-widget-highlighted-title" style="padding:2rem;text-align:center;background:#eee;">No posts found matching your criteria.</div>';
            }
            $query->the_post();
            $post = get_post();
            wp_reset_postdata();
        }
        $title = get_the_title($post);
        $image = get_the_post_thumbnail_url($post, 'full');
        if (!$image) {
            $image = 'https://via.placeholder.com/600x400?text=No+Image';
        }
        $permalink = get_permalink($post);
        $categories = get_the_category($post->ID);
        $category_name = !empty($categories) ? $categories[0]->name : '';
        $category_link = !empty($categories) ? get_category_link($categories[0]->term_id) : '';

        // VC Design Options
        $css_class = vc_shortcode_custom_css_class($atts['css'], ' ');
        $el_class = !empty($atts['el_class']) ? ' ' . esc_attr($atts['el_class']) : '';
        $rtl = is_rtl() ? 'rtl' : '';

        $html = '<div class="blog-widget blog-featured-widget-highlighted-title ' . $rtl . $css_class . $el_class . '">';

        $html .= '<div class="news-category-container">';
        if ($category_name && $category_link) {
            $html .= '<div class="news-category-label"><a href="' . esc_url($category_link) . '" style="color:#fff;text-decoration:none;">' . esc_html($category_name) . '</a></div>';
        } elseif ($category_name) {
            $html .= '<div class="news-category-label">' . esc_html($category_name) . '</div>';
        }
        $html .= '</div>';

        $html .= '<div class="news-row-container">';
        $html .= '<div class="news-title-container">';
        if (!empty($atts['short_title'])) {
            $html .= '<h3 class="news-short-title">' . esc_html($atts['short_title']) . '</h3>';
        }
        $html .= '<h2 class="news-title highlighted-title"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h2>';
        $html .= '</div>';
        $html .= '<div class="news-image"><a href="' . esc_url($permalink) . '"><img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '" /></a></div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
add_shortcode('blog_featured_widget_highlighted_title', 'blog_featured_widget_highlighted_title_shortcode'); 