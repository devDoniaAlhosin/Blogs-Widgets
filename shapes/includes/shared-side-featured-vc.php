<?php
if (!function_exists('shared_side_featured_vc_array')) {
    /**
     * Returns a VC params array for side/featured post widgets, including Design tab.
     *
     * @param string $name        Widget name
     * @param string $base        Shortcode base
     * @param string $description Widget description
     * @return array
     */
    function shared_side_featured_vc_array($name, $base, $description) {
        return array(
            'name' => $name,
            'base' => $base,
            'category' => __('Custom Widgets', 'blog-widgets'),
            'icon' => 'vc_icon-vc-media-grid',
            'description' => $description,
            'params' => array(
                array(
                    'type' => 'checkbox_search_posts',
                    'heading' => __('Select Posts for Sidebar', 'blog-widgets'),
                    'param_name' => 'post_ids_sidebar',
                    'value' => array(), 
                    'description' => __('Select one or more posts to display in the sidebar. Use the search box to filter.', 'blog-widgets'),
                    'admin_label' => true,
                    'save_always' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Featured Post', 'blog-widgets'),
                    'param_name' => 'post_id_featured',
                    'value' => function_exists('blog_widgets_get_posts_dropdown') ? blog_widgets_get_posts_dropdown() : array(),
                    'description' => __('Choose a specific post for the featured area (leave empty for random)', 'blog-widgets'),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Category Filter', 'blog-widgets'),
                    'param_name' => 'category',
                    'value' => function_exists('blog_widgets_get_categories_dropdown') ? blog_widgets_get_categories_dropdown() : array(),
                    'description' => __('Filter posts by category (if no posts selected, will use random posts from this category)', 'blog-widgets'),
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
                    'heading' => __('Featured from Sticky Posts', 'blog-widgets'),
                    'param_name' => 'featured_from_sticky',
                    'value' => array(
                        __('No', 'blog-widgets') => 'no',
                        __('Yes', 'blog-widgets') => 'yes',
                    ),
                    'description' => __('If set to Yes, a random sticky post will be used as the featured post (unless a specific featured post is selected above). If set to No, sticky posts are ignored and the featured post is chosen from the filtered posts above.', 'blog-widgets'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __('CSS box', 'blog-widgets'),
                    'param_name' => 'css',
                    'group' => __('Design Options', 'blog-widgets'),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Extra class name', 'blog-widgets'),
                    'param_name' => 'el_class',
                    'description' => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'blog-widgets'),
                    'group' => __('Design Options', 'blog-widgets'),
                ),
            ),
        );
    }
} 