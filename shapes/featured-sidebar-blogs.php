<?php
require_once __DIR__ . '/includes/shared-side-featured-vc.php';
require_once __DIR__ . '/includes/shared-side-featured-logic.php';

// [featured_sidebar_blogs] shortcode
function featured_sidebar_blogs_shortcode($atts) {
    blog_widgets_enqueue_shape_css('featured_sidebar_blogs');
    $atts = shortcode_atts([
        'post_ids_sidebar' => '',
        'post_id_featured' => '',
        'category' => '',
        'tag' => '',
        'featured_from_sticky' => 'no',
        'css' => '',
        'el_class' => '',
    ], $atts, 'featured_sidebar_blogs');

    list($sidebar_posts, $featured_post) = shared_side_featured_logic($atts);

    ob_start();
    if (!empty($atts['category'])) {
        $cat_obj = get_category_by_slug($atts['category']);
        if ($cat_obj) {
            echo '<div class="selected-category">' . esc_html($cat_obj->name) . '</div>';
        }
    }
    ?>
    <div class="blog-widget-featured-sidebar-grid  blog-widget <?php echo vc_shortcode_custom_css_class($atts['css'], ' '); ?><?php echo !empty($atts['el_class']) ? ' ' . esc_attr($atts['el_class']) : ''; ?>">
        <div class="digest-sidebar">
            <?php foreach ($sidebar_posts as $post): ?>
                <article>
                    <h3><a href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html(get_the_title($post)); ?></a></h3>
                    <time datetime="<?php echo esc_attr(get_the_date('c', $post)); ?>">
                        <?php 
                        $arabic_months = array(
                            'January' => 'يناير',
                            'February' => 'فبراير',
                            'March' => 'مارس',
                            'April' => 'أبريل',
                            'May' => 'مايو',
                            'June' => 'يونيو',
                            'July' => 'يوليو',
                            'August' => 'أغسطس',
                            'September' => 'سبتمبر',
                            'October' => 'أكتوبر',
                            'November' => 'نوفمبر',
                            'December' => 'ديسمبر'
                        );
                        $english_date = get_the_date('j F Y', $post);
                        $arabic_date = str_replace(array_keys($arabic_months), array_values($arabic_months), $english_date);
                        $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                        $arabic_numbers = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
                        $arabic_date = str_replace($english_numbers, $arabic_numbers, $arabic_date);
                        echo esc_html($arabic_date);
                        ?>
                    </time>
                    <?php
                    $sidebar_cats = get_the_category($post->ID);
                    if (!empty($sidebar_cats)) {
                        $cat = $sidebar_cats[0];
                        $cat_name = $cat->name;
                        $cat_link = get_category_link($cat->term_id);
                        if (strtolower($cat_name) === 'uncategorized') {
                            $cat_name = __('Uncategorized', 'blog-widgets');
                        }
                    }
                    ?>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="digest-featured">
            <?php if ($featured_post): ?>
                <?php
                $featured_cats = get_the_category($featured_post->ID);
                if (!empty($featured_cats)) {
                    $cat = $featured_cats[0];
                    $cat_name = $cat->name;
                    $cat_link = get_category_link($cat->term_id);
                    if (strtolower($cat_name) === 'uncategorized') {
                        $cat_name = __('Uncategorized', 'blog-widgets');
                    }
                    echo '<div class="featured-category"><a href="' . esc_url($cat_link) . '" style="color:#fff; text-decoration:none;">' . esc_html($cat_name) . '</a></div>';
                }
                ?>
                <article class="banner-container">
                    <div class="banner-overlay">
                        <h2><a href="<?php echo esc_url(get_permalink($featured_post)); ?>"><?php echo esc_html(get_the_title($featured_post)); ?></a></h2>
                    </div>
                    <?php if (has_post_thumbnail($featured_post)): ?>
                        <div class="widget-2-featured-image">
                            <?php echo get_the_post_thumbnail($featured_post, 'full', ['alt' => esc_attr(get_the_title($featured_post))]); ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('featured_sidebar_blogs', 'featured_sidebar_blogs_shortcode');

// Visual Composer Integration for Featured & Sidebar Blogs
function featured_sidebar_blogs_vc_map() {
    if (!function_exists('vc_map')) {
        return;
    }
    vc_map(shared_side_featured_vc_array(
        __('Featured & Sidebar Blogs', 'blog-widgets'),
        'featured_sidebar_blogs',
        __('Display a featured blog post with a sidebar of other posts', 'blog-widgets')
    ));
}
add_action('vc_before_init', 'featured_sidebar_blogs_vc_map'); 