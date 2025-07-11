<?php
if (!function_exists('blog_widgets_shared_banner_vc_array')) {
    /**
     * Returns a Visual Composer array for a banner widget shape.
     *
     * @param string $name        Widget name (for VC display)
     * @param string $base        Widget base (shortcode base)
     * @param string $category    VC tab/category
     * @param string $description Widget description
     * @return array
     */
    function blog_widgets_shared_banner_vc_array($name, $base, $category, $description) {
        return array(
            'name' => $name,
            'base' => $base,
            'category' => $category,
            'icon' => 'vc_icon-vc-media-grid',
            'description' => $description,
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Select Blog Post', 'blog-widgets'),
                    'param_name' => 'post_id',
                    'value' => function_exists('blog_widgets_get_posts_dropdown') ? blog_widgets_get_posts_dropdown() : array(),
                    'description' => __('Choose a specific blog post to display', 'blog-widgets'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Category Filter', 'blog-widgets'),
                    'param_name' => 'category',
                    'value' => function_exists('blog_widgets_get_categories_dropdown') ? blog_widgets_get_categories_dropdown() : array(),
                    'description' => __('Filter posts by category', 'blog-widgets'),
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __('Show Random Post from Category', 'blog-widgets'),
                    'param_name' => 'random_from_category',
                    'value' => array(__('Yes', 'blog-widgets') => 'yes'),
                    'description' => __('If checked, will show a random post from the selected category', 'blog-widgets'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Tag Filter', 'blog-widgets'),
                    'param_name' => 'tag',
                    'value' => function_exists('blog_widgets_get_tags_dropdown') ? blog_widgets_get_tags_dropdown() : array(),
                    'description' => __('Filter posts by tag', 'blog-widgets'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Include Sticky Posts Only', 'blog-widgets'),
                    'param_name' => 'sticky_only',
                    'value' => array(
                        __('No', 'blog-widgets') => 'no',
                        __('Yes', 'blog-widgets') => 'yes',
                    ),
                    'description' => __('Show only sticky posts', 'blog-widgets'),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Short Title', 'blog-widgets'),
                    'param_name' => 'short_title',
                    'description' => __('Optional short title to display above the main title', 'blog-widgets'),
                ),
            ),
        );
    }
    // Helper functions for dropdowns (if not already defined)
if (!function_exists('blog_widgets_get_posts_dropdown')) {
    function blog_widgets_get_posts_dropdown() {
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        
        $dropdown = array(__('-- Select Post --', 'blog-widgets') => '');
        foreach ($posts as $post) {
            $dropdown[$post->post_title] = $post->ID;
        }
        
        return $dropdown;
    }
}

if (!function_exists('blog_widgets_get_categories_dropdown')) {
    function blog_widgets_get_categories_dropdown() {
        $categories = get_categories(array(
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ));
        
        $dropdown = array(__('-- All Categories --', 'blog-widgets') => '');
        foreach ($categories as $category) {
            $dropdown[$category->name] = $category->slug;
        }
        
        return $dropdown;
    }
}

if (!function_exists('blog_widgets_get_tags_dropdown')) {
    function blog_widgets_get_tags_dropdown() {
        $tags = get_tags(array(
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ));
        
        $dropdown = array(__('-- All Tags --', 'blog-widgets') => '');
        foreach ($tags as $tag) {
            $dropdown[$tag->name] = $tag->slug;
        }
        
        return $dropdown;
    }
} 
} 