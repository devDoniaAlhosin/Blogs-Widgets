<?php
require_once __DIR__ . '/includes/shared-banner-vc.php';

function blog_widget_shape_1_vc_map() {
    if (!function_exists('vc_map')) {
        return;
    }
    vc_map(blog_widgets_shared_banner_vc_array(
        __('Banner Blog', 'blog-widgets'),
        'blog_widget_shape_1',
        __('Custom Widgets', 'blog-widgets'),
        __('Display a single blog post as a banner', 'blog-widgets')
    ));
}
add_action('vc_before_init', 'blog_widget_shape_1_vc_map');


if (!function_exists('blog_widget_shape_1_shortcode')) {
    function blog_widget_shape_1_shortcode($atts) {
        blog_widgets_enqueue_shape_css('blog_widget_shape_1');
        $atts = shortcode_atts([
            'post_id' => '',
            'short_title' => '',
            'category' => '',
            'random_from_category' => 'no',
            'tag' => '',
            'sticky_only' => 'no',
        ], $atts, 'blog_widget_shape_1');
        if (!empty($atts['post_id'])) {
            $post_id = intval($atts['post_id']);
            if (!$post_id) {
                return '<div class="blog-widget shape-1" style="padding:2rem;text-align:center;background:#eee;">No post ID provided.</div>';
            }
            $post = get_post($post_id);
            if (!$post || $post->post_status !== 'publish') {
                return '<div class="blog-widget shape-1" style="padding:2rem;text-align:center;background:#eee;">Post not found.</div>';
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
                    return '<div class="blog-widget shape-1" style="padding:2rem;text-align:center;background:#eee;">No sticky posts found.</div>';
                }
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
                return '<div class="blog-widget shape-1" style="padding:2rem;text-align:center;background:#eee;">No posts found matching your criteria.</div>';
            }
            
            $query->the_post();
            $post = get_post();
            wp_reset_postdata();
        }
        
        $title = get_the_title($post);
        $short_title = $atts['short_title'];
        $image = get_the_post_thumbnail_url($post, 'full');
        if (!$image) {
            $image = 'https://via.placeholder.com/1200x400?text=No+Image';
        }
        $html = '<div class="blog-widget shape-1">';
        $html .= '<div class="widget-image" style="background-image:url(' . esc_url($image) . ')">';
        $html .= '<div class="overlay">';
        if (!empty($short_title)) {
            $html .= '<h5 class="short-title">' . esc_html($short_title) . '</h5>';
        }
        $html .= '<h2><a href="' . esc_url(get_permalink($post)) . '">' . esc_html($title) . '</a></h2>';
      
        $html .= '</div></div></div>';
        return $html;
    }
}
add_shortcode('blog_widget_shape_1', 'blog_widget_shape_1_shortcode');

