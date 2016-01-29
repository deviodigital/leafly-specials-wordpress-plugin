<?php
/**
 * Scripts
 *
 * @since       1.0.0
 * @package     LeaflySpecials\Scripts
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load scripts and styles
 *
 * @since       1.0.0
 * @return      void
 */
function leaflyspecials_load_scripts() {
	wp_enqueue_style( 'leaflyspecials', LEAFLYSPECIALS_URL . 'assets/css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'leaflyspecials_load_scripts' );


/**
 * Load admin scripts and styles
 *
 * @since       1.1.0
 * @return      void
 */
function leaflyspecials_load_admin_scripts() {
	wp_enqueue_style( 'leaflyspecials', LEAFLYSPECIALS_URL . 'assets/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'leaflyspecials_load_admin_scripts' );
