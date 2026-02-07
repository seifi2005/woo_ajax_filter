<?php
/*
Plugin Name: Woo Advanced Ajax Filter
Description: فیلتر پیشرفته ووکامرس (دسته‌بندی، قیمت، ویژگی‌ها) با Ajax
Version: 1.1
Author: سیفی
*/

if (!defined('ABSPATH')) exit;

/* enqueue assets */
add_action('wp_enqueue_scripts', function () {

    // jQuery UI
    wp_enqueue_script('jquery-ui-slider');

    // CSS اسلایدر
    wp_enqueue_style(
        'jquery-ui-css',
        'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css'
    );

    // CSS افزونه
    wp_enqueue_style(
        'woo-advanced-filter-css',
        plugin_dir_url(__FILE__) . 'assets/css/style.css'
    );

    // JS افزونه
    wp_enqueue_script(
        'woo-advanced-filter-js',
        plugin_dir_url(__FILE__) . 'assets/js/filter.js',
        ['jquery', 'jquery-ui-slider'],
        '1.1',
        true
    );

    wp_localize_script('woo-advanced-filter-js', 'wooFilter', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

/* shortcode */
add_shortcode('woo_advanced_filter', function () {
    ob_start();
?>

    <div id="woo-filter">
        <!-- Categories -->
        <div class="filter-box">
            <h4>دسته‌بندی</h4>
            <?php
            $parent_cats = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'parent'     => 0
            ]);

            if (!is_wp_error($parent_cats) && !empty($parent_cats)) :
                foreach ($parent_cats as $parent) :
                    if (!is_object($parent) || !isset($parent->term_id)) continue;

                    // دریافت زیردسته‌ها تا عمق 1
                    $children = get_terms([
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                        'parent'     => $parent->term_id
                    ]);

                    $has_children = (!is_wp_error($children) && !empty($children));
            ?>
                    <div class="cat-group">
                        <?php if ($has_children) : ?>
                            <div class="cat-parent"><?= esc_html($parent->name); ?></div>
                            <div class="cat-children">
                                <?php foreach ($children as $child) : ?>
                                    <?php if (!is_object($child) || !isset($child->term_id)) continue; ?>
                                    <label class="cat-child">
                                        <input type="checkbox"
                                            class="filter-category"
                                            value="<?= esc_attr($child->term_id); ?>">
                                        <span class="cat-name"><?= esc_html($child->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <label class="cat-single">
                                <input type="checkbox"
                                    class="filter-category"
                                    value="<?= esc_attr($parent->term_id); ?>">
                                <span class="cat-name"><?= esc_html($parent->name); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <!-- Price -->
        <div class="filter-box">
            <h4>قیمت</h4>
            <div id="price-slider"></div>
            <p class="price-range">
                <span id="min-price"></span> تومان
                تا
                <span id="max-price"></span> تومان
            </p>
            <input type="hidden" id="price-min">
            <input type="hidden" id="price-max">
        </div>

        <div class="filter-box">
            <button type="button" id="woo-filter-apply" class="button woo-filter-apply">اعمال فیلتر</button>
        </div>
    </div>

<?php
    return ob_get_clean();
});

/* ajax */
add_action('wp_ajax_woo_advanced_filter', 'woo_advanced_filter_ajax');
add_action('wp_ajax_nopriv_woo_advanced_filter', 'woo_advanced_filter_ajax');

function woo_advanced_filter_ajax()
{

    $args = [
        'post_type' => 'product',
        'posts_per_page' => 12,
        'tax_query' => ['relation' => 'AND'],
        'meta_query' => [],
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    ];

    if (!empty($_POST['categories'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => array_map('intval', $_POST['categories'])
        ];
    }

    if (isset($_POST['min'], $_POST['max'])) {
        $args['meta_query'][] = [
            'key'     => '_price',
            'value'   => [intval($_POST['min']), intval($_POST['max'])],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC'
        ];
    }

    $q = new WP_Query($args);

    if ($q->have_posts()) {
        woocommerce_product_loop_start();
        while ($q->have_posts()) {
            $q->the_post();
            wc_get_template_part('content', 'product');
        }
        woocommerce_product_loop_end();
    } else {
        echo '<p class="no-result">محصولی یافت نشد</p>';
    }

    wp_reset_postdata();
    wp_die();
}
