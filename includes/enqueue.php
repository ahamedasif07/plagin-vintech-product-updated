<?php
/**
 * Enqueue Plugin Assets
 */

add_action( 'wp_enqueue_scripts', 'ventech_enqueue_frontend_assets' );
function ventech_enqueue_frontend_assets() {
    wp_enqueue_style(
        'ventech-products',
        VENTECH_PRODUCTS_URL . 'assets/css/ventech-products.css',
        [],
        '1.0.0'
    );
    wp_enqueue_script(
        'ventech-products',
        VENTECH_PRODUCTS_URL . 'assets/js/ventech-products.js',
        [ 'jquery' ],
        '1.0.0',
        true
    );
}

add_action( 'admin_enqueue_scripts', 'ventech_enqueue_admin_assets' );
function ventech_enqueue_admin_assets( $hook ) {
    $screen = get_current_screen();
    if ( $screen && $screen->post_type === 'ventech_product' ) {
        wp_enqueue_media();
        wp_enqueue_style(
            'ventech-admin',
            VENTECH_PRODUCTS_URL . 'assets/css/ventech-admin.css',
            [],
            '1.0.0'
        );
    }
}
