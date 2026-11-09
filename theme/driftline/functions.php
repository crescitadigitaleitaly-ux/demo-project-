<?php
/**
 * Driftline theme bootstrap.
 *
 * @package driftline
 */

defined( 'ABSPATH' ) || exit;

define( 'DL_VERSION', '1.0.0' );
define( 'DL_DIR', get_template_directory() );
define( 'DL_URI', get_template_directory_uri() );

require DL_DIR . '/inc/blocks.php';
require DL_DIR . '/inc/seo.php';
require DL_DIR . '/inc/content.php';
require DL_DIR . '/inc/setup.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/site.css' );
		register_nav_menus(
			array(
				'header-left'  => __( 'Header, left side', 'driftline' ),
				'header-right' => __( 'Header, right side', 'driftline' ),
				'footer'       => __( 'Footer', 'driftline' ),
			)
		);
	}
);

/** File version for cache busting. */
function dl_asset_ver( $rel ) {
	$path = DL_DIR . $rel;
	return file_exists( $path ) ? (string) filemtime( $path ) : DL_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'driftline', DL_URI . '/assets/css/site.css', array(), dl_asset_ver( '/assets/css/site.css' ) );
		wp_enqueue_script( 'driftline', DL_URI . '/assets/js/site.js', array(), dl_asset_ver( '/assets/js/site.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
);

// Mark JS as available before first paint; fall back to static content if the script never loads.
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js');setTimeout(function(){if(!window.dlReady){document.documentElement.classList.remove('js');}},4000);</script>\n";
		if ( ! has_site_icon() ) {
			printf( "<link rel=\"icon\" href=\"%s\" type=\"image/svg+xml\">\n", esc_url( DL_URI . '/assets/img/favicon.svg' ) );
		}
		foreach ( array( '400', '600', '700' ) as $w ) {
			printf( "<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url( DL_URI . "/assets/fonts/poppins-latin-{$w}-normal.woff2" ) );
		}
	},
	1
);

// Trim front-end weight the site does not use.
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}
);
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'xmlrpc_enabled', '__return_false' );

// Hide usernames from the REST API and author archives for logged-out visitors.
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);
add_action(
	'template_redirect',
	function () {
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);
