<?php
require_once __DIR__ . '/includes/shared-banner-vc.php';

function blog_widget_box_vc_map() {
    if (!function_exists('vc_map')) {
        return;
    }
    $params = blog_widgets_shared_banner_vc_array(
        __('Blog Box', 'blog-widgets'),
        'blog_widget_box',
        __('Custom Widgets', 'blog-widgets'),
        __('Display a blog post in a box/card with subtitle and image', 'blog-widgets')
    );
    
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
        'description' => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'blog-widgets'),
        'group' => __('Design Options', 'blog-widgets'),
    );
    
    vc_map($params);
}
add_action('vc_before_init', 'blog_widget_box_vc_map');

if (!function_exists('blog_widget_box_shortcode')) {
    function blog_widget_box_shortcode($atts) {
        blog_widgets_enqueue_shape_css('blog-widget-box');
        $atts = shortcode_atts([
            'post_id' => '',
            'short_title' => '',
            'category' => '',
            'random_from_category' => 'no',
            'tag' => '',
            'sticky_only' => 'no',
            'css' => '',
            'el_class' => '',
        ], $atts, 'blog_widget_box');
        // Fetch post logic (same as shape 1)
        if (!empty($atts['post_id'])) {
            $post_id = intval($atts['post_id']);
            if (!$post_id) {
                return '<div class="blog-widget blog-box" style="padding:2rem;text-align:center;background:#eee;">No post ID provided.</div>';
            }
            $post = get_post($post_id);
            if (!$post || $post->post_status !== 'publish') {
                return '<div class="blog-widget blog-box" style="padding:2rem;text-align:center;background:#eee;">Post not found.</div>';
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
                    return '<div class="blog-widget blog-box" style="padding:2rem;text-align:center;background:#eee;">No sticky posts found.</div>';
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
                return '<div class="blog-widget blog-box" style="padding:2rem;text-align:center;background:#eee;">No posts found matching your criteria.</div>';
            }
            $query->the_post();
            $post = get_post();
            wp_reset_postdata();
        }
        $title = get_the_title($post);
        $short_title = $atts['short_title'];
        $image = get_the_post_thumbnail_url($post, 'full');
        if (!$image) {
            $image = 'https://via.placeholder.com/600x400?text=No+Image';
        }
        $permalink = get_permalink($post);

        // Blog box markup with design options
        $css_class = vc_shortcode_custom_css_class($atts['css'], ' ');
        $el_class = !empty($atts['el_class']) ? ' ' . esc_attr($atts['el_class']) : '';
        
        $html = '<div class="blog-widget blog-widget-box' . $css_class . $el_class . '" style="position:relative;">';
        $html .= '<img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '" class="blog-box-image">';
        $html .= '<div class="blog-box-overlay">';
        if (!empty($short_title)) {
            $html .= '<div class="blog-box-subtitle">' . esc_html($short_title) . '</div>';
        }
        $html .= '<h2 class="blog-box-title"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h2>';
        $html .= '</div></div>';
        return $html;
    }
}
add_shortcode('blog_widget_box', 'blog_widget_box_shortcode'); 