<?php
if (!function_exists('blog_widgets_render_banner')) {
    function blog_widgets_render_banner($args) {
        $title = esc_html($args['title']);
        $permalink = esc_url($args['permalink']);
        $image = esc_url($args['image']);
        $subtitle = !empty($args['subtitle']) ? esc_html($args['subtitle']) : '';
        $background_class = !empty($args['background_class']) ? esc_attr($args['background_class']) : '';
        $text_color_class = !empty($args['text_color_class']) ? esc_attr($args['text_color_class']) : '';
        $container_class = !empty($args['container_class']) ? esc_attr($args['container_class']) : '';
        $html = '<div class="blog-widget side-image-banner ' . $background_class . ' ' . $container_class . '">';
        $html .= '<div class="side-banner-text-conntainer">';
        $html .= '<div class="side-banner-text">';
        if ($subtitle) {
            $html .= '<h3 class="side-banner-subtitle">' . $subtitle . '</h3>';
        }
        $html .= '<h2 class="side-banner-title ' . $text_color_class . '"><a href="' . $permalink . '">' . $title . '</a></h2>';
        $html .= '</div></div>';
        $html .= '<div class="side-banner-image"><img src="' . $image . '" alt="' . $title . '"></div>';
        $html .= '</div>';
        return $html;
    }
} 