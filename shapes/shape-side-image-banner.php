<?php
require_once __DIR__ . '/includes/shared-banner-vc.php';
require_once __DIR__ . '/includes/shared-banner-render.php';

if (!function_exists('blog_widget_side_image_banner_vc_map')) {
    function blog_widget_side_image_banner_vc_map() {
        if (!function_exists('vc_map')) {
            return;
        }
        $params = blog_widgets_shared_banner_vc_array(
            __('Side Image Blog Banner', 'blog-widgets'),
            'blog_widget_side_image_banner',
            __('Custom Widgets', 'blog-widgets'),
            __('Display a blog post with text on the left and image on the right', 'blog-widgets')
        );
        // Add Design tab options
        $params['params'][] = array(
            'type' => 'dropdown',
            'heading' => __('Background Color', 'blog-widgets'),
            'param_name' => 'background',
            'value' => array(
                __('White', 'blog-widgets') => 'white',
                __('Dark', 'blog-widgets') => 'dark',
            ),
            'description' => __('Choose the background color for the banner.', 'blog-widgets'),
            'group' => __('Design', 'blog-widgets'),
            'std' => 'white'
        );
        $params['params'][] = array(
            'type' => 'dropdown',
            'heading' => __('Text Color', 'blog-widgets'),
            'param_name' => 'text_color',
            'value' => array(
                __('White', 'blog-widgets') => 'text-white',
                __('Blue', 'blog-widgets') => 'text-blue',
                __('Black', 'blog-widgets') => 'text-black',
            ),
            'description' => __('Choose the text color for the banner.', 'blog-widgets'),
            'group' => __('Design', 'blog-widgets'),
            'std' => ''
        );
        $params['params'][] = array(
            'type' => 'dropdown',
            'heading' => __('Show Subtitle', 'blog-widgets'),
            'param_name' => 'subtitle',
            'value' => array(
                __('Yes', 'blog-widgets') => 'yes',
                __('No', 'blog-widgets') => 'no',
            ),
            'description' => __('Display the subtitle above the main title.', 'blog-widgets'),
            'std' => 'yes'
        );
        vc_map($params);
    }
}
add_action('vc_before_init', 'blog_widget_side_image_banner_vc_map'); 

if (!function_exists('blog_widget_side_image_banner_shortcode')) {
    function blog_widget_side_image_banner_shortcode($atts) {
        blog_widgets_enqueue_shape_css('blog_widget_side_image_banner');
        $atts = shortcode_atts([
            'post_id' => '',
            'short_title' => '',
            'category' => '',
            'random_from_category' => 'no',
            'tag' => '',
            'sticky_only' => 'no',
            'background' => 'white', 
            'subtitle' => '',
            'text_color' => '',
        ], $atts, 'blog_widget_side_image_banner');
        if (!empty($atts['post_id'])) {
            $post_id = intval($atts['post_id']);
            if (!$post_id) {
                return '<div class="blog-widget shape-3" style="padding:2rem;text-align:center;background:#eee;">No post ID provided.</div>';
            }
            $post = get_post($post_id);
            if (!$post || $post->post_status !== 'publish') {
                return '<div class="blog-widget shape-3" style="padding:2rem;text-align:center;background:#eee;">Post not found.</div>';
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
                    return '<div class="blog-widget shape-3" style="padding:2rem;text-align:center;background:#eee;">No sticky posts found.</div>';
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
                return '<div class="blog-widget shape-3 style="padding:2rem;text-align:center;background:#eee;">No posts found matching your criteria.</div>';
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
        // Determine background and subtitle based on shortcode attributes
        $background_class = (!empty($atts['background']) && $atts['background'] === 'dark') ? 'bg-dark' : 'bg-white';
        // Remove text color logic from shortcode handler
        $text_color_class = '';
        if (!empty($atts['text_color']) && in_array($atts['text_color'], ['text-white', 'text-blue', 'text-black'])) {
            $text_color_class = $atts['text_color'];
        }
        $subtitle = (!empty($atts['subtitle']) && $atts['subtitle'] === 'no') ? '' : $short_title;
        return blog_widgets_render_banner([
            'title' => $title,
            'permalink' => $permalink,
            'image' => $image,
            'subtitle' => $subtitle,
            'background_class' => $background_class,
            'text_color_class' => $text_color_class
        ]);
    }
}
add_shortcode('blog_widget_side_image_banner', 'blog_widget_side_image_banner_shortcode');

