<?php
/**
 * Register Custom Post Type: Products
 * Register Custom Taxonomy: Product Category
 */

// ─── Register CPT ────────────────────────────────────────────────────────────
add_action( 'init', 'ventech_register_product_cpt' );
function ventech_register_product_cpt() {
    $labels = [
        'name'               => 'Products',
        'singular_name'      => 'Product',
        'menu_name'          => 'Products',
        'add_new'            => 'Add New Product',
        'add_new_item'       => 'Add New Product',
        'edit_item'          => 'Edit Product',
        'new_item'           => 'New Product',
        'view_item'          => 'View Product',
        'search_items'       => 'Search Products',
        'not_found'          => 'No products found',
        'not_found_in_trash' => 'No products found in Trash',
    ];

    register_post_type( 'ventech_product', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'products' ],
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'menu_icon'          => 'dashicons-products',
        'show_in_rest'       => true,
    ] );
}

// ─── Register Taxonomy ───────────────────────────────────────────────────────
add_action( 'init', 'ventech_register_product_category' );
function ventech_register_product_category() {
    $labels = [
        'name'              => 'Product Categories',
        'singular_name'     => 'Product Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Product Categories',
    ];

    register_taxonomy( 'product_category', [ 'ventech_product' ], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => [ 'slug' => 'product-category' ],
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ] );
}

// ─── Flush rewrite on activation ────────────────────────────────────────────
register_activation_hook( VENTECH_PRODUCTS_PATH . '../ventech-products.php', 'ventech_flush_rewrites' );
function ventech_flush_rewrites() {
    ventech_register_product_cpt();
    ventech_register_product_category();
    flush_rewrite_rules();
}

