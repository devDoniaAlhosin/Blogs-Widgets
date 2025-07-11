<?php
/*
Plugin Name: Blogs Widgets
Description: Scalable plugin for blog banner/widgets shapes.
Version: 1.0
Author: Donia Alhosin
*/

add_action('plugins_loaded', function() {
    $shapes_dir = plugin_dir_path(__FILE__) . 'shapes/';
    
    if (is_dir($shapes_dir)) {
        foreach (glob($shapes_dir . '*.php') as $file) {
            include_once $file;
        }
    }
});


add_action('init', function() {
    if (function_exists('vc_add_shortcode_param')) {
        vc_add_shortcode_param('checkbox_search_posts', function($settings, $value) {
            $posts = get_posts(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            $options = array();
            foreach ($posts as $post) {
                $options[] = array(
                    'label' => $post->post_title . ' (ID: ' . $post->ID . ')',
                    'value' => $post->ID
                );
            }
            $values = $value ? explode(',', $value) : array();
            ob_start();
            echo '<div class="vc-checkbox-search-posts-field"></div>';
            echo '<input type="hidden" class="wpb_vc_param_value" name="' . esc_attr($settings['param_name']) . '" value="' . esc_attr($value) . '"/>';
            echo '<script type="text/javascript">jQuery(function(){
                var param = {
                    param_type: "checkbox_search_posts",
                    $field: jQuery(".vc-checkbox-search-posts-field:last"),
                    param_name: "' . esc_js($settings['param_name']) . '",
                    value: "' . esc_js($value) . '",
                    options: ' . json_encode($options) . '
                };
                jQuery(document).trigger("vcParamAdd", [param]);
            });</script>';
            return ob_get_clean();
        }, plugins_url('assets/js/vc-checkbox-search-posts.js', __FILE__));
    }
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'blog-widgets-global-font',
        plugins_url('assets/css/blog-widgets-global.css', __FILE__),
        [],
        '1.0'   
    );
});

function blog_widgets_enqueue_shape_css($tag) {
    static $enqueued = [];
    if (isset($enqueued[$tag])) return;
    $css_map = [
        'blog_widget_shape_1' => 'shape-1.css',
        'featured_sidebar_blogs' => 'shape-2.css', 
        'blog_widget_side_image_banner' => 'shape-side-image-banner.css',
        'blog-widget-box' => 'blog-widget-box.css',
        'default_blog_news_widget' => 'default-blog-news-widget.css',
        'blog_featured_widget_highlighted_title' => 'blog-featured-widget-highlighted-title.css',
        ''
    ];
    if (isset($css_map[$tag])) {
        wp_enqueue_style(
            'blog-widgets-' . $tag,
            plugins_url('assets/css/' . $css_map[$tag], __FILE__),
            [],
            '1.0'
        );
        $enqueued[$tag] = true;
    }
}

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script('vc-checkbox-search-posts', plugins_url('assets/js/vc-checkbox-search-posts.js', __FILE__), array('jquery'), '1.0', true);
});




define('BLOG_WIDGETS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BLOG_WIDGETS_PLUGIN_URL', plugin_dir_url(__FILE__));

