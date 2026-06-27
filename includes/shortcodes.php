<?php
/**
 * Shortcodes:
 * [ventech_products category="slug"] – product grid
 * [ventech_product_detail id="123"] – single product detail
 */

// ─── Product Grid Shortcode ──────────────────────────────────────────────────
add_shortcode( 'ventech_products', 'ventech_products_shortcode' );
function ventech_products_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'category' => '',
        'columns'  => 3,
        'limit'    => -1,
    ], $atts, 'ventech_products' );

    $args = [
        'post_type'      => 'ventech_product',
        'posts_per_page' => intval( $atts['limit'] ),
        'post_status'    => 'publish',
    ];

    if ( ! empty( $atts['category'] ) ) {
        $args['tax_query'] = [[
            'taxonomy' => 'product_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $atts['category'] ),
        ]];
    }

    $query = new WP_Query( $args );
    ob_start();

    if ( $query->have_posts() ) :
    ?>
    <div class="ventech-products-grid ventech-cols-<?php echo intval( $atts['columns'] ); ?>">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="ventech-product-card">
            <div class="ventech-product-image">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium' ); ?>
                <?php else : ?>
                    <div class="ventech-no-image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none" stroke="#ccc" stroke-width="2"><rect x="10" y="10" width="60" height="60" rx="4"/><circle cx="30" cy="30" r="8"/><path d="M10 55 L25 40 L38 52 L52 36 L70 55"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ventech-product-name">
                <span class="ventech-product-code"><?php echo esc_html( get_post_meta( get_the_ID(), '_ventech_product_code', true ) ); ?></span>
                <?php the_title(); ?>
            </div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    else :
        echo '<p class="ventech-no-products">No products found in this category.</p>';
    endif;

    return ob_get_clean();
}

// ─── Dynamic Category Archive Template Hook ──────────────────────────────────
// Replaces the archive loop on product_category taxonomy pages
add_filter( 'template_include', 'ventech_product_category_template' );
function ventech_product_category_template( $template ) {
    if ( is_tax( 'product_category' ) ) {
        $plugin_template = VENTECH_PRODUCTS_PATH . 'templates/taxonomy-product_category.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    if ( is_singular( 'ventech_product' ) ) {
        $plugin_template = VENTECH_PRODUCTS_PATH . 'templates/single-ventech_product.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