// ─── Add SVG icon field to taxonomy ─────────────────────────────────────────
add_action( 'product_category_add_form_fields', 'ventech_cat_add_svg_field' );
function ventech_cat_add_svg_field( $taxonomy ) {
    ?>
    <div class="form-field">
        <label for="ventech_cat_icon">Category SVG Icon</label>
        <select name="ventech_cat_icon" id="ventech_cat_icon">
            <?php foreach ( ventech_get_icon_options() as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description">Choose an icon for this category (shown in nav menu).</p>
    </div>
    <?php
}

add_action( 'product_category_edit_form_fields', 'ventech_cat_edit_svg_field' );
function ventech_cat_edit_svg_field( $term ) {
    $icon = get_term_meta( $term->term_id, 'ventech_cat_icon', true );
    ?>
    <tr class="form-field">
        <th><label for="ventech_cat_icon">Category SVG Icon</label></th>
        <td>
            <select name="ventech_cat_icon" id="ventech_cat_icon">
                <?php foreach ( ventech_get_icon_options() as $key => $label ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
}

add_action( 'edited_product_category', 'ventech_save_cat_icon' );
add_action( 'created_product_category', 'ventech_save_cat_icon' );
function ventech_save_cat_icon( $term_id ) {
    if ( isset( $_POST['ventech_cat_icon'] ) ) {
        update_term_meta( $term_id, 'ventech_cat_icon', sanitize_text_field( $_POST['ventech_cat_icon'] ) );
    }
}

// ─── Available Icons ─────────────────────────────────────────────────────────
function ventech_get_icon_options() {
    return [
        'air_diffusers'          => 'Air Diffusers',
        'registers_grilles'      => 'Registers & Grilles',
        'floor_air'              => 'Floor Air Distribution',
        'vav_terminals'          => 'VAV Terminals',
        'volume_control'         => 'Volume Control & Relief Dampers',
        'fire_smoke'             => 'Fire & Smoke Dampers',
        'outside_acoustic'       => 'Outside & Acoustic Louvers',
        'airflow_control'        => 'Airflow Control & Monitoring',
        'critical_environments'  => 'Critical Environments',
        'noise_control'          => 'Noise Control',
        'damper'                 => 'Damper',
        'motorised_diffuser'     => 'Motorised Diffuser-Grille',
        'vav_diffuser'           => 'VAV Diffuser-Grille',
        'accessories'            => 'Accessories',
        'custom'                 => 'Custom / Other',
    ];
}

// ─── SVG Icon Renderer ───────────────────────────────────────────────────────
function ventech_get_category_icon_svg( $icon_key ) {
    $icons = [
        'air_diffusers' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="6" width="36" height="36" rx="2"/><circle cx="24" cy="24" r="10"/><circle cx="24" cy="24" r="4"/><line x1="24" y1="6" x2="24" y2="14"/><line x1="24" y1="34" x2="24" y2="42"/><line x1="6" y1="24" x2="14" y2="24"/><line x1="34" y1="24" x2="42" y2="24"/></svg>',

        'registers_grilles' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="40" height="40" rx="2"/><line x1="4" y1="12" x2="44" y2="12"/><line x1="4" y1="20" x2="44" y2="20"/><line x1="4" y1="28" x2="44" y2="28"/><line x1="4" y1="36" x2="44" y2="36"/><line x1="12" y1="4" x2="12" y2="44"/><line x1="20" y1="4" x2="20" y2="44"/><line x1="28" y1="4" x2="28" y2="44"/><line x1="36" y1="4" x2="36" y2="44"/></svg>',

        'floor_air' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="30" width="40" height="14" rx="2"/><line x1="12" y1="30" x2="12" y2="44"/><line x1="20" y1="30" x2="20" y2="44"/><line x1="28" y1="30" x2="28" y2="44"/><line x1="36" y1="30" x2="36" y2="44"/><path d="M16 26 C16 14 32 14 32 26"/><path d="M10 22 C10 6 38 6 38 22"/></svg>',

        'vav_terminals' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><circle cx="24" cy="24" r="16"/><circle cx="24" cy="24" r="2" fill="currentColor"/><line x1="24" y1="8" x2="24" y2="16"/><line x1="24" y1="32" x2="24" y2="40"/><line x1="8" y1="24" x2="16" y2="24"/><line x1="32" y1="24" x2="40" y2="24"/><path d="M16 16 L24 24 L32 16"/></svg>',

        'volume_control' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="16" width="40" height="16" rx="2"/><line x1="24" y1="4" x2="24" y2="16"/><line x1="24" y1="32" x2="24" y2="44"/><path d="M14 24 L34 24" stroke-width="3"/><circle cx="24" cy="24" r="4" fill="currentColor" stroke="none"/></svg>',

        'fire_smoke' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><path d="M24 6 C18 14 12 16 14 26 C16 34 22 38 24 42 C26 38 32 34 34 26 C36 16 30 14 24 6Z"/><path d="M24 22 C22 26 20 28 21 32 C22 35 23 36 24 38 C25 36 26 35 27 32 C28 28 26 26 24 22Z" fill="currentColor" stroke="none"/></svg>',

        'outside_acoustic' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="8" width="40" height="32" rx="2"/><line x1="4" y1="18" x2="44" y2="18"/><line x1="4" y1="28" x2="44" y2="28"/><path d="M10 18 L14 8"/><path d="M20 18 L24 8"/><path d="M30 18 L34 8"/><path d="M10 28 L14 40"/><path d="M20 28 L24 40"/><path d="M30 28 L34 40"/></svg>',

        'airflow_control' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="16" width="12" height="16" rx="1"/><rect x="20" y="10" width="12" height="28" rx="1"/><rect x="34" y="20" width="8" height="8" rx="1"/><path d="M4 40 L44 40"/><path d="M8 14 L8 8 L40 8 L40 18"/></svg>',

        'critical_environments' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="40" height="40" rx="3"/><rect x="10" y="10" width="28" height="28" rx="2"/><rect x="16" y="16" width="16" height="16" rx="2"/><circle cx="24" cy="24" r="3" fill="currentColor" stroke="none"/></svg>',

        'noise_control' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 20 L16 20 L24 10 L24 38 L16 28 L8 28 Z"/><path d="M30 18 Q36 24 30 30"/><path d="M34 14 Q44 24 34 34"/></svg>',

        'damper' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="40" height="40" rx="2"/><line x1="8" y1="24" x2="40" y2="24"/><path d="M12 12 L36 36" stroke-width="2.5"/><path d="M24 8 L24 16 M24 32 L24 40"/></svg>',

        'motorised_diffuser' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><circle cx="24" cy="24" r="18"/><circle cx="24" cy="24" r="10"/><circle cx="24" cy="24" r="3" fill="currentColor" stroke="none"/><path d="M24 6 L26 14 L22 14 Z"/><path d="M42 24 L34 26 L34 22 Z"/><path d="M24 42 L22 34 L26 34 Z"/><path d="M6 24 L14 22 L14 26 Z"/></svg>',

        'vav_diffuser' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><circle cx="24" cy="24" r="16"/><path d="M14 14 L34 34"/><path d="M34 14 L14 34"/><circle cx="24" cy="24" r="5"/></svg>',

        'accessories' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><circle cx="24" cy="24" r="18"/><path d="M24 6 L24 12 M24 36 L24 42 M6 24 L12 24 M36 24 L42 24"/><path d="M11.5 11.5 L15.7 15.7 M32.3 32.3 L36.5 36.5 M36.5 11.5 L32.3 15.7 M15.7 32.3 L11.5 36.5"/></svg>',

        'custom' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="40" height="40" rx="4"/><line x1="24" y1="14" x2="24" y2="34"/><line x1="14" y1="24" x2="34" y2="24"/></svg>',
    ];

    return isset( $icons[ $icon_key ] ) ? $icons[ $icon_key ] : $icons['custom'];
}
