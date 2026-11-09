<?php
/**
 * Titles, descriptions and search-engine visibility. The demo is kept out of search results.
 *
 * @package driftline
 */

defined( 'ABSPATH' ) || exit;

add_theme_support( 'title-tag' );

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( is_singular() ) {
			$custom = get_post_meta( get_queried_object_id(), '_dl_title', true );
			if ( $custom ) {
				return $custom;
			}
		}
		return $title;
	}
);

/** Meta description for the current view. */
function dl_meta_description() {
	if ( is_singular() ) {
		$desc = get_post_meta( get_queried_object_id(), '_dl_desc', true );
		if ( $desc ) {
			return $desc;
		}
		$post = get_queried_object();
		if ( $post && ! empty( $post->post_excerpt ) ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}
	}
	return get_bloginfo( 'description' );
}

add_action(
	'wp_head',
	function () {
		$desc  = dl_meta_description();
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : home_url( '/' );
		if ( $desc ) {
			printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
		}
		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $url ) );
		printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $desc ) );
		printf( "<meta property=\"og:type\" content=\"website\">\n<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
		$img = get_option( 'dl_og_image' );
		if ( $img ) {
			printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $img ) );
		}
		echo "<meta name=\"theme-color\" content=\"#ffffff\">\n";
	},
	2
);
remove_action( 'wp_head', 'rel_canonical' );

// Concept demo: never index, whatever the Reading setting says.
add_filter(
	'wp_robots',
	function ( $robots ) {
		unset( $robots['max-image-preview'], $robots['index'], $robots['follow'] );
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
		return $robots;
	},
	999
);
add_filter(
	'robots_txt',
	function () {
		return "User-agent: *\nDisallow: /\n";
	},
	999
);
add_action(
	'send_headers',
	function () {
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}
);
add_filter( 'wp_sitemaps_enabled', '__return_false' );
