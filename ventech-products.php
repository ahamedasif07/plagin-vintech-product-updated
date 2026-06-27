<?php
/**
 * Plugin Name: Ventech Products Manager
 * Description: Custom Product Post Type with dynamic nav categories, product grid, and detail pages with PDF download.
 * Version: 1.0.0
 * Author: Ventech
 * Text Domain: ventech-products
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'VENTECH_PRODUCTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'VENTECH_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );

require_once VENTECH_PRODUCTS_PATH . 'includes/cpt-register.php';
require_once VENTECH_PRODUCTS_PATH . 'includes/nav-walker.php';
require_once VENTECH_PRODUCTS_PATH . 'includes/meta-boxes.php';
require_once VENTECH_PRODUCTS_PATH . 'includes/shortcodes.php';
require_once VENTECH_PRODUCTS_PATH . 'includes/enqueue.php';
require_once VENTECH_PRODUCTS_PATH . 'includes/ajax-handlers.php';
