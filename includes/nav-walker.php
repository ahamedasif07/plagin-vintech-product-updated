<?php
/**
 * Dynamic Product Category Nav Dropdown
 * Automatically injects product categories under "Products" nav item
 */

// ─── Hook into nav menu output ───────────────────────────────────────────────
add_filter( 'wp_nav_menu_items', 'ventech_inject_product_categories', 10, 2 );
function ventech_inject_product_categories( $items, $args ) {
    // Only apply to the primary menu (adjust 'primary' to match your theme's menu location)
    if ( ! in_array( $args->theme_location, [ 'primary', 'main-menu', 'primary-menu', 'main_menu' ], true ) ) {
        return $items;
    }

    // Check if "Products" menu item exists in the items
    if ( strpos( $items, 'ventech-products-parent' ) === false && strpos( strtolower( $items ), 'products' ) === false ) {
        return $items;
    }

    // Already has injected dropdown – skip
    if ( strpos( $items, 'ventech-cat-dropdown' ) !== false ) {
        return $items;
    }

    return $items;
}

// ─── Walker class for custom nav rendering ───────────────────────────────────
class Ventech_Nav_Walker extends Walker_Nav_Menu {

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        // Check if this is the "Products" parent menu item
        if ( strtolower( $item->title ) === 'products' || has_term( '', 'product_category' ) ) {
            $categories = get_terms( [
                'taxonomy'   => 'product_category',
                'hide_empty' => false,
                'parent'     => 0,
            ] );

            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                $classes   = implode( ' ', $item->classes );
                $classes  .= ' menu-item-has-children ventech-has-dropdown';
                $output   .= '<li class="' . esc_attr( $classes ) . '">';
                $output   .= '<a href="' . esc_url( $item->url ) . '" class="ventech-nav-products-link">';
                $output   .= esc_html( $item->title );
                $output   .= ' <span class="ventech-arrow">▾</span></a>';
                $output   .= '<div class="ventech-cat-dropdown">';
                $output   .= '<div class="ventech-cat-grid">';

                foreach ( $categories as $cat ) {
                    $icon_key  = get_term_meta( $cat->term_id, 'ventech_cat_icon', true );
                    $icon_svg  = ventech_get_category_icon_svg( $icon_key ?: 'custom' );
                    $cat_url   = get_term_link( $cat );
                    $output   .= '<a href="' . esc_url( $cat_url ) . '" class="ventech-cat-item">';
                    $output   .= '<span class="ventech-cat-icon">' . $icon_svg . '</span>';
                    $output   .= '<span class="ventech-cat-name">' . esc_html( $cat->name ) . '</span>';
                    $output   .= '</a>';
                }

                $output .= '</div></div></li>';
                return;
            }
        }

        // Default rendering for other items
        parent::start_el( $output, $item, $depth, $args, $id );
    }
}

// ─── Shortcode: nav dropdown standalone ─────────────────────────────────────
add_shortcode( 'ventech_product_nav', 'ventech_product_nav_shortcode' );
function ventech_product_nav_shortcode() {
    $categories = get_terms( [
        'taxonomy'   => 'product_category',
        'hide_empty' => false,
        'parent'     => 0,
    ] );

    if ( is_wp_error( $categories ) || empty( $categories ) ) {
        return '<p>No product categories found.</p>';
    }

    ob_start();
    ?>
    <div class="ventech-cat-grid-standalone">
        <?php foreach ( $categories as $cat ) :
            $icon_key = get_term_meta( $cat->term_id, 'ventech_cat_icon', true );
            $icon_svg = ventech_get_category_icon_svg( $icon_key ?: 'custom' );
            $cat_url  = get_term_link( $cat );
        ?>
        <a href="<?php echo esc_url( $cat_url ); ?>" class="ventech-cat-item-standalone">
            <span class="ventech-cat-icon"><?php echo $icon_svg; ?></span>
            <span class="ventech-cat-name"><?php echo esc_html( $cat->name ); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
