<?php

/**
 * Template: Product Category Archive
 * Shown when browsing /product-category/{slug}/
 */

get_header();

$term = get_queried_object();



?>

<main class="ventech-archive-main">

    <!-- Category Hero Banner -->

    <div class="ventech-category-hero">
        <div class="ventech-category-hero-inner">
            <?php
            $icon_key = get_term_meta($term->term_id, 'ventech_cat_icon', true);
            $icon_svg = ventech_get_category_icon_svg($icon_key ?: 'custom');
            ?>
            <div class="ventech-hero-icon"><?php echo $icon_svg; ?></div>
            <div class="ventech-hero-text">
                <h1 class="ventech-category-title"><?php echo esc_html(strtoupper($term->name)); ?></h1>
                <?php if ($term->description) : ?>
                    <p class="ventech-category-desc"><?php echo esc_html($term->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="ventech-breadcrumb" aria-label="Breadcrumb">
        <div class="ventech-container">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span class="ventech-bc-sep">›</span>
            <a href="<?php echo home_url('/products'); ?>">Products</a>
            <span class="ventech-bc-sep">›</span>
            <span><?php echo esc_html($term->name); ?></span>
        </div>
    </nav>

    <!-- Products Grid -->
    <div class="ventech-container ventech-archive-content">
        <?php
        $query = new WP_Query([
            'post_type'      => 'ventech_product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => [[
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ]],
        ]);

        if ($query->have_posts()) : ?>
            <div class="ventech-products-grid ventech-cols-3">
                <?php while ($query->have_posts()) : $query->the_post();
                    $product_code = get_post_meta(get_the_ID(), '_ventech_product_code', true);
                ?>
                    <a href="<?php the_permalink(); ?>" class="ventech-product-card">
                        <div class="ventech-product-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <div class="ventech-no-image">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" fill="none" stroke="#d0d5dd"
                                        stroke-width="2">
                                        <rect x="10" y="10" width="100" height="100" rx="6" />
                                        <circle cx="42" cy="42" r="14" />
                                        <path d="M10 88 L36 64 L56 80 L78 55 L110 88" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="ventech-product-info">
                            <?php if ($product_code) : ?>
                                <span class="ventech-product-code-badge"><?php echo esc_html($product_code); ?></span>
                            <?php endif; ?>
                            <h3 class="ventech-product-name"><?php the_title(); ?></h3>
                        </div>
                    </a>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="ventech-no-products">
                <p>No products found in this category yet.</p>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php get_footer(); ?>